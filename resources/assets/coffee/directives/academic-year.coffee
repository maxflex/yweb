angular.module('App').directive 'academic', ->
    restrict: 'E'
    template: "{{ year }}–{{ +(year) + 1 }}"
    scope:
        year: '='
