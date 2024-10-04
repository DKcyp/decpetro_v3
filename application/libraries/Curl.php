<?php 
  defined('BASEPATH') OR exit('No direct script access allowed');
   
  class Curl{ 
    function get($url, $param = null) {
      if ($param == null) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $buffer = curl_exec($curl_handle);

        return $buffer;
      } else {
        $params = '?';
        foreach ($param as $key => $value){
          $params .= $key.'='.$value.'&';
        }

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url.$params);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        $buffer = curl_exec($curl_handle);
        
        return $buffer;
      }
    }
 
    function post($url, $array) {
      $curl_handle = curl_init();
      curl_setopt($curl_handle, CURLOPT_URL, $url);
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl_handle, CURLOPT_POST, 1);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $array);
      
      $buffer = curl_exec($curl_handle);
      return $buffer;
    }
 
    function put($url, $array) {
      $curl_handle = curl_init();
      curl_setopt($curl_handle, CURLOPT_URL, $url);
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($array));
      curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "PUT");
      $buffer = curl_exec($curl_handle);

      return $buffer;
    }
 
    function delete($url, $array) {
      $curl_handle = curl_init();
      curl_setopt($curl_handle, CURLOPT_URL, $url);
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($array));
      curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "DELETE");
      $buffer = curl_exec($curl_handle);

      return $buffer;
    }
  }