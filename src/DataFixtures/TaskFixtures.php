<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Task;
use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    private array $categoryTitles = [
        'php',
        'other',
        'js',
    ];
    private UserService $userService;

    public function __construct(UserService $userService)
    {

        $this->userService = $userService;
    }
    public function load(ObjectManager $manager): void
    {
        $users = [];

        for ($i = 0; $i < 3; $i++){
            $user = $this->userService->create("user$i 111", "user $i");
            $manager->persist($user);
            $users[] = $user;
        }

        $categories = [];

        for ($i = 0; $i < 3; $i++){
            $category = new Category($this->categoryTitles[$i], $users[$i]);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 1; $i < 10; $i++){
            $category = $categories[random_int(0, 2)];

            $task = new Task('Some task ' . $i, 'Text ' . $i, $category, $category->getUser());
            $manager->persist($task);
        }

        $manager->flush();
    }
}
