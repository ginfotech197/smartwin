<?php
class manual_result_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

    function select_series(){
        $sql="select * from play_series";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_ten_digit_draw_time(){
        $sql="select * from draw_master where draw_master_id not in
(select draw_master_id from result_master where date(record_time)=(curdate())) AND draw_master_id not in  
(select draw_master_id from manual_result_digit where date(record_time)=curdate()) order by serial_number";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_card_draw_time(){
        $sql="select * from card_draw_master where card_draw_master_id not in
(select card_draw_master_id from card_result_master where date(record_time)=date(curdate()))";
        $result = $this->db->query($sql,array());
        return $result;
    }



    function insert_digit_game_manual_result($master){
        $return_array=array();

        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            if( $master->lucky_zone!= -1){
                $sql="insert into manual_result_digit (
                    play_series_id 
                    ,draw_master_id
                    ,game_date
                    ,result
                  ) VALUES (1,?,curdate(),?)";
  
              $result=$this->db->query($sql,array(
              $master-> draw_master_id
              ,$master->lucky_zone
              ));
            }

            
            if( $master->rajlaxmi!=-1){
                $sql="insert into manual_result_digit (
                    play_series_id 
                    ,draw_master_id
                    ,game_date
                    ,result
                  ) VALUES (2,?,curdate(),?)";
  
              $result=$this->db->query($sql,array(
              $master-> draw_master_id
              ,$master->rajlaxmi
              ));
            }
           
            if(  $master->smartwin !=-1 ){
                $sql="insert into manual_result_digit (
                    play_series_id 
                    ,draw_master_id
                    ,game_date
                    ,result
                  ) VALUES (3,?,curdate(),?)";
  
              $result=$this->db->query($sql,array(
              $master-> draw_master_id
              ,$master->smartwin
              ));
            }

           
         
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
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_digit_game_manual_result',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_digit_game_manual_result',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function




    function select_game1_payout(){
        $sql="select *,1 as game_no, 'TWO DIGIT' as game_name from play_series";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function select_game2_payout(){
        $sql="select *,2 as game_no,'12 CARDS' as game_name from card_price_details";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function update_stockist_details($stockist){
        $return_array=array();
        try{
            $this->db->trans_start();
            $sql="update stockist set stockist_name=?, user_id=?, user_password=? where stockist_id=?";
            $result=$this->db->query($sql,array(
            $stockist->stockist_name
            ,$stockist->user_id
            ,$stockist->user_password
            ,$stockist->stockist_id
            ));
            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
                throw new Exception('error adding purchase master');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully recorded';
        }
        catch(mysqli_sql_exception $e){
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
            $return_array['error_code']=$err->code;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function


	 function get_second_and_last_total($draw_id){
        $sql="call secondLastTotal(?)";
        $result = $this->db->query($sql,array($draw_id));
        return $result;
    }

    function select_manual_result(){
        $sql="SELECT manual_result_digit.play_series_id,manual_result_digit.draw_master_id,series_name,end_time,meridiem,result FROM manual_result_digit
        inner join play_series on manual_result_digit.play_series_id = play_series.play_series_id
        inner join draw_master on manual_result_digit.draw_master_id = draw_master.draw_master_id
        where active=1 and game_date=curdate()";
        $result = $this->db->query($sql,array());
        return $result;
    }



    
    function update_current_manual($master){
        //print_r($master);
        $return_array=array();

        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            if( $master->lucky_zone!= -1){
                //echo $master->lucky_zone;

                $query="select * from manual_result_digit  where draw_master_id=? and play_series_id=1 and game_date=curdate()";
                $count_row=$this->db->query($query,array($master-> draw_master_id));
                if($count_row->num_rows() ==1){

                    $sql="update manual_result_digit set result=? where draw_master_id=? and play_series_id=1 and game_date=curdate()";
                    $result=$this->db->query($sql,array($master->lucky_zone,$master-> draw_master_id));
               
                }else{

                    $sql="insert into manual_result_digit (
                        play_series_id 
                        ,draw_master_id
                        ,game_date
                        ,result
                      ) VALUES (1,?,curdate(),?)";
      
                    $result=$this->db->query($sql,array(
                    $master-> draw_master_id
                    ,$master->lucky_zone
                    ));

                }
             
            }

            
            if( $master->rajlaxmi!=-1){

                    $query="select * from manual_result_digit  where draw_master_id=? and play_series_id=2 and game_date=curdate()";
                    $count_row=$this->db->query($query,array($master-> draw_master_id));
                    if($count_row->num_rows() ==1){
    
                        $sql="update manual_result_digit set result=? where draw_master_id=? and play_series_id=2 and game_date=curdate()";
                        $result=$this->db->query($sql,array($master->rajlaxmi,$master-> draw_master_id));
                   
                    }else{
    
                        $sql="insert into manual_result_digit (
                            play_series_id 
                            ,draw_master_id
                            ,game_date
                            ,result
                          ) VALUES (2,?,curdate(),?)";
          
                        $result=$this->db->query($sql,array(
                        $master-> draw_master_id
                        ,$master->rajlaxmi
                        ));
    
                    }
            }
           
            if(  $master->smartwin !=-1 ){

                    $query="select * from manual_result_digit  where draw_master_id=? and play_series_id=3 and game_date=curdate()";
                    $count_row=$this->db->query($query,array($master-> draw_master_id));
                    if($count_row->num_rows() ==1){
    
                        $sql="update manual_result_digit set result=? where draw_master_id=? and play_series_id=3 and game_date=curdate()";
                        $result=$this->db->query($sql,array($master->smartwin,$master-> draw_master_id));
                   
                    }else{
    
                        $sql="insert into manual_result_digit (
                            play_series_id 
                            ,draw_master_id
                            ,game_date
                            ,result
                          ) VALUES (3,?,curdate(),?)";
          
                        $result=$this->db->query($sql,array(
                        $master-> draw_master_id
                        ,$master->smartwin
                        ));
    
                    }
            }
           
         
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
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','update_current_manual',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','update_current_manual',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function


}//final

?>