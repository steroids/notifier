<?php

namespace steroids\notifier\forms\meta;

use steroids\core\base\SearchModel;
use \Yii;
use steroids\notifier\models\Notification;

abstract class NotificationSearchFormMeta extends SearchModel
{
    /**
     * @var integer
     */
    public $userId;
    /**
     * @var boolean
     */
    public $skipRead;

    public function rules()
    {
        return [
            ...parent::rules(),
            ['userId', 'integer'],
            ['skipRead', 'steroids\\core\\validators\\ExtBooleanValidator'],
        ];
    }

    public function sortFields()
    {
        return [];
    }

    public function createQuery()
    {
        return Notification::find();
    }

    public static function meta()
    {
        return [
            'userId' => [
                'label' => Yii::t('steroids', 'Пользователь'),
                'appType' => 'integer',
                'isSortable' => false
            ],
            'skipRead' => [
                'label' => Yii::t('steroids', 'Прочитано?'),
                'appType' => 'boolean'
            ]
        ];
    }
}
