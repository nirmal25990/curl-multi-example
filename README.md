# curl-multi-example
PHP has a set of [cURL](https://www.php.net/cURL "Client URL Library") functions to let your script download other webpages. If you use cURL to scrape data or build mashups, you may need to fetch more than one page. This could create a massive performance problem, adding seconds to your own script's runtime because you have to wait for several individual cURL requests to come back.

Let’s say that your app is hitting APIs from these servers:

#### Site	Time
* google:	.3s
* microsoft:	.4s
* facebook.com:	.5s

Your total time will be .12s, just for api calls.

By using [curl_multi_exec](https://www.php.net/manual/en/function.curl-multi-exec.php), you can execute those requests in parallel(simultaneously), and you’ll only be limited by the slowest request, which is about .5 sec to facebook.com in this case, assuming your download bandwidth is not slowing you down.

Unfortunately using the [curl_multi_exec](https://www.php.net/manual/en/function.curl-multi-exec.php) is poorly documented in the PHP manual so I decided to write a simple example which can help anyone.

## Requirements
* PHP 5+

No external library required for this.

## Usage

```php
<?php
// Error flag to check any error
$isError = false;
// Result object
$finalObject = new stdClass();
// Error object
$errorObject = new stdClass();
// For response in json
header('Content-Type: application/json');

// cURL generate function
function getArrayOfOptions($nUrl){
    return array(
        CURLOPT_URL => $nUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        )
        );
}

// Host url
$hostUrl = "https://jsonplaceholder.typicode.com";

// Urls object
$urls = new stdClass();
$urls->getUsers = $hostUrl."/users";
$urls->getTodos = $hostUrl.'/todos';
$urls->getAlbums = $hostUrl.'/albums';
$urls->getPhotos = $hostUrl.'/photos';
$urls->getPosts = $hostUrl.'/posts';
$urls->getComments = $hostUrl.'/comments';

// Empty object to store data
$ch = new stdClass();
// create the multiple cURL handle
$mh = curl_multi_init();

// Set options and add them in handle for each url
foreach($urls as $key => $value) {
    // Create cURL resources
    $ch->$key = curl_init();
    // Set URL and other appropriate options
    curl_setopt_array($ch->$key, getArrayOfOptions($urls->$key));
    // Add handles
    curl_multi_add_handle($mh, $ch->$key);
}

// Execute the multi handle
do {
    $status = curl_multi_exec($mh, $active);
    if ($active) {
        curl_multi_select($mh);
    }
} while ($active && $status == CURLM_OK);

// Remove handles
foreach($urls as $key => $value) {
    curl_multi_remove_handle($mh, $ch->$key);
}

// Close the multiple cURL handles
curl_multi_close($mh);

// Get responsis
$response_getUsers = curl_multi_getcontent($ch->getUsers);
$response_getTodos = curl_multi_getcontent($ch->getTodos);
$response_getAlbums = curl_multi_getcontent($ch->getAlbums);
$response_getPhotos = curl_multi_getcontent($ch->getPhotos);
$response_getPosts = curl_multi_getcontent($ch->getPosts);
$response_getComments = curl_multi_getcontent($ch->getComments);

if(json_decode($response_getUsers)){
    $finalObject->users = json_decode($response_getUsers);
} else {
    $isError = true;
    $errorObject->error = "No data found!";
}

if(json_decode($response_getTodos)){
    $finalObject->todos = json_decode($response_getTodos);
} else {
    $isError = true;
    $errorObject->error = "No data found!";
}

if(json_decode($response_getAlbums)){
    $finalObject->albums = json_decode($response_getAlbums);
} else {
    $isError = true;
    $errorObject->error = "No data found!";
}

if(json_decode($response_getPhotos)){
    $finalObject->photos = json_decode($response_getPhotos);
} else {
    $isError = true;
    $errorObject->error = "No data found!";
}

if(json_decode($response_getPosts)){
    $finalObject->posts = json_decode($response_getPosts);
} else {
    $isError = true;
    $errorObject->error = "No data found!";
}

if(json_decode($response_getComments)){
    $finalObject->comments = json_decode($response_getComments);
} else {
    $isError = true;
    $errorObject->error = "No data found!";
}

// If want to see result
// echo "<pre>";
// print_r(json_decode($response_getUsers));
// echo "</pre>";

// Display error or result
if($isError){
    http_response_code(400);
    echo json_encode($errorObject);
} else {
    echo json_encode($finalObject);
}

?>
```

## Authors
   * [NiRmal](https://github.com/nirmal25990)
   
