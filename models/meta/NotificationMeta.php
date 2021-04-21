<?php

namespace steroids\notifier\models\meta;

use steroids\core\base\Model;
use steroids\core\behaviors\TimestampBehavior;
use \Yii;

/**
 * @property string $id
 * @property integer $userId
 * @property string $templateName
 * @property integer $refId
 * @property string $content
 * @property string $paramsJson
 * @property boolean $isRead
 * @property string $createTime
 */
abstract class NotificationMeta extends Model
{
    public static function tableName()
    {
        return 'notifications';
    }

    public function fields()
    {
        return [
        ];
    }

    public function rules()
    {
        return [
            ...parent::rules(),
            [['userId', 'refId'], 'integer'],
            ['templateName', 'string', 'max' => 255],
            [['content', 'paramsJson'], 'string'],
            ['isRead', 'steroids\\core\\validators\\ExtBooleanValidator'],
        ];
    }

    public function behaviors()
    {
        return [
            ...parent::behaviors(),
            TimestampBehavior::class,
        ];
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('steroids', 'ID'),
                'appType' => 'primaryKey',
                'isPublishToFrontend' => false
            ],
            'userId' => [
                'label' => Yii::t('steroids', 'Пользователь'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'templateName' => [
                'isPublishToFrontend' => false
            ],
            'refId' => [
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'content' => [
                'label' => Yii::t('steroids', 'Контент'),
                'appType' => 'text',
                'isPublishToFrontend' => false
            ],
            'paramsJson' => [
                'appType' => 'text',
                'isPublishToFrontend' => false
            ],
            'isRead' => [
                'appType' => 'boolean',
                'isPublishToFrontend' => false
            ],
            'createTime' => [
                'label' => Yii::t('steroids', 'Добавлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => false,
                'touchOnUpdate' => false
            ]
        ]);
    }
}
