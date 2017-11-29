angular
    .module 'App'
    .directive 'programItem', ->
        templateUrl: '/directives/program'
        scope:
            item:   '='
            level:  '=?'
            levelstring: '='
        controller: ($timeout, $element, $scope) ->
            $scope.level = 0 if not $scope.level

            $scope.getChildLevelString = (child_index) ->
                str = if $scope.levelstring then $scope.levelstring else ''
                str + (child_index + 1) + '.'