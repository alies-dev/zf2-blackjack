(function () {
    'use strict';

    angular
        .module('app')
        .filter('score', score);

    score.$inject = [];

    function score() {
        return filter;

        /**
         * @param {Number} score
         */
        function filter(score) {
            if (!score) {
                return '?';
            }

            return score;
        }
    }
})();
