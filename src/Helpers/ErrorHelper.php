<?php

namespace APILIB\Helpers;


class ErrorHelper {

	protected $invoked=false;

	protected $errors=[
		20102=>'API Key not valid.',
		20103=>'You have no access to this section.',
		20201=>'SignUp failed!',
		20110=>'Email not registered.',
		20120=>'User not active.',
		20130=>'Passwort not matching.',
		20301=>'Validation failed!',
		20401=>'Create failed!',
		20402=>'Update failed!',
		20403=>'Deletion failed!',
	];


	public function setInvoked($invoked){
		$this->invoked=true;
	}

	public function getInvoked(){
		return $this->invoked;
	}

	public function noValidAPIKey($response){

		$data['error'] = true;
		$data['error_id'] = 20102;
		$data['error_message'] = $this->errors[$data['error_id']];
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function authorisationFailed($response){

		$data['error'] = true;
		$data['error_id'] = 20103;
		$data['error_message'] = $this->errors[$data['error_id']];
		$this->setInvoked(true);
		return  $response->withJson($data);

	}

	public function signupFailed($response){

		$errors['password']=$this->errors[20201];


		$data['error'] = true;
		$data['error_id'] = 20201;
		$data['error_message'] = $this->errors[$data['error_id']];
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function signinFailedEmailNotRegistered($response){

		$error_id=20110;

		$errors['email']=$this->errors[$error_id];

		$data['error'] = true;
		$data['error_id'] = $error_id;
		$data['error_messages'] = $this->errors[$error_id];
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function signinFailedUserNotActive($response){

		$error_id=20120;

		$errors['email']=$this->errors[$error_id];

		$data['error'] = true;
		$data['error_id'] = $error_id;
		$data['error_message'] = $this->errors[$error_id];
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function signinFailedPasswordNotMatching($response){

		$error_id=20130;

		$errors['password']=$this->errors[$error_id];

		$data['error'] = true;
		$data['error_id'] = $error_id;
		$data['error_message'] = $this->errors[$error_id];
		$data['error_details'] = $errors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function validationFailed($response,$validationErrors){

		$data['error'] = true;
		$data['error_id'] = 20301;
		$data['error_message'] = $this->errors[$data['error_id']];
		$data['error_details'] = $validationErrors;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function createFailed($response,$details=''){

		$data['error'] = true;
		$data['error_id'] = 20401;
		$data['error_message'] = $this->errors[$data['error_id']];
		$data['error_details'] = $details;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function updateFailed($response,$details=''){

		$data['error'] = true;
		$data['error_id'] = 20402;
		$data['error_message'] = $this->errors[$data['error_id']];
		$data['error_details'] = $details;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

	public function deleteFailed($response,$details=''){

		$data['error'] = true;
		$data['error_id'] = 20403;
		$data['error_message'] = $this->errors[$data['error_id']];
		$data['error_details'] = $details;
		$this->setInvoked(true);
		return  $response->withJson($data);
	}

}
