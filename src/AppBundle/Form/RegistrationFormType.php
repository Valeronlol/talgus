<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationForm;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'expanded' => true,
                'choices' => [
                    'ROLE_ADMIN' => 'ROLE_ADMIN', //Uncomment if admin can create admins
                    'ROLE_MANAGER' => 'ROLE_MANAGER',
                    'ROLE_SELLER' => 'ROLE_SELLER',
                ],
                'multiple' => true,
            ]);
    }

    public function getParent()
    {
        return BaseRegistrationForm::class;
    }
}