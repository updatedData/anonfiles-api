#Usage

##Upload

To obtain an API-Key register at https://anonfiles.com/register and go to https://anonfiles.com/docs/api after you've logged in.

```
$api = new \UpdatedData\AnonFiles\Api($yourApiToken);
$response = $api->upload($pathToYourFile);
```

##FileInfo

```
$api = new \UpdatedData\AnonFiles\Api($yourApiToken);
$response = $api->fileInfo($fileIdentifier);
```

