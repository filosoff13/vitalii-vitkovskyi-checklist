<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Task;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskType extends AbstractType
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('text', TextType::class)
            ->add('category', null, [
                'choice_label' => 'title',
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    return $categoryRepository->selectByUser($this->getUser($this->getUser()));
                },
            ])
            ->add('users', null, [
                'choice_label' => 'username',
                'label' => 'Shared user'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'empty_data' => static function (FormInterface $form) {
                return new Task(
                    $form->get('title')->getData(),
                    $form->get('text')->getData(),
                    $form->get('category')->getData()
                );
            },
        ]);
    }

    private function getUser(): ?UserInterface
    {
        return $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
    }
}
