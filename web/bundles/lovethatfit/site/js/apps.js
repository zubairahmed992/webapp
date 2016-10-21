var app = angular.module('myApp', []);
app.directive('validPasswordC', function() {
  return {
    require: 'ngModel',
    scope: {
      reference: '=validPasswordC'
    },
    link: function(scope, elm, attrs, ctrl) {
      ctrl.$parsers.unshift(function(viewValue, $scope) {
        var noMatch = viewValue != scope.reference
        ctrl.$setValidity('noMatch', !noMatch);
        return (noMatch)?noMatch:!noMatch;
      });

      scope.$watch("reference", function(value) {;
        ctrl.$setValidity('noMatch', value === ctrl.$viewValue);
      });
    }
  }
});
app.controller('mainController', function($scope, $http) {
    $scope.formData = {};
    $scope.loginData = {};
    $scope.submitForm = function(){
        var fdata = angular.toJson($scope.formData);
        console.log(fdata);
        $http({
          method  : 'POST',
          url     : '../visitor/update',
          data    : fdata, //forms user object
          headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
        }).success(function(data){
              console.log(data);
              alert('successfull save in db');
         });
    }
    // {$scope.loginfrom = function(){
    //     var ldata = angular.toJson($scope.loginData);
    //      $http({
    //      method  : 'POST',
    //      url     : 'http://localhost/webapp/web/app_dev.php/login_check',
    //      data    : ldata, //forms user object
    //      headers : {'Content-Type': 'application/x-www-form-urlencoded'} 
    //    }).success(function(data){
     //         console.log(data);
     //         alert('login save');
     //    });
     // }

});// JavaScript Document


