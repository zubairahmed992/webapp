<?php

/**
 * Created by PhpStorm.
 * User: sadam.hussain
 * Date: 5/10/2017
 * Time: 8:54 PM
 */
namespace LoveThatFit\WebServiceBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * The order.placed event is dispatched each time an order is created
 * in the system.
 */
class CalibrationEvent extends Event
{
    const NAME = 'calibration.initiated';

    protected $calibration;

    public function __construct($calibration)
    {
        $this->calibration = $calibration;
    }

    public function getCalibration()
    {
        return $this->calibration;
    }
}