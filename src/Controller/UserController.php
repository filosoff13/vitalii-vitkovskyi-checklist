<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\FlashMessagesEnum;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/registration", name="registration", methods={"POST"})
     *
     * @IsGranted("IS_ANONYMOUS_USER")
     */
    public function registration(Request $request, UserService $userService): Response
    {
        $userService->createAndFlush(
            (string) $request->request->get('password'),
            (string) $request->request->get('username'));
        $this->addFlash(FlashMessagesEnum::SUCCESS, "You have been registered!");

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function list(UserService $userService): Response
    {
        $users = $userService->getUserList();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/login", name="login")
     *
     * @IsGranted("IS_ANONYMOUS_USER")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $this->addFlash(FlashMessagesEnum::FAIL, $error
            ? $error->getMessage()
            : 'You should be authenticated'
        );

        return $this->redirectToRoute('page_home');
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function logout(): void
    {
        throw new \Exception('Unreachable statement');
    }
}

