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
         <div class="row">
            <div class="col-10">
               <div class="card">
                  <div class="card-body">
                     {{-- <div class="card-header text-end">
                        <a href="settings/users-list" class="btn btn-primary"><i class="uil uil-eye"></i> Users List</a>
                    </div> --}}
                     <h4 class="header-title mb-3">User Details</h4>
                     <div id="progressbarwizard">
                        <ul class="nav nav-pills nav-justified form-wizard-header mb-3">
                           <li class="nav-item">
                              <a href="#personalDetails" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 py-2 active">
                              <i class="mdi mdi-account-circle font-18 align-middle me-1"></i>
                              <span class="d-none d-sm-inline">Personal Informations</span>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="#officialDetails" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 py-2">
                              <i class="mdi mdi-office-building font-18 align-middle me-1"></i>
                              <span class="d-none d-sm-inline">Official Informations</span>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="#kycDetails" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 py-2">
                              <i class="mdi mdi-camera-document font-18 align-middle me-1"></i>
                              <span class="d-none d-sm-inline">KYC Verifications</span>
                              </a>
                           </li>
                           <li class="nav-item">
                              <a href="#submitDetails" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 py-2">
                              <i class="mdi mdi-checkbox-marked-circle-outline font-18 align-middle me-1"></i>
                              <span class="d-none d-sm-inline">Finish</span>
                              </a>
                           </li>
                        </ul>
                        <div class="tab-content b-0 mb-0">
                           <div id="bar" class="progress mb-3" style="height: 7px;">
                              <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-success"></div>
                           </div>
                           <div class="tab-pane active" id="personalDetails">
                              <form action="" id="personalDetailsForm" method="post" enctype="multipart/form-data" >
                                 @csrf
                                 <div class="row">
                                    <div class="col-md-4 mb-2">
                                       <label for="simpleinput" class="form-label">Employee ID</label>
                                       <input type="text" id="simpleinput" name="userID" readonly value="{{$userIDData}}" class="form-control" placeholder="Enter employee ID">
                                        <input type="hidden" name="rolesProvided" value="{{$usersData->rolesProvided ?? '' }}">
                                       <span class="userIDErr"></span>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                       <label for="simpleinput" class="form-label">Pancard</label>
                                       <input type="text" id="pancard" name="pancard" class="form-control mb-1" placeholder="Enter pancard no." value="{{$usersData->pancard ?? ''}}">
                                       <span class="pancardErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <label for="simpleinput" class="form-label">Full Name</label>
                                       <input type="text" id="fullName" name="fullName" class="form-control mb-1" placeholder="Enter full name" value="{{$usersData->fullName ?? ''}}">
                                       <span class="fullNameErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <label for="simpleinput" class="form-label">Mobile No.</label>
                                       <input type="text" id="mobile" name="mobile" class="form-control mb-1" placeholder="Enter mobile no." value="{{$usersData->mobile ?? ''}}">
                                       <span class="mobileErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <label for="example-email" class="form-label">Email</label>
                                       <input type="email" id="email" name="email" class="form-control mb-1" placeholder="Enter email" value="{{$usersData->email ?? ''}}">
                                       <span class="emailErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <div class="position-relative" id="datepicker4">
                                          <label class="form-label">Date Of Birth</label>
                                         <input type="text" class="form-control mb-1" 
                                           data-provide="datepicker" 
                                           data-date-autoclose="true" 
                                           data-date-container="#datepicker4" 
                                           placeholder="Date of birth" 
                                           name="dateOfBirth"
                                           id="dob" 
                                           data-date-autoclose="true"
                                           value="{{ isset($usersData->dateOfBirth) ? date('m/d/Y', strtotime($usersData->dateOfBirth)) : date('m/d/Y') }}">
                                          <span class="dateOfBirthErr"></span>
                                       </div>
                                    </div>
                                   <div class="col-md-4 mb-2">
                                        <label class="form-label">Gender</label>
                                        <select class="form-control mb-1" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" @if(!empty($usersData->gender) && $usersData->gender=='Male') selected @endif>Male</option>
                                            <option value="Female" @if(!empty($usersData->gender) && $usersData->gender=='Female') selected @endif>Female</option>
                                        </select>
                                        <span class="genderErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Blood Group</label>
                                        <select class="form-control mb-1" id="employeeBloodGroup" name="bloodGroup">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'A+') selected @endif>A+</option>
                                             <option value="A-" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'A-') selected @endif>A-</option>
                                             <option value="B+" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'B+') selected @endif>B+</option>
                                             <option value="B-" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'B-') selected @endif>B-</option>
                                             <option value="O+" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'O+') selected @endif>O+</option>
                                             <option value="O-" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'O-') selected @endif>O-</option>
                                             <option value="AB+" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'AB+') selected @endif>AB+</option>
                                             <option value="AB-" @if(!empty($usersData->bloodGroup) && $usersData->bloodGroup == 'AB-') selected @endif>AB-</option>

                                        </select>
                                        <span class="bloodGroupErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                       <label for="example-textarea" class="form-label">Address</label>
                                       <textarea class="form-control mb-1" name="address" id="example-textarea" rows="1">{{$usersData->address ?? ''}}</textarea>
                                       <span class="addressErr"></span>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                            <label for="simpleinput" class="form-label">Profile Image</label>
                                            <input name="profile" type="file" class="form-control" >
                                            <span class="profileErr"></span> 
                                             @if(!empty($usersData->profile))
                                             <div class="row">
                                               <img src="{{ Storage::url($usersData->profile) }}" alt="Profile" style="width:100px;">
                                             </div>
                                          @endif                   
                                    </div>

                                    <!-- end row -->
                                    <div class="col-md-12 mb-2 text-end">
                                       <input type="hidden" name="oldProfile" value="{{$usersData->profile ?? ''}}">
                                       <button type="submit" id="" class="btn btn-primary">Save & Next <i class="mdi mdi-arrow-right ms-1"></i></button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                           <div class="tab-pane" id="officialDetails">
                              <form action="" id="officialDetailsForm" method="post">
                                 @csrf
                                 <div class="row">
                                    <div class="col-md-3 mb-2">
                                       <label for="simpleinput" class="form-label">Mobile No.</label>
                                       <input type="text" id="simpleinput" name="officialMobile" class="form-control" placeholder="Enter mobile no." value="{{$usersData->officialMobile ?? ''}}">
                                       <span class="officialMobileErr"></span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                       <label for="example-email" class="form-label">Email</label>
                                       <input type="email" id="example-email" name="officialEmail" class="form-control" placeholder="Enter email" value="{{$usersData->officialEmail ?? ''}}">
                                       <span class="officialEmailErr"></span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                       <label for="example-email" class="form-label">Document Email</label>
                                       <input type="email" id="example-email" name="documentEmail" class="form-control" placeholder="Enter email" value="{{$usersData->documentEmail ?? ''}}">
                                       <span class="officialEmailErr"></span>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                       <div class="position-relative" id="datepicker4">
                                          <label class="form-label">Date Of Joining</label>
                                        <input type="text" class="form-control" 
                                        name="dateOfJoining" 
                                        data-provide="datepicker" 
                                        placeholder="Date of joining" 
                                        data-date-autoclose="true"
                                        value="{{ isset($usersData->dateOfJoining) ? date('m/d/Y', strtotime($usersData->dateOfJoining)) : date('m/d/Y') }}">
                                          <span class="dateOfJoiningErr"></span>
                                       </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Branch</label>
                                        <select class="select2 form-control select2-multiple" data-toggle="select2" multiple="multiple" name="branch[]" data-placeholder="Select Branches ...">
                                            <option value="">Select Branch</option>
                                            @foreach($branchesData as $branch)
                                               @php
                                                    if (!empty($usersData->branch)) {
                                                        $branchIds = explode(',', $usersData->branch);
                                                    } else {
                                                        $branchIds = []; // Assign an empty array if $usersData->branch is empty
                                                    }
                                                @endphp
                                                <option value="{{ $branch->cityID }}" {{ in_array($branch->cityID, $branchIds) ? 'selected' : '' }}>{{ $branch->cityName }}</option>
                                            @endforeach
                                        </select>
                                        <span class="branchErr"></span>
                                    </div>

                                    <div class="col-md-3 mb-2">
                                       <label class="form-label">Department</label>
                                       <select class="form-control select2" data-toggle="select2" id="department" name="department">
                                          <option value="">Select Department</option>
                                          @foreach($departmentsData as $arr)
                                          <option value="{{$arr->departmentsId}}" @if(!empty($usersData->department) && $arr->departmentsId==$usersData->department)) selected @endif>{{$arr->departmentsName}}</option>
                                          @endforeach
                                       </select>
                                       <span class="departmentErr"></span>
                                    </div>

                                    @if(!empty($usersData->designation))
                                    <div class="col-md-3 mb-2">
                                       <label class="form-label">Designations</label>
                                       <select class="form-control select2" data-toggle="select2" name="designation" id="designationsReplace">
                                          <option value="">Select Designations</option>
                                          @foreach($designationsData as $arr)
                                          <option value="{{$arr->designationsId}}" @if(!empty($usersData->designation) && $arr->designationsId==$usersData->designation)) selected @endif>{{$arr->designationsName}}</option>
                                          @endforeach
                                       </select>
                                       <span class="designationErr"></span>
                                    </div>
                                    @else
                                    <div class="col-md-3 mb-2">
                                       <label class="form-label">Designations</label>
                                       <select class="form-control select2" data-toggle="select2" name="designation" id="designationsReplace">
                                          <option value="">Select Designations</option>
                                       </select>
                                       <span class="designationErr"></span>
                                    </div>
                                    @endif
                                 </div>
                                 <!-- end row -->
                                 <ul class="pager wizard mb-0 list-inline">
                                    <li class="previous list-inline-item">
                                       <button type="button" class="btn btn-light" id="backToPersonalBtn"><i class="mdi mdi-arrow-left me-1"></i> Back to Personal Informations</button>
                                    </li>
                                    <li class="list-inline-item float-end">
                                       <button type="submit" class="btn btn-primary">Save & Next <i class="mdi mdi-arrow-right ms-1"></i></button>
                                    </li>
                                 </ul>
                              </form>
                           </div>
                           <div class="tab-pane" id="kycDetails">
                              <form action="/"  method="post" enctype="multipart/form-data" class="dropzone kycDetailsForm" id="myAwesomeDropzone" data-plugin="dropzone" data-previews-container="#file-previews" data-upload-preview-template="#uploadPreviewTemplate">
                                 @csrf
                                  
                                 <input name="file" type="file" id="file" style="visibility: hidden;"/>
                                  <a href="javascript:void(0)" id="uploadIcon"> 
                                 <div class="dz-message needsclick">
                                    <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                                    <h3>Click to upload document.</h3>
                                    <span class="text-muted font-13">(Please upload your document in PDF format.)</span>
                              </div>
                              @if(!empty($usersData->kycDocument))
                                 <div class="row">
                                   <a target="_blank" href="{{ Storage::url($usersData->kycDocument) }}">View Document</a>
                                 </div>
                              @endif
                              </a>
                                 <!-- end row -->
                                 <ul class="pager wizard mb-0 list-inline">
                                    <li class="previous list-inline-item">
                                       <button type="button" class="btn btn-light" id="backToOfficialBtn"><i class="mdi mdi-arrow-left me-1"></i> Back to Official Informations</button>
                                    </li>
                                    <li class="list-inline-item float-end">
                                       <button type="submit" class="btn btn-primary">Submit <i class="mdi mdi-arrow-right ms-1"></i></button>
                                    </li>
                                 </ul>
                              </form>
                           </div>
                           <div class="tab-pane" id="submitDetails">
                              <div class="row">
                                 <div class="col-12">
                                    <div class="text-center">
                                       <h1 class="mt-0 text-success"><i class="mdi mdi-check-all"></i></h1>
                                       <h2 class="mt-0 text-success">Thank you !</h2>
                                       <p class="w-75 mb-2 mx-auto">Your data has been validated and saved in our database.
                                       </p>
                                    </div>
                                 </div>
                                 <!-- end col -->
                              </div>
                           </div>
                        </div>
                        <!-- tab-content -->
                     </div>
                     <!-- end #progressbarwizard-->
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
</div>
</div>
</div>
            
