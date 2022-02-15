<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationService
{
    public function paginator()
    {
        new Paginator();
    }

}