angular.module 'App'
    .directive 'plural', ->
        restrict: 'E'
        scope:
            count: '='      # кол-во
            type: '@'       # тип plural age | student | ...
            noneText: '@'   # текст, если кол-во равно нулю
        templateUrl: '/directives/plural'
        controller: ($scope, $element, $attrs, $timeout) ->
            $scope.textOnly = $attrs.hasOwnProperty('textOnly')
            $scope.hideZero = $attrs.hasOwnProperty('hideZero')

            $scope.when =
                'yacht': ['яхта', 'яхты', 'яхт']
