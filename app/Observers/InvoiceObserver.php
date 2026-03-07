<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Delivery;
use App\Models\Order;

class InvoiceObserver
{
    /**
     * Handle the Invoice "updated" event.
     * Create an unassigned Delivery record when invoice is marked as paid.
     * Admin will assign the driver manually.
     */
    public function updated(Invoice $invoice)
    {
        // wasChanged() is correct here — isDirty() returns false after save
        if ($invoice->wasChanged('status') && $invoice->status === 'paid') {
            $existing = Delivery::where('invoice_id', $invoice->id)->first();
            if (!$existing) {
                Delivery::create([
                    'invoice_id'       => $invoice->id,
                    'customer_id'      => $invoice->customer_id,
                    'assigned_user_id' => null,
                    'status'           => 'pending',
                    'notes'            => $invoice->notes ?? null,
                ]);
            } else {
                $existing->update(['notes' => $invoice->notes ?? null]);
            }

            // Sync the linked Order: mark payment received, keep status as confirmed
            // (status progresses to out_for_delivery / delivered via the Delivery events)
            $order = Order::where('invoice_id', $invoice->id)->first();
            if ($order) {
                $latestPayment = $invoice->payments()->latest()->first();
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => $latestPayment?->payment_method,
                ]);
            }
        }
    }
}
