<?php
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;

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


if (!function_exists('getTimeZone')) {
    function getTimeZone()
    {
        if (request()->hasHeader('X-Timezone')) {
            $timezone = request()->header('X-Timezone');
        } else {         
            $ip = request()->ip();
            $url = "http://ip-api.com/json/$ip";

            $tz = file_get_contents($url);
            $timezone = json_decode($tz, true)['timezone'];
            return $timezone;
        }
    }


}

if (! function_exists('toAppTimezone')) {
    /**
     * Convert a UTC datetime into a relevant timezone (teacher or student).
     *
     * @param  string|\DateTimeInterface|null  $datetime
     * @param  mixed|null  $user   Optional user or teacher; defaults to Auth user
     * @param  string  $format     Output format (default: Y-m-d H:i A)
     * @param  string  $fallback   Fallback timezone (default: UTC)
     * @return string|null
     */
    function toAppTimezone($datetime, $user = null, string $format = 'Y-m-d H:i A', string $fallback = 'UTC'): ?string
    {
        if (empty($datetime)) return null;

        try {
            $dt = $datetime instanceof Carbon ? $datetime->copy() : Carbon::parse($datetime, 'UTC');
            $u = $user ?? Auth::user();

            // Find timezone: teacher → student → fallback
            $tz = optional($u?->teacherProfile)->timezone
                ?? optional($u?->studentProfile)->timezone
                ?? $fallback;

            if (! in_array($tz, \DateTimeZone::listIdentifiers())) {
                $tz = $fallback;
            }

            return $dt->setTimezone($tz)->format($format);
        } catch (\Throwable $e) {
            \Log::error('toAppTimezone failed: '.$e->getMessage());
            return null;
        }
    }
}