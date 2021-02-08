<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Admin_model');
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


    public function angular_view_welcome(){
        ?>
<!--    	<div class="d-flex col-12" ng-include="'application/views/header.php'"></div>-->
    	<div class="d-flex col-12" ng-include="headerPath"></div>

        <div class="d-flex col-12"><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br><br><br><br><br>  <br><br><br><br><br><br><br><br><br><br><br><br>   
        <div class="d-flex">
                <div class="col">
                 
                </div>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="vendor-div">
                <!-- Nav tabs -->
<!--                <ul class="nav nav-tabs nav-justified indigo" role="tablist">-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><i class="fa fa-user" ></i> New Product</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-heart"></i> Product List</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(3)"><i class="fa fa-envelope"></i>About Product</a>-->
<!--                    </li>-->
<!--                </ul>-->
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
<!--                    <div ng-show="isSet(1)">-->
<!--                        <div id="my-tab-1">-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div ng-show="isSet(2)">-->
<!--                        <div id="my-tab-1">-->
<!--                            This is tab 2-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div ng-show="isSet(3)">-->
<!--                        <div id="my-tab-1">-->
<!--                            This is tab 3-->
<!--                        </div>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
        <?php
    }
    
    
      function logout_cpanel(){
          session_unset();
          session_destroy();
        $newdata = array(
            'person_id'  => '',
            'person_name'     => '',
            'user_id'=> '',
            'person_cat_id'     => '',
            'is_logged_in' => 0
        );
        $this->session->set_userdata($newdata);

        echo json_encode($newdata,JSON_NUMERIC_CHECK);
    }


    function reset_admin_password(){
        $personId=$this->session->userdata('person_id');
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Admin_model->update_admin_password((object)$post_data['masterData'],$personId);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }






}
?>