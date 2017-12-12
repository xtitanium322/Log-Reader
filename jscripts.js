/*---------------------------------------------*/
	// quick test function
/*---------------------------------------------*/
function js_test() 
{
   alert("test");
}
// angular app for handling the php files through ajax
var jsonapp = 
	angular.module('jsonapp', []).config
							(
							   function($locationProvider) 
							   {
								$locationProvider.html5Mode(false); // true will remove the hash from url
							   }
	).filter('hyperlink', function ($sce) { //converts text hyperlink to actual hyperlink
			return function (str) 
			{
				if(str) // make sure the string exists
				return $sce.trustAsHtml
				(
					str.
					replace(/</g, '&lt;').
					replace(/>/g, '&gt;').
					replace(/(http[^\s]+)/g, '<a style=" color:orange;" href="$1">link</a>')
			    );
			}
	}).filter('dateFormatter', function() { // converts date string from php to actual readable dates based on filter settings 
		  return function(input) 
		  {
			 try{ 		   
				var t = new Date(input);
				//t.setUTCHours(5); //adjust for EST timezone - doesn't work because any date with time recorded now gets offset
				return t.toISOString();
			 }
			 catch(e) 
			 {		 
				if(e instanceof RangeError) 
				{
					//console.log("date provided out of range/undefined");
					return "--/--/----"; // make sure there is a good response from the function
				}
			 }
		  };
	});
	
// directive: check if the file is valid
jsonapp.directive('validFile',function(){
	 return {
		  require:'ngModel',
		  link:function(scope,el,attrs,ctrl){
				ctrl.$setValidity('validFile', el.val() != '');
				//change event is fired when file is selected
				el.bind('change',function(){
					 ctrl.$setValidity('validFile', el.val() != '');
					 scope.$apply(function(){
						  ctrl.$setViewValue(el.val());
						  ctrl.$render();
					 });
				});
		  }
	 }
});

// filter: absolute value of a number
jsonapp.filter('absValue', function() {
	return function(num) { return Math.abs(num); }
});

// Controller
jsonapp.controller('jsonctrl', function($scope, $http, $window, $location, $anchorScroll, $timeout ) // injecting various services to controller: http for Ajax, window for control over browser window, location for url manipulations
{
	jsonapp.directive('file', function () {
		 return {
			  scope: {
					file: '='
			  },
			  link: function (scope, el, attrs) {
					el.bind('change', function (event) {
						 var file = event.target.files[0];
						 scope.file = file ? file : undefined;
						 scope.$apply();
					});
			  }
		 };
	});
	
	$scope.log_objects = {};
	$scope.processing_message = "waiting for log text...";
	$scope.filter_mode = ""; // empty filter string returns undefined result, which is then checked for in the filter condition and so all elements are selected
	
	$scope.processLog = function()
	{
		$scope.processing_message = "processing content...";
				
		try
		{
			$scope.log_objects = angular.fromJson($scope.json_log);
		}
		catch(e)
		{
			$scope.processing_message = "invalid JSON string...";
		}
		
		if($scope.log_objects === undefined || $scope.log_objects.length == null || $scope.log_objects.length == 0)
			$scope.processing_message = "no content found...";
		else
			$scope.processing_message = "completed";
	}
	
	$scope.processLogLimited = function(filter_value)
	{
		$scope.log_objects = angular.fromJson($scope.json_log);

		var temp_log = [];

		for(var i=0; i < $scope.log_objects.length; i++) 
		{
			if($scope.log_objects[i].message.indexOf(filter_value) !== -1)			
			temp_log.push
			(
				$scope.log_objects[i] //{message: $scope.log_objects[i].message}
			);
		}
		$scope.log_objects = temp_log;
		//console.log(temp_log); // filtered results
	}	
});









$(document).ready(function()
{
	$('input[name="upload_log_file"]').change(function(){
		var fileName = $(this).val();
		 $("#file_path_display").text(fileName);
	});
});