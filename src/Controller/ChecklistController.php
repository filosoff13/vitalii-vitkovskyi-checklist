<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/checklist", name="checklist")
 */
class ChecklistController extends AbstractController
{
    private array $categories = [
        1 => [
            'title' => 'php',
            'tasks' => [1, 2, 3]
        ],
        2 => [
            'title' => 'other',
            'tasks' => [4, 5, 6]
        ],
        3 => [
            'title' => 'js',
            'tasks' => [2, 4, 6]
        ],
    ];

    private array $tasks = [
        1 => [ 'title' => 'Some task 1',
                'text' => 'Text 1',
                'id'   => 1
            ],
        2 => [ 'title' => 'Some task 2',
                'text' => 'Text 2',
                'id'   => 2
            ],
        3 => [ 'title' => 'Some task 3',
                'text' => 'Text 3',
                'id'   => 3
           ],
        4 => [ 'title' => 'Some task 4',
                'text' => 'Text 4',
                'id'   => 4
            ],
        5 => [ 'title' => 'Some task 5',
                'text' => 'Text 5',
                'id'   => 5
            ],
        6 => [ 'title' => 'Some task 6',
                'text' => 'Text 6',
                'id'   => 6
            ]
    ];

    /**
     * @Route("/", name="_all")
     */
    public function ListAll(): Response
    {
        return $this->render('checklist/index.html.twig', [
            'tasks' => $this->tasks,
        ]);
    }

    /**
     * @Route("/{category_id}", name="_by_category", requirements={"category_id" = "\d+"})
     * @throws \Exception
     */
    public function ListByCategory($category_id): Response
    {
        $category_id = $this->categories[(int) $category_id] ?? null;
        if (!$category_id){
            throw new \Exception('You ask for the category which does not exist');
        }
        $tasksIds = $category_id['tasks'];
        $tasks = array_filter($this->tasks, function (array $task) use ($tasksIds){
            return in_array($task['id'], $tasksIds, true);
        });
        return $this->render('checklist/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/{category_id}/{taskId}", name="_get", requirements={"category_id" = "\d+", "taskId" = "\d+"})
     * @throws \Exception
     */
    public function getAction($category_id, string $taskId): Response
    {
        $category_id = $this->categories[(int) $category_id] ?? null;
        if (!$category_id){
            throw new \Exception('You ask for the category which does not exist');
        }
        $tasksIds = $category_id['tasks'];
        $tasks = array_filter($this->tasks, function (array $task) use ($tasksIds){
            return in_array($task['id'], $tasksIds, true);
        });

        if (!isset($tasks[(int) $taskId])){
            throw new \Exception('There no task in this category');
        }

        $task = $tasks[(int) $taskId];
        return $this->render('checklist/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
