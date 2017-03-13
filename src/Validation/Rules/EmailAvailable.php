<?php


namespace APILIB\Validation\Rules;


use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule
{

	private $User;
	private $id;

	public function __construct($User, $id=0)
	{
		$this->User = $User;
		$this->id = $id;
	}

	public function validate($input)
	{

		return !$this->User->where(['email'=>$input])->where('id', '!=', $this->id)->withTrashed()->count();

	}
}
