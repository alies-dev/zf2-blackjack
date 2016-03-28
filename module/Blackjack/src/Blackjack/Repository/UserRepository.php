<?php

namespace Blackjack\Repository;

use Doctrine\ORM\EntityRepository;
use Blackjack\Entity\User;

/**
 * Class UserRepository
 *
 * @package Blackjack\Repository
 * @method User findOneBySessionId($sessionId)
 */
class UserRepository extends EntityRepository
{

}