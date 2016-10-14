<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VolumeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', 'text', array(
                    'label' => 'Title',
                    'required'=>true
                ))
                ->add('serial', 'entity', array(
                    'class'=>'AppBundle:Serial',
                    'property'=>'title',
                    'required'=>true,
                    'multiple'=>false
                    
                ))
                ->add('content', 'textarea', array(
                    
                    'label' => 'Content',
                    'required'=>true
                ))
                ->add('price', 'integer', array(
                    'label'=>'Price',
                    'required'=>true
                ))
                ->add('apply', 'submit', array(
                    'label'=>'SUBMIT',
                ));
    }
    
    public function getName() {
        return;
    }
}
