angular.module 'App'
    .directive 'digitsOnly', ->
        restrics:  'A'
        require: 'ngModel'
        link: ($scope, $element, $attr, $ctrl) ->
            filter = (value) ->
                return undefined if not value
                new_value = value.replace /[^0-9]/g, ''
                if new_value isnt value
                    $ctrl.$setViewValue new_value
                    $ctrl.$render()

                value

            $ctrl.$parsers?.push filter
