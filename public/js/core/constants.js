(function () {
    'use strict';

    angular
        .module('app')
        .constant('constantsCore', {
            MESSAGES: {
                welcome: 'Welcome to Blackjack.',
                replay: 'Do you want replay?',
                win: 'You win!',
                draw: 'Draw!',
                lose: 'You lose!',
                unknownResult: 'Unknown'
            },
            GAME: {
                blackjackScore: 21
            },
            GAME_RESULT_CODE: {
                win: 1,
                draw: 0,
                lose: -1
            }
        });
})();
