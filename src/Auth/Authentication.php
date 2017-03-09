<?php

namespace APILIB\Auth;

class Authentication {

	private static $current_user;

	public static function attempt($request, $User, $settings){

		$api_key=$request->getHeaderLine($settings['auth_header']['api_key_name']);



		if (strpos($api_key, 'Bearer ') !== false) {
			$api_key=str_replace('Bearer ','',$api_key);
			self::$current_user=$User->where('api_key', $api_key)->first();
			return self::$current_user;
		}

		return null;
	}

	public static function getUser(){
		return self::$current_user;
	}




}
