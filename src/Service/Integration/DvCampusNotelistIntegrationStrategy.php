<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Entity\User;
use App\Enum\ApiIntegrationsEnum;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Namshi\JOSE\JWT;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DvCampusNotelistIntegrationStrategy extends AbstractIntegrationStrategy
{
    const HOST = 'https://dv-campus-notelist.allbugs.info/api';
    const CREATE_USER_URL = '/user';
    const LOGIN_URL = '/login_check';

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
        // TODO: deserialize to some model

        // TODO: check
        $enabled = (bool) ($data['notelist-enabled'] ?? false);

        $repository = $this->em->getRepository(ApiIntegration::class);
        $apiIntegration = $repository->findOneOrNullBy([
            'user' => $user,
            'type' => ApiIntegrationsEnum::NOTELIST // TODO: create enum
        ]);

        if (!$apiIntegration) {
            if ($enabled) {
                $this->create($data, $user);
            }

            return;
        }

        if ($apiIntegration->getEnabled() === $enabled) {
            return;
        }

        if ($enabled) {
            $this->create($data, $user);
            return;
        }

        $this->em->remove($apiIntegration);
        $this->em->flush();
    }

    /**
     * @throws ValidationException
     */
    public function create(array $data, User $user): ApiIntegration
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
            ->setUser($user); // TODO: enum

        $this->em->persist($apiIntegration);
        $this->em->flush();

        return $apiIntegration;
    }

    private function login(string $username, string $password): ?string {
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
//        return $response->getStatusCode(false) === Response::HTTP_OK
//            ? $response->getContent()['token']
//            : null;
    }


    private function register(string $username, string $password): void {
//        $this->client->request('POST', self::SING_IN_URI, ['username' => $this->username, 'password' => $this->password], [], ['HTTP_ACCEPT' => 'application/json']);
//        $loginResponse = json_decode($this->client->getResponse()->getContent());
//        $authenticateHeaders = ['HTTP_TOKEN' => isset($loginResponse->Token) ? $loginResponse->Token : null, 'HTTP_EXPIREAT' => isset($loginResponse->ExpireAt) ? $loginResponse->ExpireAt : null, 'HTTP_USERNAME' => isset($loginResponse->Username) ? $loginResponse->Username : null];

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
    }

    private function makeRequest(string $url, string $method = 'GET', array $options = []): ResponseInterface
    {
        $response = $this->client->request(
            $method,
            $url,
            $options
        );

        $statusCode = $response->getStatusCode();
        // $statusCode = 200
        $contentType = $response->getHeaders(false)['content-type'][0];
        // $contentType = 'application/json'8
        $content = $response->getContent(false);
        // $content = '{"id":521583, "name":"symfony-docs", ...}'
//        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]
        return $response;
    }
}
