<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DrawReport extends CI_Controller {
    
    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('Person');
        $this -> load -> model('Draw_report_model');
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




    public function angular_view_draw_report(){
        ?>
        <style type="text/css">
            #search-results {
                max-height: 200px;
                border: 1px solid #dedede;
                border-radius: 3px;
                box-sizing: border-box;
                overflow-y: auto;
            }
            .report-table tr th,.report-table tr td{
                border: 1px solid black !important;
                font-size: 10px;
                line-height: 1.5;
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
        <div class="d-flex col-12" ng-include="headerPath"></div>
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)">Drawwise Report</a>
                    </li>

                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1" >
                            <form name="stockistForm" class="form-horizontal">
                                <div class="card">

                                    <div class="card-header">
                                        <div class="d-flex justify-content-center">
                                            <div class=""><input type="date" class="form-control" ng-model="start_date" ng-change="changeDateFormat(start_date)"></div>




                                            <div class="ml-2"><input type="button" class="btn btn-info form-control" value="Show" ng-click="getDrawWiseSaleReport(start_date)"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-center">
                                            <div class="loader mt-1" ng-show="isLoading"></div>
                                        </div>

                                        <div class="d-flex" ng-show="!isLoading">
                                            <div class="col-3"></div>
                                            <div class="col-6">
                                                <style>
                                                 tbody {
                                                        display:block;
                                                        max-height:500px;
                                                        overflow-y:auto;
                                                    }
                                                    thead, tbody tr {
                                                        display:table;
                                                        width:100%;
                                                        table-layout:fixed;
                                                    }
                                                    thead {
                                                        width: calc( 100% - 1em )
                                                    } 
                                                </style>

                                                <table cellpadding="0" cellspacing="0" class="table table-bordered table-hover report-table small text-justify">
                                                    <thead>
                                                    <tr>
                                                        <th class="p-0 text-center">S/No</th>
                                                        <th class="p-0  text-center">Game name</th>
                                                        <th class="p-0  text-center">Draw Time</th>
                                                        <th class="p-0 text-center">MRP</th>
                                                        <th class="p-0  text-center">Sales Amount</th>
                                                        <th class="p-0  text-center">Prize Value</th>
                                                        <th class="p-0 text-center ">Payout Server</th>
                                                        <th class="p-0 text-center ">Payout on Sales</th>
                                                        <th class="p-0 text-center ">Manual Result</th>
                                                        <th class="p-0 text-center ">Result on Sales</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <tr ng-repeat="x in saleReport" ng-style="$index==0 && grandTotalStyle || {{x.ticket_taken_time=='Total' && totalRowStyle}}">
                                                        <td class="p-0">
                                                            {{$index+1}}
                                                        </td>
                                                        <td class="p-0 text-center"> {{x.series_name}}</td>
                                                        <td class="p-0 text-left">{{x.draw_time}}</td>
                                                        <td class="p-0  text-center">{{(x.mrp || gameMrp) | number:2}}</td>
                                                        <td class="p-0  text-center">{{(x.sale_amount || 0) | number:2}}</td>
                                                        <td class="p-0  text-center">{{(x.prize_value || 0)| number:2}}</td>
                                                        <td class="p-0 text-center">{{x.payout_server | number:2}}</td>
                                                        <td class="p-0 text-center">{{
                                                        	(((x.prize_value / x.sale_amount)*100) || 0) | number:2
                                                        	
                                                        	}}</td>
                                                        <td class="p-0 text-center">{{x.result}}</td>
                                                        <td class="p-0 text-center">{{x.result_row + ''+x.result_column}}</td>
                                                    </tr>


                                                    </tbody>


                                                </table>
                                            </div>
                                            <div class="col-3"></div>
                                        </div>
                                    </div>
                                </div>


                                <div class="d-flex justify-content-center" ng-show="alertMsg">
                                    <div>No records found</div>
                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->
                        <div class="d-flex">
                            <!--                                                        <div class="col"><pre>stockistList={{stockistList | json}}</pre></div>-->
                            <!--                            <div class="col"><pre>terminalList={{terminalList | json}}</pre></div>-->
                        </div>
                    </div>

              


                </div>
            </div>
        </div>

        <?php
    }

    public function get_sale_report_draw_wise(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Draw_report_model->select_draw_wise_sale_report($post_data['start_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }



}
?>