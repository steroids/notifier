<?php

namespace steroids\notifier\providers;

use steroids\notifier\NotifierModule;
use steroids\notifier\providers\BaseNotifierProvider;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * When you registered you've got a @apiId
 * which you can use for send sms notification
 *
 * Registration page
 * https://sms.ru/?panel=register
 */
class SmsRuNotifierProvider extends BaseNotifierProvider
{
    /**
     * You've got this after registration
     *
     * @var string
     */
    public string $apiId;

    /**
     * Name of sender. If you've letter sender you can use it.
     * Otherwise your sender will be the default sender.
     * Not required.
     * @var string
     */
    public ?string $sender = '';

    /**
     * @inheritDoc
     */
    public static function type()
    {
        return NotifierModule::PROVIDER_TYPE_SMS;
    }

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $ch = curl_init("https://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $to = $message->to;
        $to = preg_replace('/[^0-9]+/', '', $to);
        $to = '+' . preg_replace('/^8/', '7', $to);

        $post = [
            'api_id' => $this->apiId,
            'to' => $to,
            'msg' => (string)$message,
            'json' => 1,
        ];

        // check address/number sender
        if ($this->sender) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $this->sender) || preg_match('/^[0-9]+$/', $this->sender)) {
                throw new Exception('Illegal SMS.RU from number');
            }
            $post['from'] = $this->sender;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $response = curl_exec($ch);
        curl_close($ch);

        // Success path
        if (is_string($response)) {
            $json = Json::decode($response);

            if (ArrayHelper::getValue($json, 'status') === 'OK') {
                foreach ($json['sms'] as $phone => $data) {
                    if ($data['status'] !== 'OK') {
                        throw new Exception('SMS.RU request failed: ' . $data->status_code . '. ' . $data->status_text);
                    }
                }
                return;
            }
        }

        throw new Exception('SMS.RU request failed: Response: ' . (string)$response);
    }

}
