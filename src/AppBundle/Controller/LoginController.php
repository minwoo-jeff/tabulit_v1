<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\UserHashes;
use AppBundle\Entity\Users;
use AppBundle\Controller\TabController;
use AppBundle\Form\Type\RegistrationType;

class LoginController extends TabController {

    public function indexAction() {
        $request = Request::createFromGlobals();

        if ($this->getUser() != null) {
            return $this->redirect($this->generateUrl("_post_login"));
        }
        // get the login error if there is one
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'AppBundle:Login_Page:login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
            )
        );
    }
    
    public function resetPasswordAction($error="", $lastUsername="") {
        if ($lastUsername == "")
        {
            $authenticationUtils = $this->get('security.authentication_utils');
            $lastUsername = $authenticationUtils->getLastUsername();
        }
        
        return $this->render(
            'AppBundle:Login_Page:resetPassword.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error
            )
        );
    }
    
    public function resetPasswordPostAction() {
        // Get the user's email
        $request = Request::createFromGlobals();
        $userEmail = $request->request->get('_username', '');
        
        // Get the user object
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:Users')
            ->findOneBy(array('email' => $userEmail));
            
        if (!$user)
        {
            $error = "User not found!";
        }
        else
        {
            // Generate a user hash
            $hash = $this->generateUserHash($user);
            
            // Send reset password email
            $mailer = $this->get('mailer');
            $message = $mailer->createMessage()
                ->setSubject('Tabulit Password Reset')
                ->setFrom('admin@tabulit.com')              
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'AppBundle:Email:resetPassword.html.twig', array(
                        'id' => $user->getId(),
                        'hash' => $hash->getRc4Key(),
                        'hash_time' => $hash->getCreated())
                    ),
                    'text/html'
                );
            $mailer->send($message);
            $this->logAction("Reset password mail has been sent for ".$user->getId(), self::LOGTYPE_EDIT, $user->getId());
            $error = "NONE";
        }
        
        return $this->resetPasswordAction($error, $userEmail);
    }
    
    public function resetPasswordCompleteAction($id, $hash, $hashTime)
    {
        // Get the user that is attempting to reset their password
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:Users')
            ->find($id);
            
        // Check to see if the hash is valid
        if ($this->checkUserHash($user, $hash, $hashTime))
        {
            $form = $this->createResetForm($id, $hash, $hashTime);
            
            return $this->render(
                'AppBundle:Login_Page:resetPasswordComplete.html.twig', array(
                'form' => $form->createView()
                )
            );
        }
        
        return $this->resetPasswordAction("Something went wrong. We couldn't validate the request. Please try again. If errors persist, please contact an administrator to reset your password.");
    }
    
    public function resetPasswordCompletePostAction(Request $request)
    {
        $form = $this->createResetForm(0, 0, 0);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Get the user that is attempting to reset their password
            $user = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->find($form["id"]->getData());
            
            echo $form["id"]->getData();
                
            // Check to see if the hash is valid
            if ($this->checkUserHash($user, $form["h"]->getData(), $form["ht"]->getData()))
            {
                $encoderFactory = $this->get('security.encoder_factory');
                $encoder = $encoderFactory->getEncoder($user);
                $password = $encoder->encodePassword($form["password"]->getData(), $user->getSalt());
                $user->setPassword($password);
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->logAction("Password has been reset for user ".$user->getFirstName()." ".$user->getLastName()."with id ".$user->getId(), self::LOGTYPE_EDIT, $user->getId());
                $this->expireUserHash($user);
                
                $this->get('session')->getFlashBag()->add('notice','Password reset successful!');
                return $this->indexAction();
            }
            else
            {
                return $this->resetPasswordAction("Something went wrong. We couldn't validate the request. Please try again. If errors persist, please contact an administrator to reset your password.");
            }
        }

        return $this->render(
                'AppBundle:Login:resetPasswordComplete.html.twig', array(
                'form' => $form->createView()
                )
            );
    }
    
    public function createResetForm($user, $hash, $hashTime)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_login_resetpassword_complete_post'))
            ->setMethod('POST')
            ->add('id', 'hidden', array('data' => $user))
            ->add('h', 'hidden', array('data' => $hash))
            ->add('ht', 'hidden', array('data' => $hashTime))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                    'required' => true,
                    'first_options' => array('label' => 'Create a new password'),
                    'second_options' => array('label' => 'Confirm your new password')))
            ->add('submit', 'submit', array('label' => 'Change Password'))
            ->getForm();
    }
    
    public function logincheckAction() {
        return null;
    }

}
