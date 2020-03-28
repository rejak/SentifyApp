<?php
require_once 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;
session_start();
$config = require_once 'config.php';
// get and filter oauth verifier
$oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');
// check tokens
if (empty($oauth_verifier) ||
    empty($_SESSION['oauth_token']) ||
    empty($_SESSION['oauth_token_secret'])
) {
    // something's missing, go and login again
    header('Location: ' . $config['url_login']);
}
// connect with application token
$connection = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $_SESSION['oauth_token'],
    $_SESSION['oauth_token_secret']
);
// request user token
$token = $connection->oauth(
    'oauth/access_token', [
        'oauth_verifier' => $oauth_verifier
    ]
);
// connect with user token
$twitter = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $token['oauth_token'],
    $token['oauth_token_secret']
);
$user = $twitter->get('account/verify_credentials');
$arr=array();
$arr["screen_name"]= $user->screen_name;
$arr["id"]= $user->id;
$arr["lang"]=$user->lang;
$arr["name"]=$user->name;
$arr["loc"]=$user->location;
$arr["ver"]=$user->created_at;
$arr["des"]=$user->description;
$arr["pic"]=$user->profile_image_url_https;
$arr["fol"]=$user->followers_count;
$arr["fav"]=$user->favourites_count;
$arr["stat"]=$user->friends_count;
// if something's wrong, go and log in again
if(isset($user->error)) {
    header('Location: ' . $config['url_login']);
}else{
    session_start();
    $_SESSION['varScreen'] = $arr["screen_name"];
    $_SESSION['varId'] = $arr["id"];
    $_SESSION['varLang']=$arr["lang"];
    $_SESSION['varName'] = $arr["name"];
    $_SESSION['varLoc'] = $arr["loc"];
    $_SESSION['varVer'] = $arr["ver"];
    $_SESSION['varDes'] = $arr["des"];
    $_SESSION['varPic']= $arr["pic"];
    $_SESSION['varFol']= $arr["fol"];
    $_SESSION['varFav']= $arr["fav"];
    $_SESSION['varStat']= $arr["stat"];

    header('Location: ' . $config['url_dashboard']);
}
// // post a tweet
// $status = $twitter->post(
//     "statuses/update", [
//         "status" => "Use @SentifyId for your twitter analytic"
//     ]
// );
// echo ('Created new status with #' . $status->id . PHP_EOL);
// echo ('User : ' . $user->location . PHP_EOL);
// print_r($status);
?>
<<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Page Title</title>
    <button>Ini tombol</button>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    
</body>
</html>