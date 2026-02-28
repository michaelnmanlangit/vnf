<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Delivery;

class InvoiceObserver
{
    /**
     * Handle the Invoice "updated" event.
     * Create an unassigned Delivery record when invoice is marked as paid.
     * Admin will assign the driver manually.
     */
    public function updated(Invoice $invoice)
    {
        if ($invoice->isDirty('status') && $invoice->status === 'paid') {
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
                // Update notes on existing delivery to reflect the invoice notes
                $existing->update(['notes' => $invoice->notes ?? null]);
            }
        }
    }
}
