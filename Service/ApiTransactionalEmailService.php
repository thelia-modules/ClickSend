<?php

namespace ClickSend\Service;

use ClickSend\ApiException;
use ClickSend\ClickSend as ModuleClickSend;
use ClickSend;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;

class ApiTransactionalEmailService
{
    private ClickSend\Api\TransactionalEmailApi $apiInstance;

    public function __construct()
    {
        $config = ClickSend\Configuration::getDefaultConfiguration()
            ->setUsername(ModuleClickSend::getConfigValue(ModuleClickSend::API_USERNAME_CONFIG_KEY))
            ->setPassword(ModuleClickSend::getConfigValue(ModuleClickSend::API_KEY_CONFIG_KEY));

        $this->apiInstance = new ClickSend\Api\TransactionalEmailApi(null, $config);
    }

    /**
     * @param ClickSend\Model\EmailRecipient[] $to
     * @param ClickSend\Model\EmailRecipient[] $cc
     * @param ClickSend\Model\EmailRecipient[] $bcc
     * @param ClickSend\Model\EmailFrom|null $from
     * @param string|null $subject
     * @param string|null $body
     * @param array|null $attachements
     * @param float|null $schedule
     * @return ClickSend\Model\Email
     */
    public function createEmailMessage(
        array $to = null,
        array $cc = null,
        array $bcc = null,
        ClickSend\Model\EmailFrom $from = null,
        string $subject = null,
        string $body = null,
        array $attachements = null,
        float $schedule = null
    ): ClickSend\Model\Email
    {
        $email = new ClickSend\Model\Email();

        $email->setTo($to)
            ->setCc($cc)
            ->setBcc($bcc)
            ->setFrom($from)
            ->setSubject($subject)
            ->setBody($body)
            ->setAttachments($attachements)
            ->setSchedule($schedule)
        ;

        return $email;
    }

    /**
     * @param string $toName
     * @param string $toEmail
     * @param string $fromName
     * @param string $emailAddressId
     * @param string $subject
     * @param string $body
     * @return ClickSend\Model\Email
     */
    public function createSimpleEmailMessage(
        string $toName,
        string $toEmail,
        string $fromName,
        string $emailAddressId,
        string $subject,
        string $body
    ): ClickSend\Model\Email
    {
        $email = new ClickSend\Model\Email();

        $email->setTo([$this->createEmailRecipient($toName, $toEmail)])
            ->setFrom($this->createEmailFrom($fromName, $emailAddressId))
            ->setSubject($subject)
            ->setBody($body)
        ;

        return $email;
    }

    /**
     * @throws ApiException
     * @throws \JsonException
     */
    public function sendEmail($email)
    {
        if (ClickSend\ClickSend::IS_TEST) {
            Tlog::getInstance()->info(Translator::getInstance()->trans('Your module ClickSend is in test mode, your email was not send to ClickSend'));
            return [];
        }

        return json_decode($this->apiInstance->emailSendPost($email), false, 512, JSON_THROW_ON_ERROR);
    }

    private function createEmailRecipient(string $name, string $email): ClickSend\Model\EmailRecipient
    {
        $recipient = new ClickSend\Model\EmailRecipient();
        $recipient->setName($name)
            ->setEmail($email)
        ;

        return $recipient;
    }

    private function createEmailFrom(string $name, string $id): ClickSend\Model\EmailFrom
    {
        $from = new ClickSend\Model\EmailFrom();
        $from->setName($name)
            ->setEmailAddressId($id)
        ;

        return $from;
    }
}