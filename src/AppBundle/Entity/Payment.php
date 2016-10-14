<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;
    
/**
 * Payment 
 * 
 * @ORM\Table(name="payment")
 * @ORM\Entity
 */
class Payment extends BasePayment {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
