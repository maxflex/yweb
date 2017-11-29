angular
    .module 'App'
    .controller 'Programs', ($scope, $timeout) ->
        $timeout ->
            $scope.ready = true
        , 300