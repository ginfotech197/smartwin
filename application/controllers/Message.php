<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message extends CI_Controller {

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
        if (!isset($is_logged_in) || $is_logged_in != 1 || $person_cat_id!=1) {
            echo 'you have no permission to use admin area'. '<a href="#!play" ng-click="goToFrontPage()">Login</a>';
            die();
        }
    }




    public function angular_view_game_message(){
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
        <div class="d-flex col-12" ng-include="'application/views/header.php'"></div>
        <div class="d-flex col-12" data-ng-controller="resultCtrl">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Add new messge</a>
                    </li>

                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1" class="bg-cpanel">
                                <form name="msgForm" class="form-horizontal">
                                                            
                               
                                <div class="d-flex justify-content-center mt-1 pt-2">
                                    <label  class="col-2">Message</label>
                                    <div class="col-3">
                                        <input class="form-control" required ng-model="message" maxlength="50">
                                    </div>
                                </div>

                              

                                <div class="d-flex justify-content-center mt-3">
                                    <div class="col-3">
                                    	
                                    </div>
                                    <div class="col-3">
                                        <input class="btn-secondary" type="button" value="Submit" ng-click="submitNewMessage(message)" ng-disabled="msgForm.$invalid">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-2">
                                    <div class="">
                                        <span ng-show="submitStatus" class="text-success h5">Msg added</span>
                                    </div>
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

    

	  function add_new_message(){
	        $post_data =json_decode(file_get_contents("php://input"), true);
	        $result=$this->Result_model->insert_game_message($post_data['msg']);
	        $report_array['records']=$result;
	        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    	}

}
?>