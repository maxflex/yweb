angular.module('App').directive 'ngMark', ->
    restrict: 'A'
    scope:
        word: '@'
    controller: ($scope, $element, $attrs, $timeout) ->
        $timeout ->
            $($element).mark $scope.word,
                separateWordSearch: true
                accuracy:
                    value: 'exactly'
                    limiters: ['!', '@', '#', '&', '*', '(', ')', '-', '–', '—', '+', '=', '[', ']', '{', '}', '|', ':', ';', '\'', '\"', '‘', '’', '“', '”', ',', '.', '<', '>', '/', '?']
