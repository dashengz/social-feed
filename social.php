<?php
/**
 * TC Social Feed
 * @version v1.0.0
 * @author Jonathan Dasheng Zhang (zhang10@tc.columbia.edu)
 */
require_once('secret.php');
// require_once('twitter_helper.php');

$api_twitter = 'https://api.twitter.com';
$api_instagram = 'https://api.instagram.com';

// If these token ever expire, need to regenerate with necessary credentials
// For Twitter, just uncomment auth_twitter() and regenerate
$token_twitter = $TOKEN_T;

// For Instagram, need to visit
// https://api.instagram.com/oauth/authorize/?client_id={CLIENT_ID}&redirect_uri=https://www.tc.columbia.edu&response_type=code&scope=public_content
// And regenerate the CODE for auth_instagram()
// Currently, the app is in sandbox mode, and is owned by Jonathan Zhang (zhang10@tc.columbia.edu)
// To show your feed, you need to be added as a sandbox user, and follow authentication to generate an access token.
// Detailed instruction coming soon.
$token_instagram = $TOKEN_I;

$handle_twitter = '';
$handle_instagram = '';
if (isset($_REQUEST['twitter'])) {
    $handle_twitter = $_REQUEST['twitter'];
}
if (isset($_REQUEST['instagram'])) {
    $handle_instagram = $_REQUEST['instagram'];
}

$request_timeline = '/1.1/statuses/user_timeline.json';
$params_timeline = array(
    'screen_name' => $handle_twitter,
    'exclude_replies' => true,
    'include_rts' => false
);

$request_media = '/v1/users/self/media/recent';
$params_media = array(
    'count' => '9'
);

$response = Array();

if (strlen($handle_twitter)) {
    $response["twitter"] = process_twitter_data(http_get_twitter($request_timeline, $params_timeline));
}
if (strlen($handle_instagram)) {
    $response["instagram"] = process_instagram_data(http_get_instagram($request_media, $params_media));
}

if (count($response)) echo json_encode($response);


function process_twitter_data($data)
{
    $tweets = Array();
    foreach ($data as $tweet) {
        $t = Array();
        $t["date"] = $tweet["created_at"];
        $t["text"] = $tweet["full_text"];
        $t["retweet_count"] = $tweet["retweet_count"];
        $t["favorite_count"] = $tweet["favorite_count"];
        $t["user"] = $tweet["user"]["screen_name"];
        // the urls in entities are sometimes missing (eg. in media posts),
        // so we need to grab the url in the tweet
        preg_match('/https?:\/\/[^ ]*/', $tweet["text"], $matches_url);
        $tweet_url = '';
        if (count($matches_url)) $tweet_url = $matches_url[0];
        $t["tweet_url"] = $tweet_url;
        // entities
        $t["entities"] = $tweet["entities"];
        // For later, need to configure php.ini
        // if (strlen($tweet_url)) array_merge($t, get_tweet_and_meta($tweet_url));

        // Add this tweet in the tweets array
        array_push($tweets, $t);
    }
    return $tweets;
}

function process_instagram_data($data)
{
    $instas = Array();
    foreach ($data["data"] as $insta) {
        $i = Array();
        $i["user"] = $insta["user"]["username"];
        // Convert to milliseconds
        $i["date"] = $insta["created_time"] . "000";
        $i["caption"] = "";
        if ($insta["caption"]) $i["caption"] = $insta["caption"]["text"];
        $i["type"] = $insta["type"];
        $i["link"] = $insta["link"];
        $i["tags"] = $insta["tags"];
        $i["location"] = "";
        if ($insta["location"]) $i["location"] = $insta["location"]["name"];
        $i["image"] = $insta["images"]["standard_resolution"]["url"];

        // Add this insta in the instas array
        array_push($instas, $i);
    }
    return $instas;
}

function http_get_twitter($path, $params)
{
    global $api_twitter, $token_twitter;
    $opts = array(
        'http' => array(
            'method' => 'GET',
            'header' => 'Authorization: Bearer ' . $token_twitter
        )
    );
    $context = stream_context_create($opts);
    // support for extended tweets
    $params['tweet_mode'] = 'extended';
    $json = file_get_contents($api_twitter . $path . '?' . http_build_query($params), false, $context);
    return json_decode($json, true);
}

function http_get_instagram($path, $params)
{
    global $api_instagram, $token_instagram;
    $opts = array(
        'http' => array(
            'method' => 'GET'
        )
    );
    $context = stream_context_create($opts);
    $params['access_token'] = $token_instagram;
    $json = file_get_contents($api_instagram . $path . '?' . http_build_query($params), false, $context);
    return json_decode($json, true);
}

//function auth_twitter()
//{
//    global $api_twitter, $KEY_T, $SECRET_T;
//    $key = $KEY_T;
//    $secret = $SECRET_T;
//    $request = $api_twitter . '/oauth2/token';
//    $credential = base64_encode($key . ':' . $secret);
//    $opts = array(
//        'http' => array(
//            'method' => 'POST',
//            'header' => 'Authorization: Basic ' . $credential . "\r\n" .
//                'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
//            'content' => http_build_query(array(
//                'grant_type' => 'client_credentials'
//            ))
//        )
//    );
//    $context = stream_context_create($opts);
//    $json = file_get_contents($request, false, $context);
//    $result = json_decode($json, true);
//    return $result['access_token'];
//}

//function auth_instagram()
//{
//    global $api_instagram, $KEY_I, $SECRET_I, $CODE_I;
//    $key = $KEY_I;
//    $secret = $SECRET_I;
//    $code = $CODE_I;
//    $redirect_instagram = 'https://www.tc.columbia.edu';
//    $request = $api_instagram . '/oauth/access_token';
//    $opts = array(
//        'http' => array(
//            'method' => 'POST',
//            'header' => 'Content-Type: application/x-www-form-urlencoded;charset=UTF-8',
//            'content' => http_build_query(array(
//                'client_id' => $key,
//                'client_secret' => $secret,
//                'grant_type' => 'authorization_code',
//                'redirect_uri' => $redirect_instagram,
//                'code' => $code
//            ))
//        )
//    );
//    $context = stream_context_create($opts);
//    $json = file_get_contents($request, false, $context);
//    $result = json_decode($json, true);
//    return $result['access_token'];
//}