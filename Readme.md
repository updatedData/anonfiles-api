# Usage

The Api provides the ``upload`` and `fileInfo` function.

Any successful call will return an instance of a ``FileResponse`` while an error will result in a `ErrorResponse`


## Examples

### Upload

To obtain an API-Key register at https://anonfiles.com/register and go to https://anonfiles.com/docs/api after you've logged in.

```
$api = new \UpdatedData\AnonFiles\Api($yourApiToken);
$response = $api->upload($pathToYourFile);
```

### FileInfo

An API-Key is not required for a file lookup, but it's high encouraged to use provide an token since ``upload``
 will throw a ``RuntimeException`` without a key
```
$api = new \UpdatedData\AnonFiles\Api();
$response = $api->fileInfo($fileIdentifier);
```

