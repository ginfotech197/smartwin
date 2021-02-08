app.controller("resultCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is Result controller";
	
    $scope.tab = 1;
    $scope.sort = {
        active: '',
        descending: undefined
    };

    $scope.changeSorting = function(column) {
        var sort = $scope.sort;

        if (sort.active == column) {
            sort.descending = !sort.descending;
        }
        else {
            sort.active = column;
            sort.descending = false;
        }
    };
    $scope.getIcon = function(column) {
        var sort = $scope.sort;

        if (sort.active == column) {
            return sort.descending
                ? 'glyphicon-chevron-up'
                : 'glyphicon-chevron-down';
        }

        return 'glyphicon-star';
    };

    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
        if(newTab==1){
            $scope.isUpdateableFr=false;
        }
    };

    $scope.selectedTab = {
        "color" : "white",
        "background-color" : "#655D5D",
        "font-size" : "15px",
        "padding" : "5px"
    };

    $scope.grandTotalStyle = {
        "color" : "white",
        "background-color" : "#ff6600",
        "font-size": "15px",
    };

    $scope.totalRowStyle = {
        "background-color" : "#94b8b8",
        "font-size": "10px",
    };

    $scope.start_date=new Date();
    $scope.end_date=new Date();
    
    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };

    // get total sale report for 2d game
    $scope.alertMsg=false;
    
    $scope.getResultListByDate=function(searchDate){
		var dt=$scope.changeDateFormat(searchDate);
        var request = $http({
            method: "post",
            url: site_url+"/Result/get_result_by_date",
            data: {
            	result_date: dt
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.resultData=response.data;
            if($scope.resultData.length==0){
            	$scope.alertMsg=true;
            };
        });
    }; 
    
    
    
    
    
    $scope.message='';
    $scope.submitNewMessage=function(message){
        var request = $http({
            method: "post",
            url: site_url+"/Message/add_new_message",
            data: {
                msg: message
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.messageRecord=response.data.records;
            if($scope.messageRecord.success==1){
            	 $scope.message='';
                $scope.submitStatus=true;
                $timeout(function() {
                    $scope.submitStatus = false;
                }, 5000);
                
            }
        });
       
    };



    $scope.resetData={};
    $scope.resetPassword=function(resetData){
           console.log(resetData);
        var request = $http({
            method: "post",
            url: site_url+"/Admin/reset_admin_password",
            data: {
                masterData: resetData
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.resetRecord=response.data.records;
            if($scope.resetRecord.success==1){
                alert('Password reset successfully');
                $scope.logoutCpanel();
             
                
            }
        });
       
    };
    
    
    
    
      
    
     $scope.logoutCpanel=function () {
        var request = $http({
            method: "post",
            url: site_url+"/Admin/logout_cpanel",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $window.location.href = base_url+'#!';
        });
    };

    $scope.getUserData=function(){
        var request = $http({
            method: "post",
            url: site_url+"/Play/get_sessiondata",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $rootScope.huiSessionData=response.data;
            $scope.headerPath=$rootScope.huiSessionData.headerUrl;
           if($rootScope.huiSessionData.person_cat_id==1){
                $scope.text1="Welcome";
                $scope.text2="Admin";
            }
            if($rootScope.huiSessionData.person_cat_id==4){
                $scope.text1="Welcome";
                $scope.text2="Stockist";
            }
            $scope.headerPath=response.data.headerUrl;
            $window.x=100;
        });
    };

    $scope.getUserData();


});

