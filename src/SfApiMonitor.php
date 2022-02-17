<?php
namespace Stockfeel\Monitor_PHP;

class SfApiMonitor {
    private $monitorUrl;
    private $postFields;
    public $status;

    function __construct($url, $key) {
        $this->monitorUrl = $url;
        $this->postFields = array();
        $this->status = 0;
        $this->key = $key;
    }

    function genPostFieldsFromGet($sourceUrl) { 
        $explodedUrl = explode('?', $sourceUrl);
        $url = $explodedUrl[0];
        $paramStr = empty($explodedUrl[1]) ? '' : $explodedUrl[1];
        parse_str($paramStr, $params);

        $this->postFields = array(
            'projectID'=> $this->key,
            'url'=> $url,
            'params'=> $params,
            'method'=> 'get',
            'batchID'=> $this->getHashTimeStamp(),
            'status'=> 'start'
        );

        return $this;
    }

    function genPostFieldsFromPost($sourceUrl, $data) {
        $this->postFields = array(
            'projectID'=> $this->key,
            'url'=> $sourceUrl,
            'params'=> $data,
            'method'=> 'post',
            'batchID'=> $this->getHashTimeStamp(),
            'status'=> 'start'
        );
        return $this;
    }

    function genPostFieldsAtEnd() {
        $this->postFields['status'] = $this->status;
        return $this;
    }

    function fireAsyncCurl() {
        $this->execAsyncUrl();
    }

    private function execAsyncUrl() {
        $ch = curl_init($this->monitorUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->postFields, null, '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function getHashTimeStamp() {
        return hash('md5', microtime());
    }
}
?>
