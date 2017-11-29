angular
    .module 'App'
    .controller 'Order', ($scope, $timeout, $http, Grades, Subjects, Request, StreamService) ->
        bindArguments($scope, arguments)
        $timeout ->
            # @todo: client_id, referer, referer_url, user agent
            $scope.order = {}
            $scope.popups = {}

        $scope.filterPopup = (popup) ->
            $scope.popups[popup] = true

        $scope.select = (field, value) ->
            $scope.order[field] = value
            $scope.popups = {}

        $scope.request = ->
            $scope.sending = true
            $scope.errors = {}
            Request.save $scope.order, ->
                StreamService.run('client_request', streamString())
                dataLayerPush
                    event: 'purchase'
                    ecommerce:
                        currencyCode: 'RUB'
                        purchase:
                            actionField:
                                id: googleClientId()
                            products: [
                                # класс
                                brand: $scope.order.grade
                                # предметы_филиал
                                category: (if $scope.order.subjects then $scope.order.subjects.sort().join(',') else '') + '_' + $scope.order.branch_id
                                quantity: 1
                            ]
                $scope.sending = false
                $scope.sent = true
                $('body').animate scrollTop: $('.header').offset().top
            , (response) ->
                $scope.sending = false
                angular.forEach response.data, (errors, field) ->
                    $scope.errors[field] = errors
                    selector = "[ng-model$='#{field}']"
                    $('html,body').animate({scrollTop: $("input#{selector}, textarea#{selector}").first().offset().top}, 0)
                    input = $("input#{selector}, textarea#{selector}")
                    input.focus()
                    input.notify errors[0], notify_options if isMobile
                StreamService.run('client_request_attempt', response.data[Object.keys(response.data)[0]][0])

        streamString = ->
            stream_string = []
            stream_string.push("class=#{$scope.order.grade}") if $scope.order.grade
            if $scope.order.subjects
                subj = []
                $scope.order.subjects.forEach (subject_id) -> subj.push(Subjects.short_eng[subject_id])
                stream_string.push("subjects=" + subj.join('+'))
            if $scope.order.branch_id
                stream_string.push("address=" + _.find($scope.Branches, {id: parseInt($scope.order.branch_id)}).code)
            stream_string.join('_')

        $scope.isSelected = (subject_id) ->
            return false if not ($scope.order and $scope.order.subjects)
            $scope.order.subjects.indexOf(subject_id) isnt -1

        $scope.selectSubject = (subject_id) ->
            $scope.order.subjects = [] if not $scope.order.subjects
            if $scope.isSelected subject_id
                $scope.order.subjects = _.without $scope.order.subjects, subject_id
            else
                $scope.order.subjects.push subject_id

        $scope.selectedSubjectsList = ->
            return false if not $scope.order?.subjects?.length

            subjects = []
            for subject_id in $scope.order.subjects
                subjects.push $scope.Subjects[subject_id].name

            subjects.join ', '