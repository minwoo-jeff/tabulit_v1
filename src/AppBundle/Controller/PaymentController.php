<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Bridge\Buzz\JsonResponse;

class PaymentController extends TabController {
 
    
    public function prepareAction($user_id, $amount) 
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:Users')->find($user_id);
        $gatewayName = 'pay_with_paypal';

        $storage = $this->get('payum')->getStorage('AppBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('USD');
        if ($amount == 299) {
            $payment->setTotalAmount(299);
            $payment->setDescription('10 Coin Purchase at 2.99');
        } else if ($amount == 549) {
            $payment->setTotalAmount(549);
            $payment->setDescription('20 Coin Purchase at 5.49');
        } else if ($amount == 749) {
            $payment->setTotalAmount(749);
            $payment->setDescription('30 Coin Purchase at 7.49');
        } else {  
            $payment->setTotalAmount(899);
            $payment->setDescription('40 Coin Purchase at 8.99');
        }
        $payment->setClientId($user->getId());
        $payment->setClientEmail($user->getEmail());

        $storage->update($payment);
        
        $captureToken = $this->get('payum.security.token_factory')->createCaptureToken(
            $gatewayName, 
            $payment, 
            '_prepare_done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());    
    }
    
    public function doneAction(Request $request)
    {
        $token = $this->get('payum.security.http_request_verifier')->verify($request);
        
        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum.security.http_request_verifier')->invalidate($token);

        // Once you have token you can get the model from the storage directly. 
        //$identity = $token->getDetails();
        //$payment = $payum->getStorage($identity->getClass())->find($identity);

        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();
        
        $user = $this->getUser();
        $paid = $payment->getTotalAmount();
        if ($status->isFailed()) {
            $this->addFlash('notice', "Payments of $".$paid." have failed. Credits have not been added to your account.");
            $this->get('payum.security.http_request_verifier')->invalidate($token);
            return $this->redirect($this->generateUrl('_pricing_page', array('user'=>$user)));   
        } else if ($status->isCaptured() || $status->isAuthorized()){
            // Change status to authorize
            $status->markAuthorized();
            // Authorized Payment
            if ($paid == 299) {
                $coin = 10;
                $new_coin = $coin + $user->getCoin();
                $user->setCoin($new_coin);
            } else if ($paid == 549) {
                
                $coin = 20;
                $new_coin = $coin + $user->getCoin();
                $user->setCoin($new_coin);
            } else if ($paid == 749) {
               
                $coin = 30;
                $new_coin = $coin + $user->getCoin();
                $user->setCoin($new_coin);
            } else {
                
                $coin = 40;
                $new_coin = $coin + $user->getCoin();
                $user->setCoin($new_coin);
            } 
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $this->addFlash('notice', "Payments of $".$paid." have successfully been made. ".$coin." credits have not been added to your account!");
            // Done with token. Invalidate it.
            $this->get('payum.security.http_request_verifier')->invalidate($token);
            return $this->redirect($this->generateUrl('_profile', array('user'=>$user)));
        } 
       
        return new JsonResponse(array(
            'status' => $status->getValue(),
            'payment' => array(
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ),
        ));
    }
}