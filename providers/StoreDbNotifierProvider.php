<?php


namespace steroids\notifier\providers;


use steroids\notifier\models\Notification;
use steroids\notifier\NotifierMessage;
use steroids\notifier\NotifierModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use function PHPUnit\Framework\isEmpty;

class StoreDbNotifierProvider extends BaseNotifierProvider
{

    public $modelClass = Notification::class;

    /**
     * @inheritDoc
     */
    public static function type()
    {
        return NotifierModule::PROVIDER_TYPE_STORE;
    }

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $params = $message->params;
        $refId = ArrayHelper::remove($params, 'refId');
        ArrayHelper::remove($params, 'content'); // Remove content from params

        $notification = new $this->modelClass([
            'userId' => $message->userId,
            'templateName' => $message->templateName,
            'refId' => $refId,
            'content' => (string)$message,
            'paramsJson' => !empty($params) ? Json::encode($params) : null
        ]);

        $notification->saveOrPanic();
    }
}