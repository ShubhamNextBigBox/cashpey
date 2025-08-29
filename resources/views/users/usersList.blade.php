@extends('layouts.master')
@section('page-title',$page_info['page_title'])
@section('main-section')
<div class="content-page">
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{$page_info['page_title']}}</a></li>
                                <li class="breadcrumb-item active">{{$page_info['page_name']}}</li>
                            </ol>
                        </div>
                        <h4 class="page-title">{{$page_info['page_name']}}</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <div class="card-header text-end">
                        @if(isSuperAdmin())
                          <a href="users/add-users" class="btn btn-primary"><i class="uil uil-plus"></i> Add New</a>
                        @endif
                    </div>
                        <div class="card-body">
                            <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        @if(isSuperAdmin()  || role()=='CRM Support')
                                        <th>Roles</th>
                                        <!--<th>Users Access</th>-->
                                        <th>Added By</th>
                                         @endif
                                        <th>Added On</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                              @foreach($usersData as $key => $arr)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{ucwords($arr->fullName)}}</td>
                                        <td>{{getUserNameById('lms_designations','designationsId',$arr->designation,'designationsName')}}</td></td>
                                        @if(isSuperAdmin() || role()=='CRM Support')
                                            <td>
                                               @if($arr->rolesProvided=='0')
                                                  <span class="badge bg-danger">None</span>
                                               @else
                                                  <span class="badge bg-success">Yes</span>
                                               @endif
                                            </td>
                                       
                                        <!--<td>-->
                                        <!--   @if($arr->createUserCheck=='')-->
                                        <!--      <span class="badge bg-danger">No</span>-->
                                        <!--   @else-->
                                        <!--      <span class="badge bg-success">Yes</span>-->
                                        <!--   @endif-->
                                        <!--</td>-->

                                         {{-- Custom Helper for getting name from id check helper for more info--}}
                                        <td>{{getUserNameById('users','userID',$arr->addedBy,'displayName')}}</td>
                                         @endif    
                                         {{-- Custom Helper for getting name from id ends--}}
                                        <td>{{date('d, M Y',strtotime($arr->addedOn))}}</td>
 
                                        @if(!empty($arr->kycDocument))
                                            @if($arr->rolesProvided == '1')
                                                @if($arr->status == '1')
                                                    <td>
                                                        <div>
                                                            <input type="checkbox" id="switch{{$arr->userID}}" checked data-switch="success" data-userID="{{$arr->userID}}" value="0" class="status-switch" {{ !isSuperAdmin() && role() != 'CRM Support' ? 'disabled' : '' }}>
                                                            <label for="switch{{$arr->userID}}" data-on-label="Yes" data-off-label="Yes" class="mb-0 d-block"></label>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td>
                                                        <div>
                                                            <input type="checkbox" id="switch{{$arr->userID}}" data-switch="success" data-userID="{{$arr->userID}}" value="1" class="status-switch" {{ !isSuperAdmin() && role() != 'CRM Support' ? 'disabled' : '' }}>
                                                            <label for="switch{{$arr->userID}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                                        </div>
                                                    </td>
                                                @endif
                                            @else
                                                <td>
                                                    <a href="javascript::void(0)" class="text-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
                                                       data-bs-custom-class="secondary-tooltip" data-bs-title="Roles Pending" style="font-size: 18px;margin-left: 5px;">
                                                        <i class='mdi mdi-information'></i>
                                                    </a>
                                                </td>
                                            @endif
                                        @else
                                            <td>
                                                <a href="javascript::void(0)" class="text-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
                                                   data-bs-custom-class="secondary-tooltip" data-bs-title="KYC Pending" style="font-size: 18px;margin-left: 5px;">
                                                    <i class='mdi mdi-information'></i>
                                                </a>
                                            </td>
                                        @endif
                                  
                                        <td>
                                             @if($arr->rolesProvided=='1')
                                                <a href="javascript:void(0)" class="text-success viewDetails" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="success-tooltip" data-bs-title="View" style="font-size: 18px;" data-userID="{{$arr->userID}}">
                                                    <i class='mdi mdi-eye'></i>
                                                </a> 
                                            @endif
                                                @if(isSuperAdmin() || role()=='CRM Support')
                                            <a href="users/update-users/{{base64_encode($arr->userID)}}" class="text-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="info-tooltip" data-bs-title="Edit" style="font-size: 18px;margin-left: 5px;">
                                                <i class='mdi mdi-pencil'></i>
                                            </a> 
                                            @endif
                                            @if(isSuperAdmin() || role()=='CRM Support')
                                             @if(!empty($arr->kycDocument))
                                            <a href="users/users-roles/{{base64_encode($arr->userID)}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="primary-tooltip" data-bs-title="Add / Edit Roles" style="font-size: 18px;margin-left: 5px;">
                                                <i class='mdi mdi-account-lock-open'></i>
                                            </a> 
                                            @endif
                                            <a href="" class="text-danger usersDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="danger-tooltip" data-bs-title="Delete" style="font-size: 18px;margin-left: 5px;" data-userID="{{$arr->userID}}">
                                                <i class='mdi mdi-delete'></i>
                                            </a>
                                           @endif    
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div> <!-- end card-body -->
                        </div> <!-- end card-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                    </div> <!-- container -->

   

   <div id="usersViewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-center">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="primary-header-modalLabel">User Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body" id="userDataView">
                </div>
            </div>
        </div>
    </div> 

    <div id="info-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="ri-alert-line h1 text-warning"></i>
                    <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
                    <p class="mt-3"></p>
                    <input type="hidden" id="userIDDelete">
                    <button type="button" class="btn btn-success my-2" id="confirmYes">Yes</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
    {{-- Modal for add branch ends --}}
                    @endsection


