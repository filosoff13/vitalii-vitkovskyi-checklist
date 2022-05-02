<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Task;
use App\Exception\ValidationException;
use App\Model\API\ApiResponse;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
            'groups' => ['API_GET']
        ]));
    }

    /**
     * @Route(name="list", methods={"GET"})
     */
    public function list(EntityManagerInterface $em): Response
    {
        return new ApiResponse($this->serializer->serialize(
            $em->getRepository(Task::class)->selectByUser($this->getUser())->getQuery()->getResult(),
            'json',
            ['groups' => 'API_GET']
        ));
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @IsGranted("IS_SHARED", subject="task", statusCode=404)
     */
    public function delete(Task $task, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $task->getUser()){
            $em->remove($task);
        } else {
            $task->getUsers()->removeElement($this->getUser());
        }

        $em->flush();


        return new ApiResponse();
    }

    /**
     * @Route("/{id}", name="edit", methods={"PUT"})
     *
     * @IsGranted("IS_SHARED", subject="task", statusCode=404)
     */
    public function edit(Task $task, Request $request, ValidatorInterface $validator, EntityManagerInterface $em): Response
    {
        /** @var Task $task */
        $task = $this->serializer->deserialize($request->getContent(), Task::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $task
        ]);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($task);
        if ($errors->count())
        {
            throw new ValidationException('', $errors);
        }

        $em->flush();

        return new ApiResponse($this->serializer->serialize($task, 'json', [
            'groups' => ['API_GET']
        ]));
    }
}