<!-- container -->

@endsection

@section('custom-js')
                  
 <script type="text/javascript">
    
       $(document).ready(function() {

          

         // Click event handlers for "Back" buttons
         $('#backToPersonalBtn').click(function() {
             $('a[href="#personalDetails"]').tab('show');
             $(".bar").addClass("w-25");
         });

         $('#backToOfficialBtn').click(function() {
             $('a[href="#officialDetails"]').tab('show');
             $(".bar").addClass("w-50");
         });

         $('#backToKycBtn').click(function() {
             $('a[href="#kycDetails"]').tab('show');
             $(".bar").addClass("w-75");
         });

         $('#uploadIcon').on('click',function(){
            $('#file').click();
         });
         

         
          $('.nav-pills li:not(:first-child) a').addClass('disabled');

         $('#personalDetailsForm').on('submit', function(event) {
             event.preventDefault();
             
             // Create a FormData object to hold the form data
             var formData = new FormData(this);
             
             // Get CSRF token from meta tag
             var csrfToken = $('meta[name="csrf-token"]').attr('content');
             
             $.ajax({
                 url: "{{ route('step1PersonalDetails') }}",
                 type: "POST",
                 dataType: 'json',
                 headers: {
                     'X-CSRF-TOKEN': csrfToken
                 },
                 data: formData,
                 contentType: false,  // Important for file uploads
                 processData: false,  // Important for file uploads
                 success: function(data) {
                     // Process the response data here
                     if (data.response == 'success') {
                         $('.errClr').html('').hide();
                         if (data.action == 'professionalDetails') {
                             $(".bar").addClass("w-25");
                             $('#officialDetails').addClass('active');
                             $('#personalDetails').removeClass('active');
                             $('a[href="#personalDetails"]').removeClass('active');
                             $('a[href="#officialDetails"]').addClass('active');
                             $('.nav-pills li:nth-child(2) a').removeClass('disabled');
                             $('.nav-pills li:nth-child(2) a').tab('show');
                         }
                     } else {
                         $('.errClr').remove();
                         printErrorMsg(data.error);
                     }
                 },
                 error: function(error) {
                     console.error(error);
                 }
             });
         });


     
          $('#officialDetailsForm').on('submit',function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
               
                $.ajax({
                   url: "{{ route('step2OfficialDetails') }}",
                   type: "POST",
                   dataType:'json',
                   headers: {
                     'X-CSRF-TOKEN': csrfToken 
                   },
                   data: formData,
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                           $('.errClr').html('').hide();
                          if(data.action=='kycDetails'){
                           $(".bar").addClass("w-50");
                              $('#kycDetails').addClass('active');
                              $('#officialDetails').removeClass('active');
                              $('a[href="#officialDetails"]').removeClass('active');
                              $('a[href="#kycDetails"]').addClass('active');
                              $('.nav-pills li:nth-child(3) a').removeClass('disabled');
                              $('.nav-pills li:nth-child(3) a').tab('show');
                          }
                         }else{
                            $('.errClr').remove();
                            printErrorMsg(data.error);
                         }
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });   

          $('.kycDetailsForm').on('submit',function(event) {
                event.preventDefault();
                var formData = new FormData(this);
                 var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                   url: "{{ route('step3KycDetails') }}",
                   type: "POST",
                   dataType:'json',
                   headers: {
                     'X-CSRF-TOKEN': csrfToken 
                   },
                    data: formData,
                    contentType: false,
                    processData: false,
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                           // $('.errClr').html('').hide();
                          if(data.action=='submitDetails'){
                              $(".bar").addClass("w-100");
                              $('#submitDetails').addClass('active');
                              $('#kycDetails').removeClass('active');
                              $('a[href="#kycDetails"]').removeClass('active');
                              $('a[href="#submitDetails"]').addClass('active');
                              $('.nav-pills li:nth-child(4) a').removeClass('disabled');
                              $('.nav-pills li:nth-child(4) a').tab('show');
                              finish();
                          }
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });   

     
         $('#department').change(function() {
             var department = $(this).val();
             $.ajax({
                 url: '{{ route("getDesignations", ":department") }}'.replace(':department', department),
                 type: 'GET',
                 success: function(response) {
                      $('#designationsReplace').empty();
                     $.each(response, function(key, value) {
                         $('#designationsReplace').append('<option value="' + key + '">' + value + '</option>');
                     });
                 }
             });
         }); 
     
          $(document).ready(function() {
          $('#pancard').on('keyup', function() {
              var pancardValue = $(this).val();

              // Exit if input is empty
              if (!$.trim(pancardValue)) {
                  $('.pancardErr').text('');
                  return;
              }

              $.ajax({
                  url: '{{route('verifyPancard')}}', // URL to your Laravel endpoint
                  type: 'POST',
                  data: { pan: pancardValue , _token: '{{ csrf_token() }}'},
                  success: function(response) {
                    $('#email').val(response.emailId);
                    $('#mobile').val(response.mobileNumber);
                  
                     // Convert uppercase response value to Capitalized format
                      var capitalizeFirstLetter = function(string) {
                          return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
                      };

                      var genderValue = capitalizeFirstLetter(response.gender);
                      var name = capitalizeFirstLetter(response.name);
                      $('#fullName').val(name);
                      if (genderValue) {
                          $('#gender').val(genderValue);
                      }

                    $('input[name="gender"][value="' + genderValue + '"]').prop('checked', true);
                    var parts = response.dateOfBirth.split('-'); // Split by '-'
                    var formattedDate = parts[1] + '/' + parts[0] + '/' + parts[2]; // Format as "m/d/y"
                    $('#dob').val(formattedDate);
                  },
                  error: function(xhr, status, error) {
                      // Handle any errors here
                      $('.pancardErr').text('Error: Invalid Pancard' );
                  }
              });
          });
      });
 
        
         function finish() {

            $.ajax({
                   url: "{{ route('step4SaveDetails') }}",
                   type: "GET",
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                           $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                           setTimeout(function() {
                              window.location.href = "{{ route('usersList') }}";
                          }, 2000); 
                        }else{
                           $.NotificationApp.send("Oh snap!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                        }
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         } 

         function printErrorMsg(msg){
            $.each(msg,function(key,value){
                $('.'+key+'Err').html('<div class="alert alert-danger errClr mt-1" role="alert"><i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>'+value+'</strong></div>');
            });
        }

       
    });
    

    
    
</script>

@endsection