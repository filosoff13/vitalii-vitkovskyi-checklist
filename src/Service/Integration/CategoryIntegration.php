<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Entity\Task;
use App\Enum\ApiIntegrationsEnum;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CategoryIntegration
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

    public function checkAndDelete(int $id, bool $category = true): void
    {
        $repository = $this->em->getRepository(ApiIntegration::class);
        $user = $this->security->getUser();
        $apiIntegration = $repository->findOneOrNullBy([
            'user' => $user,
            'type' => ApiIntegrationsEnum::NOTELIST
        ]);

        if ($apiIntegration->getEnabled()) {
            $userPassword = $apiIntegration->getConfig()['password'];

            if (!$userPassword) {
                throw new ValidationException('Token missed');
            }

            $username = $user->getUserIdentifier();
            $token = $this->strategy->login($username, (string)$userPassword);
            if ($category) {
                $this->strategy->deleteCategory($token, $id);
            } else {
                $this->strategy->deleteTask($token, $id);
            }
        }
    }

    public function checkAndEdit(Task $task, int $externalTaskId): void
    {
        $repository = $this->em->getRepository(ApiIntegration::class);
        $user = $this->security->getUser();
        $apiIntegration = $repository->findOneOrNullBy([
            'user' => $user,
            'type' => ApiIntegrationsEnum::NOTELIST
        ]);

        if ($apiIntegration->getEnabled()) {
            $userPassword = $apiIntegration->getConfig()['password'];

            if (!$userPassword) {
                throw new ValidationException('Token missed');
            }

            $username = $user->getUserIdentifier();
            $token = $this->strategy->login($username, (string)$userPassword);
            $this->strategy->editTask($task, $token, $externalTaskId);
        }
    }
}
