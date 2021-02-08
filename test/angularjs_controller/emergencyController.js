app.controller("emergencyCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is emergencyCtrl controller";
    $('#access-emergency-panel').modal('show');
    $scope.tab = 1;
    $scope.sort = {
        active: '',
        descending: undefined
    };
    $scope.findObjectByKey = function(array, key, value) {
        for (var i = 0; i < array.length; i++) {
            if (array[i][key] === value) {
                return array[i];
            }
        }
        return null;
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
        "background-color" : "#660000",
        "font-size" : "20px",
        "padding" : "5px"
    };

    $scope.secondTab = {
        "color" : "white",
        "background-color" : "#ff9900",
        "font-size" : "20px",
        "padding" : "5px"
    };

    $scope.thirdTab = {
        "color" : "white",
        "background-color" : "purple",
        "font-size" : "20px",
        "padding" : "5px"
    };

    $scope.showPanel=false;
    $scope.checkUserAuthentication=function(psw){
        if(psw==909090){
            $scope.showPanel=true;
        }else{
            alert("Try again");
        }
    };





    $scope.drawTimeList=function(){
        var request = $http({
            method: "post",
            url: site_url+"/EmergencyResult/get_all_draw_time",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.timeList=response.data.records;
        });
    };

    $scope.drawTimeList();






    $scope.drawTimeListTesingPurpose=function(){
        var request = $http({
            method: "post",
            url: site_url+"/EmergencyResult/get_all_draw_time_for_test",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.timeListTest=response.data.records;
        });
    };

    $scope.drawTimeListTesingPurpose();


    $scope.putEmergencyResult=function (draw_time) {
         var draw_id=draw_time.draw_master_id;

        if ($window.confirm("Are you sure to give result manually now?")) {
            $scope.confirmed = 1;
        } else {
            $scope.confirmed = 0;
        }
        if($scope.confirmed){

            var request = $http({
                method: "post",
                url: site_url+"/EmergencyResult/save_missed_out_result",
                data: {
                    drawId: draw_id
                }
                ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(response){
                $scope.resultUpdaterecord=response.data.records;

                if($scope.resultUpdaterecord.success==1){
                    $scope.drawTimeListTesingPurpose();
                    alert('Result added');
                    $scope.emergencyForm.$setPristine();
                }

            });
        }
    };

    $scope.activeFailedDraw=function (draw_time) {

        var draw_id=draw_time.draw_master_id;

        if ($window.confirm("Do you want to active this draw now?")) {
            $scope.confirmed = 1;
        } else {
            $scope.confirmed = 0;
        }
        if($scope.confirmed){

            var request = $http({
                method: "post",
                url: site_url+"/EmergencyResult/save_acivated_draw_on",
                data: {
                    drawId: draw_id
                }
                ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(response){
                $scope.drawUpdateRecord=response.data.records;
                if($scope.drawUpdateRecord.success==1){
                    alert('Draw Time activated');
                    $scope.drawTimeForm.$setPristine();
                }

            });
        }
    };







// test result

$scope.resultInputAnotherTest=function (draw_time) {
    var draw_id=draw_time.draw_master_id;

   if ($window.confirm("Are you sure to give result for testing?")) {
       $scope.confirmed = 1;
   } else {
       $scope.confirmed = 0;
   }
   if($scope.confirmed){

       var request = $http({
           method: "post",
           url: site_url+"/EmergencyResult/save_result_for_test",
           data: {
               drawId: draw_id
           }
           ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
       }).then(function(response){
           $scope.resultUpdaterecord=response.data.records;

           if($scope.resultUpdaterecord.success==1){
               $scope.drawTimeList();
               alert('Result added');
               $scope.emergencyForm.$setPristine();
           }

       });
   }
};


$scope.changeDateFormat=function(userDate){
    return moment(userDate).format('YYYY-MM-DD');
};

$scope.testResultListByDate=function(searchDate){
    var dt=$scope.changeDateFormat(searchDate);
    var request = $http({
        method: "post",
        url: site_url+"/EmergencyResult/get_test_result_by_date",
        data: {
            result_date: dt
        }
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.resultData=response.data;
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





});

