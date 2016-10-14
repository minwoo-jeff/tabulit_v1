<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\TabController;
use AppBundle\Entity\Serial;
use AppBundle\Form\Type\SerialType;
use Symfony\Component\HttpFoundation\Request;

class AdminSerialsController extends TabController {
    /*
     * Params: none
     * Effects: Returns all Users Page for Admin
     */

    public function allSerialsAction() {
        $user = $this->getUser();
        // Check permissions of user as ADMIN
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serials = $this->getDoctrine()->getRepository('AppBundle:Serial')->findAll();

            return $this->render('AppBundle:Admin/Serials:all_serials.html.twig', array(
                        'serials' => $serials,
                        'user' => $user
            ));
        }
    }

    /*
     * Params: none
     * Effects: Returns Create Serial page for Admin
     */

    public function createSerialAction() {
        $user = $this->getUser();
        // Check permissions of user as ADMIN
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = new Serial();

            $form = $this->createForm(new SerialType(), $serial, array(
                'action' => $this->generateUrl('_create_serial_post')
            ));

            return $this->render('AppBundle:Admin/Serials:create_serial.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user
            ));
        }
    }

    /*
     * Params: request
     * Effects: POST Create Serial page for Admin
     */

    public function createSerialPostAction(Request $request) {
        $user = $this->getUser();
        // Check permissions for ROLE_ADMIN
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = new Serial();

            $form = $this->createForm(new SerialType(), $serial, array(
                'action' => $this->generateUrl('_create_serial_post')
            ));

            $form->handleRequest($request);
            if ($form->isValid()) {
                $serial->setCreatedOn(time());
                $serial->setActive(1);
                $serial->upload();
                $em = $this->getDoctrine()->getManager();
                $em->persist($serial);
                $em->flush();

                $this->logAction("New Serial " . $serial->getTitle() . " has been created with id " . $serial->getId() . " by " . $user->getFullName(), self::LOGTYPE_CREATE, $serial->getId());

                return $this->redirect($this->generateUrl('_admin_homepage', array('user' => $user)));
            }
//        $this->logAction("Failed attempt at creation of serial with id ".$serial->getId()." by ".$user->getFullName(), self::LOGTYPE_CREATE, $serial->getId());
//        
            return $this->render('AppBundle:Admin/Serials:create_serial.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user
            ));
        }
    }

    /*
     * Params: serial_id
     * Effects: Returns Edit Serial page for that serial for ADMINs
     */

    public function editSerialAction($serial_id) {
        $user = $this->getUser();
        // Check Perm for ROLE_ADMIN
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            $form = $this->createForm(new SerialType(), $serial, array(
                'action' => $this->generateUrl('_edit_serial_post', array('serial_id' => $serial_id))
            ));

            return $this->render('AppBundle:Admin/Serials:edit_serial.html.twig', array(
                        'serial' => $serial,
                        'user' => $user,
                        'form' => $form->createView()
            ));
        }
    }

    /*
     * Params: request, serial_id
     * Effects: POST edited Serial  for Admin
     */

    public function editSerialPostAction(Request $request, $serial_id) {
        $user = $this->getUser();
        // Check perm, even for POST
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);

            $form = $this->createForm(
                    new SerialType(), $serial, array('action' => $this->generateUrl('_edit_serial_post', array('serial_id' => $serial_id))));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $serial->setEditedOn(time());
                $serial->upload();
                $em = $this->getDoctrine()->getManager();
                $em->persist($serial);
                $em->flush();

                $this->logAction("Edited Serial " . $serial->getTitle() . "with id " . $serial->getId(), self::LOGTYPE_EDIT, $serial->getId());

                return $this->redirect($this->generateUrl('_all_serials'));
            }
            $this->logAction("Failed to edit serial " . $serial->getTitle() . " by user " . $user->getFullName(), self::LOGTYPE_EDIT, $serial->getId());

            return $this->render('AppBundle:Admin/Serials:edit_serial.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user,
                        'serial_id' => $serial_id
            ));
        }
    }

    /*
     * Delete given serial
     */

    public function deleteSerialAction($serial_id) {
        $user = $this->getUser();
        // Check perms
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);

            // Remove volumes if there are any in Serial
            $this->removeVolumes($serial);

            $em = $this->getDoctrine()->getManager();
            $this->logAction("Serial " . $serial->getTitle() . " has been removed by " . $user->getFullName(), self::LOGTYPE_DELETE, $serial_id);
            $em->remove($serial);
            $em->flush();

            $this->addFlash('notice', 'Serial and associated volumves have been removed');
            return $this->redirect($this->generateUrl('_all_serials', array(
                                'user' => $user,
                                
            )));
        }
    }

    public function removeVolumes($serial) {
        if (!isset($serial) || !$serial) {
            $em = $this->getDoctrine()->getManager();
            foreach ($serial->getVolumes() as $volume) {
                $serial->removeVolume($volume);
                $this->logAction('Volume '.$volume->getTitle().' has been removed in order to delete '.$serial->getTitle(), self::LOGTYPE_DELETE, $serial->getId());
                $em->remove($volume);
            }
            $em->flush();
        }
    }

}
