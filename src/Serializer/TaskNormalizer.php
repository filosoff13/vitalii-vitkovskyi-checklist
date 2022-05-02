<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Category;
use App\Entity\Task;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TaskNormalizer implements ContextAwareDenormalizerInterface
{
    private EntityManagerInterface $entityManager;
    private TokenStorageInterface $tokenStorage;
    private ObjectNormalizer $objectNormalizer;

    public function __construct(
        ObjectNormalizer $objectNormalizer,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->objectNormalizer = $objectNormalizer;
    }


    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Task::class;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if ($context[AbstractNormalizer::OBJECT_TO_POPULATE]?? [])
        {
            return $this->updateTask($context[AbstractNormalizer::OBJECT_TO_POPULATE], $data);
        }

        $title = $data['title'] ?? '';
        $text = $data['text'] ?? '';

        $category = $this->findCategory($data['category']['id'] ?? null);

        return new Task($title, $text, $category);
    }

    private function updateTask($objectToPopulate, $data): Task
    {
        if (!$objectToPopulate instanceof Task)
        {
            throw new LogicException('TaskNormalizer can update only Task entity');
        }

        $objectToPopulate = $this->objectNormalizer->denormalize($data, Task::class, null, [
            AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate,
            'groups' => ['API_UPDATE']
        ]);
        $categoryId = $data['category']['id'] ?? null;
        if (!$categoryId)
        {
            return $objectToPopulate;
        }

        $category = $this->findCategory($categoryId);
        $objectToPopulate->setCategory($category);

        return $objectToPopulate;
    }

    private function findCategory(?int $categoryId): ?Category
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof UserInterface)
        {
            throw new LogicException('To create task user should be authenticated');
        }
        $category = $categoryId
            ? $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $user])
            : null;

        if (!$category)
        {
            throw new ValidationException('Missed category');
        }

        return $category;
    }
}
