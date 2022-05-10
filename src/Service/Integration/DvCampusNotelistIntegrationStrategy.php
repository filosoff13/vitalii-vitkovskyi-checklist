<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Exception\ValidationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DvCampusNotelistIntegrationStrategy extends AbstractIntegrationStrategy
{
    const HOST = 'https://dv-campus-notelist.allbugs.info/api';
    const CREATE_USER_URL = '/user';

    private HttpClientInterface $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }


    public function create(array $data): ApiIntegration
    {
        // ------------------------------------
        // save password
        // make API request to the dv campus notelist
        $response = $this->makeRequest(
            self::HOST . self::CREATE_USER_URL,
            'POST',
            [
                'json' => [
                    'username' => 'user2',
                    'password' => 'password'
                    ]
            ]
        );

         if ($response->getStatusCode() !== 200) {
             // throw ValidationException
             throw new ValidationException('Not ok status code');
         }

        // save config
        // ------------------------------------


    }

    private function makeRequest(string $url, string $method = 'GET', array $options = []): ResponseInterface {
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

//        return $content;
        return $response;
    }
}