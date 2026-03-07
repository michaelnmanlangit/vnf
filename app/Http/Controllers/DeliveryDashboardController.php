<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryLocation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryDashboardController extends Controller
{
    /**
     * Show the delivery personnel dashboard with their assigned deliveries.
     */
    public function index()
    {
        $userId = auth()->id();

        $activeDelivery = Delivery::with(['invoice.items', 'invoice.order', 'customer'])
            ->where('assigned_user_id', $userId)
            ->whereIn('status', ['pending', 'in_transit'])
            ->orderByRaw("FIELD(status,'in_transit','pending')")
            ->first();

        $pastDeliveries = Delivery::with(['invoice.items', 'invoice.order', 'customer'])
            ->where('assigned_user_id', $userId)
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->limit(10)
            ->get();

        return view('delivery.dashboard', compact('activeDelivery', 'pastDeliveries'));
    }

    /**
     * Delivery personnel starts a delivery (status → in_transit).
     */
    public function start(Delivery $delivery)
    {
        if ($delivery->assigned_user_id !== auth()->id()) {
            abort(403);
        }

        if ($delivery->status === 'pending') {
            $delivery->update([
                'status'     => 'in_transit',
                'started_at' => now(),
            ]);

            // Sync linked Order status so customer portal tracks progress
            $order = $delivery->invoice?->order;
            if ($order) {
                $order->update(['status' => 'out_for_delivery']);
            }
        }

        return back()->with('success', 'Delivery started! Your location is now being tracked.');
    }

    /**
     * Delivery personnel marks delivery as done (status → delivered).
     */
    public function complete(Delivery $delivery)
    {
        if ($delivery->assigned_user_id !== auth()->id()) {
            abort(403);
        }

        if ($delivery->status === 'in_transit') {
            $delivery->update([
                'status'       => 'delivered',
                'delivered_at' => now(),
            ]);

            // Sync linked Order status so customer portal shows delivered
            $order = $delivery->invoice?->order;
            if ($order) {
                $order->update([
                    'status'       => 'delivered',
                    'delivered_at' => now(),
                ]);
            }
        }

        return redirect()->route('delivery.dashboard')->with('success', 'Delivery marked as completed!');
    }

    /**
     * Record COD cash collection and mark invoice as paid.
     */
    public function collectCod(Request $request, Delivery $delivery)
    {
        if ($delivery->assigned_user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'amount_collected' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $invoice = $delivery->invoice;
            $order   = $invoice?->order;

            // Record the cash payment
            Payment::create([
                'invoice_id'      => $invoice->id,
                'payment_method'  => 'cash',
                'amount'          => $invoice->total_amount,
                'tendered_amount' => $request->amount_collected,
                'change_amount'   => max(0, $request->amount_collected - $invoice->total_amount),
                'payment_date'    => now(),
                'notes'           => 'COD collected by driver on delivery',
            ]);

            // Mark invoice as paid
            $invoice->update(['status' => 'paid']);

            // Mark order payment as paid and mark as delivered
            if ($order) {
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'cash',
                    'status'         => 'delivered',
                    'delivered_at'   => now(),
                ]);
            }

            // Mark delivery as delivered
            if ($delivery->status === 'in_transit' || $delivery->status === 'pending') {
                $delivery->update([
                    'status'       => 'delivered',
                    'delivered_at' => now(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }

        return redirect()->route('delivery.dashboard')->with('success', 'COD collected and delivery completed!');
    }

    /**
     * Receive GPS coordinates from the delivery person's browser.
     * Called every ~5 seconds via fetch() from the delivery dashboard.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
        ]);

        $delivery = Delivery::find($request->delivery_id);

        if (!$delivery || $delivery->assigned_user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DeliveryLocation::create([
            'delivery_id' => $delivery->id,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return response()->json(['ok' => true]);
    }
}
