<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommonFunction extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Common_function_model');
    }

    function get_sessiondata(){
        echo json_encode($this->session->userdata(),JSON_NUMERIC_CHECK);
    }
    


    public function get_all_play_series(){

        $result=$this->Common_function_model->select_play_series()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }



    
    public function get_timestamp(){
    	$date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
		echo $date->format('h:i:sA');    
            
    }


    public function current_balance_by_terminal_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Common_function_model->select_terminal_balance($post_data['terminal_id']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    public function get_active_draw_time(){
        $result=$this->Common_function_model->select_current_drawtime()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
}
?>
