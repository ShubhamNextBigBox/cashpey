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
                  <h4 class="page-title">{{$page_info['page_name']}} </h4>
               </div>
            </div>
         </div>
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body">
                     {{-- 
                     <div class="card-header text-end">
                        <a href="settings/users-list" class="btn btn-primary"><i class="uil uil-eye"></i> Users List</a>
                     </div>
                     --}}
                     <h4 class="header-title mb-3">User Details</h4>
                     <form action="javascript:void(0)" method="post" id="formRoles">
                        @csrf
                        <div class="row">
                           <div class="col-md-3 mb-2">
                              <label for="simpleinput" class="form-label">Employee ID</label>
                              <input type="text" id="simpleinput" name="userID" readonly class="form-control" placeholder="Enter employee ID" readonly value="{{$usersData->userID ?? ''}}">
                              <span class="userIDErr"></span>
                              @error('userID')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>
                           <div class="col-md-3 mb-2">
                              <label for="simpleinput" class="form-label">Full Name</label>
                              <input type="text" id="simpleinput" name="fullName" class="form-control mb-1" placeholder="Enter full name" readonly value="{{ucwords($usersData->fullName) ?? ''}}">
                              <span class="fullNameErr"></span>
                              @error('fullName')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>
                           <div class="col-md-3 mb-2">
                              <label class="form-label">Department</label>
                               <input type="text" id="simpleinput" name="department" class="form-control mb-1" placeholder="Enter department" readonly value="{{getUserNameById('lms_departments','departmentsId',$usersData->department,'departmentsName')}}">
                                {{-- Custom Helper for getting name from id check helper for more info--}}
                                <span class="departmentErr"></span>
                              @error('department')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>
                          <div class="col-md-3 mb-2">
                              <label class="form-label">Designations</label>
                              <input type="text" id="simpleinput" name="designation" class="form-control mb-1" placeholder="Enter designation" readonly value="{{getUserNameById('lms_designations','designationsId',$usersData->designation,'designationsName')}}">
                              <span class="designationErr"></span>
                              @error('designation')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>
                            <div class="col-md-3 mb-2">
                              <label for="simpleinput" class="form-label">User Name</label>
                              <input type="text" id="simpleinput" name="username" class="form-control mb-1" placeholder="Enter Username" value="{{$usersRolesData[0]->userName ?? ''}}">
                              <span class="usernameErr"></span>
                              @error('username')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>
                           <div class="col-md-3 mb-2">
                              <label for="password" class="form-label">Password</label>
                               <div class="input-group input-group-merge">
                                   <input type="password" id="password" class="form-control" name="password" placeholder="Enter your password">
                                   <div class="input-group-text" data-password="false">
                                       <span class="password-eye"></span>
                                   </div>
                               </div>
                               <span class="passwordErr"></span>
                               @error('password')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>
                           <div class="col-md-2 mb-2">
                               <label for="simpleinput" class="form-label">Roles</label>
                               <select class="form-control select2" data-toggle="select2" name="roles" id="rolesDecide">
                                   <option value="">Select roles</option>
                                   @foreach($designationData as $arr)
                                   <option value="{{ $arr->designationsId }}" data-designation-name="{{ $arr->designationsName }}"  {{ !empty($usersRolesData[0]->role) && $usersRolesData[0]->role == $arr->designationsName ? 'selected' : '' }}>{{ $arr->designationsName }}</option>
                                   @endforeach
                               </select>
                               <span class="rolesErr"></span>
                               @error('roles')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                              @enderror
                           </div>   
                            <div class="col-md-1 mt-3 mb-2" id="checkboxContainer">
                                <label class="form-check-label" for="customCheck1">Create</label><br>
                                    <div class="form-check">
                                        <input type="checkbox" name="createUserCheck" class="form-check-input" id="customCheck1" value="1" @if(!empty($usersData->createUserCheck)) checked @endif>
                                    </div>
                                </div>  
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Reporting Managers</label>
                                <select class="select2 form-control select2-multiple" data-toggle="select2" multiple="multiple" name="reportingManagers[]" id="reportingManagers" data-placeholder="Select Reporting Managers ...">
                                @php 
                                $selectedReportingManagers = explode(',', $usersData->reportingManagers);

                                @endphp    
                                @foreach($users as $user)
                                    <option value="{{$user->userID}}" @if(in_array($user->userID, $selectedReportingManagers)) selected @endif>{{$user->displayName}}</option>
                                @endforeach  
                                </select>
                                <span class="reportingManagersErr"></span>
                            </div>
                               
                           
                          <div class="col-md-12 mb-2" id="modulesContainer">
                                  <label for="password" class="form-label">Select Modules</label>
                                  <select class="select2 form-control select2-multiple" data-toggle="select2" name="modules[]" multiple="multiple" id="modules" data-placeholder="Select Modules ...">
                                      @foreach($modulesData as $arr)
                                          @php
                                              $selectedModules = [];
                                              if(!empty($usersRolesData) && count($usersRolesData) > 0) {
                                                  $selectedModules = explode(',', $usersRolesData[0]->modules);
                                              }
                                          @endphp
                                          <option value="{{$arr->modulesId}}" {{ in_array($arr->modulesId, $selectedModules) ? 'selected' : '' }}>{{$arr->modulesName}}</option>
                                      @endforeach
                                  </select>
                                  <span class="modulesErr"></span>
                                  @error('modules')
                                  <div class="alert alert-danger mt-1" role="alert">
                                      <i class="ri-close-circle-line me-1 align-middle font-16"></i>
                                      <strong> {{ $message }}</strong>
                                  </div>
                                  @enderror
                              </div>


                           <div class="card-body py-0" data-simplebar data-simplebar-primary style="max-height:400px;">
                              {{-- <div id="table_show">
                              </div> --}}
                              <table class="table table-fixed table-bordered">
                                 <thead>
                                   <tr class="bg-primary position-sticky top-0">
                                       <th class="text-white">Modules <input type="checkbox" class="form-check-input modules-all-checkbox"></th>
                                       <th class="text-white">Add <input type="checkbox" class="form-check-input add-all-checkbox"></th>
                                       <th class="text-white">View <input type="checkbox" class="form-check-input view-all-checkbox"></th>
                                       <th class="text-white">Edit <input type="checkbox" class="form-check-input edit-all-checkbox"></th>
                                       <th class="text-white">Delete <input type="checkbox" class="form-check-input delete-all-checkbox"></th>
                                       <th class="text-white">Export <input type="checkbox" class="form-check-input export-all-checkbox"></th>
                                   </tr>
                                 </thead>
                                 <tbody id="tableAppend">

                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <!-- end #progressbarwizard-->
                         <div class="col-md-12 mb-2 text-end"> 
                           <input type="hidden" name="rolesProvided" value="{{$usersRolesData[0]->rolesProvided ?? '' }}">
                              <button type="submit" class="btn btn-primary">Save changes</button>
                         </div> 
                     </form>
                  </div>
                  <!-- end card-body -->
               </div>
               <!-- end card-->
            </div>
            <!-- end col -->
         </div>
      </div>
   </div>
