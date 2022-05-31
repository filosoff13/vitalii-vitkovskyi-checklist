<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Task;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskAndCategoriesFixtures extends Fixture
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->userService->create("user33", "user33");
        $manager->persist($user);

        $categories = [];

        for ($i = 0; $i < 3; $i++){
            $category = new Category(sprintf('300 %s', $i));
            $category->setUser($user);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 1; $i < 10; $i++){
            $category = $categories[random_int(0, 2)];

            $task = new Task('Some task ' . $i, 'Text ' . $i, $category);
            $task->setUser($category->getUser());
            $manager->persist($task);
        }

        $manager->flush();
    }
}
