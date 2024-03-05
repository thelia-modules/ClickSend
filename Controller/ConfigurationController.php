<?php

namespace ClickSend\Controller;

use ClickSend\ClickSend;
use ClickSend\Form\ConfigurationForm;
use ClickSend\Form\EmailParameterForm;
use ClickSend\Form\SendEmailForm;
use ClickSend\Form\SendSmsForm;
use ClickSend\Service\ApiAccountService;
use ClickSend\Service\ApiTransactionalEmailService;
use ClickSend\Service\ApiSmsService;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\AdminController;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\ConfigQuery;

#[Route('/admin/module/ClickSend', name: 'clicksend_config')]
class ConfigurationController extends AdminController
{
    #[Route('/configuration', name: 'configuration')]
    public function saveConfiguration(ApiAccountService $apiAccountService, ParserContext $parserContext) : RedirectResponse|Response
    {
        $form = $this->createForm(ConfigurationForm::getName());
        try {
            $data = $this->validateForm($form)->getData();

            ClickSend::setConfigValue(ClickSend::API_USERNAME_CONFIG_KEY, $data["api_username"]);
            ClickSend::setConfigValue(ClickSend::API_KEY_CONFIG_KEY, $data["api_key"]);

            $apiAccountService->getAccount();

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
    }

    #[Route('/email/parameter', name: 'email_parameter')]
    public function saveEmailParameter(ParserContext $parserContext) : RedirectResponse|Response
    {
        $form = $this->createForm(EmailParameterForm::getName());
        try {
            $data = $this->validateForm($form)->getData();

            ClickSend::setConfigValue(ClickSend::EMAIL_ADDRESS_CONFIG_KEY, $data["email_address"]);
            ClickSend::setConfigValue(ClickSend::EMAIL_ID_CONFIG_KEY, $data["email_address_id"]);

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
    }

    #[Route('/sms/send', name: 'send_sms')]
    public function sendSMS(ApiSmsService $apiSmsService, ParserContext $parserContext) : RedirectResponse|Response
    {
        $form = $this->createForm(SendSmsForm::getName());

        try {
            $data = $this->validateForm($form)->getData();

            $message = $apiSmsService->createSmsMessage(
                ConfigQuery::getStoreName(),
                Translator::getInstance()?->trans("SMS test from %store%, sent by ClickSend", ['%store%' => ConfigQuery::getStoreName()], ClickSend::DOMAIN_NAME),
                $data["phone"]
            );

            $apiSmsService->sendSms([$message]);

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
    }

    #[Route('/email/send', name: 'send_email')]
    public function sendEmail(
        ApiTransactionalEmailService $apiEmailService,
        ParserContext $parserContext) : RedirectResponse|Response
    {
        $form = $this->createForm(SendEmailForm::getName());

        try {
            $data = $this->validateForm($form)->getData();

            $email = $apiEmailService->createSimpleEmailMessage(
                Translator::getInstance()?->trans("Your Thelia Customer"),
                $data['email'],
                ClickSend::getConfigValue(ClickSend::EMAIL_ADDRESS_CONFIG_KEY),
                ClickSend::getConfigValue(ClickSend::EMAIL_ID_CONFIG_KEY),
                Translator::getInstance()?->trans("Email test from %store%, sent by ClickSend", ['%store%' => ConfigQuery::getStoreName()], ClickSend::DOMAIN_NAME),
                Translator::getInstance()?->trans("Email test from %store%, sent by ClickSend", ['%store%' => ConfigQuery::getStoreName()], ClickSend::DOMAIN_NAME)
            );

            $apiEmailService->sendEmail($email);

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
    }
}