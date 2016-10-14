<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\TabController;

class AdminController extends TabController {

    public function indexAction() {
        $user = $this->getUser();
        if ($user == null) {
            return $this->redirect($this->generateUrl('_please_login'));
        }
        // Check permissions of user as ADMIN
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $logs = $this->getDoctrine()->getRepository('AppBundle:Logs')->findBy(array(), array('id' => 'DESC'), 10);
            return $this->render('AppBundle:Admin:admin_dash.html.twig', array(
                        'logs' => $logs,
                        'user' => $user
            ));
        }
    }

    public function allLogsAction() {
        $user = $this->getUser();
        // Check permissions of user as ADMIN
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $logs = $this->getDoctrine()->getRepository('AppBundle:Logs')->findBy(array(), array('id' => 'DESC'), 30);

            return $this->render('AppBundle:Admin/Logs:all_logs.html.twig', array(
                        'logs' => $logs,
                        'user' => $user
            ));
        }
    }

}
