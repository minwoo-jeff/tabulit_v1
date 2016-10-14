<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\UserHashes;
use AppBundle\Entity\Users;
use AppBundle\Controller\TabController;
use AppBundle\Form\Type\RegistrationType;

class SerialController extends TabController {
    /*
     * Effects: Return Page with All Serials
     */
    public function indexAction() {
        $user = $this->getUser();
        // Check roles
        if ($user == null) {
            return $this->redirect($this->generateUrl('_login'));
        }
        $r = $this->checkPermissions($user);
        if ($r == 'ROLE_USER') {
            return $this->redirect($this->generateUrl('_verify_email'));
        } else {
            $serials = $this->getDoctrine()->getRepository('AppBundle:Serial')->findAll();

            return $this->render('AppBundle:Viewing_Page:viewpage.html.twig', array(
                        'serials' => $serials,
                        'user' => $user
            ));
        }
    }
    
    public function serialsCatAction() {
        $user = $this->getUser();
        // Check roles
        if ($user == null) {
            return $this->redirect($this->generateUrl('_login'));
        }
        $r = $this->checkPermissions($user);
        if ($r == 'ROLE_USER') {
            return $this->redirect($this->generateUrl('_verify_email'));
        } else {
            $serials = $this->getDoctrine()->getRepository('AppBundle:Serial')->findAll();

            return $this->render('AppBundle:Serials_Page:serialspage.html.twig', array(
                        'serials' => $serials,
                        'user' => $user
            ));
        }
    }
    public function singlesCatAction() {
        $user = $this->getUser();
        // Check roles
        if ($user == null) {
            return $this->redirect($this->generateUrl('_login'));
        }
        $r = $this->checkPermissions($user);
        if ($r == 'ROLE_USER') {
            return $this->redirect($this->generateUrl('_verify_email'));
        } else {
            $serials = $this->getDoctrine()->getRepository('AppBundle:Serial')->findAll();

            return $this->render('AppBundle:Singles_Page:singlespage.html.twig', array(
                        'serials' => $serials,
                        'user' => $user
            ));
        }
    }

}
