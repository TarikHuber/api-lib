<?php

namespace APILIB\Controllers;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use APILIB\Auth\Authorisation;
use APILIB\Helpers\EmailHelper;
use APILIB\Localisation\Locale as l;
use APILIB\Localisation\de;
use APILIB\Localisation\en;

class Authentication_Controller{

	protected $User;
	protected $UsersClients;
	protected $UsersClientsRoles;
	protected $validator;
	protected $error_helper;
	protected $settings;

	public function __construct($User, $UsersClients, $UsersClientsRoles,  $validator, $error_helper, $settings){

		$this->User=$User;
		$this->UsersClients=$UsersClients;
		$this->UsersClientsRoles=$UsersClientsRoles;
		$this->validator=$validator;
		$this->error_helper=$error_helper;
		$this->settings=$settings;
	}


	public function signUp($request, $response, $args) {

		l::addLocaleMessages('en',en::getMessages());
		l::addLocaleMessages('de',de::getMessages());

		$locale=$request->getHeaderLine('locale');

		if(strlen($locale)>0){
			l::setLocale($locale);
		}


		$validation= $this->validator->validateParams($request->getParams(),[
			'name'=>v::notEmpty(),
			'email'=>v::noWhitespace()->notEmpty()->email()->emailAvailable($this->User),
			'password'=>v::noWhitespace()->notEmpty(),
			'confirm_password'=>v::noWhitespace()->notEmpty()->confirmPassword($request->getParam('password'))
		]);

		if($validation->failed()){
			return $this->error_helper->validationFailed($response,$validation->getErrors());
		}

		$params['name']=$request->getParam('name');
		$params['email']=$request->getParam('email');
		$params['phone']=$request->getParam('phone');
		$params['fax']=$request->getParam('fax');
		$params['password_hash']=password_hash($request->getParam('password'),PASSWORD_DEFAULT);
		$params['api_key']=md5(uniqid(rand(), true));

		if(!$created_id = $this->User->insertGetId($params)){
			return $this->error_helper->signupFailed($response);
		}

		$this->notifyAdmins($params);

		$data['error']=false;
		return  $response->withJson($data);
	}

	public function signIn($request, $response, $args) {

		l::addLocaleMessages('en',en::getMessages());
		l::addLocaleMessages('de',de::getMessages());

		$locale=$request->getHeaderLine('locale');

		if(strlen($locale)>0){
			l::setLocale($locale);
		}

		$user=$this->User->where('email', $request->getParam('email'))->first();

		if(!$user){
			return $this->error_helper->signinFailedEmailNotRegistered($response);
		}

		if($user['active']===0){
			return $this->error_helper->signinFailedUserNotActive($response);
		}

		if(!password_verify($request->getParam('password'),$user['password_hash'])){
			return $this->error_helper->signinFailedPasswordNotMatching($response);
		}

		$params['user_id']=$user['id'];

		$clients=$user->clients->toArray();
		foreach ($clients as $key=>$client) {
			$clients[$key]['grants']=Authorisation::getUserGrants($user['id'], $client['id'], $this->UsersClientsRoles);
		}

		$data['error'] = false;
		$data['user'] = [
			'name'=>$user['name'],
			'admin'=>$user['admin'],
			'email'=>$user['email'] ,
			'api_key'=>$user['api_key'],
			'clients'=>$clients,
			'isSMTP'=>null,
		];
		return  $response->withJson($data);


	}

	public function notifyAdmins($params){

		$response_email=$params['email'];
		$response_name=$params['name'];
		$subject = l::getMessage('new_user_registered', ['name'=>$params['name']]);
		$message = l::getMessage('user_name', ['name'=>$params['name']]).'</br>';
		$message .= l::getMessage('user_email', ['email'=>$params['email']]).'</br>';

		$admins=$this->User->where('admin','=', true)->where('active','=', true)->get()->toArray();

		$mailer=EmailHelper::getMailer($this->settings['phpmailer']);
		$mailer->isSMTP();
		$mailer->setFrom($response_email, $response_name);
		$mailer->addReplyTo($response_email, $response_name);
		$mailer->isHTML(true);

		foreach ($admins as $key => $admin) {
			$mailer->addAddress($admin['email'], $admin['name']);
		}

		$mailer->Subject = $subject;
		$mailer->Body    = $message;
		$mailer->AltBody = $message;

		if(!$mailer->send()) {
			//echo 'Message could not be sent.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			//echo 'Message has been sent';
		}

	}

}
