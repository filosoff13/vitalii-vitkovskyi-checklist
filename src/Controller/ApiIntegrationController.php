<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Integration\IntegrationContext;
use Symfony\Component\HttpFoundation\Request;
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
        $user = $this->getUser();

//        $context->create(0, ['username' => $user->getUserIdentifier(), 'password' => $user->getPassword()]);

        return $this->render('api_integration/index.html.twig', []);
    }

    /**
     * @Route("/setup", name="setup", methods={"POST"})
     */
    public function setup(Request $request, IntegrationContext $context): Response {
        // if method get
//        if ($request->getMethod() == 'GET') {
//            // show form
//
//        }

        // post

        /** @var User $user */
        $user = $this->getUser();
        $context->saveIntegrations($request->request->getIterator()->getArrayCopy(), $user);


        // redirect to the index
        return $this->redirectToRoute('integration_index');
    }
}
