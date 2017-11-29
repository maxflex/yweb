angular
    .module 'App'
    .controller 'Cv', ($scope, $timeout, $http, Subjects, Cv, StreamService) ->
        bindArguments($scope, arguments)

        $timeout ->
            $scope.cv = {}
            $scope.sent = false

        $scope.send = ->
            $scope.sending = true
            $scope.errors = {}
            Cv.save $scope.cv, ->
                StreamService.run('tutor_cv', streamString())
                $scope.sending = false
                $scope.sent = true
                $('body').animate scrollTop: $('.header').offset().top
                dataLayerPush
                    event: 'cv'
                    # ecommerce:
                    #     currencyCode: 'RUR'
                    #     purchase:
                    #         actionField:
                    #             id: googleClientId()
                    #         products: [
                    #             # класс
                    #             # brand: $scope.cv.grade
                    #             # предметы_филиал
                    #             category: (if $scope.cv.subjects then $scope.cv.subjects.sort().join(',') else '')
                    #             quantity: 1
                    #         ]
            , (response) ->
                $scope.sending = false
                angular.forEach response.data, (errors, field) ->
                    $scope.errors[field] = errors
                    selector = "[ng-model$='#{field}']"
                    $('html,body').animate({scrollTop: $("input#{selector}, textarea#{selector}").first().offset().top}, 0)
                    input = $("input#{selector}, textarea#{selector}")
                    input.focus()
                    input.notify errors[0], notify_options if isMobile

        streamString = ->
            stream_string = []
            # stream_string.push("class=#{$scope.cv.grade}") if $scope.cv.grade
            if $scope.cv.subjects
                subj = []
                $scope.cv.subjects.forEach (subject_id) -> subj.push(Subjects.short_eng[subject_id])
                stream_string.push("subjects=" + subj.join('+'))
            # if $scope.cv.branch_id
            #     stream_string.push("address=" + _.find($scope.Branches, {id: parseInt($scope.cv.branch_id)}).code)
            stream_string.join('_')

        $scope.isSelected = (subject_id) ->
                return false if not ($scope.cv and $scope.cv.subjects)
                $scope.cv.subjects.indexOf(subject_id) isnt -1

        $scope.selectSubject = (subject_id) ->
            $scope.cv.subjects = [] if not $scope.cv.subjects
            if $scope.isSelected subject_id
                $scope.cv.subjects = _.without $scope.cv.subjects, subject_id
            else
                $scope.cv.subjects.push subject_id

        $scope.selectedSubjectsList = ->
            return false if not $scope.cv?.subjects?.length
            subjects = []
            for subject_id in $scope.cv.subjects
                subjects.push $scope.Subjects[subject_id].name
            subjects.join ', '