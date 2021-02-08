app.controller("stockistLimitCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is stockistLimitCtrl controller";
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
        "background-color" : "coral",
        "font-size" : "15px",
        "padding" : "5px"
    };

    $scope.getDefaultUserId=function(){

    };


    $scope.saveStockistRechargeData=function (limit) {
        var stockist_id=limit.stockist.stockist_id;
        var amount= limit.amount;
        var request = $http({
            method: "post",
            url: site_url+"/StockistLimit/save_stockist_recharge_details",
            data: {
                stockist_id: stockist_id
                ,amount : amount
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.stockistRechargeReport=response.data.records;

            if($scope.stockistRechargeReport.success==1){
                $scope.updateableStockistIndex=0;
                $scope.submitStatus = true;
                $scope.isUpdateable=true;
                alert("Current Balance is " + $scope.stockistRechargeReport.current_balance);
                $scope.limit.stockist.current_balance=$scope.stockistRechargeReport.current_balance;
                $scope.limit.amount='';
                $timeout(function() {
                    $scope.submitStatus = false;
                }, 4000);
                // $scope.stockistList.unshift($scope.stockist);
                $scope.stockistForm.$setPristine();
            }

        });
    };

    $scope.defaultLimit={};
    $scope.limit=angular.copy($scope.defaultLimit);


    var request = $http({
        method: "post",
        url: site_url+"/StockistLimit/get_all_stockist",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.stockistList=response.data.records;
    });





    $scope.resetRechargeDetails=function () {
        $scope.limit=angular.copy($scope.defaultLimit);
        $scope.isUpdateable=false;
        $scope.submitStatus = false;
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

