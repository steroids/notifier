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
        return 'notifier_notifications';
    }

    public function fields()
    {
        return [
            'id',
            'templateName',
            'refId',
            'content',
            'isRead',
            'createTime',
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
                'isPublishToFrontend' => true
            ],
            'userId' => [
                'label' => Yii::t('steroids', 'Пользователь'),
                'appType' => 'integer',
                'isPublishToFrontend' => false
            ],
            'templateName' => [
                'isPublishToFrontend' => true
            ],
            'refId' => [
                'appType' => 'integer',
                'isPublishToFrontend' => true
            ],
            'content' => [
                'label' => Yii::t('steroids', 'Контент'),
                'appType' => 'text',
                'isPublishToFrontend' => true
            ],
            'paramsJson' => [
                'appType' => 'text',
                'isPublishToFrontend' => false
            ],
            'isRead' => [
                'appType' => 'boolean',
                'isPublishToFrontend' => true
            ],
            'createTime' => [
                'label' => Yii::t('steroids', 'Добавлен'),
                'appType' => 'autoTime',
                'isPublishToFrontend' => true,
                'touchOnUpdate' => false
            ]
        ]);
    }
}
