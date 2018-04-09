<?php
/**
 * Get Twitter Card meta (if any)
 * @author Jonathan Dasheng Zhang
 */

function get_html_data($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function get_tweet($url)
{
    $dom = new DOMDocument();
    $html = get_html_data($url);
    @$dom->loadHTML($html);

    // "(.*)"
    // https?:\/\/[^ ]*
    $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;

    preg_match('/"(.*)"/', $title, $matches_tweet);
    preg_match('/https?:\/\/[^ ]*/', $title, $matches_url);

    $tweet = '';
    $url = '';
    if (count($matches_tweet)) $tweet = $matches_tweet[1];
    if (count($matches_url)) $url = $matches_url[0];

    return Array(
        "tweet" => $tweet,
        "url" => $url
    );
}

function get_twitter_meta($url)
{
    $dom = new DOMDocument();
    $html = get_html_data($url);
    @$dom->loadHTML($html);
    $title = '';
    $description = '';
    $image_url = '';

    foreach ($dom->getElementsByTagName('meta') as $meta) {
        if ($meta->getAttribute('name') == 'twitter:title')
            $title = $meta->getAttribute('content');
        if ($meta->getAttribute('name') == 'twitter:description')
            $description = $meta->getAttribute('content');
        if ($meta->getAttribute('name') == 'twitter:image')
            $image_url = $meta->getAttribute('content');
    }

    return Array(
        "title" => $title,
        "description" => $description,
        "image_url" => $image_url
    );
}

function get_tweet_and_meta($url) {
    $data = get_tweet($url);
    $card_url = $data["url"];
    $card = Array();
    if (strlen($card_url)) {
        $card_data = get_twitter_meta($card_url);
        $card["title"] = $card_data["title"];
        $card["description"] = $card_data["description"];
        $card["image_url"] = $card_data["image_url"];
    }

    return Array(
        "url" => $url,
        "tweet" => $data["tweet"],
        "card_url" => $card_url,
        "card" => $card
    );
}