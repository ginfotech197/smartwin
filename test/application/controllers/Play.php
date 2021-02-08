<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Play extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Game_model');
        
        //$this -> is_logged_in();
    }
    /*function is_logged_in() {
        $is_logged_in = $this -> session -> userdata('is_logged_in');
        $person_cat_id = $this -> session -> userdata('person_cat_id');
        if (!isset($is_logged_in) || $is_logged_in != '1' || $person_cat_id!=3) {
            echo 'you have no permission to use this area'. '<a href="#!" ng-click="goToFrontPage()">Login</a>';
            die();
        }
    }*/
    
    function get_sessiondata(){
        echo json_encode($this->session->userdata(),JSON_NUMERIC_CHECK);
    }
    

    function get_active_terminal_balance(){
        $terminal_id=$this-> session -> userdata('person_id');
        $result=$this->Game_model->select_terminal_balance($terminal_id);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }




    public function angular_view_play(){
        ?>
        <style>
            .report-table tr th,.report-table tr td{
                border: 1px solid white !important;
               /* font-size: 12px;*/
                line-height: 0px;
                white-space:nowrap;
                
            }
            .border-less {
                border-style:hidden!important;
            }

            .joditextBoxClass {
                width: 85px;
                height: 20px;
                text-align: center;
                font-weight: bold;
                font-family: Verdana, Arial, Helvetica, sans-serif;
            }
            .textBoxClass {
                width: 45px;
                height: 20px;
                text-align: center;
                font-weight: bold;
                font-family: Verdana, Arial, Helvetica, sans-serif;
            }
            

            .footer {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                background-color: red;
                color: white;
                text-align: center;
            }
            .card{
                /*height: 100vh;*/
            }
            .card-footer{
              /* height: 15vh;*/
                font-size: 9px;
                line-height: 18px;
            }
            .submit a {
                color: white;
                background-color: #AA2E85;
                padding: 5px 11px;
                border-style: 2px solid
            }
           /* .result-list{
				 height: 150px;       /* Just for the demo          */
    			overflow-y: auto;    /* Trigger vertical scroll    */
			}*/
			
			.table-style{
				border: 1px solid black !important;
                font-size: 12px;
			}
			.result-panel{
                height: 250px;
                overflow-y:scroll;
                scrollbar-color: #0b8793 #E3511A;
            }
          
            #heading-style{
                color:#0fff8a;
            }
            .inputBox{
                height: 30px;
                width:50px;
            }



           
		
            
        </style>
        <div id="pagewrap" style="border: 1px solid black;">       
        
        

            <div class="row col-xs-12 col-md-12 col-lg-12 col-sm-12"  id="content">
                <div class="col-xs-12 col-md-12 col-lg-12 col-sm-12  pt-0 pb-0" style="margin:10px">
                    	
                        <style>
                        
                            #result-table{
                                background-color:#C70039;
                                color:white;
                                font-size:x-large;
                            }

                            .footerContent{
                                background-color: #463434;
                                font-size: 10px;
                                line-height: 1.2;
                                color: antiquewhite;
                            }
                        </style>

                            <table class="table-responsive">
                                <tbody>
                                    <tr>
                                        <td style="border: 1px solid red;"><img class="img-responsive" style="width:200px;height:150px" src="img/national_lottery_logo.png"></td>
                                        <td>
                                        
                                        <table class="table-responsive table-bordered" id="result-table" border="2">
                                            <tbody>
                                                    <tr height="30" align="center">
                                                    <td colspan="" class="text-white" style="width:25vw">Draw Time : {{winningValue[0].end_time  | limitTo: 5}}{{winningValue[0].meridiem}}</td>
                                                    <td colspan="2" class="text-white" style="width:50vw">Draw date: {{(winningValue[0].draw_date) || gameStartingDate}}</td>
                                                </tr>

                                                <tr height="30" align="center">
                                                    <td colspan="" class="text-white">{{seriesList[0].series_name}}<br>(LZ 60-69)</td>
                                                    <td colspan="" class="text-white">{{seriesList[1].series_name}}<br>(RL 20-29)</td>
                                                    <td colspan="" class="text-white">{{seriesList[2].series_name}}<br>(SW 10-19)</td>
                                                </tr>

                                                <tr height="60" align="center" style="color:#280566;font-size:4vw">
                                                    <td colspan="" class="bg-special">{{winningValue[0].row_number + '' + winningValue[0].column_number}}</td>
                                                    <td colspan="" class="bg-special">{{winningValue[1].row_number + '' + winningValue[1].column_number}}</td>
                                                    <td colspan="" class="bg-special">{{winningValue[2].row_number + '' + winningValue[2].column_number}}</td>
                                                </tr>


                                            </tbody>
                                    </table>
                                        
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                                
                                
                <div class="col-xs-12 col-lg-12 col-md-12 col-sm-12 p-0">
                    <div class="d-flex flex-column text-white font-weight-bold" style="background-color:#808000;font-size:25px"> <marquee>{{scrollingMsg.message}}</marquee></div>


                   <!-- login row      -->
                    <div class="d-flex flex-column bg-gray-2" ng-show="!isLogOut"> 
                        <form class="form-inline" name="login_form">
                            <input type="text" class="form-control" ng-model="loginData.user_id" id="email" placeholder="user name" required>
                            <label for="pwd">  :</label>
                            <input type="password" class="form-control" ng-model="loginData.user_password" id="pwd" placeholder="Password" required>
                            
                            <button type="submit" class="btn btn-success ml-3" ng-click="login(loginData)" ng-disabled="login_form.$invalid">Login</button>   
                        </form> 
                    </div>
                <!-- end of login row -->

                    <div class="d-flex flex-column bg-gray-2" ng-show="isLogOut"> 
                        <div class="col-12 pull-right pt-3 pb-3">
                            <button type="submit" class="btn btn-info ml-3" ng-click="logoutCpanel()">Logout</button>   
                        </div>
                    </div>
                       
                    <div class="d-flex  bg-gray-5"> 
                        <div class="col-5 awesome">Next Draw Time :{{drawTimeList[0].end_time  | limitTo: 5}}{{drawTimeList[0].meridiem}}</div>
                        <div class="col-2 "> | </div>
                        <div class="col-5 "> Time Remining: 
                            <span class="hello" ng-show="remainingTime>=0">{{remainingTime | formatDuration}}</span>
                        </div>
                    </div>

                        
                        <div class="d-flex flex-column">
                            <div class=" submit bg-warning text-dark">
                             				                    
                                <span class="text-left pl-5" >Terminal id:<b>{{huiSessionData.user_id ? huiSessionData.user_id : 'XXXX'}}</b>
                                
                                &nbsp;</span>
                                <span class="text-left">Agent:<b>{{huiSessionData.person_name ? huiSessionData.person_name : 'XXXX'}}</b>&nbsp;</span>
                                <span class="text-left">Balance:{{activeTerminalDetails.current_balance | number:2}}&nbsp;</span>
                                <span class="text-left">Current time:{{show_time}}&nbsp;</span>
                                <input type="button" ng-show="false"  class="rounded border border-secondary" value="JODI" ng-click="singleGame=false;jodiGame=true;playInput=defaultPlayInput">
                                <input type="button" ng-show="false" class="rounded border border-secondary" value="SINGLE" ng-click="jodiGame=false;singleGame=true;playInput=defaultPlayInput">
                              
                               <a href="#" ng-click="getActiveTerminalBalance()"><i class="fas fa-sync-alt"></i></a>
                               
                                <a href="#!reportterm" ng-class="{disabled:isLogIn}" target="_blank" class="btn rounded-circle text-white pull-right" role="button">Report</a>
                                <a href="" class="btn rounded-circle text-white pull-right" role="button" ng-show="!showResultDiv" ng-click="showResultDiv=true;getResultListByDate(todayDate)">Result</a>
                                <a href="" class="btn rounded-circle text-white pull-right" role="button" ng-show="showResultDiv" ng-click="showResultDiv=false">Home</a>
                            </div>
                            <div  style="background-color:#E3511A" id="main-div" class="d-flex pt-3">
                             
                             <!-- game-table -->
                                <table ng-show="!showResultDiv" style="width:auto !important;height:max-content" class="table-responsive" border="0" align="center">
                                    <tbody>
                                        <tr>
                                            <td colspan="13"><h2><br></h2></td>
                                        </tr>

                                        <tr id="heading-style" class="text-center">
                                            <td></td>
                                            <td>Mrp</td>
                                            <td>Win</td>
                                            <td>1</td>
                                            <td>2</td>
                                            <td>3</td>
                                            <td>4</td>
                                            <td>5</td>
                                            <td>6</td>
                                            <td>7</td>
                                            <td>8</td>
                                            <td>9</td>
                                            <td>0</td>
                                            <td>Qty</td>
                                            <td>Amount</td>
                                            <td>{{winningValue[0].end_time  | limitTo: 5}}{{winningValue[0].meridiem}}</td>
                                        </tr>

                                        <tr class="text-center">
                                            <td style="color:#f4f4f9;font-size: 23px;">{{seriesList[0].series_name}}</td>
                                            <td style="color:#ffffff;font-size: 15px;">{{(seriesList[0].mrp*10) || ''}}</td>
                                            <td style="color:#ffffff;font-size: 15px;">{{seriesList[0].winning_price}}</td>
                                            <td><input type="text" ng-model="seriesOne[1]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[2]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[3]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[4]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[5]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[6]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[7]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[8]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[9]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesOne[0]" class="inputBox"></td>
                                            <td><input type="text" class="inputBox" ng-model="totalBoxSum1" readonly></td>
                                            <td><input type="text" class="inputBox" ng-model="totalTicketBuy1" readonly></td>
                                            <td><input type="text" class="inputBox bg-special font-weight-bold text-center" ng-value="winningValue[0].row_number + '' + winningValue[0].column_number"  readonly></td>
                                           
                                        </tr>

                                        <tr class="text-center">
                                            <td style="color:#f4f4f9;font-size: 23px;">{{seriesList[1].series_name}}</td>
                                            <td style="color:#ffffff;font-size: 15px;">{{(seriesList[1].mrp*10) || ''}}</td>
                                            <td style="color:#ffffff;font-size: 15px;">{{seriesList[1].winning_price}}</td>
                                            <td><input type="text" ng-model="seriesTwo[1]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[2]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[3]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[4]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[5]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[6]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[7]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[8]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[9]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesTwo[0]" class="inputBox"></td>
                                            <td><input type="text" class="inputBox" ng-model="totalBoxSum2" readonly></td>
                                            <td><input type="text" class="inputBox" ng-model="totalTicketBuy2" readonly></td>
                                            <td><input type="text" class="inputBox bg-special font-weight-bold text-center" ng-value="winningValue[1].row_number + '' + winningValue[1].column_number" readonly></td>
                                           
                                        </tr>

                                        <tr class="text-center">
                                            <td style="color:#f4f4f9;font-size: 23px;">{{seriesList[2].series_name}}</td>
                                            <td style="color:#ffffff;font-size: 15px;">{{(seriesList[2].mrp*10) || ''}}</td>
                                            <td style="color:#ffffff;font-size: 15px;">{{seriesList[2].winning_price}}</td>
                                            <td><input type="text" ng-model="seriesThree[1]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[2]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[3]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[4]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[5]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[6]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[7]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[8]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[9]" class="inputBox"></td>
                                            <td><input type="text" ng-model="seriesThree[0]" class="inputBox"></td>
                                            <td><input type="text"  class="inputBox" ng-model="totalBoxSum3" readonly></td>
                                            <td><input type="text" class="inputBox" ng-model="totalTicketBuy3" readonly></td>
                                            <td><input type="text" class="inputBox bg-special font-weight-bold text-center" ng-value="winningValue[2].row_number + '' + winningValue[2].column_number" readonly></td>
                                           
                                        </tr>
                                       
                                        <tr>
                                            <td colspan="11"></td>
                                            <td><input type="button" value="Clear" ng-click="clearDigitinputBox()"></td>
                                            <td style="color:#ffffff;font-size: 20px;">Total</td>
                                            <td><input type="text" class="inputBox" ng-value="totalBoxSum1+totalBoxSum2+totalBoxSum3" readonly></td>
                                            <td><input type="text" class="inputBox" ng-value="totalTicketBuy1+totalTicketBuy2+totalTicketBuy3" readonly></td>
                                        </tr>

                                        <tr>
                                            <td><input type="button" value="Advance Book" ng-disabled="!isLogOut"></td>
                                            <td><input type="button" value="Buy" ng-click="submitGameValues(seriesOne,seriesTwo,seriesThree)" ng-disabled="false"></td>
                                            <td colspan="11"></td>
                                            
                                        </tr>

                                    </tbody>

                                </table>    

                            <!-- end of game table -->
                                <!-- result-table  -->
                                <div class="result-panel row col-xs-12 col-md-12 col-sm-12 col-lg-12" ng-show="showResultDiv">
                                
                                    <table align="center"  st-safe-src="resultData" st-table="displayCollection"  style="width: 0%;color:white" cellpadding="0" cellspacing="0" class="table table-bordered table-hover report-table small text-justify">
                                           <thead>
                                           
                                                <tr height="33" align="center" style="border-style: hidden;border-bottom:groove">
                                                    <td class="text-left" style="border-left-style:hidden!important;border-right-style:hidden!important;border-top-style:hidden!important">
                                                        <input type="date" class="form-control" ng-model="result_date">
                                                    </td>
                                                    <td style="border-left-style:hidden!important;border-right-style:hidden!important;border-top-style:hidden!important" class="text-left border-less"><a href="#" class="btn btn-success rounded-circle" ng-click="getResultListByDate(result_date)" role="button">Show</a></td>
                                                </tr>

                                                <tr height="33" align="center">
                                                    <td class="text-center font-weight-bold" width="20%">Date</td>
                                                    <td class="text-center font-weight-bold" width="20%">Slab</td>
                                                    <td class="text-center font-weight-bold" width="20%">Lucky Zone</td>
                                                    <td class="text-center font-weight-bold" width="20%">Rajlaxmi</td>
                                                    <td class="text-center font-weight-bold" width="20%">Smartwin</td>
                                                </tr>
                                           
                                           
                                           </thead>
                                           
                                           
                                           
                                           
                                            <tbody>
                                                

                                                <tr height="33" align="center" data-ng-if="displayCollection.length==0">
                                                    <td class="text-center font-weight-bold" width="50%">No records found</td>
                                                    <td class="text-center" width="20%">XX</td>
                                                    <td class="text-center" width="20%">XX</td>
                                                    <td class="text-center" width="20%">XX</td>
                                                    <td class="text-center" width="20%">XX</td>
                                                </tr>
                                               
                                                <tr height="33" align="center" ng-repeat="x in displayCollection">
                                                    <td class="text-center" width="20%">{{x.draw_date}}</td>
                                                    <td class="text-center" width="20%">{{x.end_time +' ' +x.meridiem}}</td>
                                                    <td class="text-center" width="20%">{{(x.lucky_zone) < 10 ? ('0'+x.lucky_zone) : (x.lucky_zone)}}</td>
                                                    <td class="text-center" width="20%">{{x.rajlaxmi < 10 ? ('0'+x.rajlaxmi) : (x.rajlaxmi)}}</td>
                                                    <td class="text-center" width="20%">{{x.smartwin < 10 ? ('0'+x.smartwin) : (x.smartwin)}}</td>
                                                </tr>
                                                
                                            </tbody>		 	
                                    </table>
                                
                                </div>
                           
                            
                            </div>
                        </div>


                        <div class="d-flex footerContent pt-1 pb-1">
                            It's a amusement Game. So  use of  this website as lottery or any other illegal means is strictly prohibited.
