<?php

namespace App\Services;

use App\Mail\OrderStatusMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send an order status notification email to a customer.
     */
    public function notifyOrderStatus(User $customer, string $title, string $body, array $data = []): bool
    {
        if (empty($customer->email)) {
            Log::warning('EmailNotification: customer has no email address.', ['customer_id' => $customer->id]);
            return false;
        }

        try {
            Mail::to($customer->email)->send(new OrderStatusMail($title, $body, $data));
            return true;
        } catch (\Throwable $e) {
            Log::error('EmailNotification: failed to send order status email.', [
                'customer_id' => $customer->id,
                'error'       => $e->getMessage(),
            ]);
            return false;
        }
    }
}
