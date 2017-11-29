angular
    .module 'App'
    .controller 'Empty', ($scope, $timeout, $filter, $http, StreamService) ->
        bindArguments($scope, arguments)

        # для развертывания предметов на главной странице
        # не нужно, чтобы отправлялось событие при свертывании
        $scope.expand_items = {}
        $scope.expandStream = (action, type) ->
            # type = $scope.$eval "'#{type}' | filter:cut:false:10"
            type = $filter('cut')(type, false, 20, '...')
            $scope.expand_items[type] = not $scope.expand_items[type]
            StreamService.run(action, type) if $scope.expand_items[type]

        $timeout ->
            # gallery methods
            $scope.gallery = {}

        $scope.initReviews = (count, min_score, grade, subject, university)->
            $scope.search =
                page: 1
                count: count
                min_score: min_score
                grade: grade
                subject: subject
                university: university
                ids: []
            $scope.reviews = []
            $scope.has_more_pages = true
            searchReviews()

        $scope.nextReviewsPage = ->
            StreamService.run('all_reviews', 'more')
            $scope.search.page++
            searchReviews()

        searchReviews = ->
            $scope.searching_reviews = true
            $http.get('/api/reviews/block?' + $.param($scope.search)).then (response) ->
                $scope.searching_reviews = false
                $scope.reviews = $scope.reviews.concat(response.data.reviews)
                $scope.search.ids = _.pluck($scope.reviews, 'id')
                $scope.has_more_pages = response.data.has_more_pages
