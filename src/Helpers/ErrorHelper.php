<?php

namespace APILIB\Helpers;
use APILIB\Localisation\Locale as l;

class ErrorHelper {

	protected $invoked=false;

	public function setInvoked($invoked){
		$this->invoked=true;
	}

	public function getInvoked(){
		return $this->invoked;
	}

	public function noValidAPIKey($response){

		$data['error'] = true;
		$data['error_id'] = 20102;
		$data['error_message'] = l::getMessage('invalid_api_key_message');
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function authorisationFailed($response){

		$data['error'] = true;
		$data['error_id'] = 20103;
		$data['error_message'] = l::getMessage('authorisation_failed_message');
		$this->setInvoked(true);
		return  $response->withJson($data);

	}

	public function signupFailed($response){

		$errors['password']=l::getMessage('signup_failed_message');


		$data['error'] = true;
		$data['error_id'] = 20201;
		$data['error_message'] = l::getMessage('signup_failed_message');
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function signinFailedEmailNotRegistered($response){

		$error_id=20110;

		$errors['email']=l::getMessage('email_not_registered_message');

		$data['error'] = true;
		$data['error_id'] = $error_id;
		$data['error_messages'] = l::getMessage('email_not_registered_message');
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function signinFailedUserNotActive($response){

		$error_id=20120;

		$errors['email']=l::getMessage('user_not_activated_message');

		$data['error'] = true;
		$data['error_id'] = $error_id;
		$data['error_message'] = l::getMessage('user_not_activated_message');
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function signinFailedPasswordNotMatching($response){

		$error_id=20130;

		$errors['password']=l::getMessage('invalid_password_message');

		$data['error'] = true;
		$data['error_id'] = $error_id;
		$data['error_message'] = l::getMessage('invalid_password_message');
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function validationFailed($response,$validationErrors){

		$data['error'] = true;
		$data['error_id'] = 20301;
		$data['error_message'] = l::getMessage('validation_failed_message');
		$data['error_details'] = $validationErrors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function createFailed($response,$details=''){

		$data['error'] = true;
		$data['error_id'] = 20401;
		$data['error_message'] = l::getMessage('creation_failed_message');
		$data['error_details'] = $details;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function updateFailed($response,$details=''){

		$data['error'] = true;
		$data['error_id'] = 20402;
		$data['error_message'] = l::getMessage('updating_failed_message');
		$data['error_details'] = $details;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function deleteFailed($response,$details=''){

		$data['error'] = true;
		$data['error_id'] = 20403;
		$data['error_message'] = l::getMessage('deletion_failed_message');
		$data['error_details'] = $details;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

}
