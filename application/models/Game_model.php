<?php
class game_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }
    
    function logout_current_session($userid){           
            
			//Getting database id of the terminal//
            $sql="select person_id from person where user_id=?";
            $result = $this->db->query($sql,array($userid));
            if($result->num_rows()>0){
                $person_id=$result->row()->person_id;
            }else{
				$person_id='';
			}
            
            //set zero active user sum_value
            
            $sql="update active_terminal set check_sum=0,last_loggedout=now() where person_id=?";
    		$result = $this->db->query($sql,array($person_id));
    		return $result;
    }//end of function


    function select_terminal_balance($terminal_id){
        $sql="select * from stockist_to_person where person_id=?";
        $result = $this->db->query($sql,array($terminal_id));
        return $result->row();
    }


    function select_play_series(){
        $sql="select * from play_series";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }


    function insert_game_values($playDetails,$drawId,$purchasedTicket){
        
    	$financial_year=get_financial_year();
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
		
        $terminal_id=$this->session->userdata('user_id');
       
        $return_array=array();
        try{
            $this->db->trans_start();
            //Getting database id of the terminal//
            $sql="select person_id from person where user_id=?";
            $result = $this->db->query($sql,array($terminal_id));
            if($result==FALSE){
                throw new Exception('error getting person_id');
            }
            $person_id=$result->row()->person_id;
          
            $sr='SW';
                //$barcode=$drawId.'-'.$sr.'-'.$ticket_taken_date.'-'.$ticket_taken_time.'-'.$terminal_id;
            $barcode=$sr.''.$bcd;
                //ADDING INTO PLAY_MASTER//

                $sql="insert into play_master (
                       play_master_id
                      ,terminal_id
                      ,draw_master_id
                    ) VALUES (?,?,?)";
                $result=$this->db->query($sql,array(
                    $barcode
                ,$person_id
                ,$drawId
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
            $return_array['message']='Successfully recorded';
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
        return (object)$return_array;
    }

    function select_from_draw_master(){
        $sql="select * from draw_master where active=1";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }

    function select_draw_interval(){
        $data=array();
        $sql="select * from draw_master where active=1";
        $result=$this->db->query($sql,array());
        if($result!=null){
            $data['active_draw']=$result->row();
        }else{
            $data['active_draw']='';
        }

        $interval_list="SELECT end_time,diff FROM `draw_master` where serial_number > 
        (select serial_number from draw_master where active=1)";
        $result2=$this->db->query($interval_list,array());
        if($result2!=null){
            $data['interval_list']=$result2->result();
        }else{
            $data['interval_list']='';
        }
        return (object)$data;
    }

    function select_next_draw_time_only(){
        $sql="SELECT end_time from draw_master WHERE 
        serial_number =(SELECT serial_number from draw_master where active=1)+1";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result->row();
        }else{
            return null;
        }
    }

    function select_draw_name_list(){
        $sql="SELECT * FROM `draw_master` order by serial_number";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }


//    testing to get activation of game
    function select_game_activation(){
        $sql="select * from game_activation_table";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }


    function select_game_result_after_each_draw($draw_id){
        $return_array=array();
        try{
            $this->db->trans_start();
            $sql="select curdate() as today_date";
            $result=$this->db->query($sql,array());
            $today=$result->row()->today_date;
            $return_array['today']=$today;

            $sql="select date_format(curtime(),'%H-%i-%s') as today_time";
            $result=$this->db->query($sql,array());
            $today_time=$result->row()->today_time;
            $return_array['today_time']=$today_time;


            $sql="select get_final_result_row(?, ?, ?) as row_number,get_final_result_column(?,?,?) as column_number, 1 as series_id";
            $result=$this->db->query($sql,array($draw_id,1,$today,$draw_id,1,$today));
            if($result==FALSE){
                throw new Exception('error getting result');
            }
            $record[]=$result->row();

            $sql="select get_final_result_row(?, ?, ?) as row_number,get_final_result_column(?,?,?) as column_number, 2 as series_id";
            $result=$this->db->query($sql,array($draw_id,2,$today,$draw_id,2,$today));
            if($result==FALSE){
                throw new Exception('error getting result');
            }
            $record[]=$result->row();


            $sql="select get_final_result_row(?, ?, ?) as row_number,get_final_result_column(?,?,?) as column_number, 3 as series_id";
            $result=$this->db->query($sql,array($draw_id,3,$today,$draw_id,3,$today));
            if($result==FALSE){
                throw new Exception('error getting result');
            }
            $record[]=$result->row();

            $sql="select get_final_result_row(?, ?, ?) as row_number,get_final_result_column(?,?,?) as column_number, 4 as series_id";
            $result=$this->db->query($sql,array($draw_id,4,$today,$draw_id,4,$today));
            if($result==FALSE){
                throw new Exception('error getting result');
            }
            $record[]=$result->row();

            $sql="select get_final_result_row(?, ?, ?) as row_number,get_final_result_column(?,?,?) as column_number, 5 as series_id";
            $result=$this->db->query($sql,array($draw_id,5,$today,$draw_id,5,$today));
            if($result==FALSE){
                throw new Exception('error getting result');
            }
            $record[]=$result->row();

            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['records']=$record;

            //INSERT INTO result_master AND result_details TABLE

            $result_master_id = 'RSLT'.'-'.$today.'-'.$draw_id.'-'.$today_time;
            $sql="insert into result_master (
				   result_master_id
				  ,draw_master_id
				  ,game_date
				) VALUES (?,?,?)";
            $result=$this->db->query($sql,array(
                $result_master_id
            ,$draw_id
            ,$today
            ));
            if($result==FALSE){
                throw new Exception('error adding play_master');
            }

            //INSERT INTO result_details TABLE

            $sql="insert into result_details (
				   result_details_id
				  ,result_master_id
				  ,play_series_id
				  ,result_row
				  ,result_column
				) VALUES (?,?,?,?,?)";
            foreach($record as $index=>$value){
                $row=(object)$value;
                $result=$this->db->query($sql,array(
                    $result_master_id.'-'.($index+1)
                ,$result_master_id
                ,$index+1
                ,$row->row_number
                ,$row->column_number
                ));
            }


            $err=(object) $this->db->error();
            $return_array['error']= create_log($err->code,$this->db->last_query(),'game_model','select_game_result_after_each_draw',"log_file.csv");
        }catch (Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']= create_log($err->code,$this->db->last_query(),'game_model','select_game_result_after_each_draw',"log_file.csv");
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;


    }



    function select_previous_game_result(){
        $sql="select 
		result_details.play_series_id
		,result_details.result_details_id
		, result_details.result_row as row_number
		, result_details.result_column as column_number
		, draw_master.start_time
		, draw_master.end_time
		, draw_master.meridiem
        ,date_format(record_time,'%d/%m/%Y') as draw_date
        ,serial_number
		from result_details
		inner join result_master on result_details.result_master_id = result_master.result_master_id
		inner join draw_master on result_master.draw_master_id = draw_master.draw_master_id
        where date(record_time)=curdate() order by serial_number DESC,play_series_id ASC limit 3";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }


    function select_today_result_sheet(){
        $sql="select serial_number,result_row,result_column,t3.draw_master_id,start_time,end_time,meridiem
         from result_details t1
inner join (select * from result_master where date(record_time)=date(now())) t2 
on t1.result_master_id=t2.result_master_id
inner join draw_master t3 on t2.draw_master_id=t3.draw_master_id
order by serial_number";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }

    function select_result_sheet_by_date($result_date){
        $sql="select date_format(record_time,'%d/%m/%Y') as draw_date,end_time,meridiem,max(lucky_zone) as lucky_zone ,max(rajlaxmi) as rajlaxmi
        , max(smartwin) as smartwin,single_result,jumble_number
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
        ,single_result
        ,jumble_number
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
    
    
    function select_message(){
        $sql="SELECT * FROM `game_message` order by id desc limit 1";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }

    function select_result_summary_data_by_year_month($game,$year,$month){
        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

            $all_date="select distinct date_format(game_date,'%Y-%m-%d') as game_date from result_master
            where (EXTRACT(MONTH FROM game_date))=? and (EXTRACT(YEAR FROM game_date))=?";
            $result=$this->db->query($all_date,array($month,$year))->result();
    
            if($result!=null){
                $return_array['date_list']=$result;
                    
                $query1='select draw_master_id,draw_time,';
    
                foreach ($result as $row)
                {
                    $convert_date = strtotime($row->game_date);
                    $day = date('j',$convert_date);
                    $query1.= ' max(`'.$day.'`) as `'.'day'.$day.'`,';
                }
                $query1= substr($query1, 0, -1);
                $query1.='from ( ';
                $query1.= 'SELECT draw_master_id,draw_time,';
        
                foreach ($result as $row)
                {
                   $convert_date = strtotime($row->game_date);
                   $day = date('j',$convert_date);
                   $query1.= ' CASE
                   WHEN game_date="'.$row->game_date.'" THEN single_result ELSE ""
                    END as "'.$day.'",';
                }
                $query1= substr($query1, 0, -1);
                $query1.=' FROM (select result_master.draw_master_id,date_format(game_date,"%Y-%m-%d") as game_date,
                concat(end_time," ",meridiem) as draw_time,single_result from result_master
                inner join draw_master ON draw_master.draw_master_id = result_master.draw_master_id
                where (EXTRACT(MONTH FROM game_date))=? and (EXTRACT(YEAR FROM game_date))=?
                order by game_date,draw_master.draw_master_id)as t1 order by game_date,t1.draw_master_id
                ) as t2 group by draw_master_id,draw_time
                order by draw_master_id; ';
                $summary_result=$this->db->query($query1,array($month,$year))->result();
                
                if($summary_result!=null){
                    $return_array['data']=$summary_result;
                }else{
                    $return_array['data']=null;
                }
            }else{

                $return_array['date_list']=null;
                $return_array['data']=null;
            }
            $this->db->trans_complete();

        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'game_model','select_result_summary_data_by_year_month',"log_file.csv");
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'game_model','select_result_summary_data_by_year_month',"log_file.csv");
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }






}//final

?>