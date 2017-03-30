<?php namespace AjiSubastian\CZLibs\TwitchTV;

/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 28/3/2017 10:36
 */

class Utils
{

    function __construct()
    {
        //Constructor
    }

    public function getFileObject($s, $id) {

        header("Cache-Control: no-cache,no-store");
        header("Content-Type: application/vnd.apple.mpegurl");
        header("Content-Disposition: attachment;filename='{$id}.m3u8'");

        echo $s;
        exit;
    }

}