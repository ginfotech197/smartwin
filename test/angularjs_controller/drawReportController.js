app.controller("drawReportCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is Draw wise Sales Report controller";
    

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

    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };

    $scope.isLoading=false;
    $scope.isLoading2=true;


    $scope.getMrp=function(){
        var request = $http({
            method: "post",
            url: site_url+"/commonFunction/get_all_play_series",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.mrpRecord=response.data.records[0];
        });
    };
    $scope.getMrp();

    // get total sale report for 2d game
    $scope.alertMsg=true;

    $scope.getDrawWiseSaleReport=function (start_date) {

        $scope.isLoading=true;
        $scope.alertMsg=false;
        var start_date=$scope.changeDateFormat(start_date);



        var request = $http({
            method: "post",
            url: site_url+"/DrawReport/get_sale_report_draw_wise",
            data: {
                start_date: start_date
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.saleReport=response.data.records;

            $scope.gameMrp=$scope.mrpRecord.mrp;
            $scope.isLoading=false;
            if($scope.saleReport.length==0){
                $scope.alertMsg=true;
            }else{
                $scope.alertMsg=false;
            }

        });


    };

    //$scope.getAllTerminalTotalSale($scope.start_date,$scope.end_date);





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

