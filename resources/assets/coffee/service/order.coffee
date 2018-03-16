angular.module 'App'
    .service 'OrderService', ($http, Order) ->
        @order =
            name: ''
            comment: ''
            phone: ''
            yacht_id: null
        @sending = false
        @sent = false

        this.send = ->
            @sending = true
            Order.save @order, =>
                closeModal()
                @sending = false
                @sent = true
                # $scope.sent_ids.push($scope.order.yacht_id)
                # $.cookie('sent_ids', JSON.stringify($scope.sent_ids))

        this