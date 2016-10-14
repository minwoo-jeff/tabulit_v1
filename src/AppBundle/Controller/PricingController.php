<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

class PricingController extends TabController {
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
            return $this->render('AppBundle:Pricing_Page:pricing_page.html.twig',array(
                'user'=>$user
            ));
        }
    }
}