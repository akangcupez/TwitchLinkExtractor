<?php namespace CZ\CZTwitch;

/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 28/3/2017 07:55
 */

use CZ\Core\Request;

class URLExtractor extends Request
{
    const X_CHANNEL_STREAM  = 101;
    const X_RECORDED_VOD    = 102;

    private $configKeys     = array('clientId', 'oauthToken');

    private $params = array
    (
        self::X_RECORDED_VOD    => array(
            'allow_source'      => "true",
            'allow_spectre'     => "true",
            "player_backend"    => "html5",
            "baking_bread"      => "false"
        ),
        self::X_CHANNEL_STREAM  => array(
            'player'            => "twitchweb",
            'allow_audio_only'  => "true",
            'allow_source'      => "true",
            'type'              => "any"
        )
    );

    /**
     * URLExtractor constructor
     *
     * @param array|null $config default: NULL
     */
    function __construct(array $config = null)
    {
        parent::__construct($config, $this->configKeys);
    }

    /**
     * This method will be useful for usage in framework such as Codeigniter
     * <p>Use getConfigKeys method to see valid keys accepted by this library</p>
     *
     * @param array|null $config default: NULL
     */
    protected function initialize(array $config = null)
    {
        parent::__construct($config, $this->configKeys);
    }

    /**
     * Get acceptable config keys as used in this library
     *
     * @return array
     */
    public function getConfigKeys()
    {
        return $this->configKeys;
    }

    /**
     * Get specific config based on key string
     *
     * @param String $key
     *
     * @return mixed
     */
    public function getConfig($key)
    {
        return $this->config[$key];
    }

    /**
     * Get Client ID as defined in config
     *
     * @return String|null
     */
    public function getClientId()
    {
        return (isset($this->config['clientId'])) ? $this->config['clientId'] : null;
    }

    /**
     * Get initial Twitch API token request
     *
     *
     * @param $url
     *
     * @return mixed|string
     */
    private function getToken($url)
    {
        if ($this->isValidValue($url))
        {
            $this->response = $this->execute($url);

            return $this->getResponse(Request::RESULT_OBJECT);
        }

        return null;
    }

    /**
     * Clean-up videoId input string, so it will work whether using 'v' prefix or not
     *
     * @param $video_id String
     *
     * @return string
     */
    private function fixVideoId($video_id)
    {
        $s = strtolower(substr($video_id, 0, 1));
        if ($s === "v") {
            return substr($video_id, 1, strlen($video_id) - 1);
        }

        return $video_id;
    }

    public function extractVideoUrl($video_id)
    {
        $video_id = $this->fixVideoId($video_id);
        $url = "https://api.twitch.tv/api/vods/{$video_id}/access_token?client_id=";
        $url .= $this->config['clientId'];

        return $this->extract($video_id, $this->getToken($url), self::X_RECORDED_VOD);
    }

    public function extractChannelUrl($channel, $checkOnlineStatus = false)
    {
        if ($checkOnlineStatus) {
            $is_channel_online = $this->isChannelOnline($channel);
            if ($is_channel_online)
            {
                return $this->_extractChannelUrl($channel);
            }
            else {
                http_response_code(204);
                exit;
            }
        }

        return $this->_extractChannelUrl($channel);
    }

    public function isChannelOnline($channel)
    {
        $url = "https://api.twitch.tv/kraken/streams/{$channel}?client_id=";
        $url .= $this->config['clientId'];

        $this->response = $this->execute($url);

        $res = $this->getResponse(Request::RESULT_ARRAY);
        if ($this->isValidArray($res)) {
            return (isset($res['stream']) && !is_null($res['stream']));
        }

        return false;
    }

    private function _extractChannelUrl($channel)
    {
        $url = "http://api.twitch.tv/api/channels/{$channel}/access_token?client_id=";
        $url .= $this->config['clientId'];
        $url .= "&adblock=true&player_type=popout&platform=web&need_https=true&hide_ads=true";
        if (isset($this->config['oauthToken']) && $this->isValidValue($this->config['oauthToken'])) {
            $url .= "&oauth_token=" . $this->config['oauthToken'];
        }

        return $this->extract($channel, $this->getToken($url), self::X_CHANNEL_STREAM);
    }

    private function extract($id, $response, $mode)
    {
        $token_sig = json_decode($response, true);
        $token = strval($token_sig['token']);
        $sig = $token_sig['sig'];

        $host = "https://usher.ttvnw.net";
        $params = $this->params[$mode];

        if ($mode === self::X_RECORDED_VOD)
        {
            $host .= "/vod/{$id}.m3u8";
            $params['nauth'] = $token;
            $params['nauthsig'] = $sig;

        } elseif ($mode === self::X_CHANNEL_STREAM) {

            $host .= "/api/channel/hls/{$id}.m3u8";
            $params['token'] = $token;
            $params['sig'] = $sig;
        }

        $params['p'] = strval(time());
        $url = $this->buildUrl($host, null, $params);

        $this->response = $this->execute($url);

        return $this->getResponse(self::RESULT_OBJECT);
    }

}