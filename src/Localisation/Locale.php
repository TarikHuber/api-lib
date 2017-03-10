<?php

namespace APILIB\Localisation;

use StringTemplate;

class Locale {

  protected static $prefix='{{';
    protected static $suffix='}}';
    protected static $locales=[];
    protected static $locale='en';


    public static function setLocales($locales){
      self::$locales=$locales;
    }

    public static function addLocales($locales){
      self::$locales=array_merge(self::$locales, $locales);
    }

    public static function addLocaleMessages($locale, $messages){

      if(array_key_exists($locale, self::$locales)){
        self::$locales[$locale]=array_merge(self::$locales[$locale], $messages);
      }else{
        self::$locales[$locale]=$messages;
      }

    }

    public static function setLocale($locale){
      self::$locale=$locale;
    }
    public static function setPrefix($prefix){
      self::$prefix=$prefix;
    }

    public static function setSuffix($suffix){
      self::$suffix=$suffix;
    }

    public static function getMessage($key, $args=[]){

      if(array_key_exists(self::$locale, self::$locales)){

        $messages=self::$locales[self::$locale];

        if(array_key_exists($key, $messages)){

          $engine = new StringTemplate\Engine(self::$prefix, self::$suffix);

          return $engine->render($messages[$key],$args);

        }else{
          return $key;
        }

      }else{
        return $key;
      }



    }

  }
