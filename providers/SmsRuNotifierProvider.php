<?php

namespace steroids\sms;

use steroids\notifier\providers\BaseNotifierProvider;
use steroids\notifier\structures\SmsNotifyParameters;
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
     * @var array|null
     */
    public ?array $lastResult = null;

    /**
     * @param string $templatePath
     * @param SmsNotifyParameters $params
     * @throws Exception
     */
    public function send(string $templatePath, $params)
    {
        $ch = curl_init("http://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $post = [
            "api_id" => $this->apiId,
            "to" => $params->receiver,
            "text" => $params->text,
        ];

        // check address/number sender
        if ($params->sender) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $params->sender) || preg_match('/^[0-9]+$/', $params->sender)) {
                throw new Exception('Illegal SMS.RU from number');
            }
            $post['from'] = $params->sender;
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
