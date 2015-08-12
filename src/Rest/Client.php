<?php

namespace Everlution\RestClient;

class Client
{
    private $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function get($resource, array $queryParams = array())
    {
        $url = $this->buildUrl($resource);

        $curl = new Curl();

        return $curl->get($url, $queryParams);
    }

    public function post($resource, array $params = array())
    {
        $url = $this->buildUrl($resource);

        $curl = new Curl();

        return $curl->post($url, $params);
    }

    public function put($resource, array $params = array())
    {
        $url = $this->buildUrl($resource);

        $curl = new Curl();

        return $curl->put($url, $params);
    }

    public function delete($resource, array $queryParams = array())
    {
        $url = $this->buildUrl($resource);

        $curl = new Curl();

        return $curl->delete($url, $queryParams);
    }

    private function buildUrl($resource)
    {
        return sprintf('%s%s', $this->baseUrl, $resource);
    }
}
