angular.module('App')
    .factory 'Tutor', ($resource) ->
        $resource apiPath('tutors'), {id: '@id', type: '@type'},
            search:
                method: 'POST'
                url: apiPath('tutors', 'search')
            reviews:
                method: 'GET'
                isArray: true
                url: apiPath('reviews')

    .factory 'Yacht', ($resource) ->
        $resource apiPath('yachts'), {id: '@id', type: '@type'},
            search:
                method: 'POST'
                url: apiPath('yachts', 'search')

    .factory 'Request', ($resource) ->
        $resource apiPath('requests'), {id: '@id'}, updatable()

    .factory 'Cv', ($resource) ->
        $resource apiPath('cv'), {id: '@id'}, updatable()

    .factory 'Stream', ($resource) ->
        $resource apiPath('stream'), {id: '@id'}

apiPath = (entity, additional = '') ->
    "/api/#{entity}/" + (if additional then additional + '/' else '') + ":id"

updatable = ->
    update:
        method: 'PUT'
countable = ->
    count:
        method: 'GET'
