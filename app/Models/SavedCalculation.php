<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedCalculation extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'session_id',
        'calc_type',
        'input_params',
        'result',
    ];

    protected function casts(): array
    {
        return [
            'input_params' => 'array',
            'result'       => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
