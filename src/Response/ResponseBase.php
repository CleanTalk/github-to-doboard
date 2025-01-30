<?php

namespace CleantalkHubGitKeeper\Response;

use CleantalkHubGitKeeper\Request\IssueRequest;

abstract class ResponseBase
{
    const CONTENT_TYPE = 'application/x-www-form-urlencoded';

    protected $url;

    protected $data;

    abstract public function __construct();

    abstract public function process(IssueRequest $request);

    public function send($route = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $route);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: ' . static::CONTENT_TYPE,
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ( $error = error_get_last() ) {
            throw new \Exception($error['message'], $http_code);
        }
        return $res;
    }
}