Viewing this website is on your own risk. All the information shown here is sponsored and we warn you that Amusement  Numbers are only for fun.
We are not responsible for any issues or scam. We respect all country, state rules/laws. If you not agree with our site disclaimer. Please quit our site right now.
 @Copyright2019 All rights reserved to SmartWin software development co..
                        </div>
                </div>

                <div class="d-flex" ng-show="false">
                        <div class="col-3">test1</div>
                        <div class="col-3"><pre>resultData={{resultData | json}}</pre></div>
                        <div class="col-3"></div>
                </div>
                


<!--        PRINT PAGE-->

        <div class="container" id="receipt-div" ng-show="false" ng-repeat="x in barcodeList">
            <div ng-repeat="x in barcodeList">
                <h4>{{x.bcd}}</h4>
                <div class="d-flex col-12 mt-1 pl-0">
                    <label  class="col-2">Barcode</label>
                    <div class="col-6">
                        <span ng-bind="x.bcd">: </span>
                    </div>
                </div>
                <div class="d-flex col-12 mt-1 pl-0">
                    <label  class="col-3">Commander 2DIGIT {{x.series_name}} - {{seriesList[0].mrp}}</label>
                </div>

                <div class="d-flex col-12 mt-1 pl-0">
                    <label  class="col-1">Date:</label><span ng-bind="purchase_date"></span>

                    <label  class="col-1">Dr.Time:</label> <span ng-bind="ongoing_draw" class="col-1"></span>
                </div>
                <hr style="border-top: dotted 1px;" />

                <div class="d-flex flex-wrap align-content-start">
                    <div class="p-2" ng-repeat="i in allGameValue track by $index">
                        {{i + ','}}&nbsp;

                    </div>
                </div>


                <hr style="border-top: dotted 1px;" />
                <div class="d-flex col-12">
                    <label  class="col-1">MRP</label><span>: {{seriesList[0].mrp}}</span>
                    <label  class="col-1">Qty:</label> <span ng-bind="totalticket_qty| number:2"></span>
                    <label  class="col-2">{{purchase_time}}</label>
                </div>
                <div class="d-flex col-12">
                    <label  class="col-1">Rs:</label><span ng-bind="totalticket_purchase|number: 2"></span>
                </div>
                <div class="d-flex col-12">
                    <label  class="col-2">Terminal Id</label><span>: <?php echo ($this->session->userdata('user_id'));?></span>

                </div>
                <div class="d-flex col-12">
                    <angular-barcode ng-model="x.bcd" bc-options="barcodeOilBill" bc-class="barcode" bc-type="img"></angular-barcode>
                </div>

            </div>


        </div>




