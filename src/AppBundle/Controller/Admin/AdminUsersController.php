<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\TabController;
use AppBundle\Entity\Users;
use AppBundle\Form\Type\UserEditType;
use Symfony\Component\HttpFoundation\Request;

class AdminUsersController extends TabController {
    /*
     * Params: None
     * Effects: Gathers list of all users and displays on page
     */

    public function allUsersAction() {
        // $user = Current user
        $user = $this->getUser();
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            // $users = Query for all users
            $users = $this->getDoctrine()->getRepository('AppBundle:Users')->findAll();


            return $this->render('AppBundle:Admin/Users:all_users.html.twig', array(
                        'users' => $users,
                        'user' => $user
            ));
        }
    }

    /*
     * Params: $user_id
     * Effects: Displays edit page for $user_id
     */

    public function editUserAction($user_id) {
        // $user = Current Uesr Query
        $user = $this->getUser();
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            // $e_user = Query for user with $user_id
            $e_user = $this->getDoctrine()->getRepository('AppBundle:Users')->find($user_id);

            $form = $this->createForm(new UserEditType(), $e_user, array(
                'action' => $this->generateUrl('_edit_user_post', array('user_id' => $user_id))
            ));

            return $this->render('AppBundle:Admin/Users:edit_user.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user,
                        'e_user' => $e_user
            ));
        }
    }

    /*
     * Params: $user_id
     * Effects: POST edited params of user
     */

    public function editUserPostAction(Request $request, $user_id) {
        $user = $this->getUser();
        if ($this->checkPermissions($user) != 'ROLE_ADMIN') {
            return $this->redirect($this->generateUrl('_no_permission'));
        } else {
            $e_user = $this->getDoctrine()->getRepository('AppBundle:Users')->find($user_id);
            $form = $this->createForm(
                    new UserEditType(), $e_user, array('action' => $this->generateUrl('_edit_user_post', array('user_id' => $user_id))));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                if ($this->checkPermissions($e_user) == 'ROLE_WRITER') {
                   $e_user->setCoin(20);
                }
                $em->persist($e_user);
                $em->flush();

                $this->logAction(
                        "Edited User ID: " . $e_user->getId() . " :" . $e_user->getFirstName() . " " . $e_user->getLastName() . " by Admin:" . $user->getFullName(), self::LOGTYPE_EDIT, $e_user->getId());
                return $this->redirect($this->generateUrl('_all_users'));
            }

            $this->logAction(
                    "Failed to edit user " . $e_user->getId() . " :" . $e_user->getFirstName() . " " . $e_user->getLastName() . " by " . $user->getFullName(), self::LOGTYPE_EDIT, $e_user->getId());
            return $this->render('AppBundle:Admin/Users:edit_user.html.twig', array(
                        'form' => $form->createView(),
                        'user' => $user,
                        'e_user' => $e_user
            ));
        }
    }

}
