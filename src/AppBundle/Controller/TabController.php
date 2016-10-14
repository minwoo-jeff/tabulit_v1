<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\UserHashes;
use AppBundle\Entity\Logs;

class TabController extends Controller {

    // Logging types
    const LOGTYPE_NONE = 0;
    const LOGTYPE_CREATE = 1;
    const LOGTYPE_EDIT = 2;
    const LOGTYPE_DELETE = 3;
    const LOGTYPE_OTHER = 4;

    protected function logAction($note, $type = LOGTYPE_NONE, $target = -1) {
        $log = new Logs();
        $log->setUser($this->getUser());
        $log->setCreated(time());
        $log->setType($type);
        $log->setTargetId($target);
        $log->setNote($note);

        $em = $this->getDoctrine()->getManager();
        $em->persist($log);
        $em->flush();
    }

    protected function generateUserHash($user) {
        // Get the current hash
        $currentHash = $this->getDoctrine()
                ->getRepository('AppBundle:UserHashes')
                ->findOneBy(array('user' => $user));

        $em = $this->getDoctrine()->getManager();
        if ($currentHash != NULL) {
            $em->remove($currentHash);
            $em->flush();
        }

        // Create a random key generator
        $generator = new SecureRandom();

        // Grab the RC4 Salt from the configs
        $rc4_salt = $this->container->getParameter("salt.rc4");

        // Generate a RC4 key and timestamp
        $rc4Key = hash('sha256', $rc4_salt . $generator->nextBytes(24));
        $rc4Timestamp = time();

        // Save the hash to the database
        $request = Request::createFromGlobals();
        $userHash = new UserHashes();
        $userHash->setUser($user);
        $userHash->setRc4key($rc4Key);
        $userHash->setIp($request->getClientIp());
        $userHash->setCreated($rc4Timestamp);
        $userHash->setExpiry($rc4Timestamp + 600); // Expires in 10 minutes

        $em->persist($userHash);
        $em->flush();

        return $userHash;
    }

    protected function checkUserHash($user, $hash, $hashTime) {
        // Get the user's current hash based of the user id provided
        $userHash = $this->getDoctrine()
                ->getRepository('AppBundle:UserHashes')
                ->findOneBy(array('user' => $user->getId()));

        // Does the user actually have a hash?
        if ($userHash) {
            // Is the hash valid ?
            if ($userHash->getRc4key() == $hash && $userHash->getCreated() == $hashTime) {
                // Hash is valid, check to see if it has expired
                if ($userHash->getExpiry() > time()) {
                    // Hash hasn't expired. All is well.
                    return true;
                }
            }
        }
        return false;
    }

    protected function expireUserHash($user) {
        $currentHash = $this->getDoctrine()
                ->getRepository('AppBundle:UserHashes')
                ->findOneBy(array('user' => $user));

        $em = $this->getDoctrine()->getManager();
        if ($currentHash != NULL) {
            $em->remove($currentHash);
            $em->flush();
        }
    }

    protected function checkPermissions($user) {
        foreach ($user->getRoles()as $role) {
            $r = $role->getRole();
            if ($r == "ROLE_USER") {
                return "ROLE_USER";
            } else if ($r == "ROLE_READER") {
                return "ROLE_READER";
            } else if ($r == "ROLE_WRITER") {
                return "ROLE_WRITER";
            } else if ($r == "ROLE_ADMIN") {
                return "ROLE_ADMIN";
            }
        }
    }

}
