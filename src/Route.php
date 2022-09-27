<?php

namespace UpdatedData\AnonFiles;

enum Route: string
{
    case UPLOAD = 'https://api.anonfiles.com/upload?token=%s';
    case INFO = 'https://api.anonfiles.com/v2/file/%s/info';
}