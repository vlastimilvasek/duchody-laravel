<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WageCoefficient extends Model
{
    public $incrementing = false;
    public $timestamps   = false;

    protected $fillable = [
        'reference_year',
        'income_year',
        'coefficient',
    ];

    protected function casts(): array
    {
        return [
            'reference_year' => 'integer',
            'income_year'    => 'integer',
            'coefficient'    => 'float',
        ];
    }
}
