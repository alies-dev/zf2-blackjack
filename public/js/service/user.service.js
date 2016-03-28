(function () {
    'use strict';

    angular
        .module('app')
        .factory('userService', userService);

    userService.$inject = ['$http', 'gameService'];

    function userService($http, gameService) {
        return {
            getScores: getScores,
            getScoresWithStatistics: getScoresWithStatistics
        };

        /**
         * @public
         * @returns {{}} - scores Object
         */
        function getScores() {
            return $http({
                url: '/api/user/scores',
                method: 'GET'
            })
                .then(function (response) {
                    response.data.data.scores = getScoresWithStatistics(response.data.data.scores);

                    return response.data;
                })
                .catch(errorHandler);
        }

        /**
         * @public
         * @param {{totalGames, totalWins, totalDraws, totalLooses, games[]}} scores
         * @returns {*}
         */
        function getScoresWithStatistics(scores) {
            scores.totalGames = scores.games.length;
            scores.totalWins = scores.games.filter(function (game) {
                return gameService.getGameResult(game) === 1;
            }).length;
            scores.totalDraws = scores.games.filter(function (game) {
                return gameService.getGameResult(game) === 0;
            }).length;
            scores.totalLooses = scores.games.filter(function (game) {
                return gameService.getGameResult(game) === -1;
            }).length;

            scores.percentWins = scores.totalGames === 0 ? 0 : (scores.totalWins * 100 / scores.totalGames);
            scores.percentDraws = scores.totalGames === 0 ? 0 : (scores.totalDraws * 100 / scores.totalGames);
            scores.percentLooses = scores.totalGames === 0 ? 0 : (scores.totalLooses * 100 / scores.totalGames);

            return scores;
        }

        /**
         * @private
         * @param {Error} error
         */
        function errorHandler(error) {
            console.log(error.statusText);
        }
    }
})();