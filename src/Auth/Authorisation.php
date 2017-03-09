<?php

namespace App\Auth;
use \App\Auth\Authentication;

class Authorisation {

	protected static $user;
	protected static $client;

	public static function attempt($request, $Clients, $UsersClients, $UsersClientsRoles, $settings){

		self::$user=Authentication::getUser();

		//$user_clients=$this->uc_model->get(['user_id'=>$this->user['id']]);
		$user_clients=$UsersClients->where('user_id', self::$user['id'])->with('client')->get();
		$client_id=$request->getHeaderLine($settings['auth_header']['client_id']);

		//Get the client_id
		//If no send use the first available
		if(empty($client_id)){
			if(!empty($user_clients)){
				$client_id=$user_clients[0]['client_id'];
			}
		}

		//Check if the user has permission to the client_id we have
		$client_access=false;

		//Loop all user_clients (that are clients for witch he has permission)
		foreach ($user_clients as $client){
			//If any of them has the client_id we are using he has permission
			if($client['client_id']==$client_id){
				self::$client=$Clients->find($client_id);
				$client_access=true;
			}
		}

		if(!$client_access){
			return false;
		}

		$user_grants=self::getUserGrants(self::$user['id'], self::$client['id'], $UsersClientsRoles);
		$current_grant=self::getCurrentGrant($request);

		//Admin has all rights
		if(self::$user['admin']){
			return true;
		}

		//If the current grant is set to '' the user has rights to it
		if($current_grant==='' | $current_grant==='me'){
			return true;
		}

		//If current grant is defined and
		//that grant is in the user grants array
		//the user has access to the action
		if($current_grant!=NULL && in_array($current_grant,$user_grants)){
			return true;
		}





		return false;
	}

	public static function getUser(){

		return self::$user;
	}

	public static function getClient(){

		return self::$client;
	}

	public static function getCurrentGrant($request){

		$route = $request->getAttribute('route');
		return $route->getName();

	}

	public static function getUserGrants($user_id, $client_id, $UsersClientsRoles){

		$user_grants_string="";

		$roles=$UsersClientsRoles->where('user_id', $user_id)
							->where('client_id', $client_id)
							->with('role')
							->get();

		foreach($roles as $role){
			$user_grants_string.=$role['role']['grants'];
		}

		$user_grants=array_map('trim', explode(',', $user_grants_string));
		return $user_grants;

	}


	public static function getUserClients($user_id, $User, $UsersClientsRoles){

		$user=$User->find($user_id);
		$clients=$user->clients->toArray();

		foreach ($clients as $key=>$cilent) {
			$clients[$key]['grants']=self::getUserGrants($user_id, $cilent['id'], $UsersClientsRoles);
		}

		return $clients;
	}



	public static function getAllGrants($router){

		$routes=[];

		foreach($this->router->getRoutes() as $routeIndex=>$route){

			$routes[$route->getName()]=$route->getName();
		}

		return $routes;

	}

}
