<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ManualResult extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Manual_result_model');
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


    public function angular_view_set_manual_result(){
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
        <div class="d-flex col-12" ng-include="'application/views/header.php'"></div>
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Manual Result</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1" class="bg-cpanel">
                            <style type="text/css">
                                .td-input{
                                    width: 90px;
                                }
                                
                                  .report-table tr th,.report-table tr td{
						                border: 1px solid black !important;
						                font-size: 12px;
						                line-height: 1.5;
						            }
                            </style>
						
                                                                                                                  
                            
                        <input type="button" class="btn-warning" value="Update" ng-show="!x" ng-click="x=!x;getEditableManual()">
                        <input type="button" class="btn-warning" value="Add new" ng-show="x" ng-click="x=!x">
                            
                           <div class="d-flex mt-5" ng-show="!x">
                          
                             <form class="form-inline" name="form1">


                             <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">Time</span>
                                    </div>
                                    <select required
                                            class=""
                                            data-ng-model="manualData.time"
                                            data-ng-options="x as (x.end_time + ' ' + x.meridiem) for x in digitDrawTime">
                                    </select>
                                </div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:aquamarine" id="basic-addon1">Lucky zone &nbsp;{{(seriesList[0].mrp * 10) || ''}}/-</span>
                                    </div>
                                    <input type="text" numbers-only disallow-spaces ng-model="manualData.lucky_zone" maxlength="2" style="width:60px" aria-label="Username" aria-describedby="basic-addon1">
                                </div>


                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:darkorange" id="basic-addon1">Rajlaxmi &nbsp;{{(seriesList[1].mrp * 10) || ''}}/-</span>
                                    </div>
                                    <input type="text" numbers-only disallow-spaces ng-model="manualData.rajlaxmi" maxlength="2" style="width:60px"  aria-label="Username" aria-describedby="basic-addon1">
                                </div>


                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:rosybrown" id="basic-addon1">Smartwin &nbsp;{{(seriesList[2].mrp * 10) || ''}}/-</span>
                                    </div>
                                    <input type="text" numbers-only disallow-spaces ng-model="manualData.smartwin" maxlength="2" style="width:60px" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                               
                                <input type="submit" value="Save" ng-click="submitManualResult(manualData)">
                            </form> 
                                
                          </div>





                          <div class="d-flex mt-5 bg-secondary" ng-show="x">
                          <div class="col-1"></div>
                          <div class="col-10">
                          
                          
                          <form class="form-inline" name="editManual">
                                <div class="input-group mr-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">{{(editableResult[0].end_time+''+editableResult[0].meridiem) || 'Time'}}</span>
                                    </div>
                                </div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:aquamarine" id="basic-addon1">Lucky zone &nbsp;{{(seriesList[0].mrp * 10) || ''}}/-</span>
                                    </div>
                                    <input type="text" numbers-only disallow-spaces ng-value="lz_val" ng-model="lz_val" maxlength="2" style="width:60px" aria-label="Username" aria-describedby="basic-addon1">
                                </div>


                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:darkorange" id="basic-addon1">Rajlaxmi &nbsp;{{(seriesList[1].mrp * 10) || ''}}/-</span>
                                    </div>
                                    <input type="text" numbers-only disallow-spaces ng-value="rl_val" ng-model="ra_val" maxlength="2" style="width:60px"  aria-label="Username" aria-describedby="basic-addon1">
                                </div>


                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="background-color:rosybrown" id="basic-addon1">Smartwin &nbsp;{{(seriesList[2].mrp * 10) || ''}}/-</span>
                                    </div>
                                    <input type="text" numbers-only disallow-spaces ng-value="sw_val" ng-model="sw_val" maxlength="2" style="width:60px" aria-label="Username" aria-describedby="basic-addon1">
                                </div>

                                <input type="submit" value="Update" ng-click="updateManualResult(editableResult[0].draw_master_id,lz_val,ra_val,sw_val)">
                                </form> 
                          
                          
                          
                          </div>
                          <div class="col-1"></div>
                             
                        </div>

                        </div> <!--//End of my tab1//-->
                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    public function get_all_series(){
        $result=$this->Manual_result_model->select_series()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }
     public function get_all_digit_draw_time(){
        $result=$this->Manual_result_model->select_ten_digit_draw_time()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    public function get_all_card_draw_time(){
        $result=$this->Manual_result_model->select_card_draw_time()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

     public function get_game2_payout(){
        $result=$this->Payout_settings_model->select_game2_payout()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }


    function get_digit_manual_result(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Manual_result_model->insert_digit_game_manual_result((object)$post_data['master']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }




    function update_stockist_by_stockist_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Stockist_model->update_stockist_details((object)$post_data['stockist']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }
    
    
     public function get_place_values(){
     	$post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Manual_result_model->get_second_and_last_total($post_data['draw_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    public function get_current_user_id(){
        $result=$this->Stockist_model->select_next_user_id_for_stockist();
        echo $result;
    }

    public function get_last_manual(){
       $result=$this->Manual_result_model->select_manual_result()->result_array();
       $report_array['records']=$result;
       echo json_encode($report_array);
   }

   function update_manual_result(){
    $post_data =json_decode(file_get_contents("php://input"), true);
    $result=$this->Manual_result_model->update_current_manual((object)$post_data['master']);
    $report_array['records']=$result;
    echo json_encode($report_array,JSON_NUMERIC_CHECK);

}

}
?>