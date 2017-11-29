angular.module("App", ['ngResource', 'angular-ladda', 'angularFileUpload', 'angular-toArrayFilter', 'thatisuday.ng-image-gallery', 'ngSanitize'])
    .config [
        'ngImageGalleryOptsProvider',
        (ngImageGalleryOptsProvider) ->
            ngImageGalleryOptsProvider.setOpts
                bubbles         :   false
                bubbleSize      :   100
    ]
    .config [
        '$compileProvider'
        ($compileProvider) ->
            $compileProvider.aHrefSanitizationWhitelist /^\s*(https?|ftp|mailto|chrome-extension|sip):/
    ]
    .config (laddaProvider) ->
        laddaProvider.setOption
            spinnerColor: '#83b060'
    .filter 'cut', ->
        (value, wordwise, max, tail = '') ->
            return '' if not value
            max = parseInt(max, 10)
            return value if not max
            return value if value.length <= max
            value = value.substr(0, max)
            if wordwise
                lastspace = value.lastIndexOf(' ')
                if lastspace isnt -1
                  if value.charAt(lastspace-1) is '.' || value.charAt(lastspace-1) is ','
                    lastspace = lastspace - 1
                  value = value.substr(0, lastspace)
            return value + tail
    .filter 'hideZero', ->
        (item) ->
            if item > 0 then item else null
    .run ($rootScope, $q, StreamService) ->
        $rootScope.streamLink = streamLink
        $rootScope.StreamService = StreamService

        # отвечает за загрузку данных
        $rootScope.dataLoaded = $q.defer()
        # конец анимации front-end загрузки и rebind маск
        $rootScope.frontendStop = (rebind_masks = true) ->
            $rootScope.frontend_loading = false
            $rootScope.dataLoaded.resolve(true)
            rebindMasks() if rebind_masks

        $rootScope.range = (min, max, step) ->
          step = step or 1
          input = []
          i = min
          while i <= max
            input.push i
            i += step
          input

          # skip_values – какие значения в enum пропускать
          # allowed_user_ids – пользователи, которым разрешено выбирать значения
          # recursion – функция была запущена рекурсивно (внизу)
        $rootScope.toggleEnum = (ngModel, status, ngEnum, skip_values = [], allowed_user_ids = [], recursion = false) ->
            # если установлено значение, которое пропускается для обычных пользователей,
            # то запрещать его смену
            return if not recursion and parseInt(ngModel[status]) in skip_values and $rootScope.$$childHead.user.id not in allowed_user_ids

            statuses = Object.keys(ngEnum)
            status_id = statuses.indexOf ngModel[status].toString()
            status_id++
            status_id = 0 if status_id > (statuses.length - 1)
            ngModel[status] = statuses[status_id]
            # if in skip_values
            $rootScope.toggleEnum(ngModel, status, ngEnum, skip_values, allowed_user_ids, true) if status_id in skip_values and $rootScope.$$childHead.user.id not in allowed_user_ids

        # обновить + ждать ответа от сервера
        $rootScope.toggleEnumServer = (ngModel, status, ngEnum, Resource) ->
            statuses = Object.keys(ngEnum)
            status_id = statuses.indexOf ngModel[status].toString()
            status_id++
            status_id = 0 if status_id > (statuses.length - 1)

            update_data = {id: ngModel.id}
            update_data[status] = status_id

            Resource.update update_data, ->
                ngModel[status] = statuses[status_id]

        $rootScope.formatDateTime = (date) ->
            moment(date).format "DD.MM.YY в HH:mm"

        $rootScope.formatDate = (date, full_year = false) ->
            return '' if not date
            moment(date).format "DD.MM.YY" + (if full_year then "YY" else "")

        $rootScope.formatDateFull = (date) ->
            moment(date).format "D MMMM YYYY"

        $rootScope.dialog = (id) ->
            $("##{id}").modal 'show'
            return

        $rootScope.closeDialog = (id) ->
            $("##{id}").modal 'hide'
            return

        $rootScope.onEnter = (event, fun, prevent_default = true) ->
            event.preventDefault() if prevent_default
            fun() if event.keyCode is 13

        $rootScope.ajaxStart = ->
            ajaxStart()
            $rootScope.saving = true

        $rootScope.ajaxEnd = ->
            ajaxEnd()
            $rootScope.saving = false

        $rootScope.findById = (object, id) ->
            _.findWhere(object, {id: parseInt(id)})

        # prop2 – второй уровень вложенности
        $rootScope.total = (array, prop, prop2 = false) ->
            sum = 0
            $.each array, (index, value) ->
                v = value[prop]
                v = v[prop2] if prop2
                sum += v
            sum

        # сколько лет прошло с указанной даты
        $rootScope.yearsPassed = (year) ->
            moment().format("YYYY") - year

        $rootScope.deny = (ngModel, prop) ->
            ngModel[prop] = +(!ngModel[prop])

        $rootScope.closestMetro = (markers) ->
            closest_metro = markers[0].metros[0]
            markers.forEach (marker) ->
                marker.metros.forEach (metro) ->
                    closest_metro = metro if metro.minutes < closest_metro.minutes
            closest_metro.station.title

        $rootScope.closestMetros = (markers) ->
            closest_metros = []
            markers.forEach (marker, index) ->
                closest_metros[index] = marker.metros[0]
                marker.metros.forEach (metro) ->
                    closest_metros[index] = metro if metro.minutes < closest_metros[index].minutes
            closest_metros

        $rootScope.photoUrl = (tutor) ->
            if tutor and tutor.photo_exists
                "https://lk.ege-repetitor.ru/img/tutors/#{tutor.id}.#{tutor.photo_extension}"
            else
                "https://lk.ege-repetitor.ru/img/tutors/no-profile-img.gif"

        $rootScope.fullName = (tutor) ->
            return tutor.last_name + ' ' + tutor.first_name + ' ' + tutor.middle_name

        $rootScope.objectLength = (obj) -> Object.keys(obj).length

        $rootScope.shortenGrades = (tutor) ->
            if tutor.grades.length <= 3
                grades = _.clone tutor.grades
                if (grades.length > 1)
                    last_grade = grades.pop()
                grade_string = grades.join ', '
                grade_string += ' и ' + last_grade if last_grade
                return grade_string + if last_grade then ' классы' else ' класс'
            else
                a = _.clone tutor.grades
                return if a.length < 1
                limit = a.length - 1
                combo_end = -1
                pairs = []
                i = 0
                while i <= limit
                    combo_start = parseInt(a[i])
                    if combo_start > 11
                        i++
                        combo_end = -1
                        pairs.push combo_start
                        continue

                    if combo_start <= combo_end
                        i++
                        continue

                    j = i
                    while j <= limit
                        combo_end = parseInt(a[j])
                        # если уже начинает искать по студентам
                        break if combo_end >= 11
                        break if parseInt(a[j + 1]) - combo_end > 1
                        j++
                    if combo_start != combo_end
                        pairs.push combo_start + '–' + combo_end + ' классы'
                    else
                        pairs.push combo_start + ' класс'
                    i++
                return pairs.join ', '

        $rootScope.countObj = (obj) ->
            Object.keys(obj).length

        $rootScope.formatBytes = (bytes) ->
          if bytes < 1024
            bytes + ' Bytes'
          else if bytes < 1048576
            (bytes / 1024).toFixed(1) + ' KB'
          else if bytes < 1073741824
            (bytes / 1048576).toFixed(1) + ' MB'
          else
            (bytes / 1073741824).toFixed(1) + ' GB'
