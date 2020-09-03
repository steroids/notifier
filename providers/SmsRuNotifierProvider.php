<?php

namespace steroids\sms;

use steroids\notifier\NotifierModule;
use steroids\notifier\providers\BaseNotifierProvider;
use yii\base\Exception;

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
    public string $sender;

    /**
     * @var array|null
     */
    public ?array $lastResult = null;

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
        $ch = curl_init("http://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $post = [
            'api_id' => $this->apiId,
            'to' => $message->to,
            'text' => (string)$message,
        ];

        // check address/number sender
        if ($this->sender) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $this->sender) || preg_match('/^[0-9]+$/', $this->sender)) {
                throw new Exception('Illegal SMS.RU from number');
            }
            $post['from'] = $this->sender;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $this->lastResult = curl_exec($ch);
        curl_close($ch);

        // Success path
        if (is_string($this->lastResult)) {
            $this->lastResult = explode("\n", $this->lastResult);

            if ($this->lastResult[0] == 100) {
                return; // OK
            }
        }

        // Failure
        ob_start();
        var_dump($this->lastResult);
        $this->lastResult = ob_get_clean();

        throw new Exception('SMS.RU request failed: ' . $this->lastResult);
    }

}
