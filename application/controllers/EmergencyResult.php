<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmergencyResult extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Emergency_model');
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


    public function angular_view_emergency_action(){
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





            <!-- The modal -->
            <div ng-show="!showPanel">
                <form name="auth_form">

                    <div class="d-flex  mt-1">
                        <label  class="col-2">Secret Key:</label>
                        <div class="col-3">
                            <input type="password" class="form-control" ng-model="user_password"  placeholder="Enter Secret Key" required>
                        </div>
                        <div class="col-3">
                            <input type="button" class="btn btn-primary"  ng-click="checkUserAuthentication(user_password)" ng-disabled="auth_form.$invalid" value="Submit"/>
                        </div>
                    </div>
                </form>
            </div>

            <!--                end of modal-->
        <div class="d-flex col-12">
            <div class="col-12"  ng-show="showPanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Add Result</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==2 && secondTab" href="#" role="tab" ng-click="setTab(2)">Active Next Draw</a>
                    </li>


                    <!-- <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==3 && thirdTab" href="#" role="tab" ng-click="setTab(3)">Test Result</a>
                    </li> -->
                </ul>






                <!-- Tab panels -->

                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1">
                            <form name="emergencyForm" class="form-horizontal">
                                <marquee><span class="text-danger">Use this Panel in emergency purpose only. Do not put advance result</span></marquee>
                                <div class="d-flex justify-content-center ">
                                    <div class="col">
                                        <div class="d-flex  mt-1">
                                            <label  class="col-3">Result Missed Out<span class="text-danger"></span></label>
                                            <div class="col-3">
                                                <select ng-disabled="isUpdateable"
                                                        class="form-control " required
                                                        data-ng-model="draw_time"
                                                        data-ng-options="x as (x.end_time + ' ' + x.meridiem) for x in timeList" ng-change="">
                                                </select>
                                            </div>
                                        </div>


                                        <div class="d-flex justify-content-center mt-2">
                                            <div class="col-4">
                                                <input type="button" class="btn btn-secondary"  ng-click="putEmergencyResult(draw_time)" ng-disabled="emergencyForm.$invalid" value="Save"/>
                                            </div>
                                        </div>


                                        <div class="d-flex mt-1" ng-show="false">
                                            <div class="col-3">
                                                <pre>timeList={{timeList | json}}</pre>

                                            </div>
                                            <div class="col-3">


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->
                    </div>




                    <div ng-show="isSet(2)">
                        <div id="row my-tab-2">
                            <form name="drawTimeForm" class="form-horizontal">
                                <marquee><span class="text-danger">Use this Panel in emergency purpose only. Check current time before use this</span></marquee>
                                <div class="d-flex justify-content-center ">
                                    <div class="col">
                                        <div class="d-flex  mt-1">
                                            <label  class="col-3">Draw Missed Out</label>
                                            <div class="col-3">
                                                <select
                                                        class="form-control " required
                                                        data-ng-model="draw_time"
                                                        data-ng-options="x as (x.end_time + ' ' + x.meridiem) for x in timeList" ng-change="">
                                                </select>
                                            </div>
                                        </div>


                                        <div class="d-flex justify-content-center mt-2">
                                            <div class="col-4">
                                                <input type="button" class="btn btn-secondary"  ng-click="activeFailedDraw(draw_time)" ng-disabled="drawTimeForm.$invalid" value="Save"/>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            <div class="">
                                                <span ng-show="submitStatus" class="text-success">Recharge successful</span>
                                                <span ng-show="updateStatus" class="text-success">Update successful</span>
                                            </div>
                                        </div>

                                        <!-- Search -->
                                        <!--                                        <input type="text" class="form-control col-3" placeholder="Search" ng-model="query" ng-focus="focus=true">-->
                                        <!--                                        <div id="search-results" ng-show="focus">-->
                                        <!--                                            <div class="search-result col-3" ng-repeat="item in stockistList | search:query"  ng-click="setQuery(item.stockist_name)">{{item.stockist_name}}</div>-->
                                        <!--                                        </div>-->

                                        <div class="d-flex mt-1" ng-show="false">
                                            <div class="col-3">
                                                <pre>timeList={{timeList | json}}</pre>

                                            </div>
                                            <div class="col-3">


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->

                    </div>


<!-- test result by new way -->
                    <div ng-show="isSet(3)">
                        <div id="row my-tab-3">
                            <form name="emergencyForm" class="form-horizontal">
                                <marquee><span class="text-danger">This result input method under testing</span></marquee>
                                <div class="d-flex justify-content-center ">
                                    <div class="col">
                                        <div class="d-flex  mt-1">
                                            <label  class="col-3">Result Test<span class="text-danger"></span></label>
                                            <div class="col-3">
                                                <select ng-disabled="isUpdateable"
                                                        class="form-control " required
                                                        data-ng-model="draw_time"
                                                        data-ng-options="x as (x.end_time + ' ' + x.meridiem) for x in timeListTest" ng-change="">
                                                </select>
                                            </div>
                                        </div>


                                        <div class="d-flex justify-content-center mt-2">
                                            <div class="col-4">
                                                <input type="button" class="btn btn-secondary"  ng-click="resultInputAnotherTest(draw_time)" ng-disabled="emergencyForm.$invalid" value="Save"/>
                                            </div>
                                        </div>


                                        <div class="d-flex mt-1" ng-show="true">
                                                                               
                                        <table st-safe-src="resultData" st-table="displayCollection"  style="width: 100%" cellpadding="0" cellspacing="0" class="table table-bordered table-hover report-table small text-justify" ng-show="!loginModal">
                                                <tbody>
                                                <tr height="33" align="center" style="border-style: hidden">
                                                    <td class="text-left">
                                                        <input type="date" class="form-control" ng-model="result_date">
                                                    </td>
                                                    <td class="text-left"><a href="#" class="btn btn-info rounded-circle" ng-click="testResultListByDate(result_date)" role="button">Show</a></td>
                                                </tr>
                                                <tr height="33" align="center">
                                                    <td class="text-left font-weight-bold" width="20%">Time</td>
                                                    <td class="text-left font-weight-bold" width="20%">Game Name</td>
                                                    <td class="text-left font-weight-bold" width="20%">Result</td>
                                                </tr>

                                                <tr height="33" align="center" ng-repeat="x in displayCollection">
                                                    <td class="text-left" width="20%">{{x.end_time +' ' +x.meridiem}}</td>
                                                    <td class="text-left" width="20%">{{x.draw_name}}</td>
                                                    <td class="text-left" width="20%">{{x.result_row + ''+ x.result_column}}</td>
                                                </tr>

                                                </tbody>


                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->
                    </div>


                    <!--                    Show Mustard Oil Bill-->

                </div>
            </div>
        </div>

        <?php
    }
    function save_missed_out_result(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Emergency_model->insert_result_manually($post_data['drawId']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }



    function save_result_for_test(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Emergency_model->insert_result_for_testing($post_data['drawId']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }



    public function get_all_draw_time(){
        $result=$this->Emergency_model->select_missed_out_result_time()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }



    public function get_all_draw_time_for_test(){
        $result=$this->Emergency_model->select_time_for_test()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }


    function save_acivated_draw_on(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Emergency_model->update_draw_master($post_data['drawId']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    public function get_test_result_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Emergency_model->select_test_result_sheet_by_date($post_data['result_date'])->result_array();
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }

}
?>