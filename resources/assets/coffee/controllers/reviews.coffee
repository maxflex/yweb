angular
    .module 'App'
    .constant 'REVIEWS_PER_PAGE', 5
    .controller 'Reviews', ($scope, $timeout, $http, Subjects, StreamService) ->
        bindArguments($scope, arguments)

        $timeout ->
            $scope.reviews = []
            $scope.page = 1
            $scope.has_more_pages = true
            search()

        $scope.popup = (index) ->
            $scope.show_review = index

        $scope.nextPage = ->
            StreamService.run('all_reviews', 'more')
            $scope.page++
            search()

        # $scope.$watch 'page', (newVal, oldVal) -> $.cookie('page', $scope.page) if newVal isnt undefined

        search = ->
            $scope.searching = true
            $http.get('/api/reviews?page=' + $scope.page).then (response) ->
                console.log(response)
                $scope.searching = false
                $scope.reviews = $scope.reviews.concat(response.data.reviews)
                $scope.has_more_pages = response.data.has_more_pages
                # if $scope.mobile then $timeout -> bindToggle()
