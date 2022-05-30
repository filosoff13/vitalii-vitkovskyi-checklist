<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Entity\ApiIntegrationCategory;
use App\Entity\ApiIntegrationTask;
use App\Entity\Category;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\ApiIntegrationsEnum;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DvCampusNotelistIntegrationStrategy extends AbstractIntegrationStrategy
{
    const HOST = 'https://dv-campus-notelist.allbugs.info/api';
    const CREATE_USER_URL = '/user';
    const LOGIN_URL = '/login_check';
    const CATEGORY_URL = '/category';
    const CATEGORY_DELETE_URL = '/category/%d';
    const NOTE_URL = '/note';

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
            $this->getCategories($user, $token, $apiIntegration);
            $this->getTasks($user, $token, $apiIntegration);
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

    public function getCategories(User $user, string $token, ?ApiIntegration $apiIntegration): void
    {
        $repository = $this->em->getRepository(Category::class);
        $categories = $repository->findBy(['user' => $user]);
        foreach ($categories as $category) {
            $repository = $this->em->getRepository(ApiIntegrationCategory::class);
            $apiIntegrationCategory = $repository->findBy(['category' => $category->getId()]);
            if ($apiIntegrationCategory) {
                continue;
            }
            $externalId = $this->postCategory($category->getTitle(), $token);

            $apiIntegrationCategory = new ApiIntegrationCategory();
            $apiIntegrationCategory->setExternalId($externalId)
                ->setCategory($category)
                ->setApiIntegration($apiIntegration);
            $this->em->persist($apiIntegrationCategory);
        }

        $this->em->flush();
    }

    public function getTasks(User $user, $token, ?ApiIntegration $apiIntegration): void
    {
        $repository = $this->em->getRepository(Task::class);
        $tasks = $repository->findByUser($user);
        $repository = $this->em->getRepository(ApiIntegrationTask::class);
        foreach ($tasks as $task) {
            $apiIntegrationTask = $repository->findBy(['task' => $task]);
            if ($apiIntegrationTask) {
                continue;
            }

            $externalCategoryId = $this->postTask($task, $token);

            $apiIntegrationTask = new ApiIntegrationTask();
            $apiIntegrationTask->setExternalId($externalCategoryId)
                ->setTask($task)
                ->setApiIntegration($apiIntegration);
            $this->em->persist($apiIntegrationTask);
        }

        $this->em->flush();
    }

    private function postTask(Task $task, string $token): int
    {
        $category = $task->getCategory();

        $repository = $this->em->getRepository(ApiIntegrationCategory::class);
        $apiIntegrationCategory = $repository->findBy(['category' => $category->getId()]);
        $externalCategoryId = $apiIntegrationCategory[0]->getExternalId();
        $response = $this->makeRequest(
            self::HOST . self::NOTE_URL,
            'POST',
            [
                'headers' => ['Authorization' => sprintf('Bearer %s', $token)],
                'json' => [
                      'title' => $task->getTitle(),
                      'text' => $task->getText(),
                      "category" => [
                            'id' => $externalCategoryId
                      ]
                ]
            ]
        );

        $statusCode = $response->getStatusCode(false);
        if ($statusCode === Response::HTTP_UNAUTHORIZED) {
            throw new \Exception('JWT Token not found');
        }

        return json_decode($response->getContent(), true)['id'];
    }

    private function postCategory(string $name, $token): int
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

        return json_decode($response->getContent(), true)['id'];
    }

    public function deleteCategory($token, int $id): void
    {
        $response = $this->makeRequest(
            sprintf(self::HOST . self::CATEGORY_DELETE_URL, $id),
            'DELETE',
            [
                'headers' => ['Authorization' => sprintf('Bearer %s', $token)]
            ]
        );

        $statusCode = $response->getStatusCode(false);
        if ($statusCode === Response::HTTP_UNAUTHORIZED) {
            throw new \Exception('JWT Token not found');
        }
    }

    public function login(string $username, string $password): ?string
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

    public function verify(?ApiIntegration $apiIntegration, bool $enabled, User $user, array $data): void
    {
        if ($enabled) {
            $userPassword = $data['notelist-token'] ?? null;
            if (!$userPassword) {
                throw new ValidationException('Token missed');
            }

            $username = $user->getUserIdentifier();
            $token = $this->login($username, $userPassword);

            $this->getCategories($user, $token, $apiIntegration);
            $this->getTasks($user, $token, $apiIntegration);
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
