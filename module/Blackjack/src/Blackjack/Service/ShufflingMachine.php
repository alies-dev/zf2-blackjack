<?php

namespace Blackjack\Service;

use Blackjack\Entity\Card;

/**
 * Class ShufflingMachine
 */
class ShufflingMachine
{
    /**
     * Uses infinity deck/set
     *
     * @return Card
     */
    public function getRandomCard()
    {
        $suites = Card::getAllowedSuites();
        $suitIndex = array_rand($suites);
        $ranks = Card::getAllowedValues();
        $rankIndex = array_rand($ranks);

        return Card::fromSuitAndRank($suites[$suitIndex], $ranks[$rankIndex]);
    }
}
