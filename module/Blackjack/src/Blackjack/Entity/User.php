<?php

namespace Blackjack\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="Blackjack\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="session_id", type="string")
     */
    private $sessionId;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="\Blackjack\Entity\Game", mappedBy="user",
     *     cascade={"persist", "remove"})
     */
    private $games;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     * @return User
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @param Game $game
     * @return $this
     */
    public function addGame(Game $game)
    {
        $this->games->add($game);

        return $this;
    }
}