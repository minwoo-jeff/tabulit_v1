<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token as PayToken;

/**
 * PaymentToken
 * 
 * @ORM\Table(name="payment_token")
 * @ORM\Entity
 */
class PaymentToken extends PayToken {
    
}