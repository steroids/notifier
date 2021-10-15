<?php

namespace steroids\notifier\exceptions;

use Yii;

class InvalidPhoneNumberException extends \Exception
{
    public static function getDefaultMessage()
    {
        return Yii::t('steroids', "Некорректный номер телефона");
    }
}