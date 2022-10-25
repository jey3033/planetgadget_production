define([], function () {
    'use strict';

    return function (rules) {
        const currentRules = rules.getRules();
        rules.getRules = function () {
            return {'country_id': true , ...currentRules };
        }
        return rules;

    };
});
