<?php
class Common_function_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
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


    function select_current_drawtime(){
        $sql="select * from draw_master where active=1";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
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


    function select_draw_name_list_for_manual_game_input(){
        $sql="select * from draw_master where draw_master_id not in
        (select draw_master_id from result_master where date(record_time)=(curdate())) order by serial_number";
        $result=$this->db->query($sql,array());
        if($result!=null){
            return $result;
        }else{
            return null;
        }
    }



    function select_all_terminal(){
        $sql="select 
             stockist_to_person.person_id
            ,stockist_to_person.stockist_id
            , stockist.stockist_name
            ,stockist.user_id as stockist_user_id
            , person.person_name
            , person.user_id
            , person.user_password
            from stockist_to_person
            inner join stockist on stockist_to_person.stockist_id = stockist.stockist_id
            inner join person on stockist_to_person.person_id = person.person_id
            where stockist.inforce=1";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function is_active_draw($draw_id){
        $sql="select count(*) as total from draw_master where active=1 and draw_master_id=?";
        $result = $this->db->query($sql,array($draw_id));
        return $result;
    }

    function select_terminal_balance($terminal_id){
        $sql="select * from stockist_to_person where person_id=?";
        $result = $this->db->query($sql,array($terminal_id));
        return $result->row();
    }




}//final

?>