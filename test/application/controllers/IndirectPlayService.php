<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IndirectPlayService extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Indirect_play_model');
        $this -> load -> model('Common_function_model');

        $this -> is_logged_in();
    }
   
   function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		$person_cat_id = $this -> session -> userdata('person_cat_id');
		if (!isset($is_logged_in) || $is_logged_in != 1 || $person_cat_id!=1) {
			echo 'you have no permission to use admin area'. '<a href="#!" ng-click="goToFrontPage()">Login</a>';
			die();
		}
	}


    public function angular_view_game_input(){
        ?>
        <style type="text/css">
            #search-results {
                max-height: 200px;
                border: 1px solid #dedede;
                border-radius: 3px;
                box-sizing: border-box;
                overflow-y: auto;
            }

            h1 
            { 
                text-shadow:3px 1px #fb0a9c; 
                font-size:4vw;
            } 
        </style>
        <div class="d-flex col-12" ng-include="headerPath"></div>
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Help Terminal</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==2 && selectedTab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-envelope"></i>Stockist list</a>
                    </li> -->
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1" class="bg-cpanel">
                            <form name="gameForm" class="form-horizontal">
                                <div class="d-flex justify-content-center ">
                                    <div class="col">
                                         
                                        <div class="d-flex mt-1">
                                            <div class="col-4">
                                                <h1>Smartwin</h1>
                                            </div>
                                            <div class="col-4" >
                                                <b>Draw Time: &nbsp;</b>{{currentTime.end_time  | limitTo: 5}}{{currentTime.meridiem}}
                                            </div>
                                           
                                            <div class="col-4" ><b>Balance: &nbsp;</b>{{(activeTerminalDetails.current_balance) | number:2}}</div>
                                        </div>
                                        <div class="d-flex  mt-1">
                                            
                                        <table  style="width:auto !important;height:max-content" class="table-responsive" border="0" align="center">
                                    <tbody>
                                        <tr>
                                            <td colspan="13"><h2><br></h2></td>
                                        </tr>

                                        <tr id="heading-style" class="text-center">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>

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
                                        </tr>

                                        <tr class="text-center">
                                            <td><a href="#" ng-click="getActiveDrawTime()"><i class="fas fa-sync-alt"></i></a></td>
                                            <td> 
                                            
                                                <select ng-model="draw_id" class="form-control" required>
                                                    <option selected disabled>Select Time </option>
                                                    <option ng-repeat="x in timeList" value="{{x.draw_master_id}}">
                                                        {{(x.end_time |limitTo: 5) + ' '+ (x.meridiem)}}
                                                    </option>
                                                </select>                                             
                                             
                                             </td>


                                            <td> 
                                            
                                            <select required
                                                    class="form-control "
                                                    data-ng-model="terminal"
                                                    data-ng-options="x as x.user_id for x in terminalList" ng-change="getTerminalBalance(terminal.person_id)">
                                            </select>
                                            
                                             
                                             
                                             </td>
                                            <td></td>

                                            <td><input type="text" ng-model="seriesOne[1]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[2]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[3]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[4]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[5]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[6]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[7]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[8]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[9]" class="inputBox form-control"></td>
                                            <td><input type="text" ng-model="seriesOne[0]" class="inputBox form-control"></td>
                                            <td><input type="text" style="height:30px;width:70px" class="form-control text-right" ng-model="totalBoxSum1" readonly></td>
                                            <td><input type="text" style="height:30px;width:70px" class="form-control text-right" ng-model="totalTicketBuy1" readonly></td>
                                           
                                           
                                        </tr>

                                                                             
                                                                               

                                    </tbody>

                                </table>   

                                        </div>
                                      
                                      


                                        <div class="d-flex justify-content-center mt-2">
                                            <div class="col-4">
                                                <input type="button" ng-click="submitGameValues(draw_id,terminal,seriesOne)" ng-disabled="gameForm.$invalid" value="Save"/>
                                                <input type="button" ng-click="clearDigitInputBox()" value="Clear"/>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center mt-2">
                                            <div class="">
                                                <span ng-show="submitStatus" class="text-success">Record successfully added</span>
                                                <span ng-show="updateStatus" class="text-success">Update successful</span>
                                            </div>
                                        </div>


                                        <div class="d-flex mt-1" ng-show="false">
                                            <div class="col-3"> 
                                                <pre>currentTime={{currentTime | json}}</pre>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->
                    </div>

                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            <style type="text/css">
                                .bee{
                                    background-color: #d9edf7;
                                }
                                .banana{
                                    background-color: #c4e3f3;
                                }
                                #stockist-table-div table th{
                                    background-color: #1b6d85;
                                    color: #a6e1ec;
                                    cursor: pointer;
                                }
                                a[ng-click]{
                                    cursor: pointer;
                                }

                            </style>
                            <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                            <div id="stockist-table-div" class="d-flex">
                                <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                    <tr>
                                        <th>SL></th>
                                        <th ng-click="changeSorting('stockist_name')">Name<i class="glyphicon" ng-class="getIcon('stockist_name')"></i></th>
                                        <th ng-click="changeSorting('user_id')">Login Id<i class="glyphicon" ng-class="getIcon('user_id')"></i></th>
                                        <th ng-click="changeSorting('user_password')">Password<i class="glyphicon" ng-class="getIcon('user_password')"></i></th>
                                        <th>Edit</th>
                                    </tr>
                                    <tbody ng-repeat="s in stockistList | filter : searchItem  | orderBy:sort.active:sort.descending">
                                    <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                        <td>{{ $index+1}}</td>
                                        <td>{{s.stockist_name}}</td>
                                        <td>{{s.user_id}}</td>
                                        <td>{{s.user_password}}</td>
                                        <td ng-click="updateStockistFromTable(s)"><a href="#"><i class="fa fa-edit"></i></a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                               

                </div>
            </div>
        </div>

        <?php
    }
    function save_new_stockist(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Stockist_model->insert_new_stockist((object)$post_data['stockist']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    public function get_all_terminal(){
        $result=$this->Common_function_model->select_all_terminal()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }


    public function get_all_time(){
        $result=$this->Common_function_model->select_draw_name_list_for_manual_game_input()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    public function is_active_draw_time(){
        $result=$this->Common_function_model->is_active_draw()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

  
    public function inser_2d_play_input(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Indirect_play_model->insert_game_values((object)$post_data['playDetails'],$post_data['drawId'],$post_data['terminalId'],$post_data['purchasedTicket']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    public function get_current_user_id(){
        $result=$this->Stockist_model->select_next_user_id_for_stockist();
        echo $result;
    }

 

}
?>