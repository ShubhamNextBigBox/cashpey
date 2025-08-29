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
                  <div class="col-md-8">
                     <form action="" method="get">
                        <div class="row">
                           <div class="col-md-4">
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
                                    <option value="exportAll" {{ $filter === 'exportAll' ? 'selected' : '' }}>Export All Data</option>
                                    <option value="exportByDate" {{ $filter === 'exportByDate' ? 'selected' : '' }}>Export by Date Range</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4 searchBoxContainer">
                              <div class="input-group">
                                 <input type="text" name="search" class="form-control searchInput" autocomplete="off" placeholder="Name, email, mobile...">
                                 <button class="btn btn-primary" type="submit" id="searchButton">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="col-md-4 dateRangeContainer"  style="display: none;">
                              <div class="input-group">
                                 <input type="text" class="form-control date singledaterange" name="searchRange" data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary customSearchButton" type="submit">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="col-md-7 customSortContainer" style="display: none;">
                              <div class="input-group">
                                 <input type="text" name="searchCustom" class="form-control customSearchInput" placeholder=" Search by name...">
                                 <input type="text" class="form-control date customDateRange" name="dateCustom" data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary customSearchButton" type="submit">
                                 <i class="uil uil-search"></i>
                                 </button>
                              </div>
                           </div>
                           <div class="col-md-2 exportAllContainer" style="display: none;">
                              <button class="btn btn-primary" type="submit">
                              <i class="uil uil-export"></i> Export
                              </button>
                           </div>
                           <div class="col-md-5 exportByDateContainer" style="display: none;">
                              <div class="input-group">
                                 <input type="text" class="form-control date exportDateRange" name="exportRange" data-toggle="date-picker" data-cancel-class="btn-warning">
                                 <button class="btn btn-primary exportByDateRangeButton" type="submit">
                                 <i class="uil uil-export"></i> Export
                                 </button>
                              </div>
                           </div>
                           @if(!empty($filter))
                           <div class="col-md-1">
                              <a href="reporting/approval-matrix-leads" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
                              <i class="uil uil-refresh"></i>
                              </a>
                           </div>
                           @endif
                        </div>
                     </form>
                  </div>
                 <div class="col-md-4 text-end">
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#open-modal-add">
                           <i class="uil uil-plus"></i> Add Attendance
                  </button>
               </div>
               </div>
            </div>
            <div class="card-body" style="overflow-x:auto;">
               <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                  <thead>
                     <tr style="font-size:14px;">
                        <th>#</th>
                        <th>Action</th>
                        <th>Employee Name</th>
                        <th>Attendance Date</th>
                        <th>Sign In</th>
                        <th>Sign Out</th>
                        <th>Working Hours</th>
                     </tr>
                  </thead>
                  <tbody>
                     @if(count($attendanceList)>0)
                     @foreach($attendanceList as $key => $arr)
                      
                     @php   
                        $signIN = new DateTime($arr->signIN);
                        $signOut = $arr->signOut ? new DateTime($arr->signOut) : null;
 
                        if ($signOut) {
                            $interval = $signIN->diff($signOut);
                            $timeDifference = $interval->format('%h hours %i minutes');
                        } else {
                            $timeDifference = '-';
                        }
                     @endphp   
                     <tr style="font-size:15px;">
                        <td>{{++$key}}</td>
                        <td><a href="#" class="text-success open-modal" data-user-id="{{$arr->userID}}" data-attendance-date="{{$arr->attendanceDate}}"  data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="success-tooltip" data-bs-title="View Attendance" style="font-size: 16px;margin-left: 6px;"><i class='mdi mdi-eye'></i></a>
                           <a href="#" class="text-info open-modal-edit" data-update-id="{{$arr->id}}"  data-emp-name="{{getUserNameById('users', 'userID',$arr->userID, 'displayName')}}" data-punch-in="{{$arr->signIN}}" data-punch-out="{{$arr->signOut}}" data-attendance-date="{{$arr->attendanceDate}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="info-tooltip" data-bs-title="Attendance Edit" style="font-size: 16px;margin-left: 6px;">
                            <i class='mdi mdi-square-edit-outline'></i></a>     
                        </td>     
                        <td>{{getUserNameById('users', 'userID',$arr->userID, 'displayName')}}</td>
                        <td>{{df($arr->attendanceDate)}}</td>
                        <td>{{$arr->signIN}}</td>
                        <td>{{$arr->signOut ?? '-'}}</td>
                        <td>{{ $timeDifference }}</td>
                     </tr>
                     @endforeach
                     @else
                        <tr>
                           <td colspan="9" class="text-center">No Record Found</td>
                        </tr>
                     @endif

                   
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Employee Name</th>
                        <th>Attendance Date</th>
                        <th>Sign In</th>
                        <th>Sign Out</th>
                        <th>Working Hours</th>
                     </tr>
                  </tfoot>
               </table>
               <div class="row">
                  {{ $attendanceList->appends($queryParameters)->links('pagination::bootstrap-5') }}
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

<div id="open-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content"  data-simplebar data-simplebar-md style="max-height:600px;">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Attendance Record</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
               <div id="attendanceLogShow">
                  
               </div>      
         </div>
      </div>
   </div>
</div>


