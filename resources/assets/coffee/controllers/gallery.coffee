angular
    .module 'App'
    .controller 'Gallery', ($scope, $timeout, StreamService) ->
        bindArguments($scope, arguments)

        angular.element(document).ready ->
            $scope.all_photos = []
            _.each $scope.groups, (group) ->
                $scope.all_photos = $scope.all_photos.concat group.photo

        $scope.openPhoto = (photo_id) ->
            StreamService.run('photogallery', "open_#{photo_id}")
            $scope.gallery.open($scope.getFlatIndex(photo_id))

        $scope.getFlatIndex = (photo_id) ->
            _.findIndex $scope.all_photos, id: photo_id
