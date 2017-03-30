<?php

/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 28/3/2017 08:29
 */

//TODO: fix vendor path when generate vendor folder from composer
require_once("vendor/autoload.php");

use AjiSubastian\CZLibs\TwitchTV\URLExtractor;
use AjiSubastian\CZLibs\TwitchTV\Utils;

class CZ_TwitchExtractor
{
    public $url_extractor;
    public $util;

    function __construct(array $config = null)
    {
        $this->url_extractor = new URLExtractor($config);
        $this->util = new Utils();
    }

}