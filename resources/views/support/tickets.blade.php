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
                     <form action=" " method="get">
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
                                    @if(isSuperAdmin())
                                     <option value="exportAll" {{ $filter === 'exportAll' ? 'selected' : '' }}>Export All Data</option>
                                    @endif
                                    @if(rolesAccess('All Leads', 'export') || isSuperAdmin() || isAdmin())
                                     <option value="exportByDate" {{ $filter === 'exportByDate' ? 'selected' : '' }}>Export by Date Range</option>
                                    @endif
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4 searchBoxContainer">
                              <div class="input-group">
                                 {{-- <input type="text" name="search" class="form-control searchInput" placeholder="Search..."> --}}
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
                           <div class="col-md-8 customSortContainer" style="display: none;">
                              <div class="input-group">
                                 <input type="text" name="searchCustom" class="form-control customSearchInput" placeholder="Custom Search...">
                                 <input type="text" class="form-control date customDateRange" name="dateCustom" data-toggle="date-picker" data-cancel-class="btn-warning">
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
                              <a href="support/tickets" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
                              <i class="uil uil-refresh"></i>
                              </a>
                           </div>
                           @endif

                        </div>
                     </form>
                  </div>
                   <div class="col-md-4 text-end">
                        <button type="button" id="addFreshLeadBtn" class="btn btn-primary" 
                        data-bs-toggle="modal" data-bs-target="#success-header-modal">
                        <i class="uil uil-plus"></i> Add Ticket
                        </button>
                   </div>
               </div>
            </div>
            <div class="card-body" data-simplebar data-simplebar-lg style="overflow-x:auto;">
               <table  class="table w-100 table-striped" style="white-space: nowrap;">
                  <thead>
                     <tr style="font-size:14px;">
                        <th>#</th>
                        <th>Action</th>
                        <th>Ticket ID</th>
                        <th>Status</th>
                        <th>Subject</th>
                        <th>Department</th>
                        <th>Query Type</th>
                        <th>Generated By</th>
                        <th>Assigned To</th>
                        <th>Solved By</th>
                        <th>Process Time</th>
                        <th>Priority</th>
                        <th>Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($tickets->currentPage() - 1) * $tickets->perPage() + 1;
                     @endphp
                     @if ($tickets->isEmpty())
                     <tr>
                        <td colspan="18" class="text-center">No data found</td>
                     </tr>
                     @else
                     @foreach($tickets as $key => $arr)
                        @php
                            $statusColors = [
                                'Open' => 'info',
                                'Pending' => 'warning',
                                'Hold' => 'danger'
                            ];
                            $color = $statusColors[$arr->status] ?? 'success';

                            $priorityColors = [
                                'Low' => 'secondary',
                                'Medium' => 'warning',
                                'High' => 'danger'
                            ];
                            $priorColor = $priorityColors[$arr->priority] ?? 'success';

                        @endphp
                     <tr style="font-size:15px;">
                        <td>{{$serial++}}</td>
                        <td><a target="_blank" href="support/view-ticket/{{$arr->ticketID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                 data-bs-custom-class="primary-tooltip" data-bs-title="Ticket View" style="font-size: 16px;margin-left: 6px;"><i class='mdi mdi-eye'></i></a>
                           <a href="#" class="text-info open-modal" data-ticket-id="{{$arr->ticketID}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="info-tooltip" data-bs-title="Ticket Assign" style="font-size: 16px;margin-left: 6px;">
                            <i class='mdi mdi-square-edit-outline'></i></a>     
                        </td>
                        <td>{{$arr->ticketID}}</td>
                        <td><span class="badge bg-{{$color}}">{{$arr->status}}</span></td>
                        <td>{{$arr->subject}}</span></td>
                        <td>{{$arr->department}}</span></td>
                        <td>{{$arr->queryType}}</span></td>
                        <td>{{getUserNameById('users','userID',$arr->generatedBy,'displayName')}}</span></td>
                        <td>{{getUserNameById('users','userID',$arr->assignTo,'displayName')}}</span></td>
                        <td>{{getUserNameById('users','userID',$arr->solvedBy,'displayName')}}</span></td>
                        <td> </td>
                        <td><span class="badge bg-{{$priorColor}}">{{$arr->priority}}</span></td>
                        <td>{{dft($arr->addedOn)}}</td>
                     </tr>
                     @endforeach
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Ticket ID</th>
                        <th>Status</th>
                        <th>Subject</th>
                        <th>Department</th>
                        <th>Query Type</th>
                        <th>Generated By</th>
                        <th>Assigned To</th>
                        <th>Solved By</th>
                        <th>Process Time</th>
                        <th>Priority</th>
                        <th>Date</th>
                     </tr>
                  </tfoot>
               </table>
               <div class="row">
                  {{ $tickets->appends($queryParameters)->links('pagination::bootstrap-5') }}
               </div>
            </div>
         </div>   
      </div>
      <!-- end col -->
   </div>
   <!-- end row -->
</div> 

