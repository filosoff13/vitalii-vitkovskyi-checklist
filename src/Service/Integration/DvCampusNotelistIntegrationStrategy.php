<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Entity\Category;
use App\Entity\User;
use App\Enum\ApiIntegrationsEnum;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DvCampusNotelistIntegrationStrategy extends AbstractIntegrationStrategy
{
    const HOST = 'https://dv-campus-notelist.allbugs.info/api';
    const CREATE_USER_URL = '/user';
    const LOGIN_URL = '/login_check';
    const CATEGORY_URL = '/category';

    private HttpClientInterface $client;
    private EntityManagerInterface $em;

    /**
     * @param HttpClientInterface $client
     * @param EntityManagerInterface $em
     */
    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->em = $em;
    }

    public function save(array $data, User $user): void
    {
        $enabled = (bool) ($data['notelist-enabled'] ?? false);

        $repository = $this->em->getRepository(ApiIntegration::class);
        $apiIntegration = $repository->findOneOrNullBy([
            'user' => $user,
            'type' => ApiIntegrationsEnum::NOTELIST
        ]);

        if (!$apiIntegration) {
            $token = $this->create($data, $user);
            $this->getCategories($user, $token);
            $this->getPosts($user, $token);
            return;
        }

        if ($apiIntegration->getEnabled() === $enabled) {
            return;
        }

        $this->verify($apiIntegration, $enabled, $user, $data);
    }

    /**
     * @throws ValidationException
     */
    public function create(array $data, User $user): string
    {
        $userPassword = $data['notelist-token'] ?? null;
        if (!$userPassword) {
            throw new ValidationException('Token missed');
        }

        $username = $user->getUserIdentifier();
        $token = $this->login($username, $userPassword);

        if (!$token) {
            $this->register($username, $userPassword);
            $token = $this->login($username, $userPassword);
        }

        if (!$token) {
            throw new \Exception('not valid token');
        }

        $apiIntegration = new ApiIntegration();
        $apiIntegration->setConfig([
            'password' => $userPassword,
            'token' => $token
        ])
            ->setType(ApiIntegrationsEnum::NOTELIST)
            ->setUser($user);

        $this->em->persist($apiIntegration);
        $this->em->flush();

        // TODO run command for synchronisation


        return $token;
    }

    /**
     * @throws \Exception
     */
    public function getCategories(User $user, $token): void
    {
        $repository = $this->em->getRepository(Category::class);
        $categories = $repository->findBy(['user' => $user]);
        foreach ($categories as $category) {
            $this->postCategory($category->getTitle(), $token);
        }
    }

    public function getPosts(User $user, $token): void
    {
        $repository = $this->em->getRepository(Category::class);
        $categories = $repository->findBy(['user' => $user]);
        foreach ($categories as $category) {
            $this->postCategory($category->getTitle(), $token);
        }
    }

    private function postCategory(string $name, $token): void
    {
        $response = $this->makeRequest(
            self::HOST . self::CATEGORY_URL,
            'POST',
            [
                'headers' => ['Authorization' => sprintf('Bearer %s', $token)],
                'json' => [
                    'name' => $name
                ]
            ]
        );

        $statusCode = $response->getStatusCode(false);
        if ($statusCode === Response::HTTP_UNAUTHORIZED) {
            throw new \Exception('JWT Token not found');
        }
//        if ($statusCode === Response::HTTP_OK) {
//            return Response::HTTP_CREATED;
//        }
//
//        return Response::HTTP_FORBIDDEN;
    }

    private function login(string $username, string $password): ?string
    {
        $response = $this->makeRequest(
            self::HOST . self::LOGIN_URL,
            'POST',
            [
                'json' => [
                    'username' => $username,
                    'password' => $password
                ]
            ]
        );

        if ($response->getStatusCode(false) === Response::HTTP_OK) {
            $dataArray = json_decode($response->getContent(), true);
            return $dataArray['token'];
        }

        return null;
    }


    private function register(string $username, string $password): void
    {
        $response = $this->makeRequest(
            self::HOST . self::CREATE_USER_URL,
            'POST',
            [
                'json' => [
                    'username' => $username,
                    'password' => $password
                ]
            ]
        );

        if ($response->getStatusCode(false) === Response::HTTP_BAD_REQUEST) {
            throw new \Exception('User with such name already exists');
        }
    }

    private function makeRequest(string $url, string $method = 'GET', array $options = []): ResponseInterface
    {
        $response = $this->client->request(
            $method,
            $url,
            $options
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders(false)['content-type'][0];
        $content = $response->getContent(false);

        return $response;
    }

    public function verify(?ApiIntegration $apiIntegration, bool $enabled, User $user, $data): void
    {
        if ($enabled) {
            $userPassword = $data['notelist-token'] ?? null;
            if (!$userPassword) {
                throw new ValidationException('Token missed');
            }

            $username = $user->getUserIdentifier();
            $token = $this->login($username, $userPassword);

            $this->getCategories($user, $token);
        } else {
            $userPassword = '';
            $token = '';
        }

        $apiIntegration->setConfig([
            'password' => $userPassword,
            'token' => $token
        ])
            ->setEnabled($enabled);
        $this->em->persist($apiIntegration);
        $this->em->flush();
    }
}
