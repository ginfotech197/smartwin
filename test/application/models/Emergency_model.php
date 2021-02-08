<?php
class Emergency_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }


    function select_missed_out_result_time(){
        $sql="select * from draw_master where draw_master_id not in (select draw_master_id from result_master where game_date=curdate())
order by serial_number";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function select_time_for_test(){
        $sql="select * from draw_master where draw_master_id not in (select draw_master_id from result_master2 where game_date=curdate())
order by serial_number";
        $result = $this->db->query($sql,array());
        return $result;
    }



    function insert_result_manually($drawId){

        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

            //insert into result_master and result_details
            $sql="call insert_2d_game_result_details(?)";
            $result = $this->db->query($sql, array($drawId));
            if($result==FALSE){
                throw new Exception('Result not added');
            }


            $return_array['dberror']=$this->db->error();


            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully added';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Emergency_model','insert_result_manually',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Emergency_model','insert_result_manually',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }





    function update_draw_master($drawId){

        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

            //insert into result_master and result_details
            $sql="UPDATE draw_master SET active = IF(draw_master_id=?, 1,0);";
            $result = $this->db->query($sql, array($drawId));
            if($result==FALSE){
                throw new Exception('draw time not activated');
            }


            $return_array['dberror']=$this->db->error();


            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully added';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Emergency_model','update_draw_master',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Emergency_model','update_draw_master',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }





    function insert_result_for_testing($drawId){

        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

            //insert into result_master and result_details
            $sql="call test_new_result(?,'other')";
            $result = $this->db->query($sql, array($drawId));
            if($result==FALSE){
                throw new Exception('Result not added');
            }


            $return_array['dberror']=$this->db->error();


            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully added';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Emergency_model','insert_result_for_testing',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Emergency_model','insert_result_for_testing',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }




    function select_test_result_sheet_by_date($result_date){
        $sql="select t3.end_time,t3.meridiem,t3.draw_name,t1.result_row,t1.result_column from result_details2 t1
        inner join (select * from result_master2 where date(record_time)='2019-07-30') as t2 on t1.result_master_id=t2.result_master_id
        inner join draw_master t3 on t2.draw_master_id=t3.draw_master_id
        order by t3.serial_number DESC";
        $result=$this->db->query($sql,array($result_date));
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }

}//final

?>