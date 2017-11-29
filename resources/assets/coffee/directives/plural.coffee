angular.module 'App'
    .directive 'plural', ->
        restrict: 'E'
        scope:
            count: '='      # кол-во
            type: '@'       # тип plural age | student | ...
            noneText: '@'   # текст, если кол-во равно нулю
        templateUrl: '/directives/plural'
        controller: ($scope, $element, $attrs, $timeout) ->
            $scope.textOnly = $attrs.hasOwnProperty('textOnly')
            $scope.hideZero = $attrs.hasOwnProperty('hideZero')

            $scope.when =
                'age': ['год', 'года', 'лет']
                'student': ['ученик', 'ученика', 'учеников']
                'minute': ['минуту', 'минуты', 'минут']
                'hour': ['час', 'часа', 'часов']
                'day': ['день', 'дня', 'дней']
                'rubbles': ['рубль', 'рубля', 'рублей']
                'client': ['клиент', 'клиента', 'клиентов']
                'mark': ['оценки', 'оценок', 'оценок']
                'review': ['отзыв', 'отзыва', 'отзывов']
                'request': ['заявка', 'заявки', 'заявок']
                'profile': ['анкета', 'анкеты', 'анкет']
                'address': ['адрес', 'адреса', 'адресов']
                'person': ['человек', 'человека', 'человек']
                'ton': ['тонна', 'тонны', 'тонн']
                'yacht': ['яхта', 'яхты', 'яхт']
                'photo': ['фото', 'фотографии', 'фотографий']
