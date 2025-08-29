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
               <div class="card-body">
                  <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Employee Name</th>
                           <th>Designation</th>
                           <th>Assigned CM</th>
                           <th>Status</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($rm_list as $key => $arr)
                        <tr>
                           <td>{{++$key}}</td>
                           <td>{{$arr->displayName}}</td>
                           <td>{{getUserNameById('lms_designations','designationsId',$arr->designation,'designationsName')}}</td>
                           <td>
                                <!-- Hidden input to store the oldAssignedRM value -->
                               
                          @php
                                // First, try to explode the newAssignedCM. If it's empty, use oldAssignedCM.
                                $selectedCMs = !empty($arr->newAssignedCM) ? explode(',', $arr->newAssignedCM) : ( !empty($arr->oldAssignedCM) ? explode(',', $arr->oldAssignedCM) : []);
                            @endphp
                             <input type="hidden" name="oldAssignedCM[]" value="{{ $arr->oldAssignedCM }}">
                                <input type="hidden" name="userID" value="{{ $arr->userID }}">
                                <input type="hidden" name="name" value="{{ $arr->displayName }}">
                                <input type="hidden" name="userType" value="{{ getUserNameById('lms_designations', 'designationsId', $arr->designation, 'designationsName') }}">
                            <select class="select2 form-control select2-multiple" data-toggle="select2" multiple="multiple" name="newAssignedCM[]" data-placeholder="Select CM...">
                                <option value="">Select CM</option>
                                @foreach($cm_list as $val)
                                    <option value="{{ $val->userID }}" 
                                        @if(in_array($val->userID, $selectedCMs)) 
                                            selected 
                                        @endif>
                                        {{ $val->displayName }}
                                    </option>
                                @endforeach
                            </select>

                            </td>
                           @if($arr->leadAssignment=='1')
                           <td>
                              <div>
                                 <input type="checkbox" id="switch{{$arr->userID}}" checked data-switch="success" data-userID="{{$arr->userID}}" value="0" class="status-switch">
                                 <label for="switch{{$arr->userID}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                              </div>
                           </td>
                           @else
                           <td>
                              <div>
                                 <input type="checkbox" id="switch{{$arr->userID}}" data-switch="success" data-userID="{{$arr->userID}}" value="1" class="status-switch">
                                 <label for="switch{{$arr->userID}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                              </div>
                           </td>
                           @endif
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
               <!-- end card-body -->
            </div>
            <!-- end card-->
         </div>
         <!-- end col -->
      </div>
      <!-- end row -->
   </div>
   <!-- container -->
</div>
@endsection
@section('custom-js')`
                  
 <script type="text/javascript">
    
       $(document).ready(function() {
               
          $(document).on('change','.status-switch',function() {
                var status = $(this).val();
                var userID = $(this).attr('data-userID');
                $.ajax({
                   url: "{{ route('leadAssignmentRMstatusUpdate') }}",
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
         
         
         $(document).on('change', 'select[name="newAssignedCM[]"]', function() {
        var newAssignedCM = $(this).val(); // Get the selected CM values (array)
        var userID = $(this).closest('tr').find('input[name="userID"]').val(); // Get the userID from the hidden input
        var name = $(this).closest('tr').find('input[name="name"]').val(); // Get the name from the hidden input
        var userType = $(this).closest('tr').find('input[name="userType"]').val(); // Get the userType from the hidden input
        var oldAssignedCM = $(this).closest('tr').find('input[name="oldAssignedCM[]"]').val(); // Get the oldAssignedCM value from the hidden input
        
        // Check if no CM is selected (i.e., empty array)
        if (newAssignedCM.length === 0) {
            alert('Please select at least one Credit Manager.');
            return;
        }

        // Prepare the data to send via AJAX
        var data = {
            _token: "{{ csrf_token() }}", // Include CSRF token for security
            userID: userID,
            name: name,
            userType: userType,
            oldAssignedCM: oldAssignedCM,
            newAssignedCM: newAssignedCM
        };

        // Send the data using jQuery's AJAX function
        $.ajax({
            url: '{{route('assignCM')}}',  // Ensure this route exists
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.response === 'success') { 
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                }
            }
        });
    });
 
    });
 
</script>

@endsection