<div id="success-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Generate a Ticket</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="supportTicketModalForm" method="POST" enctype="multipart/form-data">
               @csrf
               <div class="row">
                  <div class="col-md-12 mb-2">
                     <label class="mb-1">Subject</label>
                     <input type="text" class="form-control" name="subject" placeholder="Write subject">
                     <span class="subjectErr"></span>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Query Type</label>

                     <select class="form-control" name="queryType">
                        <option value="">Select Query Type</option>
                        @if($supportType=='crm-support')
                           <option value="Settled To Closed">Settled To Closed</option>
                           <option value="Part Payment To Closed">Part Payment To Closed</option>
                           <option value="Others">Others</option>
                        @elseif($supportType=='it-helpdesk')
                           <option value="Laptop">Laptop</option>
                           <option value="Mail">Mail</option>
                           <option value="Internet">Internet</option>
                        @endif
                     </select>
                     <span class="queryTypeErr"></span>
                  </div> 
                  <div class="col-md-6 mb-2">
                     <label class="mb-1">Priority</label>
                     <select class="form-control" name="priority">
                        <option value="">Select Priority</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                     </select>
                     <span class="priorityErr"></span>
                  </div> 
                  <div class="col-md-12 mb-2">
                     <label class="mb-1">Enter Your Query</label>
                       <textarea name="description"></textarea>
                  </div>   
                  <div class="col-md-12">
                     <input name="file" type="file" id="file" class="form-control">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <input type="hidden" name="department" value="{{$supportType}}">
               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success" id="sendButton">Send</button>
            </div>
         </form>
      </div>
   </div>
</div>


<div id="open-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-success">
            <h4 class="modal-title text-white" id="success-header-modalLabel">Assign Ticket</h4>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1" id="supportTicketAssignModalForm" method="POST">
               @csrf
               <div class="row">
                  <div class="col-md-12 mb-2">
                     <label class="mb-1">Select user</label>
                     <select class="form-control" name="assignUser" id="assignUserID">
                        <option value="">Select User</option>
                        @foreach($techSupportUsers as $arr)
                           <option value="{{$arr->userID}}">{{$arr->displayName}}</option>
                        @endforeach
                     </select>
                      <span class="assignUserErr"></span>
                  </div> 
               </div>
            </div>
            <div class="modal-footer">
               <input type="hidden" name="ticketID" id="ticketIDAssign">
               <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
               <button type="button" class="btn btn-success" id="assignButton">Assign</button>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection

@section('custom-js')

<script type="text/javascript">


   $(document).ready(function() {
        // Click event handler for the link
        $('.open-modal').click(function(event) {
            event.preventDefault(); // Prevent default link behavior
 
            var ticketID = $(this).data('ticket-id'); // Get the Ticket from data attribute
            $('#ticketIDAssign').val(ticketID);
             $('#open-modal').modal('show');
        });
    });


    

      $(document).ready(function() {
          $('#assignButton').on('click', function() {
              // Get the form data
              var formData = $('#supportTicketAssignModalForm').serialize();
              
              $.ajax({
                  url: '{{route('assignUserTicket')}}', // Replace with your server endpoint
                  type: 'POST',
                  data: formData,
                  success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                           $('.errClr').html('').hide();
                           $('#open-modal').modal('hide');
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
          });
      });
 



    // Handle form submission
    var simplemde = new SimpleMDE({ element: $("#simplemde1")[0] });

    $('#sendButton').on('click', function(e) {
        e.preventDefault(); // Prevent the default form submission

        // Get the content from SimpleMDE editor
        var descriptionContent = simplemde.value();

        // Set the content into a hidden input field or append it to FormData
        var formData = new FormData($('#supportTicketModalForm')[0]);
        formData.append('description', descriptionContent); // Append SimpleMDE content

        // Send AJAX request
        $.ajax({
            url: '{{ route('generateTicket') }}', // URL to send the request to
            type: 'POST',
            data: formData,
            contentType: false, // Let the browser set the content type
            processData: false, // Do not process data
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
            },
            success: function(data) {
                if (data.response == 'success') {
                    $('#success-header-modal').modal('hide');
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    printErrorMsg(data.error);
                }
            }
        });
    });

   $(document).ready(function() {
    // Function to update containers based on selected option
    function updateContainers(selectedOption) {
        // Hide all containers and disable inputs
        $('.searchBoxContainer, .dateRangeContainer, .customSortContainer, .exportAllContainer, .exportByDateContainer').hide();
        $('.searchInput, .singledaterange, .customSearchInput, .customDateRange, .exportDateRange').prop('disabled', true);

        // Show the corresponding container and enable relevant inputs based on selected option
         if (selectedOption === 'sortByDate') {
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
            selectedOption = 'sortByToday'; // Default to 'sortBySearch'
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
   function printErrorMsg(msg) {
        $.each(msg, function(key, value) {
            $('.' + key + 'Err').html('<p class="text-danger mt-1 errClr font-12"><i class="ri-close-circle-line me-1 align-middle font-12"></i><strong>' + value + '</strong></p>');
        });
    }
   
</script>
@endsection