<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/create", name="create")
     */
    public function createAction(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $newCategory = new Task();
        $newCategory->setTitle('New title');

        $entityManager->persist($newCategory);
        $entityManager->flush();

        return $this->render('category/create.html.twig', [
            'id' => $newCategory->getId(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function deleteAction(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoryToDelete = $this->taskRepository->find($id);
        $entityManager->remove($categoryToDelete);
        $entityManager->flush();

        return $this->render('category/delete.html.twig', [
            'id' => $id
        ]);
    }
}
