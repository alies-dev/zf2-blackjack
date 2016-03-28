<?php

namespace Blackjack\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Hand
 *
 * @ORM\Entity()
 * @ORM\Table(name="hand")
 * @ORM\HasLifecycleCallbacks
 */
class Hand
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Game
     * @ORM\OneToOne(targetEntity="\Blackjack\Entity\Game", inversedBy="dealerHand")
     * @ORM\JoinColumn(name="dealer_game_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $dealerGame;

    /**
     * @var Game
     * @ORM\OneToOne(targetEntity="\Blackjack\Entity\Game", inversedBy="userHand")
     * @ORM\JoinColumn(name="user_game_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $userGame;

    /**
     * @var bool
     */
    private $dealer;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\Blackjack\Entity\Card", mappedBy="hand",
     *     cascade={"persist", "remove"}))
     */
    private $cards;

    /**
     * Hand constructor.
     */
    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Game $game
     *
     * @return Hand
     */
    public function setUserGame(Game $game)
    {
        $this->userGame = $game;

        return $this;
    }

    /**
     * @return Game
     */
    public function getUserGame()
    {
        return $this->userGame;
    }

    /**
     * @param Game $game
     *
     * @return Hand
     */
    public function setDealerGame(Game $game)
    {
        $this->dealerGame = $game;

        return $this;
    }

    /**
     * @return Game
     */
    public function getDealerGame()
    {
        return $this->dealerGame;
    }

    /**
     * @return Hand
     */
    public function setIsDealer()
    {
        $this->dealer = true;

        return $this;
    }

    /**
     * @return Hand
     */
    public function setIsUser()
    {
        $this->dealer = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDealer()
    {
        return $this->dealer;
    }

    /**
     * @return bool
     */
    public function isPlayer()
    {
        return !$this->dealer;
    }

    /**
     * @param Card[] $cards
     *
     * @return Hand
     */
    public function setCards(array $cards)
    {
        $this->$this->cards->clear();

        foreach ($cards as $card) {
            $card->setHand($this);
        }

        $this->cards = $cards;

        return $this;
    }

    /**
     * @param Card $card
     *
     * @return Hand
     */
    public function addCard(Card $card)
    {
        $card->setHand($this);
        $this->cards->add($card);

        return $this;
    }

    /**
     * @return Card[]
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @return array
     */
    public function getFaces()
    {
        return array(Card::RANK_JACK, Card::RANK_QUEEN, Card::RANK_KING);
    }

    /**
     * @return int
     */
    public function getTotalScore()
    {
        /**
         * 'Sort': ace rank === 11, otherwise 'Hard': ace rank === 1
         */
        $isHardHand = false;
        $sumWithoutAces = 0;
        $acesCount = 0;

        foreach ($this->getCards() as $card) {
            if (in_array($card->getRank(), $this->getFaces())) {
                $sumWithoutAces += 10;
            } elseif (is_numeric($card->getRank())) {
                $sumWithoutAces += (int)$card->getRank();
            } else {
                $acesCount++;
            }
        }

        if ($sumWithoutAces + $acesCount * Card::VALUE_ACE_SOFTHAND > Game::BLACKJACK_SCORE) {
            $isHardHand = true;
        }

        return $isHardHand
            ? $sumWithoutAces + $acesCount * Card::VALUE_ACE_HARDHAND
            : $sumWithoutAces + $acesCount * Card::VALUE_ACE_SOFTHAND;
    }
}
