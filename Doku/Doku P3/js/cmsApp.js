'use strict';

/* Controllers */

var app = angular.module('cmsApp', []);

app.filter('html', ['$sce', function ($sce) { 
    return function (text) {
        return $sce.trustAsHtml(text);
    };    
}])

app.controller('cmsCtrl', function($scope, $http, $location) {
    if(window.location.hash.substring(1) == "")
        $scope.num = 0;
    else
        $scope.num = window.location.hash.substring(2);

    console.log($scope.num);

	$scope.getData = function(name) {
		console.log("getData");
        var url = "./data/"+name+".json";
        $http.get(url).then(function(dataResponse) {
            $scope.content = dataResponse.data;
        	$scope.fillData($scope.num);
    	});
    }

    $scope.fillData = function(num) {
        //console.log(num);
        window.location.hash = "#"+num;
    	$scope.url = $scope.content[num].url;
        console.log($scope.url);
    }

    $scope.next = function() {
    	$scope.num++;
    	$scope.fillData($scope.num);
    }
});


