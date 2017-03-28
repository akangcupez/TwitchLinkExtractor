<?php

/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 28/3/2017 08:29
 */

require_once("CZLibs/vendor/autoload.php");

use CZ\CZTwitch\URLExtractor;
use CZ\CZTwitch\Utils;

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