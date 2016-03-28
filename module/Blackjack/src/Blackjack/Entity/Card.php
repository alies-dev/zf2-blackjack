<?php

namespace Blackjack\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Card
 * @ORM\Entity()
 * @ORM\Table(name="card")
 * @ORM\HasLifecycleCallbacks
 */
class Card
{
    const SUIT_CLUBS = 'C'; //♣
    const SUIT_DIAMONDS = 'D'; //♦
    const SUIT_HEARTS = 'H'; //♥
    const SUIT_SPADES = 'S'; //♠

    const RANK_ACE = 'A'; // 1 or 11
    const RANK_2 = '2';
    const RANK_3 = '3';
    const RANK_4 = '4';
    const RANK_5 = '5';
    const RANK_6 = '6';
    const RANK_7 = '7';
    const RANK_8 = '8';
    const RANK_9 = '9';
    const RANK_10 = '10';
    const RANK_JACK = 'J';
    const RANK_QUEEN = 'Q';
    const RANK_KING = 'K';

    const VALUE_ACE_SOFTHAND = 11;
    const VALUE_ACE_HARDHAND = 1;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Hand
     * @ORM\ManyToOne(targetEntity="\Blackjack\Entity\Hand",
     *     inversedBy="cards", cascade={"persist", "remove"})
     */
    private $hand;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $suit;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $rank;

    /**
     * @param string $suit
     * @param string $rank
     *
     * @return Card static
     */
    public static function fromSuitAndRank($suit, $rank)
    {
        $card = new self();
        $card
            ->setSuit($suit)
            ->setRank($rank);

        return $card;
    }

    /**
     * @return array
     */
    public static function getAllowedSuites()
    {
        return array(
            self::SUIT_CLUBS,
            self::SUIT_DIAMONDS,
            self::SUIT_HEARTS,
            self::SUIT_SPADES,
        );
    }

    /**
     * @return array
     */
    public static function getAllowedValues()
    {
        return array(
            self::RANK_ACE,
            self::RANK_2,
            self::RANK_3,
            self::RANK_4,
            self::RANK_5,
            self::RANK_6,
            self::RANK_7,
            self::RANK_8,
            self::RANK_9,
            self::RANK_10,
            self::RANK_JACK,
            self::RANK_QUEEN,
            self::RANK_KING,
        );
    }

    /** @return int|null */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Hand
     */
    public function getHand()
    {
        return $this->hand;
    }

    /**
     * @param Hand $hand
     * @return Card
     */
    public function setHand($hand)
    {
        $this->hand = $hand;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuit()
    {
        return $this->suit;
    }

    /**
     * @param string $suit
     * @return Card
     */
    public function setSuit($suit)
    {
        $this->suit = $suit;

        return $this;
    }

    /**
     * @param string $rank
     * @return Card
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }
}
