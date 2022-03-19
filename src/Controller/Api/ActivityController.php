<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Activity\Activity;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ActivityController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     * @IsGranted("ROLE_ADMIN")
     */
    public function visit(EntityManagerInterface $em, Request $request): Response
    {

    }
}
