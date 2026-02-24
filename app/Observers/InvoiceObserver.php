<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\AutoTaskAssignmentService;

class InvoiceObserver
{
    protected $autoTaskService;

    public function __construct(AutoTaskAssignmentService $autoTaskService)
    {
        $this->autoTaskService = $autoTaskService;
    }

    /**
     * Handle the Invoice "updated" event.
     * Automatically create delivery task when invoice is marked as paid
     */
    public function updated(Invoice $invoice)
    {
        // Check if status was changed to 'paid'
        if ($invoice->isDirty('status') && $invoice->status === 'paid') {
            $this->autoTaskService->assignDeliveryForPaidInvoice($invoice);
        }
    }
}
