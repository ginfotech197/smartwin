app.controller("indirectPlayCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is terminalLimitCtrl controller";
    
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

    $scope.getTerminalList=function () {
        var request = $http({
            method: "post",
            url: site_url+"/IndirectPlayService/get_all_terminal",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.terminalList=response.data.records;
        });
    };
    $scope.getTerminalList();


    
    $scope.getTime=function () {
        var request = $http({
            method: "post",
            url: site_url+"/IndirectPlayService/get_all_time",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.timeList=response.data.records;
        });
    };
    $scope.getTime();



    var request = $http({
        method: "post",
        url: site_url+"/CommonFunction/get_all_play_series",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.seriesList=response.data.records;

    });


    $scope.playInput=[];
    $scope.seriesOne=angular.copy($scope.playInput);
    $scope.clearDigitInputBox=function(){
        $scope.seriesOne=angular.copy($scope.playInput);
        
    };


    $scope.getTotalBuyTicket=function(playInput,srNo){
        var mrp=0;
        var sum=0;

        for(var idx=0;idx<10;idx++){
            if(playInput[idx]!=undefined && playInput[idx]){
                sum= sum + parseInt(playInput[idx]);
            }
        }
        $scope.ticketPrice=( $scope.seriesList[srNo].mrp * 10 );
        if(srNo==0){
            $scope.totalBoxSum1=sum;
            $scope.totalTicketBuy1=$scope.totalBoxSum1 * $scope.ticketPrice;
        }
       
    };



    $scope.$watch("seriesOne", function() {
        $scope.getTotalBuyTicket($scope.seriesOne,0)
    }, true);
    
    $scope.checkActivityOfDrawTime=function(drawId){

        var request = $http({
            method: "post",
            url: site_url+"/CommonFunction/get_all_play_series",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.seriesList=response.data.records;
    
        });
    };


    $scope.activeTerminalDetails={} ;
    $scope.getTerminalBalance=function(terminalId){
        var request = $http({
            method: "post",
            url: site_url+"/CommonFunction/current_balance_by_terminal_id",
            data: {
                terminal_id: terminalId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.activeTerminalDetails=response.data.records;
    
        });
    };



    $scope.getActiveDrawTime=function(){
        var request = $http({
            method: "post",
            url: site_url+"/CommonFunction/get_active_draw_time",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.currentTime=response.data.records[0];
    
        });
    };
    $scope.getActiveDrawTime();

    $scope.submitGameValues=function (draw_id,terminal,seriesOne) {
        
        var terminal_id = terminal.person_id;
        var balance=$scope.activeTerminalDetails.current_balance;
        
        
       
        var masterData=[];
        for(var i=0;i<9;i++){
            if(seriesOne[i]){
                masterData.push({ "play_series_id": 3, "rowNum": i, "value": seriesOne[i]});
            }
        }
       
        if(masterData.length == 0){
            alert("Input is not valid");
            return;
        }
               
        


        if($scope.remainingTime<=60000 && $scope.remainingTime>=0){
            alert("Draw closed");
            return;
        }
      
    
        // Check Terminal Balance
        if(!$scope.totalTicketBuy1)
            $scope.totalTicketBuy1=0;
        
        var purchasedTicket=$scope.totalTicketBuy1; 
        purchasedTicket=$rootScope.roundNumber(purchasedTicket,2);
        
        if(purchasedTicket > balance) {
            alert("Sorry low balance");
            $scope.disable2d=false;
            $scope.playInput=[];
            return;
        }
       
   

        var request = $http({
            method: "post",
            url: site_url+"/IndirectPlayService/inser_2d_play_input",
            data: {
                playDetails: masterData
                ,drawId: draw_id
                ,terminalId: terminal_id
                ,purchasedTicket: purchasedTicket
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            
            if($scope.reportArray.success==1){
                alert($scope.reportArray.message);
                $scope.clearDigitInputBox();
                $scope.getTerminalBalance(terminal_id);


                $scope.playInput=angular.copy($scope.defaultPlayInput);
                $scope.disable2d=false;

                $scope.barcodeList=[];
                $scope.showSeries=[];
          

                $scope.totalticket_qty=0;
            }else{
                alert($scope.reportArray.message);
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

 



});

