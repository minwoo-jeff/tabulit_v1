<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\UserHashes;
use AppBundle\Entity\Users;
use AppBundle\Controller\TabController;
use AppBundle\Form\Type\RegistrationType;

class VolumeController extends TabController {
    /*
     * Params: $serial_id
     * Effects: Return Page with All Volumes for that Serial
     */

    public function indexAction($serial_id) {
        $user = $this->getUser();
        // Check roles
        $r = $this->checkPermissions($user);
        if ($r == 'ROLE_USER') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            $volumes = $serial->getVolumes();
            return $this->render('AppBundle:Chapters_Page:chapters_page.html.twig', array(
                        'serial' => $serial,
                        'user' => $user,
                        'volumes' => $volumes
            ));
        }
    }

    /*
     * Params: $serial_id, $volume_id
     * Effects: Read that volume
     */

    public function readAction($serial_id, $volume_id) {
        $user = $this->getUser();

        // Check roles
        if ($user == null) {
            return $this->redirect($this->generateUrl('_login'));
        } else {
            $r = $this->checkPermissions($user);
            if ($r == 'ROLE_USER') {
                return $this->redirect($this->generateUrl('_verify_email'));
            }
            
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            $volume = $this->getDoctrine()->getRepository('AppBundle:Volume')->find($volume_id);
            
            if ($r == 'ROLE_ADMIN') {
                return $this->render('AppBundle:Reading_Page:reading_page.html.twig', array(
                            'serial' => $serial,
                            'volume' => $volume,
                            'user' => $user
                ));
            } else if ($this->checkUserBought($user, $volume) == true) {
                        return $this->render('AppBundle:Reading_Page:reading_page.html.twig', array(
                                    'serial' => $serial,
                                    'volume' => $volume,
                                    'user' => $user
                        ));
            }  else {   
                // Doesn't exist in list. Re-direct to the serial page
                return $this->redirect($this->generateUrl('_volumes_page', array(
                                    'serial_id' => $serial_id,
                                    'user' => $user
                )));
            }
        }
    }
    
    /*
     * Returns True if user has previously bought volume, or if volume price is 0
     */
    private function checkUserBought($user, $volume) {
        if ($volume->getPrice() == 0 && $volume == null ) {
            return true;
        }
        foreach ($user->getPurchasedVolumes() as $v){
            if ($v == $volume) {
                return true;
            }
        }
        return false;
    }
    
    public function purchaseAction($serial_id, $volume_id) {
        $user = $this->getUser();
        if ($this->checkPermissions($user) == 'ROLE_USER') {
            return $this->redirectToRoute('_verify_email');
        }
        $volume = $this->getDoctrine()->getRepository('AppBundle:Volume')->find($volume_id);
        
        // TODO: CHECK IF ALREADY PURCHASED
        
        $user_coins = $user->getCoin();
        $total_left = $user_coins - $volume->getPrice();
        if ($total_left < 0) {
            $this->get('session')->getFlashBag()->add('notice', 'There are insufficient funds in your wallet.');
            
            return $this->redirect($this->generateUrl('_volumes_page', array(
                'serial_id'=>$serial_id
            )));
        } else {
            // Set new coin amount for user;
            $user->setCoin($total_left);
            $user->addPurchasedVolume($volume);
            
            // Persist new info to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $this->logAction("Volume ".$volume->getId()." has been purchased by ".$user->getFirstName()." ".$user->getLastName()."(".$user->getId().")", self::LOGTYPE_OTHER, $volume->getId());
            $serial = $this->getDoctrine()->getRepository('AppBundle:Serial')->find($serial_id);
            return $this->redirect($this->generateUrl('_read_volume', array(
                'volume_id'=>$volume_id,
                'serial_id'=>$serial_id,
                'serial'=>$serial,
                'volume'=>$volume,
                'user'=>$user
                    )));
        }
    }
        

}
