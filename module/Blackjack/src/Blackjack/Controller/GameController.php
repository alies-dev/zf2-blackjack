<?php

namespace Blackjack\Controller;

use Blackjack\Entity\Game;
use Zend\Json\Server\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Helper\Json;
use Zend\View\Model\JsonModel;

/**
 * @inheritdoc
 */
class GameController extends AbstractActionController
{
    public function startAction()
    {
        /** @var Game $game */
        $game = $this->getServiceLocator()
            ->get('Blackjack\Manager\GameManager')->create();

        return new JsonModel(
            array(
                'success' => true,
                'message' => 'start',
                'data'    => array(
                    'game' => $game ? $game->prepareForSerialization() : null,
                ),
            )
        );
    }

    public function previousAction()
    {
        /** @var Game $game */
        $game = $this->getServiceLocator()
            ->get('Blackjack\Manager\GameManager')->previous();

        return new JsonModel(
            array(
                'success' => is_object($game),
                'message' => '',
                'data'    => array(
                    'game' => $game ? $game->prepareForSerialization() : null,
                ),
            )
        );
    }

    public function twistAction()
    {
        $gameId = (int)$this->params()->fromRoute('id', null);
        $game = $this->getGameByIdForCurrentUser($gameId);

        if (!$game) {
            return new Json(
                array('success' => false, 'message' => 'Game not found')
            );
        }

        $this->getServiceLocator()
            ->get('Blackjack\Manager\GameManager')->twist($game);

        return new JsonModel(
            array(
                'success' => true,
                'message' => '',
                'data'    => array(
                    'game' => $game->prepareForSerialization(),
                ),
            )
        );
    }

    public function stickAction()
    {
        $gameId = (int)$this->params()->fromRoute('id', null);
        $game = $this->getGameByIdForCurrentUser($gameId);

        if (!$game) {
            return new Json(
                array('success' => false, 'message' => 'Game not found')
            );
        }
        $this->getServiceLocator()
            ->get('Blackjack\Manager\GameManager')->stick($game);

        return new JsonModel(
            array(
                'success' => true,
                'message' => '',
                'data'    => array(
                    'game' => $game->prepareForSerialization(),
                ),
            )
        );
    }

    public function resetAction()
    {
        $gameId = (int)$this->params()->fromRoute('id', null);
        $game = $this->getGameByIdForCurrentUser($gameId);

        if (!$game) {
            return new Json(
                array('success' => false, 'message' => 'Game not found')
            );
        }

        $this->getServiceLocator()
            ->get('Blackjack\Manager\GameManager')->reset($game);

        return new JsonModel(
            array(
                'success' => true,
                'message' => '',
                'data'    => array(
                    'game' => $game->prepareForSerialization(),
                ),
            )
        );
    }

    private function getGameByIdForCurrentUser($gameId)
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $this->getServiceLocator()
            ->get('Blackjack\Manager\UserManager')
            ->getUser();

        /** @var Game $game */
        $game = $em->getRepository('Blackjack\Entity\Game')
            ->findOneBy(
                array(
                    'id'   => $gameId,
                    'user' => $user,
                )
            );

        return $game;
    }
}
