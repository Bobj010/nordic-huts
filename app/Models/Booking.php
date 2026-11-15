<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'house_id',
        'check_in_date',
        'check_out_date',
        'guests_count',
        'total_price',
        'status',
        'guest_name',
        'guest_phone',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'guests_count' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * User who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * House booked.
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Calculated number of nights.
     */
    public function getNightsCountAttribute(): int
    {
        if ($this->check_in_date && $this->check_out_date) {
            $days = $this->check_in_date->diffInDays($this->check_out_date);
            return (int) ($days > 0 ? $days : 1);
        }
        return 1;
    }

    /**
     * Status label and badge styling helper.
     *
     * @return array{label: string, bg: string, text: string}
     */
    public function getStatusInfoAttribute(): array
    {
        return match ($this->status) {
            'pending' => [
                'label' => 'Ожидает подтверждения',
                'bg' => 'bg-amber-100',
                'text' => 'text-amber-800',
            ],
            'confirmed' => [
                'label' => 'Подтверждено',
                'bg' => 'bg-emerald-100',
                'text' => 'text-emerald-800',
            ],
            'completed' => [
                'label' => 'Завершено',
                'bg' => 'bg-stone-100',
                'text' => 'text-stone-700',
            ],
            'cancelled' => [
                'label' => 'Отменено',
                'bg' => 'bg-rose-100',
                'text' => 'text-rose-800',
            ],
            default => [
                'label' => $this->status,
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-800',
            ],
        };
    }
}
