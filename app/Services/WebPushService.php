<?php

namespace App\Services;

use App\Models\PushSubscription as StoredSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class WebPushService
{
    public function sendToCustomer(?int $customerId, string $title, string $message, array $data = []): void
    {
        if ($customerId) $this->send(StoredSubscription::query()->where('cliente_id', $customerId)->get(), $title, $message, $data);
    }

    public function sendToCourier(?int $courierId, string $title, string $message, array $data = []): void
    {
        if ($courierId) $this->send(StoredSubscription::query()->where('repartidor_id', $courierId)->get(), $title, $message, $data);
    }

    private function send($subscriptions, string $title, string $message, array $data): void
    {
        if (! config('webpush.public_key') || ! config('webpush.private_key')) return;

        try {
            $webPush = new WebPush(['VAPID' => [
                'subject' => config('webpush.subject'),
                'publicKey' => config('webpush.public_key'),
                'privateKey' => config('webpush.private_key'),
            ]]);
            $payload = json_encode(['title' => $title, 'body' => $message, 'data' => $data], JSON_THROW_ON_ERROR);

            foreach ($subscriptions as $stored) {
                $webPush->queueNotification(Subscription::create([
                    'endpoint' => $stored->endpoint,
                    'publicKey' => $stored->public_key,
                    'authToken' => $stored->auth_token,
                    'contentEncoding' => $stored->content_encoding,
                ]), $payload);
            }

            foreach ($webPush->flush() as $report) {
                $hash = hash('sha256', $report->getRequest()->getUri()->__toString());
                if ($report->isSuccess()) StoredSubscription::query()->where('endpoint_hash', $hash)->update(['last_used_at' => now()]);
                elseif ($report->isSubscriptionExpired()) StoredSubscription::query()->where('endpoint_hash', $hash)->delete();
            }
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
