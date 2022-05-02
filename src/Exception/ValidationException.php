<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException extends Exception
{
    private ?ConstraintViolationList $errorsList;

    public function __construct(string $message = "", ConstraintViolationList $errorsList = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
        $this->errorsList = $errorsList ?: new ConstraintViolationList() ;
    }

    public function getErrorsList(): ?ConstraintViolationList
    {
        return $this->errorsList;
    }

}
