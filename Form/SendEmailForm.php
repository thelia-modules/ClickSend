<?php

namespace ClickSend\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class SendEmailForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add('email', TextType::class,
                [
                    "required" => true,
                    "label" => Translator::getInstance()?->trans("Email")
                ]);
    }
}