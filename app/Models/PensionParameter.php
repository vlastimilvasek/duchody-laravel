<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PensionParameter extends Model
{
    protected $primaryKey = 'year';
    public $incrementing  = false;
    protected $keyType    = 'integer';

    protected $fillable = [
        'year',
        'basic_amount',
        'average_wage',
        'reduction_boundary_1',
        'reduction_boundary_2',
        'reduction_boundary_3',
        'percentage_rate_per_year',
        'reduction_rate_1',
        'reduction_rate_2',
        'reduction_rate_3',
        'child_bonus',
        'min_percentage_amount',
    ];

    protected function casts(): array
    {
        return [
            'year'                    => 'integer',
            'basic_amount'            => 'integer',
            'average_wage'            => 'integer',
            'reduction_boundary_1'    => 'integer',
            'reduction_boundary_2'    => 'integer',
            'reduction_boundary_3'    => 'integer',
            'percentage_rate_per_year' => 'float',
            'reduction_rate_1'        => 'float',
            'reduction_rate_2'        => 'float',
            'reduction_rate_3'        => 'float',
            'child_bonus'             => 'integer',
            'min_percentage_amount'   => 'integer',
        ];
    }
}
