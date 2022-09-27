<?php

namespace UpdatedData\AnonFiles;

use GuzzleHttp\Psr7\Response;

class ErrorResponse
{
    public readonly string $message;
    public readonly ApiError $error;

    public function __construct(\stdClass $response)
    {
        $this->message = $response->error->message;
        $this->error = ApiError::tryFrom($response->error->code);
    }
}