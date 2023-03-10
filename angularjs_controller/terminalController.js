app.controller("terminalCtrl", function ($scope,$http,$filter,$rootScope,dateFilter,$timeout,$interval,$window) {
    $scope.msg = "This is admin controller";
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
    };

    $scope.selectedTab = {
        "color" : "white",
        "background-color" : "coral",
        "font-size" : "15px",
        "padding" : "5px"
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
            $scope.getInforcedStockist();
            $window.x=100;
        });
    };

    $scope.getUserData();
    
    // get all terminal list

    var request = $http({
        method: "post",
        url: site_url+"/Terminal/get_all_terminal",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.terminalList=response.data.records;
        var stockistId=$rootScope.huiSessionData.user_id;
        var personCatTd=$rootScope.huiSessionData.person_cat_id;
        if(personCatTd==4){
            $scope.terminalList=alasql("SELECT *  from ? where stockist_user_id=?",[$scope.terminalList,stockistId]);
        }
    });




    $scope.saveTerminalData=function (terminal) {
        var master=angular.copy(terminal);
        master.stockist=terminal.stockist.stockist_id;
        var stockist_sl_no= terminal.stockist.serial_no;
        var stockist_id= terminal.stockist.stockist_id;
        var request = $http({
            method: "post",
            url: site_url+"/Terminal/save_new_terminal",
            data: {
                terminal: master
                ,stockist_sl_no: stockist_sl_no
                ,stockist_id: stockist_id
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.terminalReport=response.data.records;
            if($scope.terminalReport.success==1){
                $scope.updateableTerminalIndex=0;
                $scope.submitStatus = true;
                $scope.isUpdateable=true;
                $timeout(function() {
                    $scope.submitStatus = false;
                }, 4000);

                $scope.terminal.person_id = $scope.terminalReport.person_id;
                var tempTerminal={};
                tempTerminal.person_id=$scope.terminalReport.person_id;
                tempTerminal.stockist_id=$scope.terminal.stockist.stockist_id;
                tempTerminal.stockist_name=$scope.terminal.stockist.stockist_name;
                tempTerminal.person_name=$scope.terminal.person_name;
                tempTerminal.user_id=$scope.terminal.user_id;
                tempTerminal.user_password=$scope.terminal.user_password;
                $scope.terminalList.unshift(tempTerminal);
                $scope.terminalForm.$setPristine();
            }

        });
    };

    $scope.defaultTerminal={
        person_name: ""
        ,user_id: ""
        ,user_password: ""
    };
    $scope.terminal=$scope.defaultTerminal;
    $scope.randomPass=function(length, addUpper, addSymbols, addNums) {
        var lower = "abcdefghijklmnopqrstuvwxyz";
        var upper = addUpper ? lower.toUpperCase() : "";
        var nums = addNums ? "0123456789" : "";
        var symbols = addSymbols ? "!#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~" : "";

        var all = lower + upper + nums + symbols;
        while (true) {
            var pass = "";
            for (var i=0; i<length; i++) {
                pass += all[Math.random() * all.length | 0];
            }

            // criteria:
            if (!/[a-z]/.test(pass)) continue; // lowercase is a must
            if (addUpper && !/[A-Z]/.test(pass)) continue; // check uppercase
            if (addSymbols && !/\W/.test(pass)) continue; // check symbols
            if (addNums && !/\d/.test(pass)) continue; // check nums

            $scope.terminal.user_password=pass;
            return $scope.terminal.user_password;
        }
    }

    $scope.getInforcedStockist=function(){
        var request = $http({
            method: "post",
            url: site_url+"/Terminal/get_all_stockist",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.stockistList=response.data.records;
            if($scope.stockistList.length!=0){
                $scope.terminal.stockist = $scope.stockistList[0];
                $scope.getNextUserId($scope.terminal.stockist.serial_no,$scope.terminal.stockist.stockist_id);
            }
            var stockistId=$rootScope.huiSessionData.user_id;
            var personCatTd=$rootScope.huiSessionData.person_cat_id;
            if(personCatTd==4){
                $scope.stockistList=alasql("SELECT *  from ? where user_id=?",[$scope.stockistList,stockistId]);
            }
        });
    };



    $scope.updateTerminalFromTable = function(terminal) {
        $scope.tab=1;
        $scope.terminal = angular.copy(terminal);
        $scope.isUpdateable=true;
        var index=$scope.terminalList.indexOf(terminal);
        $scope.updateableTerminalIndex=index;
        var stockistIndex=$scope.findObjectByKey($scope.stockistList,'stockist_id',terminal.stockist_id);
        
        $scope.terminal.stockist=stockistIndex;
        $scope.terminalForm.$setPristine();
    };

    $scope.updateTerminalByTerminalId=function(terminal){
        var master = terminal;
        master.stockist_id=terminal.stockist.stockist_id;
        var request = $http({
            method: "post",
            url: site_url+"/Terminal/update_terminal_by_terminal_id",
            data: {
                terminal: master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.terminalReport=response.data.records;
            if($scope.terminalReport.success==1){
                $scope.updateStatus = true;
                $scope.isUpdateable=true;
                $timeout(function() {
                    $scope.updateStatus = false;
                }, 4000);
                $scope.terminalList[$scope.updateableTerminalIndex]=$scope.terminal;
                $scope.terminalForm.$setPristine();
            }

        });
    };


    $scope.resetTerminalDetails=function () {
        var tempStockist = $scope.terminal.stockist;
        $scope.terminal={};
        $scope.terminal.stockist = tempStockist;
        $scope.getNextUserId($scope.terminal.stockist.serial_no,$scope.terminal.stockist.stockist_id);
        $scope.isUpdateable=false;
    };

    $scope.getNextUserId=function (serialNo,stockistId) {
        var request = $http({
            method: "post",
            url: site_url+"/Terminal/get_current_user_id",
            data: {
                serialNo: serialNo
                ,stockistId: stockistId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.terminal.user_id=response.data;
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

