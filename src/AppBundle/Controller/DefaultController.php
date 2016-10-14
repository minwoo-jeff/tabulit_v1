<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use AppBundle\Entity\Users;
use AppBundle\Form\Type\RegistrationType;

class DefaultController extends TabController {

    public function indexAction() {
        return $this->render('AppBundle:Landing_Page:index.html.twig');
    }
    
    public function termsAction() {
        return $this->render('AppBundle:Information:terms_and_conditions.html.twig');
    }
    
    public function privacyAction() {
        return $this->render('AppBundle:Information:privacy_and_security.html.twig');
    }
    
    

    public function profileAction() {
        $user = $this->getUser();
        if ($user != null) {
            $r = $this->checkPermissions($user);
            if ($r == 'ROLE_USER') {
                return $this->redirect($this->generateUrl('_verify_email'));
            } else {
                return $this->render('AppBundle:Profile_Page:profile_page.html.twig', array(
                            'user' => $user
                ));
            }
        } else {
            return $this->redirect($this->generateUrl('_homepage'));
        }
    }

    public function postLoginAction() {
        $user = $this->getUser();
        if ($user != null) {
            if ($user->getLocked() == 1) {
                return $this->redirect($this->generateUrl('_account_disabled'));
            }
            $r = $this->checkPermissions($user);

            if (!$user->getEmailVerified()) {
                return $this->redirect($this->generateUrl('_no_permission'));
            } else if ($r == 'ROLE_ADMIN') {
                return $this->redirect($this->generateUrl('_admin_homepage'));
            } else if ($r == 'ROLE_WRITER' || $r == 'ROLE_READER') {
                return $this->redirect($this->generateUrl('_viewing_page'));
            } else {
                return $this->redirect($this->generateUrl('_viewing_page'));
            }
        } else {
            return $this->redirect($this->generateUrl('_homepage'));
        }
    }

    public function registrationAction() {
        $user = new Users();

        $form = $this->createForm(new RegistrationType(), $user, array('action' => $this->generateUrl('_registration_apply')));

        return $this->render('AppBundle:Registration_Page:signup.html.twig', array(
                    'form' => $form->createView()));
    }

    public function registrationPostAction() {
        $user = new Users();

        $form = $this->createForm(new RegistrationType(), $user, array('action' => $this->generateUrl('_registration_apply')));

        $request = $this->getRequest();
        $form->handleRequest($request);

        if ($form->isValid()) {
            
            // Check reCaptcha
//            $captcha = $request->get('g-recaptcha-response', '');
//            $captcha_secret = "6Ld4TAITAAAAAJP8cELzeKqIBCGnznIR5ek59qaH"; // TODO: Update this to read from param
//            $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$captcha_secret."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
//            $jres = json_decode($response);
//            if($jres->{'success'}==false)
//            {
//                //$this->get('session')->getFlashBag()->add('error','reCaptcha human validation has failed. Please try again');
//                //goto exit_function;
//            }
            // Check for a duplicated registered email
            if ($this->getDoctrine()
                            ->getRepository('AppBundle:Users')
                            ->findOneBy(array('email' => $user->getEmail()))) {
                $this->get('session')->getFlashBag()->add('notice', 'An account with the email address \'' . $user->getEmail() . '\' already exists.');
                
                goto exit_function;
            }

            // Check to see if password is password
            if ($user->getPassword() == "password") {
                $this->get('session')->getFlashBag()->add('error', 'Your password can not be \'password\'!');
                
                goto exit_function;
            }
            
            // Encrypt the password
            $encoderFactory = $this->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            // Fill in the automatic fields
            $user->setLastIp($request->getClientIp());
            $user->setCreated(date("Y-m-d", time()));
            $user->setLastLogin(date("Y-m-d", time()));

            $user->setCoin(5);
            $user->setEmailVerified(false);
//            $user->setWarning(0);
            $user->setLocked(0);
            $user->setFailedAttempts(0);

            // Give the user the normal role
            $basicUser = $this->getDoctrine()->getRepository('AppBundle:Roles')->findOneByRole("ROLE_USER");
            $user->addRole($basicUser);

            // Create the user
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Generate a user hash
            $hash = $this->generateUserHash($user);

            //todo password salt
            // Send verfication email
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                    ->setSubject('Welcome To Tabulit')
                    ->setFrom('admin@tabulit.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppBundle:Email:registration.html.twig', array(
                        'name' => $user->getFirstName(),
                        'id' => $user->getId(),
                        'hash' => $hash->getRc4Key(),
                        'hash_time' => $hash->getCreated())
                    ), 'text/html'
            );
            $mailer->send($message);
            $this->addFlash('notice', 'Email Sent!');
            // Log the user in
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            // Log the creation
            $this->logAction("New User. ID: " . $user->getId() . ". Name: " . $user->getFirstName() . " " . $user->getLastName(), self::LOGTYPE_CREATE, $user->getId());
        }

        return $this->redirect($this->generateUrl('_viewing_page'));

        exit_function:
        return $this->redirect($this->generateUrl('_registration'));
    }

    public function confirmEmailAction($id, $hash, $hashTime) {
        // Get the user that is attempting verify their email
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:Users')
                ->find($id);

        // Check to see if the hash is valid
        if ($this->checkUserHash($user, $hash, $hashTime)) {
            $user->setEmailVerified(true);
            $basicUser = $this->getDoctrine()->getRepository('AppBundle:Roles')->findOneByRole("ROLE_USER");
            $readerUser = $this->getDoctrine()->getRepository('AppBundle:Roles')->findOneByRole("ROLE_READER");
            $user->removeRole($basicUser);
            $user->addRole($readerUser);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->expireUserHash($user);

            return $this->redirect($this->generateUrl('_viewing_page', array('user' => $user)));
        }

        return $this->redirect($this->generateUrl('_homepage', array('user' => $user)));
    }

    public function noPermissionAction() {
        return $this->render('AppBundle:Errors:no_permission.html.twig');
    }

    public function underConstructionAction() {
        return $this->render('AppBundle:Errors:under_construction.html.twig');
    }

    public function pleaseLoginAction() {
        return $this->render('AppBundle:Errors:please_login.html.twig', array(
        ));
    }

    public function accountDisabledAction() {
        return $this->render('AppBundle:Errors:account_disabled.html.twig');
    }
    
    public function verifyEmailAction() {
        return $this->render('AppBundle:Errors:verify_email.html.twig');
    }

}
