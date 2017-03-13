<?php

namespace APILIB\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;


class ConfirmPasswordException extends ValidationException
{


	public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} is not matching with the password',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} must not match with the password',
        ]
    ];



}
