#= require imagesloaded.pkgd.min
#= require_self

angular.module 'app', ['ngAnimate']

.config ['$animateProvider', ($animateProvider) ->
  $animateProvider.classNameFilter /^((?!(no-animate)).)*$/
]

.directive 'lazyload', ->
  restrict: 'A'
  link: (scope, element) ->
    state = imagesLoaded element[0].children[0]
    state.on 'done', -> element[0].classList.remove 'unloaded'
    state.on 'fail', -> element[0].classList.add 'errored'
    return


.controller 'InstagramController', ['$scope', '$http', ($scope, $http) ->

  $scope.images = []

  # $http.get('/php/php-instagram/cache/fox_inn.json').then (response) ->
  $http.get('/php/php-instagram/feed.php').then (response) ->
    $scope.images = response.data.data
  , (response) ->
    $scope.error = true
    cosole.log response
  .finally ->
    $scope.isLoaded = true

]


.controller 'MenuController', ['$scope', '$http', ($scope, $http) ->

  $scope.menus = []
  $scope.filterBy = 'Christmas'

  # $http.get('/php/php-menus/cache/menus.json').then (response) ->
  $http.get('/php/php-menus/menus.php').then (response) ->
    $scope.menus = response.data
  , (response) ->
    $scope.error = true
    cosole.log response
  .finally ->
    $scope.isLoaded = true

  $scope.getDefaultMenus = ->
    return $scope.menus.filter((m) ->
      return m.file_name.indexOf($scope.filterBy) is -1
    )

  $scope.getSpecialMenus = ->
    return $scope.menus.filter((m) ->
      return m.file_name.indexOf($scope.filterBy) > -1
    )
]


.controller 'NoticeController', ['$scope', '$http', ($scope, $http) ->

  $http.get('/assets/notice/notice.jpg').then (response) ->
    $scope.notice = true
    $scope.noticeLoaded = true
  , (response) ->
    console.log 'no notice'

  $scope.hideNotice = -> $scope.notice = false

]


