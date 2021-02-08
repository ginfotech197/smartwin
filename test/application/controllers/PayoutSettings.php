<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PayoutSettings extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Payout_settings_model');
        $this -> is_logged_in();
    }
   
   function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		$person_cat_id = $this -> session -> userdata('person_cat_id');
		if (!isset($is_logged_in) || $is_logged_in != 1 || $person_cat_id!=1) {
			echo 'you have no permission to use admin area'. '<a href="#!" ng-click="goToFrontPage()">Login</a>';
			die();
		}
	}
    function get_products(){
        $result=$this->sale_model->select_inforce_products()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }


    public function angular_view_set_payout(){
        ?>
        <style type="text/css">
            #search-results {
                max-height: 200px;
                border: 1px solid #dedede;
                border-radius: 3px;
                box-sizing: border-box;
                overflow-y: auto;
            }
        </style>
        <div class="d-flex col-12" ng-include="headerPath"></div>
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Set Payout</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1" class="bg-cpanel">
                            <style type="text/css">
                                .td-input{
                                    width: 35px;
                                    padding: 0px;
                                    margin-left: 0px;
                                    margin-right: 0px;
                                    font-weight: bold;
                                    color: #000080;
                                    border-radius: 50px;
                                }
                            </style>

                            <form name="stockistForm" class="form-horizontal">
                                <div class="d-flex justify-content-center">
                                    <div class="mt-2 bg-info">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Sl. No</th>
                                                <th class="text-center">Game</th>
                                                <th class="text-center">Mrp</th>
                                                <th class="text-center">Payout</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr ng-repeat="x in TwoDigitPayOut">
                                                <td class="text-center">{{$index+1}}</td>
                                                <td class="text-left"><input type="text" class="form-control" ng-model="x.game_name + ' - ' + x.series_name" readonly></td>
                                                <td><input type="text" class="form-control text-right" ng-value="x.mrp | number:2" readonly></td>
                                                <td><input type="text" class="form-control text-right" ng-model="x.payout"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="p-0 text-center">
                                                    <input type="button" class="btn-secondary" value="Submit" ng-click="submitGamePayout(TwoDigitPayOut)">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mt-2">
                                    <div class="">
                                        <span ng-show="payoutStatus" class="text-success h5">Update successful</span>
                                    </div>
                                </div>
                            </form>

<!--                            <div class="d-flex">-->
<!--                                <div class="col">-->
<!--                                    <pre>payOutReport = {{payOutReport | json}}</pre>-->
<!--                                </div>-->
<!--                           </div>-->
                        </div> <!--//End of my tab1//-->
                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    public function get_game1_payout(){
        $result=$this->Payout_settings_model->select_game1_payout()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }
    


    function set_game_payout(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Payout_settings_model->update_game_payout((object)$post_data['twoDigitPayOut']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }


    function update_stockist_by_stockist_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Stockist_model->update_stockist_details((object)$post_data['stockist']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    public function get_current_user_id(){
        $result=$this->Stockist_model->select_next_user_id_for_stockist();
        echo $result;
    }


}
?>