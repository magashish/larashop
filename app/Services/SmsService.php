<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class SmsService
{
    /**
     * Send an SMS using Twilio and return real delivery status.
     *
     * @param string $to
     * @param string $message
     * @param string|null $imageUrl
     * @return array
     */
    public static function sendSms(string $to, string $message, ?string $imageUrl = null, ?string $type = null): array
    {
        $sid   = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        //$from  = env('TWILIO_FROM');
        $from  = env('TWILIO_FROM_NAME');

        if (empty($sid) || empty($token) || empty($from)) {
            Log::error('Twilio credentials missing in .env');
            return ['status' => 'failed', 'error' => 'Twilio credentials missing'];
        }

        try {
            $client = new Client($sid, $token);

            $payload = [
                'from' => $from,
                'body' => $message,
            ];

            if (!empty($imageUrl)) {
                $payload['mediaUrl'] = [$imageUrl];
            }

            $msg = $client->messages->create($to, $payload);

            // Wait a short delay for Twilio to update message status
            sleep(2);
            $fetched = $client->messages($msg->sid)->fetch();

            $status = $fetched->status;
            $error  = $fetched->errorMessage;
            $code   = $fetched->errorCode;

            if (in_array($status, ['undelivered', 'failed'])) {
                Log::error('❌ Twilio message failed', [
                    'to' => $to,
                    'type'   => $type,
                    'status' => $status,
                    'error' => $error,
                    'code' => $code,
                ]);
                return [
                    'status' => 'failed',
                    'type'   => $type,
                    'twilio_status' => $status,
                    'error' => $error,
                    'error_code' => $code,
                ];
            }

            Log::info('✅ Twilio message sent', [
                'sid' => $msg->sid,
                'type'   => $type,
                'status' => $status,
                'to' => $to,
                'image' => $imageUrl,
            ]);

            return [
                'status' => 'success',
                'type'   => $type,
                'twilio_status' => $status,
                'sid' => $msg->sid,
            ];

        } catch (Exception $e) {
            Log::error('Twilio exception: ' . $e->getMessage());
            return [
                'status' => 'failed',
                'type'   => $type,
                'error' => $e->getMessage(),
            ];
        }
    }
}
