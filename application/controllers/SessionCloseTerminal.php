<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SessionCloseTerminal extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Manual_result_model');
        $this -> load -> model('Session_close_term_model');
       // $this -> load -> model('Session_close_term_model');
        //$this -> is_logged_in();
    }
    function is_logged_in() {
        $is_logged_in = $this -> session -> userdata('is_logged_in');
        $person_cat_id = $this -> session -> userdata('person_cat_id');
        if (!isset($is_logged_in) || $is_logged_in != 1 || $person_cat_id!=1) {
            echo 'you have no permission to use admin area'. '<a href="#!" ng-click="goToFrontPage()">Login</a>';
            die();
        }
    }
    

    public function angular_view_close_session(){
        ?>
        <style type="text/css">
            #search-results {
                max-height: 100px;
                border: 1px solid #dedede;
                border-radius: 3px;
                box-sizing: border-box;
                overflow-y: auto;
            }
        </style>
        <div class="d-flex">
            <div class="p-2 my-flex-item col-12">
                <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                    <!-- Brand -->
                    <a class="navbar-brand" href="#">Logo</a>

                    <!-- Links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#!cpanel">Back <i class="fas fa-home"></i></a>
                        </li>
                    </ul>
                     <div class="navbar-collapse">
					        <ul class="navbar-nav ml-auto">
					            <li class="nav-item">
					                <a class="nav-link btn btn-info text-white" href="#" ng-click="logoutCpanel()"><b>Logout</b></a>
					                
					            </li>
					        </ul>
					</div>
                </nav>
            </div>

        </div>
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
                        <div id="row my-tab-1">
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

                            <form name="resultForm" class="form-horizontal">
                                <div class="d-flex justify-content-center mt-1">
                                        <label  class="col-2">Select Terminal</label>
                                        <div class="col-3">
                                            <select
                                                    class="form-control "
                                                    data-ng-model="term_session.terminal"
                                                    data-ng-options="x as x.user_id for x in terminalList" ng-change="getTerminalLoginTime(term_session.terminal.last_loggedin)">
                                            </select>
                                        </div>
                                </div>

                              
                                <div class="d-flex justify-content-center mt-2">
                                    <label  class="col-2">Last loggedin</label>
                                    <div class="col-3">
                                        <span ng-bind="logindate"></span>&nbsp;&nbsp;{{logintime}}
                                    </div>
                                </div>
                              
                              
                                <div class="d-flex justify-content-center mt-1">
                                    <label  class="col-2">Logout</label>
                                    <div class="col-3">
                                        <select ng-model="term_session.is_logout"  class="form-control">
                                        	<option value="1">Yes</option>
                                        </select>
                                    </div>
                                </div>

                                
                                <div class="d-flex justify-content-center mt-3">
                                    <div class="col-3"></div>
                                    <div class="col-3">
                                        <input class="btn-secondary" type="button" value="Submit" ng-click="logoutRequestedTerminal(term_session)" ng-disabled="resultForm.$pristine">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-2">
                                    <div class="">
                                        <span ng-show="submitStatus" class="text-success h5">Result submitted</span>
                                    </div>
                                </div>

                            </form>

<!--                            <div class="d-flex">-->
<!--                                <div class="col-4">-->
                                    <!--<pre>sessionReport = {{sessionReport | json}}</pre>-->
<!--                                </div>-->
                             <!--  <div class="col-4"><pre>term_session ={{term_session| json}}</pre></div>-->
<!--                                <div class="col-4"><pre>showTimeList={{showTimeList | json}}</pre></div>-->
<!--                           </div>-->
                        </div> <!--//End of my tab1//-->
                    </div>

                </div>
            </div>
        </div>

        <?php
    }

    public function get_terminal(){
        $result=$this->Session_close_term_model->select_active_terminal_list()->result_array();
       
        $report_array['records']=$result;
        echo json_encode($report_array);
    }
    
     function logout_cpanel(){
    	$post_data =json_decode(file_get_contents("php://input"), true);
    	$user_id=$post_data['user_id'];
    	$result=$this->Session_close_term_model->logout_current_session($user_id);

        echo json_encode($result,JSON_NUMERIC_CHECK);
    }
  



}
?>