<?php namespace CZ\Core;

/*
 * Created by   : Aji Subastian (aKanG cuPez)
 * Mobile Phone : +62 812 888 33996
 * Email        : akangcupez@gmail.com
 * Website      : http://akangcupez.com
 * Date         : 28/3/2017 07:58
 */

use Exception;
use HttpException;
use HttpRequest;

class Request
{
    const RESULT_OBJECT         = 100; //return JSON Object result (std_object)
    const RESULT_STRING         = 101; //return JSON String result
    const RESULT_ARRAY          = 102; //return PHP Associative Arrays

    const GET                   = "GET";
    protected $requestHeader    = null;

    protected $config;
    protected $response;
    protected $errorStack;

    function __construct(array $config = null, array $configKeys = null) {
        if ($this->isValidArray($config) && $this->isValidArray($configKeys)) {
            foreach ($config as $k => $v) {
                if (in_array($k, $configKeys)) {
                    $this->config[$k] = $v;
                }
            }
        }
    }

    protected function setRequestHeader(array $requestHeaders = null) {
        $headers = array(
            'Cache-Control' => 'no-cache',
            'Accept'        => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        );

        if ($this->isValidArray($requestHeaders)) {
            foreach ($requestHeaders as $k => $v) {
                $headers[$k] = $v;
            }
        }

        $this->addRequestHeader($headers);
    }

    /**
     * @param array $requestHeaders an associative array consists of request header set
     */
    protected function addRequestHeader(array $requestHeaders) {
        if ($this->isValidArray($requestHeaders)) {
            foreach ($requestHeaders as $k => $v) {
                $this->requestHeader[] = "{$k}: {$v}";
            }
        }
    }

    /**
     * Check if response has success result
     *
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->isValidArray($this->response) && !isset($this->errorStack['error']));
    }

    /**
     * All requests will use GET method and doesn't required request body params
     *
     * @param string $url
     *
     * @return mixed|null|string
     */
    protected function execute($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::GET);
        if ($this->isValidArray($this->requestHeader)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->requestHeader);
        }

        $res = null;
        $err = null;
        try {
            $res = curl_exec($curl);
            $err = curl_error($curl);
            if (!empty($err)) $this->errorStack[] = $err;
        } catch (Exception $e) {
            $err = $e->getMessage();
            $this->errorStack[] = $err;
        } finally {
            curl_close($curl);
            return ($this->validateResponse($res, $err)) ? $res : $this->getError();
        }
    }

    /**
     * @param $host
     * @param string|null $endPoint NULL
     * @param array|string|null $params NULL
     *
     * @return string
     */
    protected function buildUrl($host, $endPoint = null, $params = null) {
        $url  = $host;
        if ($this->isValidValue($endPoint)) $url .= "/{$endPoint}";
        if ($this->isValidArray($params)) {
            $url .= "?" . $this->buildQuery($params);
        }
        elseif ($this->isValidValue($params) && is_string($params)) {
            if (substr($params, 0, 1) != "?") $url .= "?";//Append question symbol when necessary
            $url .= $params;
        }

        return $url;
    }

    /**
     * Build http query string
     *
     * @param array|null $params
     *
     * @return null|string
     */
    protected function buildQuery(array $params = null)
    {
        if ($this->isValidArray($params)) {
            return http_build_query($params, '', '&');
        }
        return null;
    }

    /**
     * @param $response object
     * @param $error String list of errors if any
     *
     * @return bool
     */
    private function validateResponse($response, $error)
    {
        return ($this->isValidValue($response) && $response !== "null" && !$this->isValidArray($error));
    }

    /**
     * Check whether given value is not null or empty
     *
     * @param $value
     *
     * @return bool
     */
    protected function isValidValue($value)
    {
        return (!(is_null($value) || empty($value)));
    }

    /**
     * Check whether given value is an array
     *
     * @param $array array
     *
     * @return bool
     */
    protected function isValidArray($array)
    {
        return (!is_null($array) && is_array($array) && count($array) > 0);
    }

    /**
     * Get error string in JSON encoded format
     */
    private function getError()
    {
        return json_encode(array("error" => $this->errorStack));
    }

    /**
     * @param $resultType int
     *
     * @return mixed|string
     */
    protected function getResponse($resultType = self::RESULT_OBJECT)
    {
        if ($resultType == self::RESULT_STRING) {
            return (string)$this->response;
        } elseif ($resultType == self::RESULT_ARRAY) {
            return json_decode($this->response, true);
        } else {
            return $this->response;
        }
    }

}