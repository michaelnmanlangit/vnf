<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryLocation;
use Illuminate\Http\Request;

class DeliveryDashboardController extends Controller
{
    /**
     * Show the delivery personnel dashboard with their assigned deliveries.
     */
    public function index()
    {
        $userId = auth()->id();

        $activeDelivery = Delivery::with(['invoice', 'customer'])
            ->where('assigned_user_id', $userId)
            ->whereIn('status', ['pending', 'in_transit'])
            ->orderByRaw("FIELD(status,'in_transit','pending')")
            ->first();

        $pastDeliveries = Delivery::with(['invoice', 'customer'])
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
        }

        return redirect()->route('delivery.dashboard')->with('success', 'Delivery marked as completed!');
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
