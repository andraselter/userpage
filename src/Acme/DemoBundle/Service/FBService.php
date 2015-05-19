<?php

namespace Acme\DemoBundle\Service;

use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Acme\DemoBundle\Entity\User;



/**
 * Class FBService
 * @package Acme\DemoBundle\Service
 */
class FBService implements OAuthAwareUserProviderInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Loads the user by a given UserResponseInterface object.
     *
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
//        $resourceOwnerName = $response->getUsername();
        $email = $response->getEmail();
        $full_name = $response->getRealName();
        $password = $response->getTokenSecret();
        $datetime = new \DateTime('now');
        $ip = $_SERVER['REMOTE_ADDR'];

        $currentUser = new User();

        $currentUser->setLastLogin($datetime);
        $currentUser->setClientIP($ip);

        $result = $this->em->getRepository('AcmeDemoBundle:User')->findOneBy(array('email'=>$email));
        if (is_null($result)) {
            $currentUser->setFullName($full_name);
            $currentUser->setPassword($password);
            $currentUser->setEmail($email);
            $currentUser->setIsActive(true);
            $currentUser->setPhone('2222');
            $currentUser->setCreated($datetime);
        }

        $currentUser->serialize();
//        var_dump($currentUser); die;

//        $this->em->persist($currentUser);
//        $this->em->flush();

        return $currentUser;

    }
}