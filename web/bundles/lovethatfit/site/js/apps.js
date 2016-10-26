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
    //$scope.regform = {};

    $scope.submitForm = function(){


        var dataObj = {
            email : $scope.regform.email,
            passsword : $scope.regform.passsword,
            passsword_c : $scope.regform.password_c,
            zip : $scope.regform.zip,
            dob : $scope.regform.dob,
            pheight : $scope.regform.pheight +','+ $scope.regform.ftin,
            pweight : $scope.regform.pweight +','+ $scope.regform.kglbs,
            clothing_type : $scope.regform.clothing_type,
            body_shape : $scope.regform.body_shape,
            device_type : $scope.regform.device_type,


        };


        var fdata = angular.toJson(dataObj);
        console.log(fdata);
        $http({
          method  : 'POST',
           url     : '../visitor/update',
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


