<?php

namespace steroids\notifier\models;

use steroids\notifier\models\meta\NotificationMeta;
use yii\helpers\Json;

/**
 * Class Notification
 * @package steroids\notifier\models
 * @property-read array $params
 */
class Notification extends NotificationMeta
{

    public function fields()
    {
        return [
            ...parent::fields(),
            'params',
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            ['isRead', 'default', 'value' => false]
        ];
    }

    /**
     * @return array|null
     */
    public function getParams()
    {
        return $this->paramsJson ? Json::decode($this->paramsJson) : null;
    }
}
