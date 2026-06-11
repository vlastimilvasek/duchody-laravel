<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PensionFund extends Model
{
    use HasUlids;

    protected $fillable = [
        'slug',
        'name',
        'company',
        'fund_type',
        'fee_management',
        'fee_performance',
        'return_1y',
        'return_3y',
        'return_5y',
        'total_assets_mil',
        'participants_count',
        'affiliate_url',
        'partner_id',
    ];

    protected function casts(): array
    {
        return [
            'fee_management'    => 'float',
            'fee_performance'   => 'float',
            'return_1y'         => 'float',
            'return_3y'         => 'float',
            'return_5y'         => 'float',
            'total_assets_mil'  => 'integer',
            'participants_count' => 'integer',
        ];
    }
}
