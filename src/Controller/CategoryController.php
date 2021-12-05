<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('name');
        $category = new Category($name);

        $em->persist($category);
        $em->flush();

        $this->addFlash('success', sprintf('Category %s was created', $name));

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     */
    public function deleteAction(string $id, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        $em->remove($category);
        $em->flush();

        $this->addFlash('success', sprintf('Category %s was removed', $category->getTitle()));

        return $this->redirectToRoute('page_home');
    }
}
