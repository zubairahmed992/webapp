<?php

namespace LoveThatFit\CartBundle\Component\Event;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
/**
 */
class AccessDeniedListener {

    public function onAccessDeniedException(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        //Get the root cause of the exception.
        while (null !== $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }
        if ($exception instanceof AccessDeniedException) {
           
        }
    }

}