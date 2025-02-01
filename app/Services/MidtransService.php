<?php

namespace App\Services;

use App\Models\Booking;
use Exception;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Membuat snap token untuk transaksi berdasarkan data booking.
     *
     * @return string Snap token untuk pembayaran.
     *
     * @throws Exception Jika gagal mendapatkan snap token.
     */
    public function createSnapToken(Booking $booking): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $booking->order_id,
                'gross_amount' => (int) $booking->total,
            ],
            'item_details' => $this->mapItemsToDetails($booking),
            'customer_details' => $this->getCustomerDetails($booking),
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Memetakan item dalam booking menjadi format Midtrans.
     *
     * @return array Daftar item dalam format yang sesuai.
     */
    protected function mapItemsToDetails(Booking $booking): array
    {
        return $booking->items->map(function ($item) {
            return [
                'id' => $item->id,
                'price' => (int) $item->price,
                'quantity' => 1,
                'name' => $item->name,
            ];
        })->toArray();
    }

    /**
     * Mengambil informasi pelanggan dari booking.
     *
     * @return array Informasi pelanggan.
     */
    protected function getCustomerDetails(Booking $booking): array
    {
        return [
            'first_name' => $booking->user->name,
            'email' => $booking->user->email,
        ];
    }

    /**
     * Menangani notifikasi dari Midtrans.
     */
    public function handleNotification(): void
    {
        $notification = new Notification;
        $booking = Booking::where('order_id', $notification->order_id)->first();

        if (! $booking) {
            return;
        }

        switch ($notification->transaction_status) {
            case 'settlement':
            case 'capture':
                $booking->update(['status' => 'confirmed']);
                break;
            case 'pending':
                $booking->update(['status' => 'pending']);
                break;
            case 'cancel':
            case 'expire':
            case 'deny':
                $booking->update(['status' => 'canceled']);
                break;
        }
    }
}
