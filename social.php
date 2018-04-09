<?php
/**
 * TC Social Feed
 * @version v0.3.0
 * @author Jonathan Dasheng Zhang (zhang10@tc.columbia.edu)
 */
require_once('secret.php');

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
    'count' => '3'
);

$request_media = '/v1/users/self/media/recent';
$params_media = array(
    'count' => '3'
);

if (strlen($handle_twitter)) {
    echo json_encode(http_get_twitter($request_timeline, $params_timeline));
}
if (strlen($handle_instagram)) {
    echo json_encode(http_get_instagram($request_media, $params_media));
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