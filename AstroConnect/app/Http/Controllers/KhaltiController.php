<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KhaltiController extends Controller
{
    /**
     * Initiate a Khalti payment for a completed appointment.
     */
    public function initiate(Request $request, Appointment $appointment)
    {
        // Safety checks
        if ($appointment->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($appointment->status !== 'completed') {
            return back()->with('error', 'You can only pay for completed sessions.');
        }

        if ($appointment->isPaid()) {
            return back()->with('info', 'This session is already paid.');
        }

        $amountInPaisa = (int) ($appointment->astrologer->consultation_fee * 100);
        $baseUrl = config('services.khalti.base_url');
        $secretKey = config('services.khalti.secret_key');

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => "Key $secretKey",
        ])->post($baseUrl . 'epayment/initiate/', [
            'return_url' => route('khalti.callback'),
            'website_url' => config('app.url'),
            'amount' => $amountInPaisa,
            'purchase_order_id' => (string) $appointment->id,
            'purchase_order_name' => 'Consultation with ' . $appointment->astrologer->user->name,
            'customer_info' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ]
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Store pending payment
            Payment::updateOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'user_id' => $appointment->user_id,
                    'astrologer_id' => $appointment->astrologer_id,
                    'khalti_pidx' => $data['pidx'],
                    'amount' => $appointment->astrologer->consultation_fee,
                    'status' => 'pending',
                ]
            );

            return redirect($data['payment_url']);
        }

        Log::error('Khalti Initiation Failed', [
            'response' => $response->body(),
            'appointment_id' => $appointment->id
        ]);

        return back()->with('error', 'Failed to initialize payment gateway. Please try again.');
    }

    /**
     * Handle the callback from Khalti.
     */
    public function callback(Request $request)
    {
        $pidx = $request->query('pidx');
        $status = $request->query('status');
        $purchase_order_id = $request->query('purchase_order_id');

        if (!$pidx) {
            return redirect()->route('appointments.user.index')->with('error', 'Invalid payment response.');
        }

        // Verify with Khalti
        $baseUrl = config('services.khalti.base_url');
        $secretKey = config('services.khalti.secret_key');

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => "Key $secretKey",
        ])->post($baseUrl . 'epayment/lookup/', [
            'pidx' => $pidx
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $payment = Payment::where('khalti_pidx', $pidx)->firstOrFail();

            if ($data['status'] === 'Completed') {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $data['transaction_id'],
                    'response_data' => $data
                ]);

                return view('pages.user.payment-result', [
                    'success' => true,
                    'appointment' => $payment->appointment,
                    'transaction_id' => $data['transaction_id']
                ]);
            } else {
                $payment->update([
                    'status' => strtolower($data['status']),
                    'response_data' => $data
                ]);

                return view('pages.user.payment-result', [
                    'success' => false,
                    'error' => 'Payment status: ' . $data['status']
                ]);
            }
        }

        return view('pages.user.payment-result', [
            'success' => false,
            'error' => 'Could not verify payment with gateway.'
        ]);
    }
}