<!-- container -->

@endsection

@section('custom-js')
                  
 <script type="text/javascript">
    
       $(document).ready(function() {
      var selectedModules = [];

      // Function to fetch module data
      function fetchModulesData(selectedModulesEdit, userID) {
         
       
          $.ajax({
              url: '{{ route('fetchModulesEditData') }}',
              type: 'GET',
              data: {
                  modules: selectedModulesEdit,
                  userID: userID
              },
              success: function(response) {
                  $('#tableAppend').html(response.html); // Use html() to replace existing content
              },
              error: function(xhr, status, error) {
                  console.error(error);
              }
          });
         
      }

// Event listener for module selection change
$('#modules').on('change', function() {
    // Get newly selected modules
    var newSelectedModules = $(this).val() || [];

    // Determine modules to unselect (deselected modules)
    var modulesToUnselect = selectedModules.filter(function(moduleId) {
        return !newSelectedModules.includes(moduleId);
    });

    // Remove data associated with unselected modules
    modulesToUnselect.forEach(function(moduleId) {
        $('#tableAppend').find('tr[data-module-id="' + moduleId + '"]').remove();
        // Remove module ID from the selectedModules array
        selectedModules.splice(selectedModules.indexOf(moduleId), 1);
    });

    // Filter out modules that are newly selected
    var modulesToFetch = newSelectedModules.filter(function(moduleId) {
        return !selectedModules.includes(moduleId);
    });

    // Loop through each module to fetch
    modulesToFetch.forEach(function(moduleId) {
        // Send AJAX request for each module ID
        $.ajax({
            url: '{{ route('fetchModules') }}', // Update with your route
            type: 'GET',
            data: { modules: moduleId },
            success: function(response) {
                // Append response to the table
                $('#tableAppend').append(response.html);

                // Add the module to the selectedModules array
                selectedModules.push(moduleId);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });

    // Call fetchModulesData with the updated selected modules and userID
    fetchModulesData(newSelectedModules, $('#simpleinput').val());
});

// Get the selected modules and employee ID
var selectedModulesEdit = $('#modules').val() || []; // Fetch initially selected modules
var userID = $('#simpleinput').val();

  var check = "<?=!empty($usersRolesData[0]->rolesProvided) ? $usersRolesData[0]->rolesProvided : 0?>";
          if(check=='1'){
// Call fetchModulesData with the selected modules and userID
fetchModulesData(selectedModulesEdit, userID);
}

    // Event handler for all checkboxes
            $('.modules-all-checkbox').change(function() {
                var checked = $(this).prop('checked');
                $('.modules-checkbox').prop('checked', checked);
            });

            // Check/uncheck all checkboxes in the Add column
            $('.add-all-checkbox').change(function() {
                var checked = $(this).prop('checked');
                $('.add-checkbox').prop('checked', checked);
            });

            // Check/uncheck all checkboxes in the View column
            $('.view-all-checkbox').change(function() {
                var checked = $(this).prop('checked');
                $('.view-checkbox').prop('checked', checked);
            });

            // Check/uncheck all checkboxes in the Edit column
            $('.edit-all-checkbox').change(function() {
                var checked = $(this).prop('checked');
                $('.edit-checkbox').prop('checked', checked);
            });

            // Check/uncheck all checkboxes in the Delete column
            $('.delete-all-checkbox').change(function() {
                var checked = $(this).prop('checked');
                $('.delete-checkbox').prop('checked', checked);
            });

            // Check/uncheck all checkboxes in the Export column
            $('.export-all-checkbox').change(function() {
                var checked = $(this).prop('checked');
                $('.export-checkbox').prop('checked', checked);
            });

       // Form submission AJAX code
       $('#formRoles').submit(function(event) {
           event.preventDefault();
           var formData = $(this).serialize();
           var csrfToken = $('meta[name="csrf-token"]').attr('content');
           $.ajax({
               url: '{{route('addUsersRoles')}}', // Update with your route
               type: 'POST',
               data: formData,
               dataType:'json',
               headers: {
                     'X-CSRF-TOKEN': csrfToken 
                },
               success: function(data) {
                   if (data.response == 'success') {
                       $('.errClr').html('').hide();
                       $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                       setTimeout(function() {
                           window.location.href = "{{ route('usersList') }}";
                       }, 2000);
                   } else {
                       $('.errClr').remove();
                       printErrorMsg(data.errors);
                   }
               },
               error: function(xhr, status, error) {
                   var errorMessage = xhr.responseJSON.message;
                   alert(errorMessage);
               }
           });
       });

    $(document).on('change', '.add-checkbox, .view-checkbox, .edit-checkbox, .delete-checkbox, .export-checkbox', function() {
        var moduleId = $(this).attr('name').split('_')[1];
        var isChecked = $(this).prop('checked');
        if (isChecked) {
            $('input[name="module_id[]"][value="' + moduleId + '"]').prop('checked', true);
        }
    });


$('#rolesDecide').change(function () {
    var designationID = $(this).val();
    var designationName = $(this).find('option:selected').data('designation-name'); // Get the designation name

    if (designationName === 'Admin') {
        $('#customCheck1').prop('disabled', false); 
        
        // Select all modules
        $('#modules option').prop('selected', true); // Select all options
        $('#modules').select2(); // Refresh select2
        $('#modules').trigger('change'); // Trigger change event for select2 to reflect changes

        // Handle the "Select All" checkbox
        $('.modules-all-checkbox').change(function() {
            var checked = $(this).prop('checked');
            $('.modules-checkbox').prop('checked', checked); // Check or uncheck all checkboxes
            // Update the select2 to reflect the changes
            $('#modules').select2().trigger('change');
        });
    } else {
        $('#customCheck1').prop('disabled', true);
        
        // Uncheck and disable the checkboxes if not Admin
        $('.modules-checkbox').prop('checked', false); // Uncheck all individual checkboxes
        $('#modules').select2().trigger('change'); // Update select2
    }
});



    // Function for printing error messages
    function printErrorMsg(msg) {
        $.each(msg, function(key, value) {
            $('.' + key + 'Err').html('<div class="alert alert-danger errClr mt-1" role="alert"><i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>' + value + '</strong></div>');
        });
    }
});


</script>

@endsection