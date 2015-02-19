'use strict';

/* Controllers */

var app = angular.module('cmsApp', []);

app.filter('html', ['$sce', function ($sce) { 
    return function (text) {
        return $sce.trustAsHtml(text);
    };    
}])


// first loads an index and includes the first file linked in it. Then gives the user the possibility to switch backwarts and forwarts without reloading the whole site.
// actually the whole body is getting reloaded every time. With a bit more implementation time, just the infobox and the content area (#inhalt) should have been loaded dynamically 
app.controller('cmsCtrl', function($scope, $http, $location) {
    if(window.location.hash.substring(1) == "")
        $scope.num = 0;
    else
        $scope.num = window.location.hash.substring(2);

    console.log($scope.num);

	$scope.getData = function(name) {
		//console.log("getData");
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

    $scope.back = function() {
        $scope.num--;
        $scope.fillData($scope.num);
    }
});


