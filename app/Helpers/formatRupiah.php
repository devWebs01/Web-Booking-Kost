<?php

if (!function_exists('formatRupiah')) {
    /**
     * Format angka menjadi format Rupiah
     *
     * @param float|int $amount
     * @param bool $prefix
     * @return string
     */
    function formatRupiah($amount, $prefix = true)
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $prefix ? 'Rp ' . $formatted : $formatted;
    }
}
