<?php

declare(strict_types=1);

namespace App\Model\Plant;

abstract class Flower implements Plant
{
    public function bloom()
    {
        echo 'Plant blooms';
    }
}