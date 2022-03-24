<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\Task;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    private ValidatorInterface $validator;
    private EntityManagerInterface $em;

    public function __construct(
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ) {
        $this->validator = $validator;
        $this->em = $em;
    }

    public function createAndFlush(string $title, string $text, int $categoryId): void
    {
        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId]);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $task = new Task($title, $text, $category);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($task);
        foreach ($errors as $error) {
            throw new ValidationException($error->getMessage());
        }

        $this->em->persist($task);
        $this->em->flush();
    }

    public function editAndFlush(Task $task, string $title, string $text, int $categoryId): void
    {
        $category = $this->em->getRepository(Category::class)->findOneBy(['id' => $categoryId, 'user' => $task->getUser()]);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $task->setTitle($title)->setText($text)->setCategory($category);

        /** @var ConstraintViolationList $errors */
        $errors = $this->validator->validate($task);
        foreach ($errors as $error) {
            throw new ValidationException($error->getMessage());
        }

        $this->em->persist($task);
        $this->em->flush();
    }
}
