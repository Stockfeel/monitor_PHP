<?php
namespace Stockfeel\Monitor_PHP;
use Stockfeel\Monitor_PHP\SfApiMonitor;

class SfFileGetContents extends SfApiMonitor {

  public function __construct($url, $key) {
      parent::__construct($url, $key);
  }

  public function useFileGetContents() {
      // [0]: url, [1]: postData
      $args = func_get_args();
      if (isset($args[1])) {
          $this->genPostFieldsFromPost($args[0], $args[1])
              ->fireAsyncCurl();
          
          $context = stream_context_create(array(
              'http' => array(
                  'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                  'method'  => 'POST',
                  'content' => http_build_query($args[1])
              )
          ));
          $result = file_get_contents($args[0], false, $context);
      } else {
          $this->genPostFieldsFromGet($args[0])
              ->fireAsyncCurl();
          
          $result = file_get_contents($args[0]);
      }
      $this->setStatus($result)
          ->genPostFieldsAtEnd()
          ->fireAsyncCurl();
      return $result;
  }

  function setStatus($result) {
      $this->status = $result == false ? 'failure' : 'success';
      return $this;
  }
}

?>