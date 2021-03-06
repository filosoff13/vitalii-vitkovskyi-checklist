<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Enum\FlashMessagesEnum;
use App\Form\CategoryType;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 *
 * @IsGranted("ROLE_USER")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function createAction(Request $request, CategoryService $categoryService): Response
    {
        $categoryName = (string) $request->request->get('name');
        $categoryService->createAndFlush($categoryName);
        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was created', $categoryName));

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em->persist($category);
            $em->flush();
            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category "%s" was created', $category->getTitle()));

            return $this->redirectToRoute('checklist_all');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     *
     * @IsGranted("IS_OWNER", subject="category", statusCode=404)
     */
    public function deleteAction(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was removed', $category->getTitle()));

        return $this->redirectToRoute('page_home');
    }
}
