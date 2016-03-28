<?php

namespace Blackjack\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Entity(repositoryClass="Blackjack\Repository\GameRepository")
 * @ORM\Table(name="game")
 * @ORM\HasLifecycleCallbacks
 */
class Game
{
    const BLACKJACK_SCORE = 21;
    const DEALER_STOP_AT = 17;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="\Blackjack\Entity\User", inversedBy="games")
     */
    private $user;

    /**
     * @var Hand
     * @ORM\OneToOne(targetEntity="\Blackjack\Entity\Hand",
     *     mappedBy="userGame", cascade={"persist", "remove"})
     */
    private $userHand;

    /**
     * @var Hand
     * @ORM\OneToOne(targetEntity="\Blackjack\Entity\Hand",
     *     mappedBy="dealerGame", cascade={"persist", "remove"})
     */
    private $dealerHand;

    /**
     * @var int
     * @ORM\Column(name="user_score", type="integer", nullable=true)
     */
    private $userScore;

    /**
     * @var int
     * @ORM\Column(name="dealer_score", type="integer", nullable=true)
     */
    private $dealerScore;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="finished", type="datetime", nullable=true)
     */
    private $finished;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return Game
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Hand
     */
    public function getUserHand()
    {
        return $this->userHand;
    }

    /**
     * @param Hand $userHand
     *
     * @return Game
     */
    public function setUserHand(Hand $userHand)
    {
        $userHand->setUserGame($this);
        $this->userHand = $userHand;

        return $this;
    }

    /**
     * @return Hand
     */
    public function getDealerHand()
    {
        return $this->dealerHand;
    }

    /**
     * @param Hand $dealerHand
     *
     * @return Game
     */
    public function setDealerHand(Hand $dealerHand)
    {
        $dealerHand->setDealerGame($this);
        $this->dealerHand = $dealerHand;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserScore()
    {
        return $this->userScore;
    }

    /**
     * @param int $userScore
     *
     * @return Game
     */
    public function setUserScore($userScore)
    {
        $this->userScore = $userScore;

        return $this;
    }

    /**
     * @return int
     */
    public function getDealerScore()
    {
        return $this->dealerScore;
    }

    /**
     * @param int $dealerScore
     *
     * @return Game
     */
    public function setDealerScore($dealerScore)
    {
        $this->dealerScore = $dealerScore;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return Game
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param \DateTime $finished
     *
     * @return Game
     */
    public function setFinished(\DateTime $finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * @return Game
     */
    public function resetFinished()
    {
        $this->finished = null;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->created = new \DateTime();
    }

    public function prepareForSerialization()
    {
        $userCards = array();
        /** @var Card $card */
        foreach ($this->getUserHand()->getCards() as $card) {
            $userCards[] = array(
                'id'   => $card->getId(),
                'suit' => $card->getSuit(),
                'rank' => $card->getRank(),
            );
        }

        $dealerCards = array();
        foreach ($this->getDealerHand()->getCards() as $index => $card) {
            $needsToHideCard = ($this->getFinished() === null) && ($index > 0);
            $dealerCards[] = array(
                'id'   => $card->getId(),
                'suit' => $needsToHideCard ? null : $card->getSuit(),
                'rank' => $needsToHideCard ? null : $card->getRank(),
            );
        }

        $result = array(
            'id'          => $this->getId(),
            'userId'      => $this->getUser()->getId(),
            'userScore'   => $this->getFinished()
                ? $this->getUserScore()
                : null,
            'dealerScore' => $this->getFinished()
                ? $this->getDealerScore()
                : null,
            'finished'    => $this->getFinished()
                ? $this->getFinished()->getTimestamp()
                : null,
            'userHand'    => array(
                'id'            => $this->getUserHand()->getId(),
                'cards'         => $userCards,
                'score' => $this->getUserHand()->getTotalScore(),
            ),
            'dealerHand'  => array(
                'id'            => $this->getDealerHand()->getId(),
                'cards'         => $dealerCards,
                'score' => $this->getFinished()
                    ? $this->getDealerHand()->getTotalScore()
                    : null,
            ),
        );

        return $result;
    }
}
