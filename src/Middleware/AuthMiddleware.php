<?php
namespace APILIB\Middleware;

use APILIB\Auth\Authentication;
use APILIB\Auth\Authorisation;

class AuthMiddleware {
	protected $User;
	protected $Client;
	protected $UserClients;
	protected $UserClientsRoles;
	protected $settings;
	protected $error_helper;

	public function __construct($User, $Client, $UserClients, $UserClientsRoles, $settings, $error_helper){
		$this->User=$User;
		$this->Client=$Client;
		$this->UserClients=$UserClients;
		$this->UserClientsRoles=$UserClientsRoles;
		$this->settings=$settings;
		$this->error_helper=$error_helper;
	}

	public function __invoke($request, $response, $next){

		if(!Authentication::attempt($request, $this->User, $this->settings)){
			return $this->error_helper->noValidAPIKey($response);
		}

		if(!Authorisation::attempt($request, $this->Client, $this->UserClients, $this->UserClientsRoles, $this->settings)){
			return $this->error_helper->authorisationFailed($response);
		}

		$request=$request->withAttribute('client',Authorisation::getClient());
		$request=$request->withAttribute('client_id',Authorisation::getClient()['id']);
		$request=$request->withAttribute('user',Authorisation::getUser());
		$request=$request->withAttribute('user_id',Authorisation::getUser()['id']);
		return $next($request, $response);
	}

}
