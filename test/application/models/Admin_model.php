<?php
class admin_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

//


	    
	
function update_admin_password($pswInfo,$personId){
   
    $return_array=array();
    try{
        $this->db->query("START TRANSACTION");
        $this->db->trans_start();

        $sql="update person set user_password=(?) where person_id=?";

        $result=$this->db->query($sql,array(
            $pswInfo->user_password
            ,$personId
        ));
     
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
        $return_array['error']=create_log($err->code,$this->db->last_query(),'Admin_model','update_admin_password',"log_file.csv");
        $return_array['success']=0;
        $return_array['message']='test';
        $this->db->query("ROLLBACK");
    }catch(Exception $e){
        $err=(object) $this->db->error();
        $return_array['error']=create_log($err->code,$this->db->last_query(),'Admin_model','update_admin_password',"log_file.csv");
        // $return_array['error']=mysql_error;
        $return_array['success']=0;
        $return_array['message']=$err->message;
        $this->db->query("ROLLBACK");
    }
    return (object)$return_array;
}//end of function    
    
    
    

}//final

?>