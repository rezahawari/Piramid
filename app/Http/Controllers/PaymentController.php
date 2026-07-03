<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Transaction;
use App\Services\Midtrans\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Issue (or reuse) a Midtrans Snap token for the owner's pending transaction.
     *
     * Always returns JSON so the frontend can call it with axios/fetch and
     * open the Snap popup with the returned token.
     */
    public function snapToken(Request $request, Transaction $transaction, MidtransService $midtrans): JsonResponse
    {
        abort_unless($request->user()->id === $transaction->user_id, 403);

        abort_unless(
            $transaction->payment_method === PaymentMethod::Midtrans
                && $transaction->payment_status === PaymentStatus::Pending,
            422,
            'Transaksi ini tidak dapat dibayar melalui Midtrans.',
        );

        $token = $midtrans->createSnapTransaction($transaction);

        return response()->json(['snap_token' => $token]);
    }
}
