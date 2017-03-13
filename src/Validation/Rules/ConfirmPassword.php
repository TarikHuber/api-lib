<?php
namespace APILIB\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;
use Respect\Validation\Validator as v;

class ConfirmPassword extends AbstractRule
{

	public $compareTo;
    public function __construct($compareTo)
    {
        $this->compareTo = $compareTo;
    }

	public function validate($input)
	{

		 return $input == $this->compareTo;

	}

}
