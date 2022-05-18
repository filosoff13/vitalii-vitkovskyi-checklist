<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ApiIntegration;
use App\Entity\User;
use App\Enum\ApiIntegrationsEnum;
use App\Service\Integration\IntegrationContext;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $repository = $em->getRepository(ApiIntegration::class);
        $apiIntegration = $repository->findOneOrNullBy([
            'user' => $user,
            'type' => ApiIntegrationsEnum::NOTELIST
        ]);
        if (!$apiIntegration) {
            $enabled = false;
        } else {
            $enabled = $apiIntegration->getEnabled();
        }

        return $this->render('api_integration/index.html.twig', [
            'integrations' => [
                ['enabled' => $enabled]
            ]
        ]);
    }

    /**
     * @Route("/setup", name="setup", methods={"POST"})
     */
    public function setup(Request $request, IntegrationContext $context): Response {

        /** @var User $user */
        $user = $this->getUser();
        $context->saveIntegrations($request->request->getIterator()->getArrayCopy(), $user);


        // redirect to the index
        return $this->redirectToRoute('integration_index');
    }
}
