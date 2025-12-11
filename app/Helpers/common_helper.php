<?php
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Hashids\Hashids;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\UseCreditService;
use Illuminate\Support\Facades\Http;

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
        // 1. Check for hidden input from the form (The best way)
        if (request()->has('timezone')) {
            return request()->input('timezone');
        }

        // 2. Check for Header (In case you use AJAX later)
        if (request()->hasHeader('X-Timezone')) {
            return request()->header('X-Timezone');
        }

        // 3. Fallback: API (Must be wrapped to prevent crashes)
        try {
            $ip = request()->ip();

            $response = Http::timeout(5)->get("http://ip-api.com/json/$ip");
            if ($response->successful()) {
                $data = $response->json();
                return $data['timezone'] ?? config('app.timezone');
            }

        } catch (\Exception $e) {
            // Silently fail if API is down, don't stop the user registration
        }

        // 4. Ultimate Fallback
        return config('app.timezone', 'UTC');
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


if (! function_exists('formatLessonDateTime')) {
    function formatLessonDateTime($dateTime, bool $showYear = false){
        if (!$dateTime) {
                return '—';
            }

        $carbon = $dateTime instanceof Carbon ? $dateTime : Carbon::parse($dateTime);

        return $showYear
            ? $carbon->format('Y M j (D) g:i a')   // e.g. 2025 Nov 19 (Thu) 4:30 pm
            : $carbon->format('M j (D) g:i a');    // e.g. Nov 19 (Thu) 4:30 pm
    }

}
if (! function_exists('formatRemainingTime')) {
    function formatRemainingTime(int $minutes): string
    {
        if ($minutes >= 60) {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;

            $hourText = $hours . ' hour' . ($hours > 1 ? 's' : '');

            if ($remainingMinutes > 0) {
                $minuteText = $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
                return $hourText . ' ' . $minuteText;
            }

            return $hourText;
        }

        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }
}


if (! function_exists('uploadProfile')) {
    function uploadProfile(User $user, UploadedFile $newImage): string
    {
        // Get the old image path from the profile
        $oldPath = $user->studentProfile?->avatar_url;
        $disk = config('filesystems.default');

        // 1. Store the new file with a unique name
        $fileName = $user->id . '_' . time() . '.' . $newImage->getClientOriginalExtension();
        $newPath = $newImage->storeAs('avatars', $fileName, $disk);

        // 2. Delete the old file *after* the new one is stored
        if ($oldPath && $newPath) {
            Storage::disk($disk)->delete($oldPath);
        }

        // 3. Return the path of the new file
        return $newPath;
    }

}

/**
 * Remove user's profile avatar from storage (if exists).
 *
 * @param User $user
 * @return void
 */
if (! function_exists('removeProfileAvatar')) {
    function removeProfileAvatar(User $user): void
    {
        if (!empty($user->profile_picture)) {
            Storage::disk(config('filesystems.default'))->delete($user->profile_picture);
        }
    }
}

if (! function_exists('uploadFile')) {
    /**
     * Upload a new file and delete the old one if it exists.
     *
     * @param UploadedFile $file      The new file object.
     * @param string       $folder    The folder to store the file in (e.g., 'plan_icons').
     * @param string|null  $oldPath   The path of the old file to delete.
     * @param string       $disk      The storage disk to use.
     * @return string                 The path of the stored file.
     */
    function uploadFile(UploadedFile $file, string $folder, ?string $oldPath = null): string
    {
        $disk = config('filesystems.default');

        $newPath = $file->store($folder, $disk);

        // 2. Delete the old file if it exists and is different from the new one
        if ($oldPath && $newPath !== $oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }
        return $newPath;
    }
}


if (!function_exists('convertUtcToLocal')) {
    /**
     * Converts a UTC datetime string to a user's local timezone
     * and returns a variety of formatted strings and Carbon objects.
     *
     * @param string|Carbon|null $utcDateTime The UTC datetime to convert.
     * @param User|null $user The user object to get the timezone from.
     * @return array|null
     */
    function convertUtcToLocal($utcDateTime, ?User $user): ?array
    {
        // 1. Handle null input
        if (!$utcDateTime) {
            return null;
        }
        
        // 2. Get the target timezone
        // Default to the application's timezone (which itself defaults to 'UTC')
        $targetTimezone = config('app.timezone', 'UTC');
        // If a user is provided, try to find their specific timezone
        if ($user) {
            $targetTimezone = optional($user->studentProfile)->tz
                ?? optional($user->teacherProfile)->tz
                ?? getTimeZone(); // <-- Fallback to app config if user has no tz
        }
        // 3. Validate the timezone
        // If the timezone is invalid or null, default to UTC
        if (!$targetTimezone || !in_array($targetTimezone, timezone_identifiers_list())) {
            $targetTimezone = 'UTC';
        }

        // 4. Create Carbon objects
        // Create the original object, ensuring it's interpreted as UTC
        $original = Carbon::parse($utcDateTime, 'UTC');
        
        // Create the local object by converting the timezone
        $local = $original->clone()->setTimezone($targetTimezone);

        // 5. Return a comprehensive array
        return [
            // --- Carbon Objects (Most useful) ---
            'original_carbon' => $original,
            'local_carbon'    => $local,
            
            // --- Timezones ---
            'original_timezone' => 'UTC',
            'local_timezone'    => $targetTimezone,
            
            // --- Original (UTC) ---
            'original_datetime' => $original->toDateTimeString(), // Y-m-d H:i:s
            'original_date'     => $original->toDateString(),     // Y-m-d
            'original_time'     => $original->format('H:i:s'),    // H:i:s
            'original_time_12h' => $original->format('h:i A'),    // 12-hour format
            'original_formatted_date' => $original->format('jS \of F Y'), // <-- NEW
            
            // --- Local (Converted) ---
            'local_datetime'    => $local->toDateTimeString(), // Y-m-d H:i:s
            'local_date'        => $local->toDateString(),     // Y-m-d
            'local_time'        => $local->format('H:i:s'),    // H:i:s
            'local_time_12h'    => $local->format('h:i A'),    // 12-hour format
            'local_formatted_date' => $local->format('jS \of F Y'), // <-- NEW
        ];
    }
}

if (!function_exists('get_current_month_credits')) {
    /**
     * Retrieves the current month's available credits by resolving and calling 
     * the UseCreditService from the Laravel container.
     *
     * @param User $user
     * @return int
     */
    function get_current_month_credits(User $user, $rule = 'show_all'): int
    {
        // 1. Resolve the UseCreditService instance from the Laravel Container.
        // This ensures all of the Service's internal dependencies are handled.
        $creditService = app(UseCreditService::class); 

        // 2. Call the service function
        $currentCredits = $creditService->getCurrentMonthCredits($user, $rule);
        // 3. Return the calculated available credits, defaulting to 0.
        return $currentCredits['available'] ?? 0;
    }
}

if (!function_exists('getCurrentTicketLedger')) {
    /**
     * Retrieves the current ticket ledger for a user by resolving and calling 
     * the UseCreditService from the Laravel container.
     *
     * @param User $user
     * @return TicketLedger|null
     */
    function getCurrentTicketLedger($user, $rule = 'show_all')
    {
        $creditService = app(UseCreditService::class); 
        return $creditService->getCurrentTicketLedger($user, $rule);
    }
}