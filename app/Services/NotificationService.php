<?php

namespace App\Services;

use App\Models\DocumentConsent;
use App\Models\Enquiry;
use App\Models\Grievance;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Send and log a transactional notification across specified delivery channel.
     */
    public function send(
        string $channel,
        string $recipient,
        string $eventType,
        string $message,
        ?string $subject = null,
        ?User $user = null,
        ?string $dltTemplateId = null
    ): NotificationLog {
        $status = NotificationLog::STATUS_DELIVERED;
        $error = null;
        $providerMsgId = 'MSG-'.Str::upper(Str::random(12));

        try {
            if ($channel === NotificationLog::CHANNEL_MAIL) {
                // In production this routes via configured Hostinger SMTP / SES; in local/testing log or array
                Mail::raw($message, function ($mail) use ($recipient, $subject) {
                    $mail->to($recipient)->subject($subject ?? 'Franchise Dekho Notification');
                });
            } elseif ($channel === NotificationLog::CHANNEL_SMS_DLT) {
                // Regulatory Indian DLT SMS simulation / provider dispatch
                Log::info("SMS_DLT dispatch to {$recipient} [Template: {$dltTemplateId}]: {$message}");
            }
        } catch (\Throwable $e) {
            $status = NotificationLog::STATUS_FAILED;
            $error = $e->getMessage();
            Log::error("Notification delivery failure: {$error}");
        }

        return NotificationLog::create([
            'user_id' => $user?->id,
            'channel' => $channel,
            'recipient' => $recipient,
            'event_type' => $eventType,
            'subject' => $subject,
            'message_body' => $message,
            'dlt_template_id' => $dltTemplateId,
            'status' => $status,
            'provider_message_id' => $providerMsgId,
            'error_details' => $error,
        ]);
    }

    /**
     * Transactional notification on verified payment & tax invoice generation.
     */
    public function notifyPaymentReceived(Order $order): void
    {
        $subject = "Payment Receipt & Tax Invoice — Order #{$order->order_number}";
        $message = "Dear {$order->billing_name},\n\nWe have successfully received your payment of {$order->formatted_total} for '{$order->servicePackage->name}'.\nYour statutory GST Tax Invoice #{$order->invoice?->invoice_number} has been generated and is available for download in your portal.\n\nThank you for choosing Franchise Dekho.\nPlatform Operations Team";

        // Dispatch Email
        $this->send(
            NotificationLog::CHANNEL_MAIL,
            $order->billing_email,
            'payment_successful',
            $message,
            $subject,
            $order->user
        );

        // Dispatch Transactional DLT SMS Notice
        if ($order->billing_phone) {
            $smsText = "Franchise Dekho: Payment of {$order->formatted_total} received for Order {$order->order_number}. Invoice generated. Check portal.";
            $this->send(
                NotificationLog::CHANNEL_SMS_DLT,
                $order->billing_phone,
                'payment_successful',
                $smsText,
                null,
                $order->user,
                'DLT-FD-PAY-01'
            );
        }
    }

    /**
     * Transactional notification when customer consent is required for brand sharing.
     */
    public function notifyConsentRequested(DocumentConsent $consent): void
    {
        $user = $consent->user;
        $subject = "Action Required: Document Sharing Authorization Request — Case #{$consent->enquiry->reference_code}";
        $brandName = $consent->opportunity?->company?->legal_name ?? 'the franchisor';
        $message = "Dear {$user->name},\n\nYour Case Manager has prepared a confidential document submission pack for {$brandName}.\nIn accordance with our strict privacy standard, no documents will be delivered without your explicit confirmation.\n\nPlease review and grant or decline authorization in your Customer Portal: ".route('customer.cases.documents.show', $consent->enquiry_id)."\n\nFranchise Dekho Coordination Team";

        $this->send(
            NotificationLog::CHANNEL_MAIL,
            $user->email,
            'consent_requested',
            $message,
            $subject,
            $user
        );
    }

    /**
     * Transactional notification when case status transitions.
     */
    public function notifyCaseStageUpdated(Enquiry $enquiry, string $newStage): void
    {
        if (! $enquiry->user) {
            return;
        }

        $subject = "Case Status Update: #{$enquiry->reference_code}";
        $message = "Dear {$enquiry->full_name},\n\nYour application coordination case has moved to stage: ".ucwords(str_replace('_', ' ', $newStage)).".\nTrack case updates and manager notes in your portal: ".route('customer.dashboard')."\n\nFranchise Dekho Case Operations";

        $this->send(
            NotificationLog::CHANNEL_MAIL,
            $enquiry->email,
            'case_stage_updated',
            $message,
            $subject,
            $enquiry->user
        );
    }
}
