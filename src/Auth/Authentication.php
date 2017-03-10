<?php

namespace APILIB\Auth;
use APILIB\Localisation\Locale as l;

class Authentication {

	private static $current_user;

	public static function attempt($request, $User, $settings){

		$api_key=$request->getHeaderLine($settings['auth_header']['api_key_name']);
		$locale=$request->getHeaderLine('locale');

		if (strpos($api_key, 'Bearer ') !== false) {
			$api_key=str_replace('Bearer ','',$api_key);
			self::$current_user=$User->where('api_key', $api_key)->first();

			if(strlen($locale)>0){
				l::setLocale($locale);
			}

			return self::$current_user;
		}

		return null;
	}

	public static function getUser(){
		return self::$current_user;
	}




}
