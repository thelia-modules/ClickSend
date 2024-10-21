<?php

namespace ClickSend\Service;

use ClickSend\ApiException;
use ClickSend\ClickSend as ModuleClickSend;
use ClickSend;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;

class ApiSmsService
{
    private ClickSend\Api\SMSApi $apiInstance;

    public function __construct()
    {
        $config = ClickSend\Configuration::getDefaultConfiguration()
            ->setUsername(ModuleClickSend::getConfigValue(ModuleClickSend::API_USERNAME_CONFIG_KEY))
            ->setPassword(ModuleClickSend::getConfigValue(ModuleClickSend::API_KEY_CONFIG_KEY));

        $this->apiInstance =  new ClickSend\Api\SMSApi(null, $config);
    }

    public function createSmsMessage(string $source, string $body, string $phone): ClickSend\Model\SmsMessage
    {
        $msg = new ClickSend\Model\SmsMessage();

        $msg->setSource($source)
            ->setBody($body)
            ->setTo($phone)
        ;

        return $msg;
    }

    /**
     * @throws ApiException
     * @throws \JsonException
     */
    public function sendSms(array $messages)
    {
        if (ClickSend\ClickSend::isTestMode()) {
            Tlog::getInstance()->info(Translator::getInstance()->trans('Your module ClickSend is in test mode, your sms was not send to ClickSend'));
            return [];
        }

        $body = new ClickSend\Model\SmsMessageCollection();
        $body->setMessages($messages);

        return json_decode($this->apiInstance->smsSendPost($body), false, 512, JSON_THROW_ON_ERROR);
    }
}