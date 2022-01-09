<?php

declare(strict_types=1);

namespace App\Model\Plant;

interface Plant
{
    public function grow(): void;
    public function photosynthesis(): void;
}
