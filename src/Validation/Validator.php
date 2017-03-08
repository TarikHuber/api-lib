<?php

namespace APILIB\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;


class Validator{


	protected $errors;

	public function validateRequest($request, $rules){

		foreach($rules as $field=> $rule){
			try{
				$rule->setName(ucfirst($field))->assert($request->getParam($field));
			}catch(NestedValidationException $e){
				$this->errors[$field]=$e->getMessages();
			}

		}

		return $this;

	}

	public function validateParams($params, $rules){

		foreach($rules as $field=> $rule){
			try{

				if(!array_key_exists($field,$params)){
					$params[$field]=NULL;
				}

				$rule->setName(ucfirst($field))->assert($params[$field]);
			}catch(NestedValidationException $e){

				$this->errors[$field]=$e->getMessages();
			}

		}

		return $this;

	}

	public function failed(){

		return !empty($this->errors);
	}

	public function getErrors(){

		return $this->errors;
	}


}
