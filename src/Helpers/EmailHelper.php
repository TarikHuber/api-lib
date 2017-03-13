<?php

namespace APILIB\Helpers;
use \PHPMailer;

class EmailHelper {

  public static function getMailer($params=[]){

    $mailer = new PHPMailer;

    foreach ($params as $key => $param) {
      if(property_exists($mailer, $key)){
        $mailer->{$key}=$param;
      }
    }

    return $mailer;

  }

}
