<?php

namespace ClickSend\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class SendSmsForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add('phone', TextType::class,
            [
                "required" => true,
                "label" => Translator::getInstance()?->trans("Phone")
            ]);
    }
}