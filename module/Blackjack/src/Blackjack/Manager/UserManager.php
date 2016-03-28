<?php

namespace Blackjack\Manager;

use Blackjack\Entity\User;
use Blackjack\Repository\UserRepository;
use Doctrine\ORM\EntityManager;

class UserManager
{
    private $cookieName = 'PHPSESSID';

    /** @var EntityManager */
    private $em;
    /** @var UserRepository */
    private $userRepository;

    /**
     * UserManager constructor.
     *
     * @param EntityManager  $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManager $entityManager, UserRepository $userRepository)
    {
        $this->em = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function getUser()
    {
        $sessionId = $_COOKIE[$this->cookieName];
        $user = $this->userRepository->findOneBySessionId($sessionId);

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->setSessionId($sessionId);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}