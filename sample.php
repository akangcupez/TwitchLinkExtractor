<?php
/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 28/3/2017 08:51
 */

/**
 * The sample is using CZ_TwitchExtractor.php and sample.php for usage
 * within framework which does not support namespacing. While for framework
 * supports namespacing, it would be simpler as follow:
 *
 * $twitch = new URLExtractor($config);
 * $result = $twitch->extractVideoUrl(VIDEO_ID);
 *
 * print_r($result);
 *
 */
require_once("CZ_TwitchExtractor.php");

//This Client ID was taken from Twitch Player
//TODO: Change this Client ID with your own client_id and access_token
$config = array(
    'clientId'      => "jhhewj60hco30on1fyaf5ud79phm0t",
    'oauthToken'    => "x32y2siwca3esjlb8r37elru122b67"
);

$twitch = new CZ_TwitchExtractor($config);
$url_extractor = $twitch->url_extractor;

//$result = $url_extractor->extractVideoUrl("v130613442");
$result = $url_extractor->extractChannelUrl("2ggaming", true);
$twitch->util->getFileObject($result, "v130613442");