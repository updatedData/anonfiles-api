<?php

namespace UpdatedData\AnonFiles;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;

class Api
{
    private readonly ?Client $client;

    public function __construct(private readonly ?string $apiKey = null, ?Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    public function fileInfo(string $fileId): FileResponse|ErrorResponse
    {
        try {
            $response = $this->client->get(sprintf(Route::INFO->value, $fileId));
        } catch (ClientException $exception) {
            return new ErrorResponse(json_decode($exception->getResponse()->getBody(), false,));
        }

        return new FileResponse(json_decode($response->getBody(), false));
    }

    public function upload(string $filePath): FileResponse|ErrorResponse
    {
        if(null === $this->apiKey) {
            throw new \RuntimeException('Upload requires the api token!');
        }

        if(!is_readable($filePath)) {
            throw new \RuntimeException(sprintf('File %s is not readable or does not exist', $filePath));
        }

        try {
            $response = $this->client->post(sprintf(Route::UPLOAD->value, $this->apiKey), [
                RequestOptions::MULTIPART => [
                    [
                        'name' => 'file',
                        'contents' => Utils::tryFopen($filePath, 'r'),
                    ],
                ],
            ]);
        } catch (ClientException $exception) {
            return new ErrorResponse(json_decode($exception->getResponse()->getBody(), false));
        }

        return new FileResponse(json_decode($response->getBody(), false));
    }
}