angular
    .module 'App'
    .constant 'REVIEWS_PER_PAGE', 5
    .controller 'Landing', ($scope, $timeout, $http, StreamService, Tutor, REVIEWS_PER_PAGE, Subjects) ->
        bindArguments($scope, arguments)

        $timeout ->
            initYoutube()
            initTutors()

        #
        # REVIEWS
        #
        $scope.initReviews = (count, min_score, grade, subject, university)->
            $scope.search_reviews =
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
            $scope.search_reviews.page++
            searchReviews()

        searchReviews = ->
            $scope.searching_reviews = true
            $http.get('/api/reviews/block?' + $.param($scope.search_reviews)).then (response) ->
                $scope.searching_reviews = false
                $scope.reviews = $scope.reviews.concat(response.data.reviews)
                $scope.search_reviews.ids = _.pluck($scope.reviews, 'id')
                $scope.has_more_pages = response.data.has_more_pages



        #
        # TUTORS
        #
        initTutors = ->
            $scope.tutors = []
            $scope.tutors_page = 1
            searchTutors()


        $scope.tutorReviews = (tutor, index) ->
            StreamService.run('tutor_reviews', tutor.id)
            if tutor.all_reviews is undefined
                tutor.all_reviews = Tutor.reviews
                    id: tutor.id
                , (response) ->
                    $scope.showMoreReviews(tutor)
            $scope.toggleShow(tutor, 'show_reviews', 'reviews', false)

        $scope.showMoreReviews = (tutor, index) ->
            tutor.reviews_page = if not tutor.reviews_page then 1 else (tutor.reviews_page + 1)
            from = (tutor.reviews_page - 1) * REVIEWS_PER_PAGE
            to = from + REVIEWS_PER_PAGE
            tutor.displayed_reviews = tutor.all_reviews.slice(0, to)
            # highlight('search-result-reviews-text')

        $scope.reviewsLeft = (tutor) ->
            return if not tutor.all_reviews or not tutor.displayed_reviews
            reviews_left = tutor.all_reviews.length - tutor.displayed_reviews.length
            if reviews_left > REVIEWS_PER_PAGE then REVIEWS_PER_PAGE else reviews_left


        $scope.nextTutorsPage = ->
            StreamService.run('load_more_tutors', $scope.tutors_page * 10)
            $scope.tutors_page++
            searchTutors()

        searchTutors = ->
            $scope.searching_tutors = true
            Tutor.search
                page: $scope.tutors_page
                take: 4
            , (response) ->
                # search_count++
                $scope.searching_tutors = false
                $scope.tutors_data = response
                $scope.tutors = $scope.tutors.concat(response.data)
                # if $scope.mobile then $timeout -> bindToggle()

        $scope.video = (tutor) ->
            StreamService.run('tutor_video', tutor.id)
            player.loadVideoById(tutor.video_link)
            player.playVideo()
            if isMobile
                $('.fullscreen-loading-black').css('display', 'flex')
            openModal('video')

        # длительность видео
        $scope.videoDuration = (tutor) ->
            duration = parseInt(tutor.video_duration)
            format = if duration >= 60 then 'm мин s сек' else 's сек'
            moment.utc(duration * 1000).format(format)

        # длительность видео в ISO
        $scope.videoDurationISO = (tutor) ->
            moment.duration(tutor.video_duration, 'seconds').toISOString()

        # stream if index isnt null
        $scope.toggleShow = (tutor, prop, iteraction_type, index = null) ->
            if tutor[prop]
                $timeout ->
                    tutor[prop] = false
                , if $scope.mobile then 400 else 0
            else
                tutor[prop] = true