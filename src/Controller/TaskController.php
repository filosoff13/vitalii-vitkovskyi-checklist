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
     * @IsGranted("IS_SHARED", subject="task", statusCode=404)
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
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $em->persist($task);
            $em->flush();
            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was created', $task->getTitle()));

            return $this->redirectToRoute('checklist_all');
        }

        return $this->renderForm('checklist/create.html.twig', [
            'form' => $form,
            ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     *
     * @IsGranted("IS_SHARED", subject="task", statusCode=404)
     */
    public function deleteAction(Task $task, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $task->getUser()){
            $em->remove($task);
        }else{
            $task->getUsers()->removeElement($this->getUser());
        }

        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was deleted', $task->getTitle()));

        return $this->redirectToRoute('checklist_all');
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Task $task, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Task "%s" was edited', $task->getTitle()));

            return $this->renderForm('checklist/edit.html.twig', [
                'form' => $form,
                'task' => $task,
            ]);
        }

        return $this->renderForm('checklist/edit.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }
}

