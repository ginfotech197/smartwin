<?php
class Indirect_play_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }





    function insert_stockist_recharge($stockist_id,$amount){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();


            //adding recharge_to_stockist//
            $sql="insert into recharge_to_stockist (
                  recharge_master
                  ,stockist_id
                  ,amount
                ) VALUES (?,?,?)";

            $result=$this->db->query($sql,array(
                $this->session->userdata('person_id')
                ,$stockist_id
                ,$amount
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }

            //update current balance of the stockist//
            $sql="update stockist set current_balance=current_balance + ? where stockist_id=?";

            $result=$this->db->query($sql,array($amount,$stockist_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }

            $sql="select current_balance from stockist where stockist_id=?";

            $result=$this->db->query($sql,array($stockist_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            $current_balance=$result->row()->current_balance;

            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully recorded';
            $return_array['current_balance']=$current_balance;
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


    function select_all_stockist(){
        $sql="select 
stockist_id, stockist_name, user_id, user_password, serial_no, current_balance from stockist";
        $result = $this->db->query($sql,array());
        return $result;
    }



    function insert_game_values($playDetails,$drawId,$terminalId,$purchasedTicket){
        $return_array=array();  
        $financial_year=get_financial_year();
        
        $sql="select * from draw_master where draw_master_id=?";
        $result = $this->db->query($sql,array($drawId));
        $draw_details = $result->row();
        
        if($draw_details->active == 0){
            $return_array['success']=0;
            $return_array['message']='Select current draw only!!';
        }else{
            

            //insert into maxtable
            $sql="insert into barcode_max (subject_name, current_value, financial_year)
            values('digit bill',1,?)
            on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
            throw new Exception('Increasing Maxtable for barcode max');
            }

            //getting from maxtable
            $sql="select * from barcode_max where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
            throw new Exception('error getting maxtable');
            }
            $bcd=leading_zeroes($result->row()->current_value,10).''.$financial_year;            



            $ticket_taken_date=get_date_value();
            $ticket_taken_time=get_time_value();
            /* GET TICKET TAKEN TIME  */
            $hr=intval(substr($ticket_taken_time, 0, 2));
            $min=intval(substr($ticket_taken_time, 2, 2));
            $sec=intval(substr($ticket_taken_time, 4, 2));
            if($hr>=12){
            $merid='PM';
            }else{
            $merid='AM';
            }
            if($hr>12){
            $hr-=12;
            }
            if($min<10){
            $min='0'.$min;
            }
            if($sec<10){
            $sec='0'.$sec;
            }
            $show_purchase_time=$hr.':'.$min.':'.$sec.''.$merid;
            /* GET TICKET TAKEN DATE */
            $yyyy=intval(substr($ticket_taken_date, 0, 4));
            $mm=intval(substr($ticket_taken_date, 4, 2));
            $dd=intval(substr($ticket_taken_date, 6, 2));
            if($dd<10){
            $dd='0'.$dd;
            }
            if($mm<10){
            $mm='0'.$mm;
            }
            $show_purchase_date=$dd.'/'.$mm.'/'.$yyyy;

            // $terminal_id=$terminalId;


            try{
            $this->db->trans_start();

            $person_id=$terminalId;
            $activityDoneBy= $this->session->userdata('person_id');

            $sr='SW';
            
            $barcode=$sr.''.$bcd;
            //ADDING INTO PLAY_MASTER//

            $sql="insert into play_master (
                 play_master_id
                ,terminal_id
                ,draw_master_id
                ,activity_done_by
                ) VALUES (?,?,?,?)";
            $result=$this->db->query($sql,array(
             $barcode
            ,$person_id
            ,$drawId
            ,$activityDoneBy
            ));
            if($result==FALSE){
                throw new Exception('error adding play_master');
            }
            //ADDING PLAY MASTER COMPLETED//

            //adding play_details//

            $sql="insert into play_details (
                play_details_id
                ,play_master_id
                ,play_series_id
                ,row_num
                ,col_num
                ,game_value
                ) VALUES (?,?,?,?,?,?)";
            $countRow=0;
            foreach($playDetails as $index=>$value){
                $countRow +=1;
                $row=(object)$value;
                for($i=0;$i<10;$i++){

                    $result=$this->db->query($sql,array(
                        $barcode . '-' . ($countRow)
                        ,$barcode
                        ,$row->play_series_id
                        ,$row->rowNum
                        ,$i
                        ,$row->value
                    ));
                    $countRow+=1;
                }
                
            }
            $return_array['barcode']=$barcode;


            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
            throw new Exception('error adding play_details');
            }

            $sql="update stockist_to_person set current_balance = current_balance - ? where person_id=?";

            $result=$this->db->query($sql,array($purchasedTicket,$person_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
            throw new Exception('error updating terminal balance');
            }

            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Print Done';
            $return_array['purchase_time']=$show_purchase_time;
            $return_array['purchase_date']=$show_purchase_date;
            }
            catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Game_model','insert_game_values',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
            }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'Game_model','insert_game_values',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
            }


        }

    	
        return (object)$return_array;
    }



}//final

?>