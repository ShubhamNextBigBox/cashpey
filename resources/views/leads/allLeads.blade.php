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
            <div class="card-header">
               <div class="row">
                  <div class="col-md-12">
                     <form action="" method="get">
                        <div class="row">
                           <div class="col-md-3">
                              <!-- Adjusted column width -->
                              <div class="input-group">
                                 <span class="input-group-text">
                                 <i class="uil uil-filter"></i>
                                 </span>
                                 <select class="form-select exportSelect" name="filter">
                                 <option value="sortBySearch"  {{ $filter === 'sortBySearch' ? 'selected' : '' }}>Search</option>
                                 <option value="sortByToday"  {{ $filter === 'sortByToday' ? 'selected' : '' }}>Today</option>
                                 <option value="sortByWeek"  {{ $filter === 'sortByWeek' ? 'selected' : '' }}>Last 7 Days</option>
                                 <option value="sortByThisMonth"  {{ $filter === 'sortByThisMonth' ? 'selected' : '' }}>Current Month</option>
                                 <option value="sortByLastMonth"  {{ $filter === 'sortByLastMonth' ? 'selected' : '' }}>Previous Month</option>
                                 <option value="sortByDate" {{ $filter === 'sortByDate' ? 'selected' : '' }}>Date Range</option>
                                 @if(isSuperAdmin())
                                 <option value="exportAll" {{ $filter === 'exportAll' ? 'selected' : '' }}>Export All Data</option>
                                 @endif
                                 @if(rolesAccess('All Leads', 'export') || isSuperAdmin() || isAdmin())
                                 <option value="exportByDate" {{ $filter === 'exportByDate' ? 'selected' : '' }}>Export by Date Range</option>
                                 @endif
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-3 searchBoxContainer">
                              <div class="input-group">
                                 <input type="text" name="search" class="form-control searchInput" placeholder="Name, email, mobile, pancard...">
                                 <button class="btn btn-primary" type="submit" id="searchButton">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="col-md-3 dateRangeContainer"  style="display: none;">
                              <div class="input-group">
                                 <input type="text" class="form-control date singledaterange" name="searchRange" data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary customSearchButton" type="submit">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           @if(isSuperAdmin())
                           <div class="col-md-3 exportAllContainer" style="display: none;">
                              <button class="btn btn-primary" type="submit">
                              <i class="uil uil-export"></i> Export
                              </button>
                           </div>
                           @endif
                           @if(rolesAccess('All Leads', 'export') || isSuperAdmin() || isAdmin())
                           <div class="col-md-4 exportByDateContainer">
                              <div class="input-group">
                                 <input type="text" class="form-control date exportDateRange" name="exportRange" id="exportRange" 
                                    data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary exportByDateRangeButton" type="submit">
                                 <i class="uil uil-export"></i> Export
                                 </button>
                              </div>
                           </div>
                           @endif
                           @if(!empty($filter))
                           <div class="col-md-1">
                              <a href="leads/all-leads" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
                              <i class="uil uil-refresh"></i>
                              </a>
                           </div>
                           @endif
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <div class="card-body" data-simplebar data-simplebar-lg style="overflow-x:auto;">
               <table  class="table w-100 table-striped" style="white-space: nowrap;">
                  <thead>
                     <tr style="font-size:14px;">
                        <th>#</th>
                        @if(rolesAccess('All Leads', 'view') || isSuperAdmin() || isAdmin())
                        <th>Action</th>
                        @endif
                       <th>Lead ID</th>
                       <th>Status</th>
                       <th>Assigned RM</th>
                       <th>Assigned CM</th>
                       <th>Name</th>
                       <th>Email</th>
                       <th>Mobile</th>
                       <th>Pancard</th>
                       <th>Monthly Income</th>
                       <th>City</th>
                       <th>Employement Type</th>
                       <th>Utm Source</th>
                       <th>Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                     @endphp
                     @if ($leads->isEmpty())
                     <tr>
                        <td colspan="15" class="text-center">No data found</td>
                     </tr>
                     @else
                     @foreach($leads as $key => $arr)
                     <tr style="font-size:15px;">
                        <td>{{$serial++}}</td>
                        @if(rolesAccess('All Leads', 'view') || isSuperAdmin() || isAdmin())
                        <td>
                           <a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                              data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                           <i class='mdi mdi-eye'></i>
                           </a>
                        </td>
                        @endif
                       <td>{{$arr->leadID}}</a></td>
                       <td><span class="badge bg-secondary">{{$arr->status}}</span></td>
                       <td>{{(!empty($arr->rmID) ? getUserNameById('users','userID',$arr->rmID,'displayName') : 'N/A')}}</td>
                       <td>{{(!empty($arr->cmID) ? getUserNameById('users','userID',$arr->cmID,'displayName') : 'N/A')}}</td>
                       @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                       <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                       @endif
                       <td>{{$arr->email}}</td>
                       <td>{{$arr->mobile}}</td>
                       <td>{{$arr->pancard}}</td>
                       <td>{{nf($arr->monthlyIncome)}}</td>
                       <!--<td>{{$arr->city}}</td>-->
                       <td>{{getUserNameById('lms_cities', 'cityID', $arr->city, 'cityName')}}</td>
                       <td>{{$arr->customerType}}</td>
                       <td>{{$arr->utmSource}}</td>
                       <!-- DFT(date-format) is a custom helper -->
                       <td>{{dft($arr->addedOn)}}</td>
                     </tr>
                     @endforeach
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                        @if(rolesAccess('All Leads', 'view') || isSuperAdmin() || isAdmin())
                        <th>Action</th>
                        @endif
                       <th>Lead ID</th>
                       <th>Status</th>
                       <th>Assigned RM</th>
                       <th>Assigned CM</th>
                       <th>Name</th>
                       <th>Email</th>
                       <th>Mobile</th>
                       <th>Pancard</th>
                       <th>Monthly Income</th>
                       <th>City</th>
                       <th>Employement Type</th>
                       <th>Utm Source</th>
                       <th>Date</th>
                     </tr>
                  </tfoot>
               </table>
               <div class="row">
                  {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
               </div>
            </div>
         </div>
      </div>
      <!-- end col -->
   </div>
   <!-- end row -->
</div>
<!-- container -->
<!-- /.modal -->
<div id="info-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-sm">
      <div class="modal-content">
         <div class="modal-body p-4">
            <div class="text-center">
               <i class="ri-alert-line h1 text-warning"></i>
               <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
               <p class="mt-3"></p>
               <input type="hidden" id="branchIdDelete">
               <button type="button" class="btn btn-success my-2" id="confirmYes">Yes</button>
               <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
            </div>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
@endsection

@section('custom-js')

<script type="text/javascript">

    $(document).ready(function() {
      // Manually activate the tab based on URL hash
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href"); // activated tab
            window.location.hash = target; // add tab id to browser URL
        });

        // Check if there's a hash in the URL and activate the corresponding tab
        if(window.location.hash) {
            $('a[data-bs-toggle="tab"][href="' + window.location.hash + '"]').tab('show');
        }
    });


       $('#checkAll').change(function() {
            $('.leadCheckbox').prop('checked', $(this).prop('checked'));
            toggleDeleteButton();
        });

        // Individual checkbox change
        $('.leadCheckbox').change(function() {
            toggleDeleteButton();
        });

        function toggleDeleteButton() {
            var anyChecked = false;
            $('.leadCheckbox').each(function() {
                if ($(this).prop('checked')) {
                    anyChecked = true;
                    return false; // Exit each loop early
                }
            });

            if (anyChecked) {
                $('.deleteButton').show();
            } else {
                $('.deleteButton').hide();
            }
        }
   

            // Handle delete button click
        $('.deleteButton').click(function() {
            var leadIDs = [];
            $('.leadCheckbox:checked').each(function() {
                leadIDs.push($(this).val());
            });

            if (leadIDs.length === 0) {
                alert('Please select at least one lead to delete.');
                return;
            }

           
            $('#info-alert-modal').modal('show');

          
            $('#leadIdDelete').val(leadIDs.join(','));

         
            $('#confirmYes').click(function() {
               
                $.ajax({
                    url: "{{ route('leadsDelete') }}",
                    type: 'POST',
                    data: {
                        leadIDs: leadIDs,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                      if(data.response=='success'){
                         $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                         setTimeout(function(){window.location.reload();}, 1000);
                       }
                    },
                    
                });

                // Hide modal after deletion
                $('#info-alert-modal').modal('hide');
            });
        });


    $('#addFreshLeadBtn').click(function() {
      $('#leadAddEditModalForm')[0].reset(); 
    });
   
     // Date picker initialization or additional actions as needed
  

   $(document).ready(function() {
    // Function to update containers based on selected option
    function updateContainers(selectedOption) {
        // Hide all containers and disable inputs
        $('.searchBoxContainer, .dateRangeContainer, .customSortContainer, .exportAllContainer, .exportByDateContainer').hide();
        $('.searchInput, .singledaterange, .customSearchInput, .customDateRange, .exportDateRange').prop('disabled', true);

        // Show the corresponding container and enable relevant inputs based on selected option
        if (selectedOption === 'sortBySearch') {
            $('.searchBoxContainer').show();
            $('.searchInput').prop('disabled', false);
        } else if (selectedOption === 'sortByDate') {
            $('.dateRangeContainer').show();
            $('.singledaterange').prop('disabled', false);
        } else if (selectedOption === 'customSort') {
            $('.customSortContainer').show();
            $('.customSearchInput, .customDateRange').prop('disabled', false);
        } else if (selectedOption === 'exportAll') {
            $('.exportAllContainer').show();
            // No specific input to enable/disable
        } else if (selectedOption === 'exportByDate') {
            $('.exportByDateContainer').show();
            $('.exportDateRange').prop('disabled', false);
        } else if (selectedOption === 'sortByToday' || selectedOption ==='sortByWeek' || selectedOption === 'sortByThisMonth' || selectedOption === 'sortByLastMonth') {
            // Only show the search button in searchBoxContainer for these options
            $('.searchBoxContainer').show();
            $('#searchButton').show();
        } else {
            // Default behavior if no valid option selected or if selectedOption is blank
            selectedOption = 'sortBySearch'; // Default to 'sortBySearch'
            $('.searchBoxContainer').show();
            $('.searchInput').prop('disabled', false);
        }
        
        // Set the selected option in the dropdown
        $('.exportSelect').val(selectedOption);
    }

    // Initialize with default option 'sortBySearch'
     @php
       $selectedOption = !empty($filter) ? $filter : 'sortBySearch';
      @endphp
    var selectedOption = '{{ $selectedOption }}';
    updateContainers(selectedOption);

    // Handle change event on the select dropdown
    $('.exportSelect').change(function() {
        var selectedOption = $(this).val();
        updateContainers(selectedOption);
    });
});


   
   

   $(document).ready(function() {
        $('#state').change(function(){
            var stateID = $(this).val();
            if(stateID) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('fetchCities') }}",
                    data: { stateID: stateID },
                    success:function(res) {
                        if(res && res.length > 0) { // Check if response is not empty
                            $("#cities").empty();
                            $.each(res,function(key,value){
                                $("#cities").append('<option value="'+value.cityID+'">'+value.cityName+'</option>'); // Fix the capitalization for cityName
                            });
                        } else {
                            $("#cities").empty().append('<option value="">No Cities Found</option>'); // Provide feedback for empty response
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Log any errors to console for debugging
                    }
                });
            } else {
                $("#cities").empty().append('<option value="">Select State First</option>'); // Provide feedback for no state selected
            }
        });
    });


       $(document).ready(function() {
        // Click event handler for the link
        $('.open-modal').click(function(event) {
            event.preventDefault(); // Prevent default link behavior
            
            var leadID = $(this).data('lead-id'); // Get the leadID from data attribute
            
            // Make AJAX request to fetch data using leadID
            $.ajax({
                url: "{{ route('freshLeadEdit') }}", // Replace with your route URL
                type: 'GET',
                data: { leadID: leadID },
                success: function(response) {
                    // Populate modal with fetched data
                    $('#nameOnPancard').val(response.name);
                    $('#email').val(response.email);
                    $('#mobile').val(response.mobile);
                    $('#pancard').val(response.pancard);
                    $('#adharNumber').val(response.aadharNo);
                    $('#loanAmount').val(response.loanRequired);
                    $('#monthlySalary').val(response.monthlyIncome);
                    var parts = response.dob.split('-'); // Split by '-'
                    var formattedDate = parts[1] + '/' + parts[2] + '/' + parts[0]; // Format as "m/d/y"
                
                    $('#dob').val(formattedDate);
                    $('input[name="gender"][value="' + response.gender + '"]').prop('checked', true);
                    $('input[name="employmentType"][value="' + response.customerType + '"]').prop('checked', true);
                    $('#state').val(response.state).trigger('change');
                    $('#cities').val(response.city).trigger('change');
                    $('#pincode').val(response.pincode);
                    $('#purpose').val(response.purpose);
                    $('#rmID').val(response.rmID);
                    $('#leadID').val(response.leadID);
                    $('#contactID').val(response.contactID);
                    
                    // Open the modal
                    $('#right-modal').modal('show');
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                }
            });
        });
    });


