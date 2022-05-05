<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Integration\IntegrationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/integration", name="integration_")
 *
 * @IsGranted("ROLE_USER")
 */
class ApiIntegrationController extends AbstractController
{
    /**
     * @Route("/index", name="index", methods={"GET"})
     */
    public function index(IntegrationContext $context): Response
    {
        $context->create(0, []);

        return $this->render('api_integration/index.html.twig', [
            'controller_name' => 'ApiIntegrationController',
        ]);
    }

    public function submit(): Response
    {
        // if is config existed
        //  change enable/disable integration
        //  redirect to index

        // redirect to setup
    }

    public function setup(int $type): Response {
        // if method get
        // show form

        // post

        // IntegrationContextService->createIntegration($type)


        // redirect to the index
    }
}
