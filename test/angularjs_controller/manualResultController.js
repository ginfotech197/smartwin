app.controller("manualResultCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is manualResultCtrl controller";
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



    $scope.x = false;   /*  This variable set for editable manual form  */

   $scope.gameList=[{game_id: 1,game_name: "2 DIGIT"}];



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

    $scope.getPlaySeries=function () {
        var request = $http({
            method: "post",
            url: site_url+"/ManualResult/get_all_series",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.seriesList=response.data.records;            
        });
    };
    $scope.getPlaySeries();

    $scope.getDigitDrawTime=function () {
        var request = $http({
            method: "post",
            url: site_url+"/ManualResult/get_all_digit_draw_time",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.digitDrawTime=response.data.records;
            $scope.manualData.time=$scope.digitDrawTime[0];
        });
    };
    $scope.getDigitDrawTime();


    $scope.lz_val='';$scope.rl_val='';$scope.sw_val=''
    $scope.getEditableManual=function () {
        var request = $http({
            method: "post",
            url: site_url+"/ManualResult/get_last_manual",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.editableResult=response.data.records;  


            $scope.editableResult.forEach(function (val, idx) {
                if (val.play_series_id == 1) {
                    $scope.lz_val=val.result;
                }
                if (val.play_series_id == 2) {
                    $scope.rl_val=val.result;
                }
                if (val.play_series_id == 3) {
                    $scope.sw_val=val.result;
                }
            });      
        });
    };


    $scope.manualData={
        lucky_zone:'',rajlaxmi:'',smartwin:''
    };




    $scope.submitManualResult=function(manualResult){
        var master={};
        
        master.draw_master_id=parseInt(manualResult.time.draw_master_id);
        if(typeof manualResult.lucky_zone === 'undefined'){
            master.lucky_zone = -1;
        }else if(manualResult.lucky_zone.length>0){
            master.lucky_zone = parseInt(manualResult.lucky_zone);
        }else{
            master.lucky_zone = -1;
        }


        if(typeof manualResult.rajlaxmi === 'undefined'){
            master.rajlaxmi = -1;
        }else if(manualResult.rajlaxmi.length>0){
            master.rajlaxmi = parseInt(manualResult.rajlaxmi);
        }else{
            master.rajlaxmi = -1;
        }

        if(typeof manualResult.smartwin === 'undefined'){
            master.smartwin = -1;
        }else if(manualResult.smartwin.length>0){
            master.smartwin = parseInt(manualResult.smartwin);
        }else{
            master.smartwin = -1;
        }
     
        if( master.lucky_zone== -1 && master.rajlaxmi== -1 && master.smartwin == -1){
            alert('input not valid');
            return;
        }
       
        var request = $http({
            method: "post",
            url: site_url+"/ManualResult/get_digit_manual_result",
            data: {
                master: master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.manualResultReport=response.data.records;
            if($scope.manualResultReport.dberror.code==1062){
                alert("Duplicate entry!!!!");
                $window.location.reload();
                return;
            }

            if($scope.manualResultReport.success==1){
                $scope.manualData={}; 
                alert("Result added manually");
                $scope.getDigitDrawTime();
                
            }
        });
       
    };





    
    $scope.updateManualResult=function(drawId,lz,rl,sw){
        var master={};
        
        master.draw_master_id=parseInt(drawId);
        if(isNaN(master.draw_master_id)){
            alert("Invalid draw time");return;
        }
        if(typeof lz === 'undefined'){
            master.lucky_zone = -1;
        }else if(lz.length>0){
            master.lucky_zone = parseInt(lz);
        }else{
            master.lucky_zone = -1;
        }


        if(typeof rl === 'undefined'){
            master.rajlaxmi = -1;
        }else if(rl.length>0){
            master.rajlaxmi = parseInt(rl);
        }else{
            master.rajlaxmi = -1;
        }

        if(typeof sw === 'undefined'){
            master.smartwin = -1;
        }else if(sw.length>0){
            master.smartwin = parseInt(sw);
        }else{
            master.smartwin = -1;
        }
     
        if( master.lucky_zone== -1 && master.rajlaxmi== -1 && master.smartwin == -1){
            alert('input not valid');return;
        }
       
        var request = $http({
            method: "post",
            url: site_url+"/ManualResult/update_manual_result",
            data: {
                master: master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.manualUpdateReport=response.data.records;
           
            if($scope.manualUpdateReport.success==1){
                alert("Updated");                
            }
        });
       
    };
    
    
    
      $scope.alertMsg=false;
    $scope.getlastAndSecondLastTotal=function(drawId){
		  var request = $http({
                method: "post",
                url: site_url+"/ManualResult/get_place_values",
                data: {
                    draw_id: drawId
                }
                ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(function(response){
                $scope.secondLastTotal=response.data.records;
                if($scope.secondLastTotal.length==0){
                    $scope.alertMsg=true;
                }else{
                    $scope.alertMsg=false;
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



    $scope.myTxt = "You have not yet clicked submit";
  $scope.myFunc = function () {
      $scope.myTxt = "You clicked submit!";
      alert('success');
  }

});