//        $(document).ready(function() {
//             // $('#submitBtn').click(function() {
//                // var panNumber = $('#panNumber').val();

//          alert();
// api();
//                function api(){
//                 // Basic validation for PAN number format
//                 // if (!/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(panNumber)) {
//                 //     $('#result').html('Invalid PAN number format.');
//                 //     return;
//                 // }

//                 // Make the AJAX request
//                 $.ajax({
//                     url: 'https://api-preproduction.signzy.app/api/v3/panextensive', 
//                     type: 'POST',
//                     contentType: 'application/json',
//                     headers: {
//                         'Authorization': 'qIOpYzj9svs3Jqsa0KWBGS5QEBiNnmhl' 
//                     },
//                     data: { BXEPK7452A: BXEPK7452A },
//                     success: function(response) {

//                      console.log(response);
//                         // Handle the successful response here
//                        // $('#result').html('Response: ' + JSON.stringify(response));
//                     },
//                     error: function(xhr, status, error) {
//                         // Handle errors here
//                         $('#result').html('Error: ' + xhr.responseText);
//                     }
//                 });
//              }
//             // });
//         });

$(document).on('submit','#leadAddEditModalForm',function(event) {
        event.preventDefault();

        var formData = $(this).serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var leadID = $('#leadID').val();
       
        $.ajax({
           url: "{{ route('leadAdd') }}",
           type: "POST",
           dataType:'json',
           headers: {
             'X-CSRF-TOKEN': csrfToken 
           },
           data: formData,
           success: function(data) {
                if(data.response=='success'){
                   $('.errClr').html('').hide();
                   $('#leadAddEditModalForm').trigger('reset');
                   $('#right-modal').modal('hide');
                   $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                   setTimeout(function(){window.location.reload();}, 1000);
                 }else{
                     printErrorMsg(data.error);
                 }
           },
           error: function(error) {
               console.error(error);
           }
       });
      

       function printErrorMsg(msg){
            $('.errClr').remove();
            $.each(msg,function(key,value){
                $('.'+key+'Err').html('<div class="text-danger errClr mt-1" style="font-size:12px;"><i class="ri-close-circle-line me-1 align-left font-12"></i><strong>'+value+'</strong></div>');
            });
        } 
});   
</script>
@endsection