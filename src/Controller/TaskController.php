<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/checklist", name="checklist_")
 */
class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @Route("/", name="all")
     */
    public function ListAll(EntityManagerInterface $em): Response
    {
        return $this->render('checklist/index.html.twig', [
            'tasks' => $em->getRepository(Task::class)->findAll(),
        ]);
    }

    /**
     * @Route("/{category_id}", name="by_category", requirements={"category_id" = "\d+"})
     */
    public function listByCategory(string $category_id, EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task::class)->findBy([
            'category' => $category_id
        ]);
        return $this->render('checklist/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/{category_id}/{taskId}", name="get", requirements={"category_id" = "\d+", "taskId" = "\d+"})
     */
    public function getAction(string $category_id, string $taskId, EntityManagerInterface $em): Response
    {
        $task = $em->getRepository(Task::class)->findOneBy([
            'category' => (int) $category_id,
            'id' => $taskId
        ]);

        return $this->render('checklist/get.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function createAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $newTask = new Task();
        $newTask->setTitle('New title')->setText('New text');

        $entityManager->persist($newTask);
        $entityManager->flush();

        return $this->render('checklist/create.html.twig', [
            'id' => $newTask->getId(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteAction(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $taskToDelete = $this->taskRepository->find($id);
        $entityManager->remove($taskToDelete);
        $entityManager->flush();

        return $this->render('checklist/delete.html.twig', [
            'id' => $id
        ]);
    }
}
