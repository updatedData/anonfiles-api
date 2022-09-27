<?php

namespace Tests\Unit\AnonFiles;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use UpdatedData\AnonFiles\Api;
use UpdatedData\AnonFiles\ApiError;
use UpdatedData\AnonFiles\ErrorResponse;
use UpdatedData\AnonFiles\FileResponse;

class ApiTest extends TestCase
{
    private string $dummyKey = '1234';

    private string $successJson = '
    {
        "status": true,
        "data": {
            "file": {
                "url": {
                    "full": "https://anonfiles.com/u1C0ebc4b0/file.txt",
                    "short": "https://anonfiles.com/u1C0ebc4b0"
                },
                "metadata": {
                    "id": "u1C0ebc4b0",
                    "name": "file.txt",
                    "size": {
                        "bytes": 6861,
                        "readable": "6.7 KB"
                    }
                }
            }
        }
    }';

    private string $errorJsonSkeleton = '
        {
            "status": false,
            "error": {
                "message": "Some Error",
                "type": "%s",
                "code": %s
            }
        }
    ';

    public function testUploadWithFileNotReadable(): void
    {
        $mock = new MockHandler([]);
        $stack = HandlerStack::create($mock);

        $this->expectException(\RuntimeException::class);

        $api = new Api($this->dummyKey, new Client(['handler' => $stack]));
        $api->upload('this-file-does-not-exist');
    }

    public function testUploadSucceeds(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->successJson)
        ]);
        $stack = HandlerStack::create($mock);
        $api = new Api($this->dummyKey,new Client(['handler' => $stack]));

        /** @var FileResponse $response */
        $response = $api->upload(__DIR__.'/file.txt');

        static::assertInstanceOf(FileResponse::class, $response);
        static::assertEquals('https://anonfiles.com/u1C0ebc4b0/file.txt', $response->longUrl);
        static::assertEquals('https://anonfiles.com/u1C0ebc4b0', $response->shortUrl);
        static::assertEquals('u1C0ebc4b0', $response->id);
        static::assertEquals('file.txt', $response->name);
        static::assertEquals(6861, $response->size);
        static::assertEquals('6.7 KB', $response->readableSize);
    }

    public function testAllTheErrors(): void
    {
        $mockResponses = [];
        foreach(ApiError::cases() as $apiError) {
            $mockResponses[] = new Response(400, [], sprintf($this->errorJsonSkeleton, $apiError->name, $apiError->value));
        }
        $mock = new MockHandler($mockResponses);
        $stack = HandlerStack::create($mock);
        $api = new Api($this->dummyKey,new Client(['handler' => $stack]));

        foreach(ApiError::cases() as $apiError) {
            /** @var ErrorResponse $response */
            $response = $api->upload(__DIR__.'/file.txt');
            static::assertInstanceOf(ErrorResponse::class, $response);
            static::assertEquals($response->error, $apiError);
        }
    }

    public function testFileExists(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->successJson)
        ]);
        $stack = HandlerStack::create($mock);
        $api = new Api($this->dummyKey,new Client(['handler' => $stack]));
        $response = $api->fileInfo('u1C0ebc4b0');
        static::assertEquals('https://anonfiles.com/u1C0ebc4b0/file.txt', $response->longUrl);
        static::assertEquals('https://anonfiles.com/u1C0ebc4b0', $response->shortUrl);
        static::assertEquals('u1C0ebc4b0', $response->id);
        static::assertEquals('file.txt', $response->name);
        static::assertEquals(6861, $response->size);
        static::assertEquals('6.7 KB', $response->readableSize);
    }

    public function testFileDoesNotExist(): void
    {
        $mock = new MockHandler([
            new Response(404, [], sprintf($this->errorJsonSkeleton, ApiError::ERROR_FILE_NOT_FOUND->name, ApiError::ERROR_FILE_NOT_FOUND->value))
        ]);
        $stack = HandlerStack::create($mock);
        $api = new Api($this->dummyKey,new Client(['handler' => $stack]));
        $response = $api->fileInfo('u1C0ebc4b0');
        static::assertInstanceOf(ErrorResponse::class, $response);
        static::assertEquals($response->error, ApiError::tryFrom(ApiError::ERROR_FILE_NOT_FOUND->value));
    }
}