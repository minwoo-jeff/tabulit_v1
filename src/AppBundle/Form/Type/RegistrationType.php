<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('email', 'email')
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'required' => true,
                    'first_options' => array('label' => 'Create a password', 'error_bubbling' => true),
                    'second_options' => array('label' => 'Confirm your password')))
                ->add('firstName', 'text')
                ->add('lastName', 'text')
                ->add('gender', 'choice', array(
                    'placeholder' => 'I am...',
                    'choices' => array('0' => 'Female', '1' => 'Male', '2'=>'Prefer Not To Disclose'),
                    'required' => true,
                ))
                ->add('birthDate', 'birthday', array(
                    'widget' => 'single_text',
                    'label' => 'Birthday'
                ))
                ->add('accepted', 'checkbox', array(
                    'label' => 'I agree with the Terms of Use and Privacy Policy',
                    'required'=>true,
               ))
                ->add('apply', 'submit', array('label' => 'SIGN UP'));
    }

    public function getName() {
        return 'u';
    }

}
