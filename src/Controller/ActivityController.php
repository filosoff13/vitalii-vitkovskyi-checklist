<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity\Activity;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/activity", name="activity_")
 */
class ActivityController extends AbstractController
{
    private PaginationService $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    /**
     * @Route("/visit", name="visit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function visit(EntityManagerInterface $em, Request $request): Response
    {
        $data = $this->paginationService->paginator(
            $em->getRepository(Activity::class)->selectVisitActivityData(),
            $request
        );

        return $this->render('activity/visit.html.twig', [
            'activities' => $data,
            'lastPage' => $this->paginationService->lastPage($data)
        ]);
    }

    /**
     * @Route("/task", name="task")
     * @IsGranted("ROLE_ADMIN")
     */
    public function task(EntityManagerInterface $em, Request $request): Response
    {
        $data = $this->paginationService->paginator(
            $em->getRepository(Activity::class)->selectTaskActivityData($this->getUser()),
            $request,
            2
        );

        return $this->render('activity/task.html.twig', [
            'data' => $data,
            'lastPage' => $this->paginationService->lastPage($data),
        ]);
    }
}
