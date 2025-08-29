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
                           <th>Assigned RM</th>
                           <th>Status</th>
                        </tr>
                     </thead>
                     <tbody>
                    @foreach($cm_list as $key => $arr)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $arr->displayName }}</td>
                            <td>{{ getUserNameById('lms_designations', 'designationsId', $arr->designation, 'designationsName') }}</td>
                            <td>
                                <!-- Hidden input to store the oldAssignedRM value -->
                                <input type="hidden" name="oldAssignedRM" value="{{ $arr->oldAssignedRM }}">
                                <input type="hidden" name="userID" value="{{ $arr->userID }}">
                                <input type="hidden" name="name" value="{{ $arr->displayName }}">
                                <input type="hidden" name="userType" value="{{ getUserNameById('lms_designations', 'designationsId', $arr->designation, 'designationsName') }}">
                                <select class="form-control-sm select2" data-toggle="select2" name="newAssignedRM">
                                <option value="">Select RM</option>
                                @foreach($rm_list as $val)
                                    <option value="{{ $val->userID }}" 
                                        @if($arr->newAssignedRM == $val->userID || (empty($arr->newAssignedRM) && $arr->oldAssignedRM == $val->userID)) 
                                            selected 
                                        @endif>
                                        {{ $val->displayName }}
                                    </option>
                                @endforeach
                            </select>

                            </td>
                            @if($arr->leadAssignment == '1')
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
                   url: "{{ route('leadAssignmentCMstatusUpdate') }}",
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
    
    
         $(document).on('change', 'select[name="newAssignedRM"]', function() {
            var newAssignedRM = $(this).val(); // Get the selected RM (Relationship Manager) value
            var userID = $(this).closest('tr').find('input[name="userID"]').val(); // Get the userID from the hidden input
            var name = $(this).closest('tr').find('input[name="name"]').val(); // Get the userID from the hidden input
            var userType = $(this).closest('tr').find('input[name="userType"]').val(); // Get the userID from the hidden input
            var oldAssignedRM = $(this).closest('tr').find('input[name="oldAssignedRM"]').val(); // Get the oldAssignedRM value from the hidden input
    
            // Check if selected RM value is empty (i.e., no RM selected)
            if (!newAssignedRM) {
                alert('Please select a Relationship Manager.');
                return;
            }
    
            // // Prepare the data to send via AJAX
            var data = {
                _token: "{{ csrf_token() }}", // Include CSRF token for security
                userID: userID,
                name: name,
                userType: userType,
                oldAssignedRM: oldAssignedRM,
                newAssignedRM: newAssignedRM
            };
    
            // Send the data using jQuery's AJAX function
            $.ajax({
                url: '{{route('assignRM')}}',  // URL to handle the update (ensure this route exists)
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(data) { 
                    if (data.response === 'success') { 
                        $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() { window.location.reload(); }, 1000);
                    } 
                    else { $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                    }    
                },

            });
        });
     
    });
 
</script>

@endsection