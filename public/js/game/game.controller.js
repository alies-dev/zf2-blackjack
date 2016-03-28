(function () {
    'use strict';

    angular
        .module('app')
        .controller('GameController', GameController);

    GameController.$inject = ['gameService', 'userService', 'constantsCore'];

    function GameController(gameService, userService, constantsCore) {
        var vm = this;
        vm.game = null;
        vm.scores = {
            show: false,
            games: []
        };

        vm.doStart = doStart;
        vm.doReset = doReset;
        vm.doTwist = doTwist;
        vm.doStick = doStick;
        vm.doShowScores = doShowScores;
        vm.getBackgroundPosition = getBackgroundPosition;
        vm.getGameTextResult = getGameTextResult;

        activate();

        /**
         * on init action
         * @private
         */
        function activate() {
            getScores()
                .then(function (scores) {
                    vm.scores = userService.getScoresWithStatistics(scores);
                    vm.scores.show = false;

                    return vm.scores;
                })
                .then(function () {
                    return getPreviousGame();
                })
                .then(function (game) {
                    if (game) {
                        vm.message = null;
                        vm.game = game;
                    } else {
                        vm.message = constantsCore.MESSAGES.welcome;
                    }
                })
                .catch(errorHandler);
        }

        /**
         * @private
         * @returns {{Promise}} - promise returns {Game} - previous game (if exist)
         * or null (if no any unfinished games found)
         */
        function getPreviousGame() {
            return gameService.getPreviousGame()
                .then(function (responseBody) {
                    return (responseBody && responseBody.success) ? responseBody.data.game : null;
                })
                .catch(errorHandler);
        }

        /**
         * @private
         * @returns {{}} - game Object
         */
        function startNewGame() {
            return gameService.startNewGame()
                .then(function (responseBody) {
                    return responseBody.data.game;
                })
                .catch(errorHandler);
        }

        /**
         * @public
         * @returns {{Promise}} - promise returns {Game}
         */
        function doReset(game) {
            //to get new games array without resetted game
            return getScores()
                .then(function (scores) {
                    return vm.scores = scores;
                })
                .then(function () {
                    return gameService.reset(game);
                })
                .then(function (responseBody) {
                    vm.game = responseBody.data.game;
                    vm.message = null;

                    return vm.game;
                })

                .catch(errorHandler);
        }

        /**
         * @private
         * @returns {{Promise}} - promise returns {Scores}
         */
        function getScores() {
            return userService.getScores()
                .then(function (responseBody) {
                    return responseBody.data.scores;
                })
                .catch(errorHandler);
        }

        /**
         * Get scores and update the page
         * @public
         * @returns {{Promise}} - promise returns {Scores}
         */
        function doShowScores() {
            vm.message = null;

            return getScores()
                .then(function (scores) {
                    vm.scores = scores;
                    vm.scores.show = true;

                    return vm.scores;
                })
                .catch(errorHandler);
        }

        /**
         * @public
         * @returns {{Promise}} - promise returns {Game}
         */
        function doStart() {
            return startNewGame()
                .then(function (game) {
                    vm.message = null;
                    vm.game = game;

                    return game;
                })
                .catch(errorHandler);
        }

        /**
         * @public
         * @returns {{Promise}} - promise returns {Game}
         */
        function doTwist() {
            return gameService.twist(vm.game)
                .then(function (response) {
                    vm.game = response.data.game;

                    return response.data.game;
                })
                .catch(errorHandler);
        }

        /**
         * @public
         * @returns {{Promise}} - promise returns {Game}
         */
        function doStick() {
            return gameService.stick(vm.game)
                .then(function (responseBody) {
                    if (!responseBody.data || !responseBody.success) {
                        throw new Error('Invalid response');
                    }

                    vm.message = constantsCore.MESSAGES.replay;
                    vm.game = responseBody.data.game;
                    addGameToScores(vm.game);

                    return vm.game;
                })
                .catch(errorHandler);
        }

        /**
         * @public
         * @param {{Game}} game
         * @returns {String}
         */
        function getGameTextResult(game) {
            switch (gameService.getGameResult(game)) {
                case (constantsCore.GAME_RESULT_CODE.win):
                    return constantsCore.MESSAGES.win;
                case (constantsCore.GAME_RESULT_CODE.draw):
                    return constantsCore.MESSAGES.draw;
                case (constantsCore.GAME_RESULT_CODE.lose):
                    return constantsCore.MESSAGES.lose;
                default:
                    return constantsCore.MESSAGES.unknownResult;
            }
        }

        /**
         * @private
         * @param {{}} game
         */
        function addGameToScores(game) {
            vm.scores.games.push(game);
            vm.scores = userService.getScoresWithStatistics(vm.scores);
        }

        /**
         * @private
         * @param {Error} error
         */
        function errorHandler(error) {
            console.log(error);
        }

        /**
         * @public
         * @param {{Card}} card
         * @returns {string} - offset for card's background
         */
        function getBackgroundPosition(card) {
            var horizontalInterval = -73;
            var horizontalFactor = 0;
            var horizontalValue = -1;
            switch (card.rank) {
                case'A':
                    horizontalFactor = 0;
                    break;
                case '2':
                    horizontalFactor = 1;
                    break;
                case '3':
                    horizontalFactor = 2;
                    break;
                case '4':
                    horizontalFactor = 3;
                    break;
                case '5':
                    horizontalFactor = 4;
                    break;
                case '6':
                    horizontalFactor = 5;
                    break;
                case '7':
                    horizontalFactor = 6;
                    break;
                case '8':
                    horizontalFactor = 7;
                    break;
                case '9':
                    horizontalFactor = 8;
                    break;
                case '10':
                    horizontalFactor = 9;
                    break;
                case 'J':
                    horizontalFactor = 10;
                    break;
                case 'Q':
                    horizontalFactor = 11;
                    break;
                case 'K':
                    horizontalFactor = 12;
                    break;
                default:
                    horizontalFactor = 0;
                    break;
            }
            horizontalValue += horizontalInterval * horizontalFactor;

            var verticalInterval = -98;
            var verticalFactor = 0;
            var verticalValue = -1;
            switch (card.suit) {
                case'C':
                    verticalFactor = 0;
                    break;
                case 'D':
                    verticalFactor = 1;
                    break;
                case 'H':
                    verticalFactor = 2;
                    break;
                case 'S':
                    verticalFactor = 3;
                    break;
                default:
                    break;
            }
            verticalValue += verticalInterval * verticalFactor;

            return horizontalValue + 'px ' + verticalValue + 'px';
        }
    }
})();
