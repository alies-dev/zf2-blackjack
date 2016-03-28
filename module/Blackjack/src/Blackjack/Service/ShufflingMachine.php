<?php

namespace Blackjack\Service;

use Blackjack\Entity\Card;

/**
 * Class ShufflingMachine
 */
class ShufflingMachine
{
    /**
     * @return Card
     * Uses infinity deck/set
     */
    public function getRandomCard()
    {
        $suites = Card::getAllowedSuites();
        $suitIndex = array_rand($suites);
        $ranks = Card::getAllowedValues();
        $rankIndex = array_rand($ranks);

        $card = new Card();
        $card
            ->setSuit($suites[$suitIndex])
            ->setRank($ranks[$rankIndex]);

        return $card;
    }
}
