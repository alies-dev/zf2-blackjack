<?php

namespace Blackjack\Repository;

use Blackjack\Entity\Game;
use Blackjack\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * Class GameRepository
 *
 * @package Blackjack\Repository
 */
class GameRepository extends EntityRepository
{
    /**
     * @param User $user
     *
     * @return Game[]
     */
    public function findFinishedByUser(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        $games = $qb
            ->where('u.finished IS NOT NULL')
            ->andWhere('u.user = :user')
            ->setParameters(array('user' => $user,))
            ->getQuery()
            ->getResult();

        return $games;
    }
}
