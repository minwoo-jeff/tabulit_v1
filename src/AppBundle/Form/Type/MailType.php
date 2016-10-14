<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', 'textarea')
            ->add('message', 'textarea')
            ->add('save', 'submit', array('label' => 'Send'));

        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mail';
    }
}
