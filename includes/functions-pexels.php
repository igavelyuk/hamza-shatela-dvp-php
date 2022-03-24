<?php
/*
* pexels key: 563492ad6f9170000100000168a64410b59c4939b835fa9575d462b0
*
* https://api.pexels.com/v1 For images
* https://api.pexels.com/videos For videos
*
* Sits at 200 requests per hour, and 20,000 per month
*
* To see how many requests you have left in this current period,
* you can look at the HTTP response header called X-Ratelimit-Remaining that comes with every response you receive.
*/
function getImages( string $query = 'nature', int $per_page = 1, string $color = '#ff0000' ){

  // Pexels API token
  $accesstoken = '563492ad6f9170000100000168a64410b59c4939b835fa9575d462b0';

  // create & initialize a curl session
  $curl = curl_init();

  $header = array();
  $header[] = 'Authorization: '.$accesstoken;

  // set our url with curl_setopt()
  curl_setopt($curl, CURLOPT_URL, "https://api.pexels.com/v1/search?query=".$query."&per_page=".$per_page."&color=".$color);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
  curl_setopt($curl, CURLOPT_HEADER, 1);

  // return the transfer as a string, also with setopt()
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

  // curl_exec() executes the started curl session
  // $output contains the output string
  $output = curl_exec($curl);

  // close curl resource to free up system resources
  // (deletes the variable made by curl_init)
  curl_close($curl);

  return $output;

}
