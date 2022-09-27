<?php

namespace UpdatedData\AnonFiles;

class FileResponse
{
    public readonly string $longUrl;
    public readonly string $shortUrl;
    public readonly string $id;
    public readonly string $name;
    public readonly string $readableSize;
    public readonly int $size;

    public function __construct(\stdClass $response)
    {
        $this->longUrl = $response->data->file->url->full;
        $this->shortUrl = $response->data->file->url->short;
        $this->id = $response->data->file->metadata->id;
        $this->name = $response->data->file->metadata->name;
        $this->readableSize = $response->data->file->metadata->size->readable;
        $this->size = $response->data->file->metadata->size->bytes;
    }
}