<!--        END-->



        <?php
    }

    

    public function inser_2d_play_input(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Game_model->insert_game_values((object)$post_data['playDetails'],$post_data['drawId'],$post_data['purchasedTicket']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    public function get_all_play_series(){
        $result=$this->Game_model->select_play_series()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    public function get_all_draw_time(){
        $result=$this->Game_model->select_from_draw_master()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
     public function get_all_draw_name_list(){
        $result=$this->Game_model->select_draw_name_list()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    

    public function get_game_activation_details(){
        $result=$this->Game_model->select_game_activation()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    public function get_draw_result(){
        $post_data =json_decode(file_get_contents("php://input"), true);

        $result=$this->Game_model->select_game_result_after_each_draw($post_data['drawId']);
        //print_r($result);
//        $report_array['records']=$result->records;
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }


    public function get_previous_result(){
        $result=$this->Game_model->select_previous_game_result()->result_array();
//        $report_array['records']=$result->records;
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }

    public function get_result_sheet_today(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Game_model->select_today_result_sheet()->result_array();
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }

    public function get_result_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Game_model->select_result_sheet_by_date($post_data['result_date'])->result_array();
        echo json_encode($result,JSON_NUMERIC_CHECK);
    }


    
    
    function logout_cpanel(){
    	$user_id=$this->session->userdata('user_id');
    	
    	$result=$this->Game_model->logout_current_session($user_id);
        $newdata = array(
            'person_id'  => '',
            'person_name'     => '',
            'user_id'=> 0,
            'person_cat_id'     => 0,
            'is_logged_in' => 0,
            'is_currently_loggedin' => 0,
        );
        $this->session->set_userdata($newdata);

        echo json_encode($newdata,JSON_NUMERIC_CHECK);
    }
    
    
    public function get_timestamp(){
    	$date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
		echo $date->format('h:i:sA');    
            
    }
    
    
    public function get_message(){
        $result=$this->Game_model->select_message()->result_array();
         $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    public function get_active_draw(){
        $result=$this->Game_model->select_draw_interval();
        $current_draw = $result->active_draw->end_time;
       
        $draw_date=array();
        $draw_date = explode(":",$current_draw);
        $dt_milli_sec = (($draw_date[0] * 60 + $draw_date[1])*60 + $draw_date[2]) * 1000;

        $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $dt = $date->format('h:i:s');
        $current_time=array();
        $current_time = explode(":",$dt);
        $cur_time_milli_sec = (($current_time[0] * 60 + $current_time[1])*60 + $current_time[2]) * 1000;
        $intervalTime = $dt_milli_sec - $cur_time_milli_sec;

        $record=array();
        $record['intervalTime']=$intervalTime;
        $record['nextIntervalList']=$result->interval_list;
        echo json_encode($record,JSON_NUMERIC_CHECK);
    }

    // public function get_next_draw_time(){
    //     $result=$this->Game_model->select_from_draw_master()->result_array();
    //     $current_draw = $result[0]['end_time'];
    //     $draw_date=array();
    //     $draw_date = explode(":",$current_draw);
    //     $cur_milli_sec = (($draw_date[0] * 60 + $draw_date[1])*60 + $draw_date[2]) * 1000;

    //     $next_time=$this->Game_model->select_next_draw_time_only();
    //     $next_draw = $next_time->end_time;
    //     $next_draw_time=array();
    //     $next_draw_time = explode(":",$next_draw);
    //     $next_milli_sec = (($next_draw_time[0] * 60 + $next_draw_time[1])*60 + $next_draw_time[2]) * 1000;

        
    //     $intervalTime = $next_milli_sec - $cur_milli_sec;
    //     echo json_encode($intervalTime,JSON_NUMERIC_CHECK);
    // }


}
?>
