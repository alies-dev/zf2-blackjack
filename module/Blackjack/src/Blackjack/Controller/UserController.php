<?php

namespace Blackjack\Controller;

use Blackjack\Entity\Game;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class UserController extends AbstractActionController
{
    public function scoresAction()
    {
        $user = $this->getServiceLocator()
            ->get('Blackjack\Manager\UserManager')->getUser();

        if (!$user) {
            return new JsonModel(
                array(
                    'success' => false,
                    'data'    => array(
                        'scores' => array('games' => array()),
                    ),

                )
            );
        }

        /** @var Game[] $games */
        $games = $this->getServiceLocator()
            ->get('Doctrine\ORM\EntityManager')
            ->getRepository('Blackjack\Entity\Game')
            ->findFinishedByUser($user);

        $gamesAttributes = array();
        foreach ($games as $game) {
            $gamesAttributes[] = array(
                'id'          => $game->getId(),
                'dealerScore' => $game->getDealerScore(),
                'userScore'   => $game->getUserScore(),
                'finished'    => $game->getFinished()->getTimestamp(),
            );
        }

        return new JsonModel(
            array(
                'success' => true,
                'message' => '',
                'data' => array(
                    'scores' => array(
                        'games' => $gamesAttributes,
                    ),
                ),
            )
        );
    }
}
