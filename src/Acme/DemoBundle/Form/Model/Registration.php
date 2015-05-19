<?php

namespace Acme\DemoBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Acme\DemoBundle\Entity\User;

class Registration
{
    /**
     * @Assert\Type(type="Acme\DemoBundle\Entity\User")
     * @Assert\Valid()
     */
    protected $user;


    protected $termsAccepted;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}