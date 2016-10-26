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
    $scope.regform = {};
    $scope.submitForm = function(){
        var fdata = angular.toJson($scope.regform);
        console.log(fdata);
        $http({
            method  : 'POST',
            url     : '/webapp/web/app_dev.php/visitor/update',
            data    : fdata, //forms user object
            headers : {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data){
            console.log(data);
            // alert('successfull save in db');
        });
    }

});// JavaScript Document

app.controller('loginController', function($scope, $http) {
    $scope.loginData = {};
    $scope.loginfrom = function(){
        var ldata = angular.toJson($scope.loginData);
        $http({
            method  : 'POST',
            url     : '/webapp/web/app_dev.php/login_check',
            data    : ldata, //forms user object
            headers : {'Content-Type': 'application/x-www-form-urlencoded'}
        }).success(function(data){
            console.log(data);
            //alert('login save');
        });


    }
});


