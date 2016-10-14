<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\TabController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AppBundle\Form\Type\MailType;
use Symfony\Component\HttpFoundation\Request;

class AdminMailController extends TabController {

    public function newMailAction() {
        $user = $this->getUser();
        // Check perms
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $form = $this->createForm(new MailType(), null, array(
                'action' => $this->generateUrl('_admin_mail_send')
            ));

            return $this->render('AppBundle:Admin/Mail:newMail.html.twig', array('form' => $form->createView(),
                        'user' => $user));
        }
    }

    public function sendMailAction(Request $request) {
        $user = $this->getUser();
        // Check perms
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $form = $this->createForm(new MailType(), null, array(
                'action' => $this->generateUrl('_admin_mail_send')
            ));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $subject = $form["subject"]->getData();
                $body = $form["message"]->getData();

                $emails = $this->getEmails();

                $mailer = $this->get('mailer');
                $message = $mailer->createMessage()
                        ->setSubject($subject)
                        ->setFrom('admin@tabulit.com')
                        ->setBcc('dbsalsdn@gmail.com')
                        ->setBody($body);
                $mailer->send($message);

                $flash = $this->get('session')->getFlashBag()->add(
                        'notice', 'message sent!');

                return $this->redirect($this->generateUrl('_new_mail', array('form' => $form->createView(),
                                    'user' => $user)));
            } else {
                $flashMessage = $this->get('session')->getFlashBag()->add(
                        'error', 'message could not be delivered!'
                );
                return $this->redirect($this->generateUrl('_new_mail', array('form' => $form->createView(),
                                    'user' => $user)));
            }
        }
    }

    public function getEmails() {
        $emails = array();

        $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();
    
        foreach ($users as $user) {
            array_push($emails, $user->getEmail());
        }
        return $emails;
    }

}
