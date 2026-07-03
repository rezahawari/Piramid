<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans): JsonResponse
    {
        $payload = $request->json()->all();

        if ($payload === []) {
            $payload = $request->all();
        }

        Log::info('Midtrans notification received.', [
            'order_id' => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'payment_type' => $payload['payment_type'] ?? null,
        ]);

        if (! $midtrans->verifySignature($payload)) {
            Log::warning('Midtrans notification rejected: invalid signature.', [
                'order_id' => $payload['order_id'] ?? null,
                'ip' => $request->ip(),
            ]);

            abort(403, 'Invalid signature.');
        }

        $midtrans->handleNotification($payload);

        return response()->json(['status' => 'ok']);
    }
}
