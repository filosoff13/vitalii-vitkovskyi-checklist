<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Activity\Activity;
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
    /**
     * @Route("/visit", name="visit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function visit(EntityManagerInterface $em, Request $request): Response
    {
        $itemsPerPage = 20;
        $page = (int) $request->get('page');
        $offset = ($page ? $page - 1 : 0) * $itemsPerPage;

        return $this->render('activity/visit.html.twig', [
            'activities' => $em->getRepository(Activity::class)->getVisitActivityData(),
            'offset' => $offset,
            'itemsPerPage' => $itemsPerPage
        ]);
    }

    /**
     * @Route("/task", name="task")
     * @IsGranted("ROLE_USER")
     */
    public function task(EntityManagerInterface $em): Response
    {
        return $this->render('activity/task.html.twig', [
            'data' => $em->getRepository(Activity::class)->getTaskActivityData($this->getUser())
        ]);
    }
}
