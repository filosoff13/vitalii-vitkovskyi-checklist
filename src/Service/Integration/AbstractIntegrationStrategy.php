<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Entity\User;

abstract class AbstractIntegrationStrategy
{
    abstract public function save(array $data, User $user): void;
    abstract public function create(array $data, User $user): ApiIntegration;
}
