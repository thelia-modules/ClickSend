<?php

namespace ClickSend\Form;

use ClickSend\ClickSend;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class EmailParameterForm extends BaseForm
{
    protected function buildForm(): void
    {
        $translator = Translator::getInstance();

        $this->formBuilder
            ->add('email_address', TextType::class, [
                "label" => $translator?->trans("Email Address Store", [], ClickSend::DOMAIN_NAME),
                "required" => true,
                "constraints" => [
                    new NotBlank()
                ],
                "data" => ClickSend::getConfigValue(ClickSend::EMAIL_ADDRESS_CONFIG_KEY)
            ])
            ->add('email_address_id', TextType::class, [
                "label" => $translator?->trans("Email Address Id", [], ClickSend::DOMAIN_NAME),
                "required" => true,
                "constraints" => [
                    new NotBlank()
                ],
                "data" => ClickSend::getConfigValue(ClickSend::EMAIL_ID_CONFIG_KEY)
            ])
        ;
    }
}