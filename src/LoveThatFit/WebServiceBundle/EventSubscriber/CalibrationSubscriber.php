<?php

/**
 * Created by PhpStorm.
 * User: sadam.hussain
 * Date: 5/10/2017
 * Time: 9:09 PM
 */
namespace LoveThatFit\WebServiceBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use LoveThatFit\WebServiceBundle\Event\CalibrationEvent;

class CalibrationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CalibrationEvent::NAME => 'onCalibration',
        );
    }

    public function onCalibration(CalibrationEvent $event)
    {
      /*Execute cURL code here*/
    }
}