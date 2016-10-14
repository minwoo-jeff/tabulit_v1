<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SerialType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', 'text', array(
                    'label'=>'Title',
                    'required'=>true
                ))
                ->add('description', 'textarea', array(
                    'label'=>'Description',
                    'required'=>true
                ))
                ->add('writtenBy', 'entity', array(
                    'label'=>'Written By',
                    'class'=>'AppBundle:Users',
                    'required'=>true,
                    'multiple'=>false,
                    'property'=>'fullName',
                    'query_builder' => function (\Doctrine\ORM\EntityRepository $repository) {
                        return $repository->createQueryBuilder('u')->innerJoin('u.roles', 'r')->where('r.role = :role')->setParameter('role', 'ROLE_WRITER');
                    }
                ))
                ->add('file', 'file', array(
                    'required'=>false
                ))
                ->add('category', 'choice', array(
                    'choices'=>array(
                        'novel'=>'Novel',
                        'single'=>'Single'
                    ),
                    'label'=>'Category',
                    'required'=>true
                ))
                ->add('apply', 'submit', array('label' => 'CREATE'));
    }
    
    public function getName() {
        
    }
}