<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case Male   = 'M';
    case Female = 'F';

    public function label(): string
    {
        return match($this) {
            Gender::Male   => 'Muž',
            Gender::Female => 'Žena',
        };
    }
}
