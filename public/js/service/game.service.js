(function () {
    'use strict';

    angular
        .module('app')
        .factory('gameService', gameService);

    gameService.$inject = ['$http', 'constantsCore'];

    function gameService($http, constantsCore) {
        return {
            getPreviousGame: getPreviousGame,
            startNewGame: startNewGame,
            twist: twist,
            stick: stick,
            reset: reset,
            getGameResult: getGameResult
        };

        function getPreviousGame() {
            return $http({
                url: '/api/game/previous',
                method: 'GET'
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(errorHandler);
        }

        function startNewGame() {
            return $http({
                url: '/api/game/start',
                method: 'POST',
                params: {}
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(errorHandler);
        }

        function twist(game) {
            return $http({
                url: '/api/game/' + game.id + '/twist',
                method: 'POST'
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(errorHandler);
        }

        function stick(game) {
            return $http({
                url: '/api/game/' + game.id + '/stick',
                method: 'PUT'
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(errorHandler);
        }

        function reset(game) {
            return $http({
                url: '/api/game/' + game.id + '/reset',
                method: 'POST'
            })
                .then(function (response) {
                    return response.data;
                })
                .catch(errorHandler);
        }

        /**
         * @private
         * @param {{}} error
         */
        function errorHandler(error) {
            console.log(error.statusText);
        }

        /**
         * @param {{dealerScore, userScore}} game
         * @returns {Number} - Game result code
         */
        function getGameResult(game) {
            var isDealerBusts = game.dealerScore > constantsCore.GAME.blackjackScore;
            var isUserBusts = game.userScore > constantsCore.GAME.blackjackScore;

            if (game.dealerScore === game.userScore) {
                return constantsCore.GAME_RESULT_CODE.draw;
            }

            if (isDealerBusts && !isUserBusts) {
                return constantsCore.GAME_RESULT_CODE.win;
            }

            if (isDealerBusts && isUserBusts && (game.userScore < game.dealerScore)) {
                return constantsCore.GAME_RESULT_CODE.win;
            }

            if (!isDealerBusts && !isUserBusts && (game.userScore > game.dealerScore)) {
                return constantsCore.GAME_RESULT_CODE.win;
            }

            return constantsCore.GAME_RESULT_CODE.lose;
        }
    }
})();