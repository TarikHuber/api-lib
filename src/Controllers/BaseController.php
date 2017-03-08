<?php

namespace APILIB\Controllers;

use Respect\Validation\Validator as v;
use APILIB\Helpers\ErrorHelper as eh;
use APILIB\Validation\Validator as validator;

class Base_Controller{

	protected $validator;
	protected $error_helper;
	protected $Model;
	protected $id='';
	protected $name_p='';
	protected $name_s='';
	protected $createWithClientID=false;

	public function __construct($Model){

		$this->validator=new validator();
		$this->error_helper=new eh();
		$this->Model=new $Model();
	}

	/**
	 * Gets the validation rules for the create and/or update method
	 * @method getValidation
	 * @param  [int]        $id [ID of the row if we use the validation for update]
	 * @return [type]            [Array of validation rules that has to pass for creation and/or update]
	 */
	function getValidation($id){
		return [];
	}

	/**
	 * Gets an array of column names for witch to calculate the total values
	 * @method getTotals
	 * @return [type]    [Aray of column names]
	 */
	function getTotals(){
		return [];
	}

	/**
	 * Gets a new query with subdata we want for get method
	 * @method getChildData
	 * @param  [Object]       $query [Eloquent query object]
	 * @return [Object]              [Eloquent query object]
	 */
	function getChildData($query){
		return $query;
	}

	/**
	 * Gets a new query with subdata we want for find method
	 * @method getChildData
	 * @param  [Object]       $query [Eloquent query object]
	 * @return [Object]              [Eloquent query object]
	 */
	function findChildData($query){
		return $query;
	}

	/**
	 * Gets the parameters for create from the request
	 * @method getParams
	 * @param  [Object]    $request [Slim request object]
	 * @return [Array]             [Array of parameters]
	 */
	function getCreateParams($request){
		return $request->getParams();
	}

	/**
	 * Gets the parameters for update from the request
	 * @method getParams
	 * @param  [Object]    $request [Slim request object]
	 * @return [Array]             [Array of parameters]
	 */
	function getUpdateParams($request, $response, $args){
		return $request->getParams();
	}

	/**
	 * Called after the data for find call is found
	 * @method postFindCall
	 * @param  [Array]       $data [Data of the find call]
	 * @return [Array]             [Modified data]
	 */
	function postFindCall($data){
		return $data;
	}


	/**
	 * Creates a new element in the model according to the request
	 * @method create
	 * @param  [Object] $request  [Slim request object]
	 * @param  [Object] $response [Slim response object]
	 * @param  [Array] $args     [Arguments array]
	 * @return [Object]           [Slim response object]
	 */
	public function create($request, $response, $args) {

		$params=$this->getCreateParams($request);

		if($this->createWithClientID){
			$params['client_id']=$request->getAttribute('client_id');
		}

		$validation= $this->validator->validateParams($params,$this->getValidation(0));

		if($validation->failed()){
			return $this->error_helper->validationFailed($response,$validation->getErrors());
		}

		if(!$created_id = $this->Model->insertGetId($params)){
			return $this->error_helper->createFailed($response,'');
		}

		$data['error']=false;
		$data[$this->name_s]=$this->Model->find($created_id);
		$response=$response->withAddedHeader('insert_id',$created_id);
		return  $response->withJson($data);

	}

	public function update($request, $response, $args) {

		$params=$this->getUpdateParams($request, $response, $args);

		$validation= $this->validator->validateParams($params,$this->getValidation($args[$this->id]));

		if($validation->failed()){
			return $this->error_helper->validationFailed($response,$validation->getErrors());
		}

		if(!$this->Model->where('id', $args[$this->id])->update($params)){
			return $this->error_helper->updateFailed($response, $this->Model);
		}

		$data['error']=false;
		$data[$this->name_s]=$this->Model->find($args[$this->id]);
		return  $response->withJson($data);

	}

	public function find($request, $response, $args) {

		$data['error'] = false;
		$data[$this->name_s] = $this->postFindCall($this->findChildData($this->Model)->find($args[$this->id]));
		return  $response->withJson($data);

	}

	public function get($request, $response, $args) {

		$this->Model->setQueryData($request->getQueryParams());

		$data['error'] = false;
		$data['totals'] = $this->Model->totals($this->getTotals());
		$data[$this->name_p] = $this->getChildData($this->Model->page())->get();
		return  $response->withJson($data);

	}

	public function delete($request, $response, $args) {

		$targetIDs=[];

		if($request->getParam('id')){
			$targetIDs=$request->getParam('id');
		}else{
			$targetIDs=[(int)$args[$this->id]];
		}

		$targets=$this->Model->whereIn('id', $targetIDs);

		if($targets->count()){
			$data['error']=!$targets->delete();
			return  $response->withJson($data);
		}else{
			return $this->error_helper->deleteFailed($response);
		}

	}




}
