<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Result extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Result_model');
        $this -> is_logged_in();
    }

    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		$person_cat_id = $this -> session -> userdata('person_cat_id');
        if (!isset($is_logged_in) || $is_logged_in != 1 || ($person_cat_id!=1 && $person_cat_id!=4)) {
            echo 'you have no permission to use admin area'. '<a href="#!" ng-click="goToFrontPage()">Login</a>';
            die();
        }
	}




    public function angular_view_game_result(){
        ?>
        <style type="text/css">
            #search-results {
                max-height: 200px;
                border: 1px solid #dedede;
                border-radius: 3px;
                box-sizing: border-box;
                overflow-y: auto;
            }
            .report-table tr th,.report-table tr td{
                border: 1px solid black !important;
                font-size: 12px;
                line-height: 1.5;
            }

            #stockist-table-div table th{
                background-color: #1b6d85;
                color: #a6e1ec;
                cursor: pointer;
            }
            a[ng-click]{
                cursor: pointer;
            }
        </style>
        <div class="d-flex col-12" ng-include="headerPath"></div>
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Total Sale</a>
                    </li>

                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1">
                            <form name="stockistForm" class="form-horizontal">
                                <div class="card">

                                    <div class="card-header p-0">
                                        <div class="d-flex justify-content-center">
                                            <div class=""><input type="date" class="form-control" ng-model="result_date" ng-change="changeDateFormat(start_date)"></div>                                            
                                            <div class="ml-2"><input type="button" class="btn btn-info form-control" value="Show" ng-click="getResultListByDate(result_date)"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-center">
                                            <div class="loader mt-1" ng-show="isLoading"></div>
                                        </div>

                                        <div class="d-flex" ng-show="!isLoading">
                                            <div class="col-4"></div>
                                            <div class="col-5">
                                                <table style="width: 20vw" cellpadding="0" cellspacing="0" class="table table-bordered table-responsive table-hover report-table small text-justify">
                                                    <thead>
                                                    <tr>
                                                        <th class="p-0 text-center" width="20%">Time</th>
                                                        <th class="p-0  text-center" width="20%">LuckyZone</th>
                                                        <th class="p-0  text-center" width="20%">Rajlaxmi</th>
                                                        <th class="p-0  text-center" width="20%">Smartwin</th>

                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <tr ng-repeat="x in resultData">
                                                        <td class="p-0" width="40%">{{x.end_time +' ' +x.meridiem}}</td>
                                                        <td class="p-0 text-center" width="20%">{{(x.lucky_zone) < 10 ? ('0'+x.lucky_zone) : (x.lucky_zone)}}</td>
                                                        <td class="p-0 text-center" width="20%">{{x.rajlaxmi < 10 ? ('0'+x.rajlaxmi) : (x.rajlaxmi)}}</td>
                                                        <td class="p-0 text-center" width="20%">{{x.smartwin < 10 ? ('0'+x.smartwin) : (x.smartwin)}}</td>

                                                    </tr>


                                                    </tbody>


                                                </table>
                                            </div>
                                            <div class="col-3"></div>
                                        </div>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-center" ng-show="alertMsg">
                                    <div>No records found</div>
                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->
                        <div class="d-flex" ng-show="false">
                                                        <div class="col"><pre>resultData={{resultData | json}}</pre></div>
                            <!--                            <div class="col"><pre>terminalList={{terminalList | json}}</pre></div>-->
                        </div>
                    </div>



                </div>
            </div>
        </div>

        <?php
    }

    

	public function get_result_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Result_model->select_result_sheet_by_date($post_data['result_date'])->result_array();
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }






}
?>