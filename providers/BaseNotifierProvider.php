<?php

namespace steroids\notifier\providers;

use yii\base\BaseObject;

abstract class BaseNotifierProvider extends BaseObject
{
    /**
     * @var array
     */
    public array $templates = [];

    public function send(string $templatePath, array $params)
    {
    }
}
