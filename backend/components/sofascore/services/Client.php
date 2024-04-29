<?php

namespace backend\components\sofascore\services;


use backend\helpers\ClientHelper;
use InvalidArgumentException;
use function sprintf;

class Client
{

    CONST METHOD_GET = 'GET';

    /** Sofascore base url */
    const BASE_URL = "https://api.sofascore.com/api/";

    private $version = "v1";

    /** Tennis url  */
    const TENNIS_URL = "sport/tennis/scheduled-events/";

    /**
     * get tennis events
     * @param null $data
     * @return array|bool|string
     */
    public function getTennis($data = null)
    {
        $data = ($data) ?? date('Y-m-d', time());

        return $this->get(self::TENNIS_URL . $data);
    }

    /**
     * Call GET request
     * @param string $endpoint
     * @param null $options
     * @return bool|string
     */
    private function get(string $endpoint, $options = null) {
        return $this->apiCall(self::METHOD_GET, $endpoint, $options);
    }

    /**
     * Create API query and execute a GET/POST request
     * @param string $method GET
     * @param string $endpoint
     * @param array|null $options
     * @return array|string
     */
    private function apiCall(string $method, string $endpoint, array $options = null)
    {

        /** create URL */
        $apiUrl = self::BASE_URL . $this->version . "/" . $endpoint;

        /** GET method */
         if($method == self::METHOD_GET) {
             $curlOptions[\CURLOPT_HTTPGET] = true;
            if(is_array($options) && count($options) > 0) $apiUrl .= "?" . http_build_query($options);
         } else {
             throw new InvalidArgumentException(
                 sprintf('An HTTP method "%s" is not supported. Use "%s".', $method, self::METHOD_GET)
             );
         }

        /** Prime the channel */
        $curlOptions[CURLOPT_URL] = $apiUrl;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_HTTPHEADER] = $this->getHTTPHeader();

        /** Unless you have all the CA certificates installed in your trusted root authority, this should be left as false. */
        $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;

        $response = $this->exec($curlOptions);

        return empty($response['error']) ? $response['body'] : $response;
    }

    /**
     * @param $options
     * @return array
     */
    private function exec($options): array
    {
        /** Set up a CURL channel */
        $curl = curl_init();
        \curl_setopt_array($curl, $options);

        /** This fetches the initial feed result. Next we will fetch the update using the fdTime value and the last URL parameter */
        $response = curl_exec($curl);
        $error = curl_error($curl);

        $result['http_code'] = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($curl,CURLINFO_EFFECTIVE_URL);
        $result['body'] = $response;
        if($error != "") $result['error'] = $error;

        curl_close($curl);

        return $result;
    }


    /**
     * Generate HTTP header
     * @return array
     */
    private function getHTTPHeader(): array
    {
        $header['Content-type'] = "application/json; charset=utf-8";
        $header['Cache-Control'] = "no-cache";
        $header['User-Agent'] = ClientHelper::getUserAgent();

        /** Build the header */
        $headers = [];
        foreach ($header as $key => $value) {
            $headers[] = "$key: $value";
        }

        return $headers;
    }

}