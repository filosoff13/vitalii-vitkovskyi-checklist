<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use App\Enum\FlashMessagesEnum;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->getMethod() === 'GET'){
            $categories = $em->getRepository(Category::class)->findAll();

            return $this->render('checklist/create.html.twig', [
                'categories' => $categories
            ]);
        }

        $title = (string) $request->request->get('title');
        $text = (string) $request->request->get('text');
        $categoryId = (int) $request->request->get('category_id');
        $category = $em->getRepository(Category::class)->find($categoryId);
        if (!$category){
            throw new NotFoundHttpException('Category not found');
        }

        $task = new Task($title, $text, $category);
        $em->persist($task);
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" added', $task->getTitle()));

        return $this->redirectToRoute('checklist_create');

    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteAction(int $id, EntityManagerInterface $em): Response
    {
        $taskToDelete = $this->taskRepository->find($id);
        if (!$taskToDelete){
            throw new NotFoundHttpException('Task was not found');
        }
        $em->remove($taskToDelete);
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was deleted', $taskToDelete->getTitle()));

        return $this->redirectToRoute('checklist_all');
    }
}
