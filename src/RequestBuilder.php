<?php

namespace MacropaySolutions\CrufdWizardClient;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class RequestBuilder
{
    private Client $client;

    public function __construct(
        string $token,
        string $url,
        array $config = []
    ) {
        $this->client = new Client(\array_merge([
            RequestOptions::TIMEOUT => 60,
            RequestOptions::CONNECT_TIMEOUT => 5,
        ], $config, [
            'base_uri' => \rtrim($url, '/') . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
        ]));
    }

    public function getBuilder(): QueryStringBuilder
    {
        return new QueryStringBuilder();
    }

    /**
     * @throws Exception with message as json
     */
    public function list(
        string $resource,
        QueryStringBuilder $queryStringBuilder,
        array $headers = [],
        bool $asyncCount = false,
        bool $viaPost = false
    ): string {
        return $this->listInOneCall($resource, $queryStringBuilder, $headers, $viaPost);
    }

    /**
     * @throws Exception with message as json
     */
    public function create(string $resource, array $content): string
    {
        try {
            $response = $this->client->post(\trim($resource, '/'), ['json' => $content]);
        } catch (RequestException $e) {
            throw new Exception(
                (string)\json_encode(['message' => $e->getMessage()]),
                $e->getResponse() instanceof ResponseInterface ? $e->getResponse()->getStatusCode() : $e->getCode()
            );
        } catch (GuzzleException $e) {
            throw new Exception((string)\json_encode(['message' => $e->getMessage()]), $e->getCode());
        }

        return $this->handleResponse($response);
    }

    /**
     * @throws Exception with message as json
     */
    public function get(
        string $resource,
        string $identifier,
        array $withRelations = [],
        array $withAppends = [],
        array $withRelationsCount = [],
        array $withRelationsExistence = [],
        array $withRelationsSums = [],
        array $withRelationsAvgs = [],
        array $withRelationsMins = [],
        array $withRelationsMaxs = [],
        string $relation = '',
        string $relatedIdentifier = ''
    ): string {
        try {
            $response = $this->client->get(\trim($resource, '/') . '/' . $identifier . (
                $relation !== '' && $relatedIdentifier !== '' ? '/' . $relation . '/' . $relatedIdentifier : ''
            ) . '?' . \http_build_query([
                    'withRelations' => \array_values($withRelations),
                    'withRelationsCount' => \array_values($withRelationsCount),
                    'withRelationsExistence' => \array_values($withRelationsExistence),
                ], '', '&', PHP_QUERY_RFC3986));
        } catch (RequestException $e) {
            throw new Exception(
                (string)\json_encode(['message' => $e->getMessage()]),
                $e->getResponse() instanceof ResponseInterface ? $e->getResponse()->getStatusCode() : $e->getCode()
            );
        } catch (GuzzleException $e) {
            throw new Exception((string)\json_encode(['message' => $e->getMessage()]), $e->getCode());
        }

        return $this->handleResponse($response);
    }

    /**
     * @throws Exception with message as json
     */
    public function update(
        string $resource,
        string $identifier,
        array $content,
        array $withRelations = [],
        array $withAppends = [],
        array $withRelationsCount = [],
        array $withRelationsExistence = [],
        array $withRelationsSums = [],
        array $withRelationsAvgs = [],
        array $withRelationsMins = [],
        array $withRelationsMaxs = [],
        string $relation = '',
        string $relatedIdentifier = ''
    ): string
    {
        try {
            $response = $this->client->put(\trim($resource, '/') . '/' . $identifier . (
                $relation !== '' && $relatedIdentifier !== '' ? '/' . $relation . '/' . $relatedIdentifier : ''
            ), ['json' => $content]);
        } catch (RequestException $e) {
            throw new Exception(
                (string)\json_encode(['message' => $e->getMessage()]),
                $e->getResponse() instanceof ResponseInterface ? $e->getResponse()->getStatusCode() : $e->getCode()
            );
        } catch (GuzzleException $e) {
            throw new Exception((string)\json_encode(['message' => $e->getMessage()]), $e->getCode());
        }

        return $this->handleResponse($response);
    }

    /**
     * @throws Exception with message as json
     */
    public function delete(
        string $resource,
        string $identifier,
        string $relation = '',
        string $relatedIdentifier = ''
    ): string {
        try {
            $response = $this->client->delete(\trim($resource, '/') . '/' . $identifier . (
                $relation !== '' && $relatedIdentifier !== '' ? '/' . $relation . '/' . $relatedIdentifier : ''
            ));
        } catch (RequestException $e) {
            throw new Exception(
                (string)\json_encode(['message' => $e->getMessage()]),
                $e->getResponse() instanceof ResponseInterface ? $e->getResponse()->getStatusCode() : $e->getCode()
            );
        } catch (GuzzleException $e) {
            throw new Exception((string)\json_encode(['message' => $e->getMessage()]), $e->getCode());
        }

        return $this->handleResponse($response);
    }

    /**
     * @throws Exception with message as json
     */
    private function handleResponse(ResponseInterface $response): string
    {
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (\strpos((string)$statusCode, '2') === 0) {
            return $contents;
        }

        throw new Exception(
            \strpos((string)$statusCode, '4') === 0 ? $contents : \json_encode(['message' => $contents]),
            $statusCode
        );
    }

    /**
     * @throws Exception with message as json
     */
    protected function listInOneCall(
        string $resource,
        QueryStringBuilder $queryStringBuilder,
        array $headers,
        bool $viaPost = false
    ): string {
        try {
            $response = $viaPost ?
                $this->client->post(\trim($resource, '/') . '/l/i/s/t', [
                    'headers' => \array_merge($headers, ['Content-Type' => 'application/x-www-form-urlencoded']),
                    'body' => \http_build_query($queryStringBuilder->getAllFilters(), '', '&', PHP_QUERY_RFC3986),
                ]) :
                $this->client->get(\trim($resource, '/') . '?' . $queryStringBuilder->getUrlQueryString(), [
                    'headers' => $headers
                ]);
        } catch (RequestException $e) {
            throw new Exception(
                (string)\json_encode(['message' => $e->getMessage()]),
                $e->getResponse() instanceof ResponseInterface ? $e->getResponse()->getStatusCode() : $e->getCode()
            );
        } catch (GuzzleException $e) {
            throw new Exception((string)\json_encode(['message' => $e->getMessage()]), $e->getCode());
        }

        return $this->handleResponse($response);
    }
}