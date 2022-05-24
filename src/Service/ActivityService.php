<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Activity\VisitActivity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ActivityService
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function createFromRequestResponse(Request $request, Response $response): void
    {
        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;

        $activity = new VisitActivity(
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $request->getClientIp(),
            $user instanceof User ? $user : null
        );

        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        $this->em->persist($activity);
        $this->em->flush($activity);
    }
}
