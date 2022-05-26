<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Enum\ApiIntegrationsEnum;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TaskIntegration
{
    private EntityManagerInterface $em;
    private Security $security;
    private DvCampusNotelistIntegrationStrategy $strategy;

    public function __construct(EntityManagerInterface $em, Security $security, DvCampusNotelistIntegrationStrategy $strategy)
    {
        $this->em = $em;
        $this->security = $security;
        $this->strategy = $strategy;
    }

    public function checkAndIntegrate(): void
    {
        $repository = $this->em->getRepository(ApiIntegration::class);
        $user = $this->security->getUser();
        $apiIntegration = $repository->findOneOrNullBy([
            'user' => $user,
            'type' => ApiIntegrationsEnum::NOTELIST
        ]);

        $enabled = $apiIntegration->getEnabled();
        if ($apiIntegration->getEnabled()) {
            $userPassword = $apiIntegration->getConfig()['password'];

            if (!$userPassword) {
                throw new ValidationException('Token missed');
            }

            $username = $user->getUserIdentifier();
            $token = $this->strategy->login($username, (string)$userPassword);

            $this->strategy->getCategories($user, $token, $apiIntegration);
            $this->strategy->getTasks($user, $token, $apiIntegration);
        }

    }
}
