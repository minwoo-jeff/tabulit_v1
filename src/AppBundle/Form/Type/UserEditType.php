<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserEditType extends AbstractType {

    public function __construct() {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('email', 'email', array(
                    'required' => true))
                ->add('roles', 'entity', array(
                    'class' => 'AppBundle:Roles',
                    'property' => 'name',
                    'required' => true,
                    'expanded' => false,
                    'multiple' => true,
                ))
                ->add('firstName', 'text')
                ->add('lastName', 'text')
                ->add('gender', 'choice', array(
                    'placeholder' => 'I am...',
                    'choices' => array('0' => 'Female', '1' => 'Male', '2' => 'Other'),
                    'required' => true,
                ))
                ->add('birthDate', 'birthday', array(
                    'widget' => 'single_text'))
                ->add('locked', 'choice', array(
                    'label'=>'Lock This Account',
                    'choices' => array(
                        '1'=>'Lock',
                        '0'=>'Unlock'
                    ),
                    'required'=>true
                ))
                ->add('apply', 'submit', array('label' => 'Save Changes'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Users',
            'validation_groups' => array('edit'),
        ));
    }

    public function getName() {
        return 'user';
    }

}
