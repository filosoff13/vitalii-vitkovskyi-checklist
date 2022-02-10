<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use App\Enum\FlashMessagesEnum;
use App\Form\TaskType;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/checklist", name="checklist_")
 *
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="all")
     */
    public function ListAll(EntityManagerInterface $em): Response
    {
        return $this->render('checklist/index.html.twig', [
            'tasks' => $em->getRepository(Task::class)->findByUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/category/{id}", name="by_category", requirements={"id" = "\d+"})
     *
     * @IsGranted("IS_OWNER", subject="category", statusCode=404)
     */
    public function listByCategory(Category $category, EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task::class)->findByCategoryAndUser($category, $this->getUser());
        return $this->render('checklist/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/{id}", name="get", requirements={"id" = "\d+"})
     *
     * @IsGranted("IS_OWNER", subject="task", statusCode=404)
     */
    public function getAction(Task $task): Response
    {
        return $this->render('checklist/get.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em, TaskService $taskService): Response
    {
        if ($request->getMethod() === 'GET') {
            $categories = $em->getRepository(Category::class)->findBy(['user' => $this->getUser()]);

            return $this->render('checklist/create.html.twig', [
                'categories' => $categories
            ]);
        }

        $title = (string) $request->request->get('title');
        $taskService->createAndFlush(
            $title,
            (string) $request->request->get('text'),
            (int) $request->request->get('category_id'),
            $this->getUser()
        );
        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was created', $title));

        return $this->redirectToRoute('checklist_create');
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->findBy(
            ['user' => $this->getUser()]
        );
        $task = new Task('', '', $category[0], $this->getUser());

        $form = $this->createForm(TaskType::class, $task);

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();
            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was created', $task->getTitle()));

            return $this->redirectToRoute('checklist_all');
        }

        return $this->renderForm('checklist/new.html.twig', [
            'form' => $form,
            ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     *
     * @IsGranted("IS_OWNER", subject="task", statusCode=404)
     */
    public function deleteAction(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was deleted', $task->getTitle()));

        return $this->redirectToRoute('checklist_all');
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Task $task, EntityManagerInterface $em, TaskService $taskService): Response
    {
        if ($request->getMethod() === 'GET') {
            $categories = $em->getRepository(Category::class)->findBy(['user' => $this->getUser()]);

            return $this->render('checklist/edit.html.twig', [
                'task' => $task,
                'categories' => $categories
            ]);
        }

        $title = (string) $request->request->get('title');
        $taskService->editAndFlush(
            $task,
            $title,
            (string) $request->request->get('text'),
            (int) $request->request->get('category_id')
        );
        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was edited', $title));

        return $this->redirectToRoute('checklist_get', ['id' => $task->getId()]);
    }
}

