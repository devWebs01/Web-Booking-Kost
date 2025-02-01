<?php

use App\Http\Controllers\HomeController;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('pages.welcome');
});

Auth::routes([
    'verify' => 'true',
]);

Route::get('/home', [HomeController::class, 'index'])
    ->middleware(['verified', 'checkRole:dev,admin', 'autoCancel'])
    ->name('home');

Route::post('/midtrans/callback', function (Request $request) {
    $data = $request->all();

    Log::info('Midtrans Callback:', $data);

    $orderId = $data['order_id'] ?? null;
    $transactionStatus = $data['transaction_status'] ?? null;

    if ($orderId) {
        $payment = Payment::where('receipt', $orderId)->first();

        if ($payment) {
            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                $payment->update(['status' => 'paid']);
            } elseif ($transactionStatus === 'pending') {
                $payment->update(['status' => 'pending']);
            } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
                $payment->update(['status' => 'failed']);
            }
        }
    }

    return response()->json(['message' => 'Callback received'], 200);
});
