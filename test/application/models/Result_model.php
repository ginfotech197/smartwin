<?php
class Result_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

    function select_result_sheet_by_date($result_date){
        $sql="select date_format(record_time,'%d/%m%Y') as draw_date,end_time,meridiem,max(lucky_zone) as lucky_zone ,max(rajlaxmi) as rajlaxmi, max(smartwin) as smartwin
        from (select *,
        case when play_series_id = 1 then (result_row*10+result_column) end as lucky_zone ,
        case when play_series_id = 2 then (result_row*10+result_column) end as rajlaxmi,
        case when play_series_id = 3 then (result_row*10+result_column) end as smartwin
        from (select 
        end_time
        ,meridiem
        ,play_series.play_series_id
        ,serial_number
        ,result_details.result_master_id
        ,result_row
        ,result_column
         ,record_time
        from result_details
        inner join (select * from result_master where date(record_time)=?)result_master on result_details.result_master_id = result_master.result_master_id
        inner join draw_master on result_master.draw_master_id = draw_master.draw_master_id
        inner join play_series on result_details.play_series_id = play_series.play_series_id) as table1) as table2
        group by result_master_id order by serial_number DESC";
        $result=$this->db->query($sql,array($result_date));
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }
    
    
        function insert_game_message($message){
        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

            $sql="insert into game_message (
                  message
                ) VALUES (?)";

            $result=$this->db->query($sql,array($message));
         
            //adding card payout//


            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully recorded';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function





}//final

?>