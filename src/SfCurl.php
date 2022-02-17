<?php
namespace Stockfeel\Monitor_PHP;
use Stockfeel\Monitor_PHP\SfApiMonitor;

class SfCurl extends SfApiMonitor {

  public function __construct($url, $key) {
      parent::__construct($url, $key);
  }

  public function useCurl() {
      // [0]: url, [1]: postData
      $args = func_get_args();
      $ch = curl_init($args[0]);
      $options = isset($args[1]) ? $args[1] : array();
      if (!empty($options[CURLOPT_POST]) && $options[CURLOPT_POST]) {
          $data = $this->parseParameters($options);

          $this->genPostFieldsFromPost($args[0], $data)
              ->fireAsyncCurl();
      } else {
          $this->genPostFieldsFromGet($args[0])
              ->fireAsyncCurl();
      }

      curl_setopt_array($ch, $options);
      $result = curl_exec($ch);

      $this->setStatus($result)
          ->genPostFieldsAtEnd()
          ->fireAsyncCurl();

      curl_close($ch);
      unset($ch);
      return $result;
  }

  public function parseParameters ($params) {
      if (isset($params[CURLOPT_POSTFIELDS])) {
          if (is_string($params[CURLOPT_POSTFIELDS])) {
              if (strpos($params[CURLOPT_POSTFIELDS], '=')) {
                  parse_str($params[CURLOPT_POSTFIELDS], $tmp);
                  return $tmp;
              } else {
                  return json_decode($params[CURLOPT_POSTFIELDS], true);
              }
          } else {
              return $params[CURLOPT_POSTFIELDS];
          }
      }
      return array();
  }

  function setStatus($result) {
      $this->status = $result == false ? 'failure' : 'success';
      return $this;
  }
}

?>