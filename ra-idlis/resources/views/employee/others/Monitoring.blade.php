@if (session()->exists('employee_login'))  
@extends('mainEmployee')
@section('title', 'Monitoring')
@section('content')
  <div class="content p-4">
    <div class="card">
      <div class="card-header bg-white font-weight-bold">
           Monitoring Entry / <a href="{{asset('employee/dashboard/others/monitoring/inspection')}}">Monitoring Tool </a> / <a href="{{asset('employee/dashboard/others/monitoring/technical')}}">Technical Findings</a> / <a href="{{asset('employee/dashboard/others/monitoring/updatestatus')}}">Update Status of CA</a>
           <br>
           <a title="Add New Monitoring" data-toggle="modal" data-target="#monModal"><button class="btn-primarys"><i class="fa fa-plus-circle"></i>&nbsp;Add new</button></a>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-hover" style="font-size: 13px;" id="example">
          <thead>
            <tr>
              <th scope="col" style="text-align: center; width:auto;">ID</th>
              <th scope="col" style="text-align: center; width:auto;">Code</th>
              <th scope="col" style="text-align: center; width:auto">Name of Facility</th>
              <th scope="col" style="text-align: center; width:250px;">Location/ <br>Address</th>
              <th scope="col" style="text-align: center; width:auto">Facility Code</th>
              {{-- <th scope="col" style="text-align: center; width:auto">Date of Monitoring</th> --}}
              <th scope="col" style="text-align: center; width:auto">Team Assigned</th>
              <th scope="col" style="text-align: center; width:auto">Monitoring Date</th>
              <th scope="col" style="text-align: center; width:auto">Span of <br> Monitoring</th>
              <th scope="col" style="text-align: center; width:auto;">NOV <br>Reference<br> number</th>
              {{-- <th scope="col" style="text-align: center; width:auto">Status of Compliance</th> --}}
              {{-- <th scope="col" style="text-align: center; width:auto">Course <br>of<br>Action/Remarks</th> --}}
              <th scope="col" style="text-align: center; width:auto">Status</th>
              <th scope="col" style="text-align: center">Options</th>
            </tr>
          </thead>
          <tbody>
            @isset($AllData)
              @foreach($AllData as $key => $value)
                <tr>
                  <td style="text-align:center">{{$value->monid}}</td>
                  <td style="text-align:center">{{AjaxController::getAppCodeByAppid($value->appid)}}</td>
                  <td style="text-align:center">{{$value->name_of_faci}}</td>
                  <td style="text-align:center; width:250px">{{$value->address_of_faci}}</td>
                  <td style="text-align:center">{{$value->type_of_faci}}</td>
                  {{-- <td style="text-align:center">{{ \Carbon\Carbon::parse($value->date_recom)->format('M d, Y') }}</td> --}}
                  <td style="text-align:center">
                    @if(isset($value->team))
                      @if(!empty(AjaxController::getTeamByTeamId($value->team)))
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#sMonModal" onclick="showteam('{{$value->team}}', '{{AjaxController::getTeamByTeamId($value->team)->montname}}')">
                          <i class="fa fa-fw fa-eye"></i>
                          Show Team
                        </button>
                      @else
                        <span style="color: red"><b>Not Yet Assigned</b></span>
                      @endif
                    @else
                      <span style="color: red"><b>Not Yet Assigned</b></span>
                    @endif
                  </td>
                  <td style="text-align:center">
                    @if($value->date_monitoring != "") 
                      <b>{{\Carbon\Carbon::parse($value->date_monitoring)->format('M d, Y')}}</b> 
                      {{-- to<b>{{\Carbon\Carbon::parse($value->date_monitoring_end)->format('M d, Y')}}</b> --}}
                    @endif
                  </td>
                  <td style="text-align:center">
                    @if($value->date_monitoring != "") 
                      @php
                        $date_start = new DateTime($value->date_monitoring);
                        $date_end = new DateTime($value->date_monitoring_end);
                        $interval = $date_start->diff($date_end);
                        $interval->d = $interval->d;
                      @endphp
                      @if($interval->d > 1)
                        {{$interval->d}} days
                      @else
                        {{$interval->d}} day
                      @endif
                    @endif
                  </td>
                  <td style="text-align:center">{{$value->novid}}</td>
                  @if($value->isApproved == "1")
                    <td style="text-align:center;" class="bg-success text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('A')[0]->trns_desc}}
                      </span>
                    </td>
                  @elseif($value->isApproved == "2" )
                    <td style="text-align:center;" class="bg-danger text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('NA')[0]->trns_desc}}
                      </span>
                    </td>
                  @elseif($value->isFinePaid != "" )
                    <td style="text-align:center;" class="bg-info text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('PP')[0]->trns_desc}}
                      </span>
                    </td>
                  @elseif($value->recommendation != "")
                    <td style="text-align:center;" class="bg-primary text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('FPE')[0]->trns_desc}}
                      </span>
                    </td>
                  @elseif($value->isCDO != "")
                    <td style="text-align:center;" class="bg-danger text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{"CDO Applied"}}
                      </span>
                    </td> 
                  @elseif($value->assessmentStatus != "")
                    <td style="text-align:center;" class="bg-warning text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('FA')[0]->trns_desc}}
                      </span>
                    </td> 
                  @elseif($value->team != "")
                    <td style="text-align:center;" class="bg-danger text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('FM')[0]->trns_desc}}
                      </span>
                    </td>
                  @elseif($value->team == "")
                    <td style="text-align:center;" class="bg-secondary text-light font-weight-bold">
                      <span style="text-shadow: 2px 2px 4px #000000">
                        {{AjaxController::getTransStatusById('NT')[0]->trns_desc}}
                      </span>
                    </td>
                  @endif

                  {{-- <td style="text-align:center">
                    <center>
                      <button class="btn btn-outline-info" data-toggle="modal" data-target="#eMonModal" onclick="getEditData(
                        '{{$value->novid}}', '{{$value->name_of_faci}}', '{{ AjaxController::getFacTypeByFacid($value->type_of_faci)[0]->facname }}', '{{\Carbon\Carbon::parse($value->date_added)->format('M d, Y')}}')" title="View {{$value->name_of_faci}}">
                        <i class="fa fa-fw fa-eye"></i>
                      </button>

                      @if($value->team != "")
                        @php
                          $url = 'employee/dashboard/processflow/assessment/'.$value->monid.'/MON/'.$value->type_of_faci;
                        @endphp
                        <button class="btn btn-outline-primary" title="Inspect {{$value->name_of_faci}}" onclick="window.location.href='{{url($url)}}'">
                          <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                      @endif

                      @if($value->team == "")
                        <button class="btn btn-outline-danger" data-toggle="modal" data-target="#dMonModal" onclick="getDelData(
                          '{{$value->monid}}', '{{$value->name_of_faci}}'
                          )" title="Delete {{$value->name_of_faci}}">
                          <i class="fa fa-fw fa-trash"></i>
                        </button>
                      @endif
                    </center>
                  </td> --}}

                  <td>
                    <div class="dropup">
                      <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-align-justify"></i>
                      </button>

                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="padding-left: 10px">

                        <button class="btn btn-outline-info" data-toggle="modal" data-target="#eMonModal" onclick="getEditData(
                          '{{$value->novid}}', '{{$value->name_of_faci}}', '{{ AjaxController::getFacTypeByFacid($value->type_of_faci)[0]->facname }}', '{{\Carbon\Carbon::parse($value->date_added)->format('M d, Y')}}')" title="View {{$value->name_of_faci}}">
                          <i class="fa fa-fw fa-eye"></i>
                        </button>

                        @if($value->team != "")
                          @php
                            $url = 'employee/dashboard/processflow/assessment/'.$value->monid.'/MON/'.$value->type_of_faci;
                          @endphp
                          <button class="btn btn-outline-primary" title="Inspect {{$value->name_of_faci}}" onclick="window.location.href='{{url($url)}}'">
                            <i class="fa fa-search" aria-hidden="true"></i>
                          </button>
                        @endif

                        @if($value->team == "")
                          <button class="btn btn-outline-danger" data-toggle="modal" data-target="#dMonModal" onclick="getDelData(
                            '{{$value->monid}}', '{{$value->name_of_faci}}'
                            )" title="Delete {{$value->name_of_faci}}">
                            <i class="fa fa-fw fa-trash"></i>
                          </button>
                        @endif
                      </div>
                    </div>
                  </td>

                </tr>
              @endforeach
            @endisset
          </tbody>
        </table>
      </div>
    </div>
  </div>  

  {{-- Monitoring Identification --}}
  <div class="modal fade" id="monModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content " style="border-radius: 0px;border: none;">
        <div class="modal-body text-justify" style=" background-color: #272b30;color: white;">
          <h5 class="modal-title text-center">
            <strong>Add New Facilities To Monitor</strong> 
            <span class="btn button-outline-primary" data-toggle="tooltip" title="" data-html="true" style="cursor: pointer" data-original-title="<b style='color:yellow'>WARNING</b>, Submitting new facilities is irreversible.">
              <i class="fa fa-question-circle" aria-hidden="true"></i>
            </span>
          </h5>
          <hr>
          <div class="input-group form-inline">
            <div class="card-body">
              <form method="POST" action="{{-- {{asset('employee/dashboard/others/mon_submit')}} --}}" data-parsley-validate>

                {{csrf_field()}}

                <input type="hidden" name="e_sappid" id="e_sappid">
                <input type="hidden" name="e_date" id="e_date" value="{{date('Y-m-d')}}">
                
                {{-- address of faci --}}
                <div class="row">
                  <div class="col-sm-4">
                    Location:{{-- <span style="color:red">*</span> --}}
                  </div>

                  <div class="col-sm-8">
                    <input type="hidden" name="address_of_faci" class="form-control form-inline w-100" readonly id="facaddr">
                    <div class="row mb-1" hidden>
                      <div class="col-sm-6">
                        Region: <br>
                        <input class="form-control w-100" type="" name="" readonly id="facr">
                      </div>

                      <div class="col-sm-6">
                        Provice: <br>
                        <input class="form-control w-100" type="" name="" readonly id="facp">
                      </div>
                    </div>

                    <div class="row mb-1" hidden>
                      <div class="col-sm-6">
                        City/Municipality: <br>
                        <input class="form-control w-100" type="" name="" readonly id="facc">
                      </div>

                      <div class="col-sm-6" hidden>
                        Barangay: <br>
                        <input class="form-control w-100" type="" name="" readonly id="facb">
                      </div>
                    </div>

                    <div class="row mb-1">
                      <div class="col-sm-6">
                        Region: <br>
                        <select class="form-control" style="width: 100%" id="rgnid" name="rgnid" autocomplete="off" required>
                          <option selected disabled value hidden>Please select</option>
                          @if(count($region) > 0) @foreach($region AS $each)
                          <option value="{{$each->rgnid}}">{{$each->rgn_desc}}</option>
                          @endforeach @endif
                        </select>
                      </div>

                      <div class="col-sm-6">
                       Province: <br>
                        <select class="form-control" style="width: 100%" id="provid" name="provid" autocomplete="off" required>
                          <option selected disabled value hidden>Please select</option>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-1">
                      <div class="col-sm-12">
                        City/Municipality: <br>
                        <select style="width: 100%" class="form-control" id="cmid" name="cmid" autocomplete="off" required>
                          <option selected disabled value hidden>Please select</option>
                        </select>
                      </div>

                      <div class="col-sm-6" hidden>
                        Barangay: <br>
                        <select style="width: 100%" class="form-control" id="brgyid" name="brgyid" autocomplete="off" required>
                          <option selected disabled value hidden>Please select</option>
                        </select>
                      </div>
                    </div>



                  </div>


                </div>

                {{-- Criteria --}}
                <div class="row mb-3 mt-3">
                  <div class="col-sm-4">
                    Type of Service:<span style="color:red">*</span>
                  </div>

                  <div class="col-sm-8">
                    <select name="name_of_faci" class="form-control w-100" onchange="changeFaciMonitoring()" id="factype" data-parsley-required-message="<b>*Type of Facility</b> required" required data-parsley="factype" required>  
                      <option disabled hidden selected value=""></option>
                      @isset($TypName)
                        @foreach($TypName as $key => $value)
                          <option value="{{$value->facid}}">{{$value->facname}}</option>
                        @endforeach
                      @endisset
                    </select>
                  </div>
                </div>

                {{-- Name of Facility --}}

                <div class="row mb-2">
                  <div class="col-sm-4">
                    Name of Facility:<span style="color:red">*</span> &nbsp;&nbsp;

                    <span class="btn button-outline-primary" data-toggle="tooltip" title="" data-html="true" style="cursor: pointer" data-original-title="All Available Facilities will be selected.">
                      <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </span>

                  </div>

                  <div class="col-sm-8">
                    <select name="type_of_faci" class="form-control w-100" id="facName" multiple onchange="changeFaciType()">
                      {{-- <option diabled hidden selected value=""></option> --}}
                    </select>
                  </div>
                </div>

                <hr>

                <div class="mx-auto">
                  <button type="button" name="btn_sub" class="btn btn-primary" data-toggle="modal" data-target="#prModal" onclick="submitprompt(document.getElementById('facName').children)"><b>SUBMIT</b></button>
                </div>
              </form> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    function submitprompt(children) {
      if(document.getElementById('factype').value == "") {
        document.getElementById('prmsg').innerHTML = "<b>Please select a facility before submitting.</b>";
        document.getElementById('prdisplay').innerHTML = "";
        document.getElementById('prsubmit').hidden=true;
        document.getElementById('prclose').hidden=false;
      } else if(document.getElementById('facName').children[0].value=="") {
        document.getElementById('prmsg').innerHTML = "<b>There are no available facility/s.</b>";
        document.getElementById('prdisplay').innerHTML = "";
        document.getElementById('prsubmit').hidden=true;
        document.getElementById('prclose').hidden=false;
      } else {
        document.getElementById('prsubmit').hidden=false;
        document.getElementById('prclose').hidden=true;
        document.getElementById('prmsg').innerHTML = "<b>Add these following facilities to monitor: </b>";
        document.getElementById('p_sappid').value = document.getElementById('e_sappid').value;
        document.getElementById('p_date').value = document.getElementById('e_date').value;
        document.getElementById('prfactype').value = document.getElementById('factype').value;

        var display = "";
        Array.from(children).forEach(function(v) {
          display += v.text + " " + "<br>";
        });

        document.getElementById('prdisplay').innerHTML = display;
      }
      
    }
  </script>

  {{-- Monitoring Evaluate --}}
  <div class="modal fade" id="eMonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content " style="border-radius: 0px;border: none;">
        <div class="modal-body text-justify" style=" background-color: #272b30;color: white;">
          <h5 class="modal-title text-center"><strong>View Monitoring</strong></h5>
          <hr>
          <div class="input-group form-inline mb-1 mt-2">
            <form class="container" method="POST" action="">
              {{csrf_field()}}

              {{-- hfsrbno --}}
              <input class="form-control w-100" type="" name="hfsrbno" id="hfsrbno" hidden>

              {{-- nov --}}
              <div class="row mb-3">
                <div class="col-sm-5 w-100">
                  NOV Reference number:
                </div>

                <div class="col-sm-7 w-100">
                  <input class="form-control w-100" type="" name="edit_nov" id="edit_nov" readonly>
                </div>
              </div>

              {{-- date issued --}}
              <div class="row mb-3">
                <div class="col-sm-5 w-100">
                    Date Added:
                </div>

                <div class="col-sm-7 w-100">
                    <input class="form-control w-100" type="" name="edit_date" id="edit_date" readonly>
                </div>
              </div>

              {{-- name --}}
              <div class="row mb-3">
                <div class="col-sm-5 w-100">
                  Name of Facility:
                </div>

                <div class="col-sm-7 w-100" colspan="12">
                  <input class="form-control w-100" type="" name="edit_name" id="edit_name" readonly>
                </div>
              </div>

              {{-- type --}}
              <div class="row mb-3">
                <div class="col-sm-5 w-100">
                  Type of Facility:
                </div>

                <div class="col-sm-7 w-100">
                  <input class="form-control w-100" type="" name="edit_type" id="edit_type" readonly>
                </div>
              </div>

              {{-- submit btn --}}
              <div class="row mt-3">
                <div class="col" colspan="6">
                  {{-- <button type="button" class="btn btn-outline-success w-100"><center>Save</center></button> --}}
                </div>
                <div class="col" colspan="6">
                  <button type="button" data-dismiss="modal" class="btn btn-outline-danger w-100"><center>Close</center></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Monitoring Team --}}
  <div class="modal fade" id="sMonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content " style="border-radius: 0px;border: none;">
        <div class="modal-body text-justify" style=" background-color: #272b30;color: white;">
          <h5 class="modal-title text-center"><strong>View Team</strong></h5>
          <hr>
          <div class="input-group form-inline mb-1 mt-2">
            <form class="container" method="POST" action="">
              
              <div class="row mt-3">
                <div class="col-sm-5">
                  Teams:
                </div>
                <div class="col-sm-7">
                  <input readonly id="steam" class="form-control w-100">
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-sm-12">
                  Members:
                </div>
                <div class="col-sm-12">
                  <select readonly class="form-control w-100" id="smember" multiple rows="5" disabled></select>
                </div>
              </div>

              {{-- submit btn --}}
              <div class="row mt-3">
                <div class="col-sm-6">
                  {{-- <button type="button" class="btn btn-outline-success w-100"><center>Save</center></button> --}}
                </div>
                <div class="col-sm-6">
                  <button type="button" data-dismiss="modal" class="btn btn-outline-danger w-100"><center>Close</center></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Monitoring Delete --}}
  <div class="modal fade" id="dMonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content " style="border-radius: 0px;border: none;">
        <div class="modal-body text-justify" style=" background-color: #272b30;color: white;">
          <h5 class="modal-title text-center"><strong>Delete Monitoring</strong></h5>
          <hr>
          <div class="input-group form-inline mb-1 mt-2">
            <form class="container" method="POST" action="{{asset('employee/dashboard/others/mon_delete')}}">
              {{csrf_field()}}

              {{-- monid --}}
              <input class="form-control w-100" type="" name="dmonid" id="dmonid" hidden>

              {{-- message --}}
              <div class="row">
                <div class="col w-100" colspan="12">
                  <center>Are you sure you want to delete <b><span style="color:red" id="delMsg"></span></b></center>
                </div>
              </div>

              {{-- submit btn --}}
              <div class="row mt-3">
                <div class="col" colspan="6">
                  <button type="submit" class="btn btn-outline-success w-100"><center>Yes</center></button>
                </div>
                <div class="col" colspan="6">
                  <button type="button" data-dismiss="modal" class="btn btn-outline-danger w-100"><center>No</center></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Monitoring Submit Prompt --}}
  <div class="modal fade" id="prModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content " style="border-radius: 0px;border: none;">
        <div class="modal-body text-justify" style=" background-color: #5a636b;color: white;">
          <h5 class="modal-title text-center">
            <strong>CONFIRMATION</strong> 
          </h5>
          <hr>
          <div class="input-group form-inline">
            <div class="card-body">
              <form method="POST" action="{{asset('employee/dashboard/others/mon_submit')}}" data-parsley-validate>

                {{csrf_field()}}

                <input type="hidden" name="e_sappid" id="p_sappid">
                <input type="hidden" name="e_date" id="p_date" value="{{date('Y-m-d')}}">
                 <input type="hidden" name="cmidVal" id="cmidVal">

                <div class="row mb-5">
                  <div class="col-sm-12" id="prmsg">
                  </div>

                  <div class="col-sm-12 mt-3" id="prdisplay">
                  </div>
                </div>

                {{-- Criteria --}}
                <div class="row mb-2" hidden>
                  <div class="col-sm-4">
                    Type of Facility:<span style="color:red">*</span>
                  </div>

                  <div class="col-sm-8" hidden>
                    <select name="name_of_faci" class="form-control w-100" onchange="changeFaciMonitoring()" id="prfactype" data-parsley-required-message="<b>*Type of Facility</b> required" required data-parsley="factype">  
                      <option disabled hidden selected value=""></option>
                      @isset($TypName)
                        @foreach($TypName as $key => $value)
                          <option value="{{$value->facid}}">{{$value->facname}}</option>
                        @endforeach
                      @endisset
                    </select>
                  </div>
                  
                </div>

                {{-- Name of Facility --}}

                <div class="row mb-2" hidden>
                  <div class="col-sm-4">
                    Name of Facility:<span style="color:red">*</span> &nbsp;&nbsp;

                    <span class="btn button-outline-primary" data-toggle="tooltip" title="" data-html="true" style="cursor: pointer" data-original-title="All Available Facilities will be selected.">
                      <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </span>

                  </div>

                  <div class="col-sm-8" hidden>
                    <select name="type_of_faci" class="form-control w-100" id="pfacName" multiple onchange="changeFaciType()">
                      {{-- <option diabled hidden selected value=""></option> --}}
                    </select>
                  </div>
                </div>

                {{-- address of faci --}}
                <div class="row" hidden>
                  <div class="col-sm-4">
                    Address of Facility:<span style="color:red">*</span>
                  </div>

                  <div class="col-sm-8">
                    <input type="text" name="address_of_faci" class="form-control form-inline w-100" readonly id="pfacaddr">
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-12">
                    <button type="submit" name="btn_sub" id="prsubmit" class="btn btn-primary w-100"><b>SUBMIT</b></button>
                  </div>
                </div>

                <div class="row" id="prclose" hidden>
                  <div class="col-sm-12">
                    <button type="button" data-dismiss="modal"  name="btn_sub" class="btn btn-danger w-100"><b>CLOSE</b></button>
                  </div>
                </div>
              </form> 
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function sendRequestRetArr(arr_data, loc, type, bolRet, objFunction) {
      try {
        type = type.toUpperCase();
        var xhttp = new XMLHttpRequest();
        if(bolRet == true) {
          xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                objFunction.functionProcess(JSON.parse(this.responseText));
              }
          };
        }
        xhttp.open(type, loc, bolRet);
        if(type != "GET") {
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.send(arr_data.join('&'));
        } else {
          xhttp.send();
        }
        if(bolRet == false) {
          objFunction.functionProcess(JSON.parse(xhttp.responseText));
          _hasReturned = 1;
        }
      } catch(errMsg) {
        console.log(errMsg);
        }
    }


    let _obj = {rgnid: "province", provid: "city_muni", cmid: "barangay"}, _arrName = {rgnid: "provid", provid: "cmid", cmid: "brgyid"}, _arrCol = {rgnid: ["provid", "provname"], provid: ["cmid", "cmname"], cmid: ["brgyid", "brgyname"]}, _arrQCol = {rgnid: "rgnid", provid: "provid", cmid: "cmid"}, _allObj = ["rgnid", "provid", "cmid", "brgyid"];
      for(let i = 0; i < _allObj.length; i++) {
        if(document.getElementsByName(_allObj[i])[0] != undefined || document.getElementsByName(_allObj[i])[0] != null) {
          document.getElementsByName(_allObj[i])[0].addEventListener('change', function() {
            procAfter(this.name);
          });
        }
      }

      function procAfter(tName) {
        if(tName in _arrName) {
          let curDom = document.getElementsByName(_arrName[tName])[0], curInOf = _allObj.indexOf(tName);
          curDom.classList.add('loading');
          if(curInOf > -1) {
            for(var i = (curInOf + 1); i < _allObj.length; i++) {
              document.getElementsByName(_allObj[i])[0].innerHTML = '<option value hidden selected disabled>Please select</option>';
            }
          }
          sendRequestRetArr(["_token="+document.getElementsByName('_token')[0].value, "rTbl="+_arrQCol[tName], "rId="+document.getElementsByName(tName)[0].value], "{{asset('client1/request')}}/"+_obj[tName], "POST", true, {
            functionProcess: function(arr) {            
              if(curDom != undefined || curDom != null) {
                curDom.innerHTML = '<option value hidden selected disabled>Please select</option>';
                arr.forEach(function(a, b, c) {
                  curDom.innerHTML += '<option value="'+a[_arrCol[tName][0]]+'">'+a[_arrCol[tName][1]]+'</option>';
                });
                curDom.classList.remove('loading');
                canProc = 1;
              }
            }
          });
        }
      }


      $('#cmid').change(function(event) {
        $("#cmidVal").val($(this).val());
      });
  </script>

  @include('employee.cmp._othersJS')
@endsection
@else
  <script type="text/javascript">window.location.href= "{{ asset('employee') }}";</script>
@endif