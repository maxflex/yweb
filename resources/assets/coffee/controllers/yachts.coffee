angular
    .module 'App'
    .controller 'Yachts', ($scope, $timeout, $http, Yacht, Order) ->
        bindArguments($scope, arguments)

        $scope.popups = {}
        $scope.full_description = {}
        $scope.sent_ids = if $.cookie('sent_ids') then JSON.parse($.cookie('sent_ids')) else []

        # сколько загрузок было
        search_count = 0

        # страница поиска
        $timeout ->
            $scope.search = {}
            if not $scope.profilePage()
                $scope.filter()

        $scope.filterPopup = (popup) ->
            $scope.popups[popup] = true

        $scope.selectManufacturer = (manufacturer) ->
            $scope.search.manufacturer = manufacturer
            $scope.popups = {}
            $scope.filter()

        $scope.orderDialog = (yacht) ->
            $scope.sending = false
            $scope.order = {yacht_id: yacht.id}
            openModal('order')

        $scope.request = ->
            $scope.sending = true
            Order.save $scope.order, ->
                closeModal()
                $scope.sending = false
                $scope.sent_ids.push($scope.order.yacht_id)
                $.cookie('sent_ids', JSON.stringify($scope.sent_ids))

        # личная страница яхты?
        $scope.profilePage = ->
            RegExp(/^\/yachts\/[\d]+$/).test(window.location.pathname)

        # чтобы не редиректило в начале
        filter_used = false
        $scope.filter = ->
            $scope.yachts = []
            $scope.page = 1
            if filter_used
                filter()
            else
                filter()
                filter_used = true

        filter = ->
            search()

        $scope.nextPage = ->
            # StreamService.run('load_more_yachts', $scope.page * 10)
            $scope.page++
            search()

        search = ->
            $scope.searching = true
            Yacht.search
                filter_used: filter_used
                page: $scope.page
                search: $scope.search
            , (response) ->
                search_count++
                $scope.searching = false
                $scope.data = response
                $scope.yachts = $scope.yachts.concat(response.data)