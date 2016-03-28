<?php

namespace Blackjack\Manager;

use Blackjack\Entity\Game;
use Blackjack\Entity\Hand;
use Blackjack\Repository\GameRepository;
use Blackjack\Service\ShufflingMachine;
use Doctrine\ORM\EntityManager;

/**
 * Class GameManager
 *
 * @package Blackjack\Manager
 */
class GameManager
{
    /** @var EntityManager */
    private $em;
    /** @var GameRepository */
    private $gameRepository;
    /** @var UserManager */
    private $userManager;
    /** @var ShufflingMachine */
    private $shufflingMachine;

    /**
     * GameManager constructor.
     *
     * @param EntityManager  $em
     * @param GameRepository $gameRepository
     * @param UserManager    $userManager
     */
    public function __construct(EntityManager $em,
        GameRepository $gameRepository, UserManager $userManager,
        ShufflingMachine $shufflingMachine
    ) {
        $this->em = $em;
        $this->gameRepository = $gameRepository;
        $this->userManager = $userManager;
        $this->shufflingMachine = $shufflingMachine;
    }

    /**
     * @return Game
     */
    public function create()
    {
        $game = new Game();
        $game->setUser($this->userManager->getUser());
        $userHand = new Hand();
        $userHand
            ->setIsUser()
            ->addCard($this->shufflingMachine->getRandomCard())
            ->addCard($this->shufflingMachine->getRandomCard());
        $game->setUserHand($userHand);

        $dealerHand = new Hand();
        $dealerHand
            ->setIsDealer()
            ->addCard($this->shufflingMachine->getRandomCard())
            ->addCard($this->shufflingMachine->getRandomCard());
        $game->setDealerHand($dealerHand);

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }

    /**
     * @return Game|null
     */
    public function previous()
    {
        $user = $this->userManager->getUser();
        $game = $this->gameRepository->findOneBy(
            array(
                'user'     => $user,
                'finished' => null,
            )
        );

        return $game;
    }

    /**
     * @param Game $game
     *
     * @return Game
     */
    public function twist(Game $game)
    {
        $game->getUserHand()->addCard(
            $this->shufflingMachine->getRandomCard()
        );

        $this->em->flush();

        return $game;
    }

    /**
     * @param Game $game
     *
     * @return Game
     */
    public function stick(Game $game)
    {
        if ($game->getFinished()) {
            return $game;
        }

        while ($game->getDealerHand()->getTotalScore() < Game::DEALER_STOP_AT) {
            $game->getDealerHand()->addCard(
                $this->shufflingMachine->getRandomCard()
            );
        }

        $this->finish($game);

        return $game;
    }

    /**
     * @param Game $game
     *
     * @return Game
     */
    public function reset(Game $game)
    {
        $this->em->remove($game->getDealerHand());
        $this->em->remove($game->getUserHand());
        $this->em->flush();

        $game
            ->resetFinished()
            ->setDealerScore(null)
            ->setUserScore(null);

        $userHand = new Hand();
        $userHand
            ->setIsUser()
            ->addCard($this->shufflingMachine->getRandomCard())
            ->addCard($this->shufflingMachine->getRandomCard());
        $game->setUserHand($userHand);

        $dealerHand = new Hand();
        $dealerHand
            ->setIsDealer()
            ->addCard($this->shufflingMachine->getRandomCard())
            ->addCard($this->shufflingMachine->getRandomCard());
        $game->setDealerHand($dealerHand);

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }

    /**
     * @param Game $game
     *
     * @return Game
     */
    private function finish(Game $game)
    {
        $game
            ->setFinished(new \DateTime())
            ->setDealerScore($game->getDealerHand()->getTotalScore())
            ->setUserScore($game->getUserHand()->getTotalScore());

        $this->em->persist($game);
        $this->em->flush();

        return $game;
    }
}
