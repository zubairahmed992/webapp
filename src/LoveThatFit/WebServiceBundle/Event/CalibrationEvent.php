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

    protected $user_id;
    protected $email;
    protected $status;

    public function __construct($user_id, $email ,$status)
    {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->status = $status;

    }


    public function getUserID()
    {
        return $this->user_id;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getStatus()
    {
        return $this->status;
    }
}