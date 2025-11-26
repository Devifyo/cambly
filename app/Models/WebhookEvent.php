<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class WebhookEvent extends Model
{
    use HasFactory;
    
    protected $fillable = ['event_id', 'type', 'payload', 'user_id'];
    protected $casts = ['payload' => 'array'];

    // --- HELPER DATA ---
    /**
     * A list of zero-decimal currencies supported by Stripe.
     * @var array
     */
    private static $zeroDecimalCurrencies = [
        'jpy', 'krw', 'vnd', 'clp', 'bif', 'djf', 'gnf', 'kmf', 'mga',
        'pyg', 'rwf', 'ugx', 'vuv', 'xaf', 'xof', 'xpf',
    ];

    /**
     * Currency symbols
     * @var array
     */
    private static $symbols = [
        'usd' => '$',
        'jpy' => '¥',
        'eur' => '€',
    ];

    public function getPayloadAttribute($value)
    {
        // 1. If the value is null, return an empty array immediately
        if (is_null($value)) {
            return [];
        }

        // 2. Decode the raw database value
        $decoded = json_decode($value, true);

        // 3. THE FIX: If the result is STILL a string, it means it was double-encoded.
        // We decode it one more time to get the actual array.
        if (is_string($decoded)) {
            return json_decode($decoded, true);
        }

        // 4. Ensure we always return an array (in case of other errors)
        return is_array($decoded) ? $decoded : [];
    }

    // --- SCOPE (Unchanged) ---
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        $query->when(data_get($filters, 'q'), function ($q, $searchTerm) {
            $q->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('payload->data->object->number', 'like', '%' . $searchTerm . '%')
                         ->orWhere('payload->data->object->lines->data[0]->description', 'like', '%' . $searchTerm . '%');
            });
        });
        $query->when(data_get($filters, 'date'), function ($q, $date) {
            $q->whereDate('created_at', $date);
        });
        return $query;
    }

    // --- PUBLIC STATIC HELPERS (for the Controller) ---
    public static function formatStripeAmount(int $amount, string $currency): float
    {
        $currency = strtolower($currency);
        if (in_array($currency, self::$zeroDecimalCurrencies)) {
            return (float)$amount;
        }
        return (float)($amount / 100);
    }

    public static function getCurrencySymbol($currencyCode): string
    {
        return self::$symbols[strtolower($currencyCode)] ?? '$';
    }

    // ===================================================================
    // --- NEW ACCESSORS (to clean up the Blade view) ---
    // ===================================================================

    /**
     * Get the core invoice object from the payload.
     */
    protected function getInvoiceAttribute(): array
    {
        return data_get($this->payload, 'data.object', []);
    }

    /**
     * Get the currency code.
     */
    public function getCurrencyAttribute(): string
    {
        return strtolower(data_get($this->invoice, 'currency', 'jpy'));
    }

    /**
     * Get the currency symbol.
     */
    public function getCurrencySymbolAccessorAttribute(): string // Renamed to avoid conflict
    {
        return self::getCurrencySymbol($this->currency);
    }
    
    /**
     * Get the raw amount paid.
     */
    public function getAmountPaidAttribute(): int
    {
        return data_get($this->invoice, 'amount_paid', 0);
    }

    /**
     * Get the fully formatted, human-readable amount.
     * e.g., "¥4,500.00"
     */
    public function getFormattedAmountAttribute(): string
    {
        $amount_float = self::formatStripeAmount($this->amount_paid, $this->currency);
        $symbol = self::getCurrencySymbol($this->currency);
        
        return $symbol . number_format($amount_float, 2);
    }

    /**
     * Get the invoice description.
     */
    public function getInvoiceDescriptionAttribute(): string
    {
        return data_get($this->invoice, 'lines.data.0.description', 'Subscription');
    }

    /**
     * Get the human-readable invoice number.
     */
    public function getInvoiceNumberAttribute(): string
    {
        return data_get($this->invoice, 'number', data_get($this->invoice, 'id', '-'));
    }

    /**
     * Get the formatted status.
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucfirst(data_get($this->invoice, 'status', 'Unknown'));
    }

    /**
     * Get the formatted created date.
     */
    public function getFormattedDateAttribute(): string
    {
        return Carbon::createFromTimestamp(data_get($this->invoice, 'created', time()))->format('M j, Y');
    }

    /**
     * Get the link to the invoice PDF.
     */
    public function getInvoicePdfAttribute(): ?string
    {
        return data_get($this->invoice, 'invoice_pdf');
    }

    /**
     * Get the type for the JS filter.
     */
    public function getDataTypeAttribute(): string
    {
        return $this->type === 'charge.refunded' ? 'refund' : 'subscription';
    }

    /**
     * Get the icon class for the view.
     */
    public function getIconClassAttribute(): string
    {
        return $this->type === 'charge.refunded' ? 'tx-refund' : 'tx-payment';
    }
}