<?php
$isError = false;
$finalObject = new stdClass();
$errorObject = new stdClass();
//For response in json
header('Content-Type: application/json');

//cURL generate function
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

//Host url
$hostUrl = "https://jsonplaceholder.typicode.com";

//urls object
$urls = new stdClass();
$urls->getUsers = $hostUrl."/users";
$urls->getTodos = $hostUrl.'/todos';
$urls->getAlbums = $hostUrl.'/albums';
$urls->getPhotos = $hostUrl.'/photos';
$urls->getPosts = $hostUrl.'/posts';
$urls->getComments = $hostUrl.'/comments';

//empty object to store data
$ch = new stdClass();
//create the multiple cURL handle
$mh = curl_multi_init();

foreach($urls as $key => $value) {
    // create cURL resources
    $ch->$key = curl_init();
    // set URL and other appropriate options
    curl_setopt_array($ch->$key, getArrayOfOptions($urls->$key));
    //add handles
    curl_multi_add_handle($mh, $ch->$key);
}

//execute the multi handle
do {
    $status = curl_multi_exec($mh, $active);
    if ($active) {
        curl_multi_select($mh);
    }
} while ($active && $status == CURLM_OK);

foreach($urls as $key => $value) {
    //remove handles
    curl_multi_remove_handle($mh, $ch->$key);
}

//close the multiple cURL handles
curl_multi_close($mh);

//get responsis
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

//If want to see result
// echo "<pre>";
// print_r(json_decode($response_getUsers));
// echo "</pre>";

if($isError){
    http_response_code(400);
    echo json_encode($errorObject);
} else {
    echo json_encode($finalObject);
}


?>