<div id="open-modal-add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Add Attendance</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="attendanceAddModalForm" method="POST">
               @csrf
               <div class="row">
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Employee Name</label>
                      <select class="select2 form-control" data-toggle="select2" name="employee"  data-placeholder="Select Employee">     
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                           <option value="{{$user->userID}}">{{$user->displayName}}</option>
                        @endforeach  
                     </select>
                     <span class="employeeErr"></span>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="form-label">Attendance Date</label>
                     <div class="position-relative" id="datepicker1">
                        <input type="text" class="form-control" name="attendanceDate" data-provide="datepicker" data-date-container="#datepicker1" data-date-autoclose="true" value="{{date('m/d/Y')}}">
                        <span class="attendanceDateErr"></span>
                     </div>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Punch IN</label>
                      <div class="input-group">
                          <input id="basic-timepicker" type="text" name="punchIn" class="form-control" placeholder="Punch IN">
                          <span class="input-group-text"><i class="ri-time-line"></i></span>
                          <span class="punchInErr"></span>
                      </div>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Punch Out</label>
                     <div class="input-group">
                          <input id="24hours-timepicker" type="text" name="punchOut" class="form-control" placeholder="Punch Out">
                         <span class="input-group-text"><i class="ri-time-line"></i></span>
                         <span class="punchOutErr"></span>
                      </div>
                  </div> 
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success">Add</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="open-modal-edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Edit Attendance</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="attendanceEditModalForm" method="POST">
               @csrf
               <div class="row">
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Employee Name</label>
                     <input type="text" name="employee" readonly class="form-control" id="employeeName" placeholder="Employee Name">
                     <span class="employeeErr"></span>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="form-label">Attendance Date</label>
                     <div class="position-relative" id="datepicker1">
                        <input type="date" class="form-control" name="attendanceDate" value="" id="attendanceDateEdit">
                        <span class="attendanceDateErr"></span>
                     </div>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Punch IN</label>
                      <div class="input-group">
                          <input type="time" name="punchIn" id="punchInEdit" class="form-control" placeholder="Punch IN">
                          <span class="input-group-text"><i class="ri-time-line"></i></span>
                          <span class="punchInErr"></span>
                      </div>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Punch Out</label>
                     <div class="input-group">
                          <input type="time" name="punchOut" id="punchOutEdit" class="form-control" placeholder="Punch Out">
                         <span class="input-group-text"><i class="ri-time-line"></i></span>
                         <span class="punchOutErr"></span>
                      </div>
                  </div> 
               </div>
            </div>
            <div class="modal-footer">
               <input type="hidden" name="updateID" id="updateID">
               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-success" id="assignButton">Update</button>
            </div>
         </form>
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
 

     $('.open-modal').click(function(event) {
            event.preventDefault(); // Prevent default link behavior
             var userID = $(this).data('user-id');
             var attendanceDate = $(this).data('attendance-date');
             $.ajax({
                    url: "{{ route('showAttendanceLog') }}",
                    type: 'GET',
                    data: {
                        userID: userID,
                        attendanceDate: attendanceDate,
                    },
                    success: function(data) {
                        $('#attendanceLogShow').html(data);
                    },
                  });
             $('#open-modal').modal('show');
      });

      
      $('.open-modal-edit').click(function(event) {
          event.preventDefault();
          var ID = $(this).data('update-id'); 
          var empName = $(this).data('emp-name');  
          var punchIn = $(this).data('punch-in');  
          var punchOut = $(this).data('punch-out');  
          var attendanceDate = $(this).data('attendance-date');  

          // Remove "AM" and "PM" from punchIn and punchOut
          punchIn = punchIn.replace(/\s*AM\s*/i, '').replace(/\s*PM\s*/i, '').trim();
          punchOut = punchOut.replace(/\s*AM\s*/i, '').replace(/\s*PM\s*/i, '').trim();

          $('#attendanceDateEdit').val(attendanceDate);
          $('#punchInEdit').val(punchIn);
          $('#punchOutEdit').val(punchOut);
          $('#updateID').val(ID);
          $('#employeeName').val(empName);
          $('#open-modal-edit').modal('show');
      });

  

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
        } else if (selectedOption === 'sortByCustom') {
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
 
 $(document).on('submit', '#attendanceEditModalForm', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: "{{ route('attendanceEdit') }}",
            type: "POST",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken 
            },
            data: formData,
            success: function(data) {
                if (data.response == 'success') {
                    $('.errClr').html('').hide();
                    $('#attendanceEditModalForm').trigger('reset');
                    $('#open-modal-edit').modal('hide');
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    printErrorMsg(data.error);
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });

    $(document).on('submit', '#attendanceAddModalForm', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: "{{ route('attendanceAdd') }}",
            type: "POST",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': csrfToken 
            },
            data: formData,
            success: function(data) {
                if (data.response == 'success') {
                    $('.errClr').html('').hide();
                    $('#attendanceAddModalForm').trigger('reset');
                    $('#open-modal-add').modal('hide');
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                    setTimeout(function() { window.location.reload(); }, 1000);
                } else {
                    printErrorMsg(data.error);
                }
            },
            error: function(error) {
                console.error(error);
            }
        });
    });

    function printErrorMsg(msg) {
        $('.errClr').remove();
        $.each(msg, function(key, value) {
            $('.' + key + 'Err').html('<div class="text-danger errClr mt-1" style="font-size:12px;"><i class="ri-close-circle-line me-1 align-left font-12"></i><strong>' + value + '</strong></div>');
        });
    }

 
</script>
@endsection