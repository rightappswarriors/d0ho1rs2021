@if (session()->exists('employee_login'))   
  @extends('mainEmployee')
  @section('title', 'HFERC Assignment')
  @section('content')
  <style>
    @media (min-width: 576px) {
      #modDiag { max-width: none; }
    }

    #modDiag {
      width: 98%;
      height: 92%;
      padding: 0;
    }

    /*.modal-body {
       max-height: calc(100vh - 200px);
       overflow-y: auto;
    }*/

    #modCont {
      height: 100%;
    }
  </style>
  {{-- <input type="text" id="CurrentPage" hidden="" value="PF012"> --}}
  <div class="content p-4">
    <div class="card">
      <div class="card-header bg-white font-weight-bold">
         HFERC Assignment
         <button class="btn btn-primary" onclick="window.history.back();">Back</button>
      </div>
      <div class="card-body">
        <div class="col-sm-12">
          <h2>@isset($AppData) {{$AppData->facilityname}} @endisset</h2>
          <h5>@isset($AppData) {{strtoupper($AppData->streetname)}}, {{strtoupper($AppData->brgyname)}}, {{$AppData->cmname}}, {{$AppData->provname}} @endisset</h5>  
          @isset($evaluation->HFERC_evalDate)
          <h5 class="font-weight-bold">Date Evaluated: {{Date('F j, Y, g:i A',strtotime($evaluation->HFERC_evalDate))}}</h5>
          @endisset
        </div>
        <hr>
        @if(isset($AppData->isAcceptedFP))
        <div class="row">
          @if(count($free) > 0 && in_array($currentUser->grpid, ['PO','NA','PO1','PO2','RLO']))
            <div class="col-md-1">
              <button class="btn btn-primary p-2" data-toggle="modal" data-target="#viewModal"><i class="fa fa-plus-circle"></i> Add</button>
            </div>
          @endif
          
          @if($isHead)
            @if($canEval)
              <div class="col-md-2">
                <button class="btn btn-primary p-2" data-toggle="modal" data-target="#evaluate"><i class="fa fa-file"></i> Recommendation</button>
              </div>
            @endif
          @endif



        @if(isset($membDone) && $canViewOthers)
        <div class="col-md-2" id="toFrame">
          
        </div>
        @endif
        @if(isset($evaluation) && isset($evaluation->HFERC_eval) )
          <div class="col-md-2">
            <button class="btn btn-primary p-2" onclick="window.location.href='{{asset('employee/dashboard/processflow/view/hfercevaluation/'.$AppData->appid.'/'.$revisionCountCurent)}}'" data-toggle="modal" data-target="#evaluate"><i class="fa fa-file"> </i> View Evaluation</button>
          </div>
        @endif
        <div class="col-md-3">
            <select name="revisioncount" class="form-control w-100">
            @php $temprev = $maxRevision; @endphp
            <option value="">Please Select Other Revision</option>
            @for($i = $maxRevision; $i > 0; $i--)
            <option value="{{$temprev}}">{{$temprev}}</option>
            @php
            $temprev = $maxRevision - 1;
            @endphp
            @endfor
            </select>
          </div>
        </div>
        {{csrf_field()}}
        
          <div class="table-responsive mt-5">
            <table class="table table-stripped" id="example">
              <thead>
                <tr>
                  <th>Member Name</th>
                  <th>Committee Position</th>
                  {{-- <th>Position</th> --}}
                  {{-- <th>Permitted To Inspect</th> --}}
                  <th>Status</th>
                  <th>Date Evaluated</th>
  {{--                 <th>View Inspection</th> --}}
                  <th>Options</th>
                </tr>
              </thead>
              <tbody>
              @foreach($hferc as $members)
                <tr>
                  <td style="font-size: 20px;">
                    {{ucfirst($members->fname.' '.$members->lname)}}
                  </td>
                 {{--  <td>
                    {{(!empty($members->position) ? ucfirst($members->position) : 'No Position Indicated')}}
                  </td> --}}
                  <td>
                    @switch($members->pos)
                      @case('C')
                        Chairperson
                      @break
                      @case('VC')
                        Vice Chairperson
                      @break
                      @case('E')
                        Member
                      @break
                     {{--  @case('S')
                        Secretariat
                      @break --}}
                    @endswitch
                  </td>
                  {{-- <td>
                    {{($members->permittedtoInspect <= 0 ? "Not Permitted" : "Permitted")}}
                  </td> --}}
                   <td>
                    {{($members->permittedtoInspect > 0 ? ($members->hasInspected <= 0 ? 'Not yet completed' : 'Evaluation Completed') : 'Not Available')}}
                  </td>
                  <td>
                    {{isset($members->inspectDate) ? Date('F j, Y g:s A',strtotime($members->inspectDate)) : 'Not yet Evaluated'}}
                  </td>
                  {{-- <td>
                    {!!($members->hasInspected > 0 ? '<a href="'.asset('employee/dashboard/processflow/evaluation/compiled/'.$members->uid.'/'.$appid.'/'.$apptype).'" class="text-info"> <i class="fa fa-eye"> View Result</i></a>' : 'Not Available')!!}
                  </td> --}}
                  <td>
                    @if($members->permittedtoInspect <= 0 && $customRights)
                      <button type="button" title="Permit to Inspect" class="btn btn-primary" onclick="promptPermit('{{$members->hfercid}}',1)">
                        <i class="fa fa-fw fa-check"></i>
                      </button>
                      <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#viewModalEdit" onclick="showData('{{$members->hfercid}}','{{ucfirst($members->fname.' '. (!empty($members->mname) ? $members->mname.',' :'').$members->lname)}}','{{$members->pos}}')">
                        <i class="fa fa-fw fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-danger" onclick="showDelete('{{$members->hfercid}}');">
                        <i class="fa fa-ban" aria-hidden="true"></i>
                      </button>
                    @else
                      {!!($members->permittedtoInspect > 0 && $members->hasInspected > 0 ? '<a href="'.asset('employee/dashboard/processflow/floorPlan/GenerateReportAssessments/'.$appid.'/'.$revisionCountCurent.'/'.$members->uid.'/').'" class="text-info"> <i class="fa fa-eye"> View Result</i></a>' : '<button type="button" title="Remove Permit to Inspect" class="btn btn-danger" onclick="promptPermit('.$members->hfercid.',2)">
                        <i class=" fa fa-window-close"></i>
                      </button>')!!}
                    @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          @else
          <div class="container">
            <div class="col-md text-center text-warning">
              <i class="fa fa-exclamation-circle mt-5 mb-3" style="font-size: 200px;" aria-hidden="true"></i>
            </div>
            <div class="col-md text-center">
              <span style="font-size: 30px;">Are you sure you want to proceed in Assigning HFERC Members on this Facility?</span>
              <br>
              <p class="lead font-weight-bold mt-3"><span class="text-danger">*</span>Allowing means you have received the floor plan and will notify the client that you do so.<span class="text-danger">*</span></p>
            </div>
            <div class="col-md mt-4">
              <div class="d-flex justify-content-center">
                <button class="btn btn-primary p-3" onclick="confirmFP()">Yes, We already received the floor plan</button>
                <button class="btn btn-danger p-3" onclick="history.back()">No, Go back</button>
              </div>
            </div>
          </div>
          @endif

        </div>
      </div>
    </div>
    
    @isset($free)
      @if(count($free) > 0)
      <div class="modal fade" id="viewModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border-radius: 0px;border: none;">
            <div class="modal-body" style=" background-color: #272b30;color: white;">
              <h5 class="modal-title text-center">Add HFERC Committee Member</h5>
              <hr>
              <div class="col-sm-12">
                <form id="memberadd">
                  {{csrf_field()}}
                    <div class="container pl-5">
                      <div class="row mb-2">
                        <div class="col-sm">
                          Member Name:
                        </div>
                        <div class="col-sm-11">
                          <select name="uid" id="uidadd" class="form-control" required>
                            @if(count($free) > 0)
                              @foreach($free as $f)
                                <option value="{{$f->uid}}">{{$f->uid}} - {{ucfirst($f->fname.' '.$f->lname)}}</option>
                              @endforeach
                            @else
                              <option value="">No Available Employee on this Region</option>
                            @endif
                          </select>
                        </div>
                      </div>
                    <div class="row mb-2 mt-3">
                      <div class="col-sm">
                        Commitee Position:
                      </div>
                      <div class="col-sm-11">
                        <select name="pos" id="pos" class="form-control" required>
                          <option value="E">Member</option>
                          <option value="C">Chairperson</option>
                          <option value="VC">Vice ChairPerson</option>
                          {{-- <option value="S">Secretariat</option> --}}
                        </select>
                      </div>
                    </div>
                    <input type="hidden" name="action" value="add">
                      <button class="btn btn-primary pt-2" type="submit">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    @endif

    <div class="modal fade" id="viewModalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 0px;border: none;">
          <div class="modal-body" style=" background-color: #272b30;color: white;">
            <h5 class="modal-title text-center">Edit HFERC Committee Member</h5>
            <hr>
            <div class="col-sm-12">
              <form id="memberedit">
                {{csrf_field()}}
                  <input type="hidden" name="action" value="edit">
                  <span id="editBody">
                    
                  </span>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="remthis modal fade" id="compareModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" id="modDiag">
            <div class="modal-content" id="modCont">
                <div class="modal-header" id="viewHead">
                    <h5 class="modal-title" id="actionModalCRUD">Results</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{-- <object data="{{asset('employee/dashboard/processflow/evaluation/compiled/HFERC1/3/PTC')}}" width="600" height="100%">
                </object> --}}
                  <div class="row h-100">
                    @isset($membDone)
                      @foreach($membDone as $mem)
                      @isset($currentUser)
                        
                        <div class="col-md">

                          <p class="text-center mt-3">Evaluation of: <span class="font-weight-bold">{{ucfirst($mem->fname . ' ' . $mem->lname)}}</span>

                              @if($currentUser->uid != $mem->uid)
                                  <a target="_blank" class="btn btn-primary" href="{{url('employee/dashboard/processflow/floorPlan/parts/'.$AppData->appid.'/'.$revisionCountCurent.'/'.$mem->uid)}}"><i class="fa fa-clone" aria-hidden="true"></i> Copy Result</a>
                              @endif
                            

                          </p>
                          <iframe src="{{asset('employee/dashboard/processflow/floorPlan/GenerateReportAssessments/'.$AppData->appid.'/'.$revisionCountCurent.'/'.$mem->uid.'/')}}"  width="100%" height="100%" >Evaluation Result of {{ucfirst($mem->fname . ' ' . $mem->lname)}}</iframe>
                        </div>
                        
                      @endisset
                      @endforeach
                    @endisset
                  </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    @isset($canEval)
      @if($canEval)
      <div class="modal fade" id="evaluate" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="border-radius: 0px;border: none;">
            <div class="modal-body" style=" background-color: #272b30;color: white;">
              <h5 class="modal-title text-center">Evalute Application</h5>
              <hr>
              <div class="col-sm-12">
                <form id="evaluateSend">
                  <div class="row">
                    {{csrf_field()}}
                    <div class="col-12 text-uppercase font-weight-bold text-center">
                      Health Facility Evaluation Review Committee (HFERC) is recommending the:
                    </div>
                  </div>
                  <div class="offset-1 d-flex justify-content-center mt-4">
                    <input type="hidden" name="action" value="evaluate">
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" required value="1" class="custom-control-input" id="customRadio" name="evaluation" value="customEx">
                      <label class="custom-control-label" for="customRadio">Approval</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" required value="2" class="custom-control-input" id="customRadio2" name="evaluation" value="customEx">
                      <label class="custom-control-label" for="customRadio2">Disapproval</label>
                    </div>
                  </div>
                  <div class="col-md-12 mt-4 text-center" style="font-size: 20px;">
                    Comments
                  </div>
                  <div class="col-md-12 mt-3">
                    <textarea name="comments" class="form-control" cols="40" rows="10"></textarea>
                  </div>
                  <div class="d-flex justify-content-center mt-4">
                    <div class="custom-control">
                      <button class="btn btn-primary btn-block p-2">Save</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    @endif

    <script type="text/javascript">
      function onClickToIFrame(){
        $('iframe').contents().find('button,#menu,nav,#return-to-top,#wrapper').remove();
      }
      
      $(document).ready(function() {
        $('#example').DataTable();
        $('select').select2({
          width: '100%'
        });
      });
      // var ToBeAddedMembers = [];
      $(function () {
        $('[data-toggle="popover"]').popover();
      })

      function showData(id, name, pos){
        $("#editBody").empty().append(
          '<input name="id" value="'+id+'" type="hidden">'+
          '<div class="container pl-5">'+
          '<div class="row mb-2 mt-3">'+
            '<div class="col-sm">'+
             ' Commitee Position:'+
            '</div>'+
           ' <div class="col-sm-11">'+
              '<select name="pos" id="posqwe" class="form-control" required>'+
                '<option value="E">Evaluator</option>'+
                '<option value="C">Chairperson</option>'+
                '<option value="VC">Vice ChairPerson</option>'+
               // ' <option value="S">Secretariat</option>'+
              '</select>'+
           ' </div>'+
          '</div>'+
            '<button class="btn btn-primary pt-2" type="submit">Submit</button>'+
        '</div>'
        )
        $("#posqwe").val(pos);
      }

      function confirmFP(){
        $.ajax({
          type: 'POST',
          data: {
            _token: $("input[name=_token]").val(),
            action: 'FP'
          },
          success: function(a){
            if(a == 'done'){
              location.reload();
            } else {
              console.log(a);
            }
          }
        })
        
      }

      function promptPermit(id,choose){
        if(choose == 1){
          let r = confirm('You are about to Permit this Personnel to Inspect Floor Plan');
          if(r){
            $.ajax({
              method: "POST",
              data: {_token: $("input[name=_token]").val(), action: 'permit', permit: 1, id: id},
              success: function(a){
                if(a == 'done'){
                  alert('Successfully executed given action');
                  location.reload();
                }
              }
            })
          }
        } else {
          let r = confirm('You are about to Remove Permit this Personnel to Inspect Floor Plan');
          if(r){
            $.ajax({
              method: "POST",
              data: {_token: $("input[name=_token]").val(), action: 'permit', permit: 0, id: id},
              success: function(a){
                if(a == 'done'){
                  alert('Successfully executed given action');
                  location.reload();
                }
              }
            })
          }
        }
      }

      @if($canEval)
        $("#evaluateSend").submit(function(e){
          e.preventDefault();
          $.ajax({
            method: "POST",
            data: $(this).serialize(),
            success: function(a){
              if(a == 'done'){
                location.reload();
              } else {
                console.log(a);
              }
            }
          })
        })
      @endif

      @if(count($free) > 0)
      $("#memberadd").submit(function(e){
        e.preventDefault();
        if($("#pos").val() != '' || $("#uidadd").val() != ''){
          $.ajax({
            method: "post",
            data: $(this).serialize(),
            success:function(a){
              if(a == 'done'){
                alert('Added Successfully');
                location.reload();
              }
            }
          })
        } else {
          alert('All fields are required. Please select from the options');
        }
      })
      @endif

      $("#memberedit").submit(function(e){
        e.preventDefault();
        if($("#posqwe").val() != '' || $("#uid").val() != ''){
          $.ajax({
            method: "post",
            data: $(this).serialize(),
            success:function(a){
              if(a == 'done'){
                alert('Edited Successfully');
                location.reload();
              }
            }
          })
        } else {
          alert('All fields are required. Please select from the options');
        }
      })

      function showDelete(id){
        var r = confirm("Are you want to remove this Evaluator on this Evaluation?");
        if (r == true) {
           $.ajax({
              method: 'POST',
              data: {_token: $("input[name=_token]").val(), 'id': id, 'action':'delete'},
              success: function(data){
                if(data == 'done'){
                  alert('Action Completed Successfully');
                  location.reload();
                }
              }
           });
        } else {
            txt = "You pressed Cancel!";
        }
      }
      @if(isset($membDone) && $canViewOthers && ($canProcessAction || in_array($currentUser->grpid, ['NA'])))
      $(function(){
        $("#toFrame").empty().append('<button class="btn btn-success p-2" data-backdrop="static" data-toggle="modal" data-target="#compareModal" onclick="onClickToIFrame()"><i class="fa fa-files-o" aria-hidden="true"></i> Compare Results</button>');
        
      })
      @endif

      $('[name=revisioncount]').change(function(event) {
        /* Act on the event */
        window.location.href = '{{url('employee/dashboard/processflow/assignmentofhferc/'.$appid.'/')}}'+'/'+$(this).val();
      });
    </script>
  @endsection
@else
  <script type="text/javascript">window.location.href= "{{ asset('employee') }}";</script>
@endif
