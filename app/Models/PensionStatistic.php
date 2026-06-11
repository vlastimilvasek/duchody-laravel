<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Carbon\Carbon $period
 * @property string $region_code
 * @property string $pension_type
 * @property int $count
 * @property int $average_amount
 * @property string|null $source
 */
class PensionStatistic extends Model
{
    protected $fillable = [
        'period',
        'region_code',
        'pension_type',
        'count',
        'average_amount',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'period'         => 'date',
            'count'          => 'integer',
            'average_amount' => 'integer',
        ];
    }
}
