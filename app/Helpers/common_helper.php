<?php
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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

if (! function_exists('bookedBy')) {
    /**
     * Check if a given availability is booked by a specific user (or the logged-in user by default).
     *
     * @param  \App\Models\Availability  $availability
     * @param  \App\Models\User|null     $user
     * @return bool
     */
    function bookedBy($availability, $user = null): bool
    {
        // Default to the logged-in user
        $user = $user ?: auth()->user();

        // If no user or availability, short-circuit
        if (! $user || ! $availability) {
            return false;
        }

        // If the availability is not booked at all
        if (! $availability->is_booked) {
            return false;
        }

        // Check if the current user's reservation exists for this availability
        return $availability->reservation()
            ->where('student_id', $user->id)
            ->exists();
    }
}


if (! function_exists('intelligentMonthStart')) {
    function intelligentMonthStart($rangeOrStart, $maybeEnd = null): ?string
    {
        // Normalize inputs
        if (is_array($rangeOrStart)) {
            $startRaw = $rangeOrStart['start'] ?? null;
            $endRaw   = $rangeOrStart['end'] ?? null;
        } else {
            $startRaw = $rangeOrStart;
            $endRaw   = $maybeEnd;
        }

        if (empty($startRaw) || empty($endRaw)) {
            return null;
        }

        try {
            // Parse with Carbon (keeps timezone if provided)
            $start = Carbon::parse($startRaw)->startOfDay();
            $end   = Carbon::parse($endRaw)->startOfDay();
        } catch (\Exception $e) {
            // invalid date strings
            return null;
        }

        // If user passed end before start, swap
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        // Build period inclusive of both start and end
        $period = CarbonPeriod::create($start, $end);

        // Count days per month key "YYYY-MM"
        $counts = [];
        foreach ($period as $dt) {
            $key = $dt->format('Y-m');
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        if (empty($counts)) {
            return null;
        }

        // Find the month with the maximum days
        // If tie, arsort keeps the order by value but to tie-break explicitly choose earliest month:
        //  - build array of (month => count), find max count, then choose earliest month among those with max count.
        $max = max($counts);
        $candidates = array_keys(array_filter($counts, function($c) use ($max) {
            return $c === $max;
        }));

        sort($candidates, SORT_STRING); // earliest month first (YYYY-MM sorts lexicographically)
        $winningMonth = $candidates[0]; // "YYYY-MM"

        // return first day of that month in Y-m-d
        $firstDay = Carbon::createFromFormat('Y-m', $winningMonth)->firstOfMonth()->toDateString();

        return $firstDay;
    }
}