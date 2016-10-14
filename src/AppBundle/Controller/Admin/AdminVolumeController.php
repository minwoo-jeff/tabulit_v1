<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\TabController;
use AppBundle\Entity\Serial;
use AppBundle\Entity\Volume;
use AppBundle\Form\Type\VolumeType;
use Symfony\Component\HttpFoundation\Request;

class AdminVolumeController extends TabController {
    /*
     * Params: {serial_id}
     * Effects: Returns all Volumes for that Serial
     */

    public function allVolumesAction($serial_id) {
        $user = $this->getUser();

        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            $volumes = $serial->getVolumes();

            return $this->render('AppBundle:Admin/Volumes:volumes.html.twig', array(
                        'serial' => $serial,
                        'user' => $user,
                        'volumes' => $volumes
            ));
        }
    }

    /*
     * Params: None
     * Effects: Creates volume for specified serial
     */

    public function createVolumeAction() {
        $user = $this->getUser();

        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $volume = new Volume();
            $form = $this->createForm(new VolumeType(), $volume, array(
                'action' => $this->generateUrl('_create_volume_post')
            ));

            return $this->render('AppBundle:Admin/Volumes:create_volume.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user
            ));
        }
    }

    /*
     * Params: None
     * Effects: POST newly created Volume
     */

    public function createVolumePostAction(Request $request) {
        $user = $this->getUser();

        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $volume = new Volume();
            $form = $this->createForm(new VolumeType(), $volume, array(
                'action' => $this->generateUrl('_create_volume_post')
            ));

            $form->handleRequest($request);
            if ($form->isValid()) {
                $volume->setUploadedDate(date("Y-m-d", time()));
                $volume->setLocked(false);
                $volume->setOverallRating(0);

                $em = $this->getDoctrine()->getManager();
                $em->persist($volume);
                $em->flush();

                $this->logAction("New Volume " . $volume->getTitle() . " has been created by " . $user->getFullName(), self::LOGTYPE_CREATE, $volume->getId());

                return $this->redirect($this->generateUrl('_all_volumes', array(
                                    'serial_id' => $volume->getSerial()->getId()
                )));
            }
            return $this->render('AppBundle:Admin/Volumes:create_volume.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user
            ));
        }
    }

    /*
     * PARAMS: serial_id, volume_id
     * Effects: READ VOLUME
     */
    public function readVolumeAction($serial_id, $volume_id) {
        $user = $this->getUser();
        if ($user != null) {
            if ($this->checkPermissions($user) == 'ROLE_ADMIN') {

                $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
                $volume = $this->getDoctrine()->getRepository('AppBundle:Volume')->find($volume_id);

                return $this->render('AppBundle:Admin/Volumes:read_volume.html.twig', array(
                    'serial'=>$serial,
                    'volume'=>$volume,
                    'user'=>$user
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('_homepage'));
        }
    }
    
    /*
     * PARAMS: $serial_id, $volume_id
     * EFFECTS: Returns Edit Volume page for that volume of serial
     */
    public function editVolumeAction($serial_id, $volume_id) {
        $user = $this->getUser();
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            $volume = $this->getDoctrine()->getRepository('AppBundle:Volume')->find($volume_id);
        
            $form = $this->createForm(new VolumeType(), $volume, array(
                'action'=>$this->generateUrl('_edit_volume_post', array(
                    'volume_id'=>$volume_id,
                    'serial_id'=>$serial_id
                ))
            ));
            
            return $this->render('AppBundle:Admin/Volumes:edit_volume.html.twig', array(
                'serial'=>$serial,
                'volume'=>$volume,
                'user'=>$user,
                'form'=>$form->createView()    
            ));
        }
    }
    
    /*
     * Params: request, serial_id, volume_id
     * Effects: POST edited Volume for Admin
     */
    public function editVolumePostAction(Request $request, $serial_id, $volume_id) {
        $user = $this->getUser();
        // Check perms
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            $volume = $this->getDoctrine()->getRepository('AppBundle:Volume')->find($volume_id);
            
            $form = $this->createForm(new VolumeType(), $volume, array(
                'action'=>$this->generateUrl('_edit_volume_post', array(
                    'volume_id'=>$volume_id,
                    'serial_id'=>$serial_id
                ))
            ));
            
            $form->handleRequest($request);
            
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($volume);
                $em->flush();
                
                $this->logAction('Edited Volume '.$volume->getId()." titled: ".$volume->getTitle()." within serial ".$serial->getTitle(),
                        self::LOGTYPE_EDIT,
                        $volume->getId());
                return $this->render('AppBundle:Admin/Volumes:read_volume.html.twig', array(
                    'user'=>$user,
                    'serial_id'=>$serial_id,
                    'volume_id'=>$volume_id,
                    'volume'=>$volume,
                    'serial'=>$serial
                ));
            }          
        }
    }
    
    /*
     * Delete given volume
     */
    public function deleteVolumeAction($serial_id, $volume_id) {
        $user = $this->getUser();
        // Check perms
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $volume = $this->getDoctrine()->getRepository('AppBundle:Volume')->find($volume_id);
            
            $em = $this->getDoctrine()->getManager();
            $this->logAction("Volume ".$volume->getTitle()."has been removed by ".$user->getFullName(), self::LOGTYPE_DELETE, $volume_id);
            $em->remove($volume);
            $em->flush();
            
            return $this->redirect($this->generateUrl('_all_volumes', array(
                'user'=>$user,
                'serial_id'=>$serial_id
            )));
            
        }
    }
}
    