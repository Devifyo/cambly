<?php
use Carbon\Carbon;
use Hashids\Hashids;


if (!function_exists('encryptId')) {
    function encryptId(int $id): string
    {
        $salt = config('app.key') ?: 'fallback-salt'; // never null
        $hashids = new Hashids($salt, 8, 'abcdefghijklmnopqrstuvwxyz1234567890');
        return $hashids->encode($id);
    }
}

if (!function_exists('decryptId')) {
    function decryptId(string $hash): ?int
    {
        $salt = config('app.key') ?: 'fallback-salt'; // same salt as encryptId
        $hashids = new Hashids($salt, 8, 'abcdefghijklmnopqrstuvwxyz1234567890');
        $decoded = $hashids->decode($hash);
        return $decoded[0] ?? null;
    }
}


if (! function_exists('format_currency')) {
    /**
     * Format a numeric amount into a currency display (like ¥4,500).
     *
     * @param  float|int  $amount
     * @param  string  $currencySymbol
     * @param  int|null  $decimals
     * @return string
     */
    function format_currency($amount, ?string $currencySymbol = null, ?int $decimals = 0): string
    {
        if (! is_numeric($amount)) {
            return ($currencySymbol ?? config('cashier.symbol', '¥')) . '0';
        }

        $symbol = $currencySymbol ?? config('cashier.symbol', '¥');

        return $symbol . number_format((float) $amount, $decimals);
    }
}


if (!function_exists('formatDate')) {
    function formatDate($date, ?string $timezone = null): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            $date = $date instanceof Carbon ? $date : Carbon::parse($date);
            if ($timezone) {
                $date->setTimezone($timezone);
            }

            $day = $date->format('j');
            $month = $date->format('F');
            $year = $date->format('Y');

            $suffix = match (true) {
                in_array($day, [11, 12, 13]) => 'th',
                $day % 10 === 1 => 'st',
                $day % 10 === 2 => 'nd',
                $day % 10 === 3 => 'rd',
                default => 'th',
            };

            return "{$day}{$suffix} of {$month} {$year}";
        } catch (\Exception $e) {
            return null;
        }
    }
}