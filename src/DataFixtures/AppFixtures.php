<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private array $categoryTitles = [
        'php',
        'other',
        'js',
    ];

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $categories = [];

        for ($i = 0; $i < 3; $i++){
            $category = new Category($this->categoryTitles[$i]);
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 1; $i < 10; $i++){
            $task = new Task('Some task ' . $i, 'Text ' . $i, $categories[random_int(0, 2)], false);
            $manager->persist($task);
        }

        $manager->flush();
    }
}
