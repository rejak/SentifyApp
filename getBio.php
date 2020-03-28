<?php
/** Just simple code to get twitter Account Bio **/
/** Rischan Mafrur  **/
/** April 28 2014   **/
ini_set('display_errors', true);
// set display error
function getBio ($screenName){
    ini_set('display_errors', 1);
    require_once('TwitterAPIExchange.php');
    $config = require_once 'config.php';
    #require_once('Cache.php');
    $settings = array(
        'oauth_access_token' => $config["oauth_token"],
        'oauth_access_token_secret' => $config["oauth_token_secret"],
        'consumer_key' => $config["consumer_key"],
        'consumer_secret' => $config["consumer_secret"]
    );
            $apiUrl = "https://api.twitter.com/1.1/users/show.json"; //twitter API 1.1
            $requestMethod = 'GET';
            $getField = '?screen_name=' . $screenName;
     
            $twitter = new TwitterAPIExchange($settings); //dont forget to clone TwitterAPIExchange
            $response = $twitter->setGetfield($getField)
                 ->buildOauth($apiUrl, $requestMethod)
                 ->performRequest();
     
            $data = json_decode($response);
            $screen_name = $data->screen_name; 
            $followers = $data->followers_count;
            $friends =  $data->friends_count;
            $protected = $data->protected;
            if ($protected==false) {
                $p = "false";
            } else {
                $p = "true";
            }
            
            $created_at = $data->created_at;
            $favourites_count = $data->favourites_count;
            $listed_count = $data->listed_count;
            $time_zone = $data->time_zone;
            $statuses_count = $data->statuses_count;
            $location = $data->location;
            $bio = array(
                "name" => $screen_name,
                "followers" => $followers, 
                "friends" => $friends,
                "protected" => $p,
                "created_at" => $created_at,
                "favourites_count" => $favourites_count,
                "listed" => $listed_count,
                "status"  => $statuses_count,
                "location" => $location,
                "time_zone" =>$time_zone
                );
            return $bio;   
}
$bio = getBio("mohrezaeffendy");
echo $bio["status"];
// foreach ($bio as $item) {
//     echo $item;
// }
?>
