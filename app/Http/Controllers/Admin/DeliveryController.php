<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * List all deliveries for the admin.
     */
    public function index(Request $request)
    {
        $query = Delivery::with(['invoice', 'customer', 'driver', 'latestLocation']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($c) => $c->where('business_name', 'like', "%{$search}%"))
                  ->orWhereHas('invoice', fn($i) => $i->where('invoice_number', 'like', "%{$search}%"))
                  ->orWhereHas('driver', fn($d) => $d->where('name', 'like', "%{$search}%"));
            });
        }

        $deliveries = $query->orderByRaw("FIELD(status,'in_transit','pending','delivered','cancelled')")
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);

        $stats = [
            'total'      => Delivery::count(),
            'pending'    => Delivery::where('status', 'pending')->count(),
            'in_transit' => Delivery::where('status', 'in_transit')->count(),
            'delivered'  => Delivery::where('status', 'delivered')->count(),
        ];

        return view('admin.deliveries.index', compact('deliveries', 'stats'));
    }

    /**
     * Show a single delivery with the Geoapify live-tracking map.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['invoice.items.inventory', 'customer', 'driver', 'latestLocation']);
        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * API: return the latest GPS location of a delivery. Polled by the admin map.
     */
    public function location(Delivery $delivery)
    {
        $loc = $delivery->latestLocation;

        if (!$loc) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'     => true,
            'latitude'  => (float) $loc->latitude,
            'longitude' => (float) $loc->longitude,
            'updated'   => $loc->created_at->diffForHumans(),
            'status'    => $delivery->status,
        ]);
    }

    /**
     * Admin can manually cancel a delivery.
     */
    public function cancel(Delivery $delivery)
    {
        if (in_array($delivery->status, ['pending', 'in_transit'])) {
            $delivery->update(['status' => 'cancelled']);
        }

        return back()->with('success', 'Delivery cancelled.');
    }

    /**
     * Admin can reassign driver.
     */
    public function reassign(Request $request, Delivery $delivery)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $delivery->update(['assigned_user_id' => $request->user_id]);

        return back()->with('success', 'Driver reassigned successfully.');
    }
}
