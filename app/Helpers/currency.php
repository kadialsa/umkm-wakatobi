<?php
if (! function_exists('format_rupiah')) {
    /**
     * Format numeric value as Indonesian Rupiah.
     *
     * @param  float|int|string  $value
     * @param  bool  $withSymbol  // tambahkan "Rp " di depan jika true
     * @return string
     */
    function format_rupiah($value, $withSymbol = true)
    {
        // pastikan $value numeric
        $number = is_numeric($value) ? $value : floatval(preg_replace('/[^\d.-]/', '', $value));
        $formatted = number_format($number, 0, ',', '.');
        return $withSymbol
            ? 'Rp ' . $formatted
            : $formatted;
    }
}
