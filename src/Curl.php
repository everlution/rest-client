<?php

namespace Everlution\RestClient;

use Symfony\Component\HttpFoundation\Response;

class Curl
{
    const TIMEOUT = 30;

    private $ch;

    public function __construct()
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * get.
     *
     * @param string $url
     * @param array $params
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($url, array $queryParams = array())
    {
        $uri = $this->buildUri($url, $queryParams);

        curl_setopt($this->ch, CURLOPT_URL, $uri);

        return $this->execCurl();
    }

    /**
     * post.
     *
     * @param string $url
     * @param array $params
     * @param array $queryParams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post($url, array $params = array(), array $queryParams = array())
    {
        $uri = $this->buildUri($url, $queryParams);

        curl_setopt($this->ch, CURLOPT_URL, $uri);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($params));

        return $this->execCurl();
    }

    /**
     * put.
     *
     * @param string $url
     * @param array $params
     * @param array $queryParams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function put($url, array $params = array(), array $queryParams = array())
    {
        $uri = $this->buildUri($url, $queryParams);

        $data = http_build_query($params);
        $putData = tmpfile();
        fwrite($putData, $data);
        fseek($putData, 0);

        curl_setopt($this->ch, CURLOPT_URL, $uri);
        curl_setopt($this->ch, CURLOPT_PUT, true);
        curl_setopt($this->ch, CURLOPT_INFILE, $putData);
        curl_setopt($this->ch, CURLOPT_INFILESIZE, strlen($data));

        return $this->execCurl();
    }

    /**
     * delete.
     *
     * @param string $url
     * @param array $queryParams
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($url, array $queryParams = array())
    {
        $uri = $this->buildUri($url, $queryParams);

        curl_setopt($this->ch, CURLOPT_URL, $uri);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->execCurl();
    }

    private function buildUri($url, array $queryParams = array())
    {
        return sprintf(
            '%s%s',
            $url,
            count($queryParams) ? '?' . http_build_query($queryParams) : ''
        );
    }

    private function execCurl()
    {
        $content = curl_exec($this->ch);

        if(curl_errno($this->ch)) {
            dump(curl_getinfo($this->ch));
            $error = sprintf(curl_error($this->ch));
            throw new \Exception($error);
        }

        $response = new Response(
            $content,
            curl_getinfo($this->ch, CURLINFO_HTTP_CODE)
        );

        $response->headers->set('Content-Type', curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE));

        return $response;
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }
}
