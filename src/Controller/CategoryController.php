<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Task;
use App\Enum\FlashMessagesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function createAction(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $name = $request->request->get('name');
        $category = new Category($name);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($category);
        foreach ($errors as $error){
            $this->addFlash(FlashMessagesEnum::FAIL, $error->getMessage());
        }

        if (!$errors->count()){
            $em->persist($category);
            $em->flush();

            $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was created', $name));
        }

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

        $this->addFlash(FlashMessagesEnum::SUCCESS, sprintf('Category %s was removed', $category->getTitle()));

        return $this->redirectToRoute('page_home');
    }
}
