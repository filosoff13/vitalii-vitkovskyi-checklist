<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Task;
use App\Exception\ValidationException;
use App\Model\API\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/task", name="task_")
 */
class TaskController extends AbstractApiController
{
    /**
     * @Route(name="create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator, EntityManagerInterface $em): Response
    {
        /** @var Task $task */
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json');

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($task);
        if ($errors->count())
        {
            throw new ValidationException('', $errors);
        }

        $task->setOwner($this->getUser());
        $em->persist($task);
        $em->flush();

        return new ApiResponse($this->serializer->serialize($task, 'json', [
            'groups' => ['API']
        ]));
    }
}
