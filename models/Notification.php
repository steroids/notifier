<?php

namespace steroids\notifier\models;

use steroids\notifier\models\meta\NotificationMeta;

class Notification extends NotificationMeta
{
    public function rules()
    {
        return [
            ...parent::rules(),
            ['isRead', 'default', 'value' => false]
        ];
    }
}
