<?php

namespace App\Service\Integration;

use App\Entity\ApiIntegration;

abstract class AbstractIntegrationStrategy
{
    abstract public function create(array $data): ApiIntegration;
}