@section('custom-js')
                  
 <script type="text/javascript">
    
       $(document).ready(function() {
            
       
       $(document).on('change','.status-switch',function() {
                var status = $(this).val();
                var userID = $(this).attr('data-userID');
                $.ajax({
                   url: "{{ route('usersStatusUpdate') }}",
                   type: "POST",
                   dataType:'json',
                   data: {
                     _token: "{{ csrf_token() }}",
                     userID:userID,
                     status:status
                   },
                   success: function(data) {
                       if(data.response=='success'){
                         $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                        setTimeout(function(){window.location.reload();}, 1000);
                       }else{
                         $.NotificationApp.send("Oh snap!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                       }
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });   


       $('.viewDetails').on('click',function() {
                var userID = $(this).attr('data-userID');
                $.ajax({
                   url: "{{ route('usersViewDetails') }}",
                   type: "POST",
                   dataType:'json',
                   data: {
                     _token: "{{ csrf_token() }}",
                     userID:userID,
                   },
                   success: function(data) {
                       if(data.response=='success'){
                         $("#usersViewModal").modal('show');
                         $("#userDataView").html(data.data);
                       }else{
                         
                       }
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });

        $(document).on('click','.usersDeleteBtn',function(e){
            e.preventDefault();  
            $('#userIDDelete').val($(this).attr('data-userID'));
            $('#info-alert-modal').modal('show');
        });

        $(document).on('click','#confirmYes',function(){
           var userID = $('#userIDDelete').val();
                $.ajax({
                   url: "{{ route('usersDelete') }}",
                   type: "GET",      
                   data: {userID:userID},
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                            $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                          //  getAllBranches();
                            setTimeout(function(){window.location.reload();}, 1000);
                            $('#info-alert-modal').modal('hide');
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
            });
        });
 
         function printErrorMsg(msg){
            $.each(msg,function(key,value){
                $('.'+key+'Err').html('<div class="alert alert-danger errClr mt-1" role="alert"><i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>'+value+'</strong></div>');
            });
        }
       
    });
    

    
    
</script>

@endsection