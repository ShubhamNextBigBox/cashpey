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
                                <!-- Filter Dropdown -->
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="uil uil-filter"></i>
                                    </span>
                                    <select class="form-select exportSelect" name="filter">
                                        <option value="sortByToday"  {{ $filter === 'sortByToday' ? 'selected' : '' }}>Today</option>
                                        <option value="sortByWeek"  {{ $filter === 'sortByWeek' ? 'selected' : '' }}>Last 7 Days</option>
                                        <option value="sortByThisMonth"  {{ $filter === 'sortByThisMonth' ? 'selected' : '' }}>Current Month</option>
                                        <option value="sortByLastMonth"  {{ $filter === 'sortByLastMonth' ? 'selected' : '' }}>Previous Month</option>
                                        <option value="sortByDate" {{ $filter === 'sortByDate' ? 'selected' : '' }}>Date Range</option>
                                        @if(isSuperAdmin())
                                            <option value="exportAll" {{ $filter === 'exportAll' ? 'selected' : '' }}>Export All Data</option>
                                        @endif
                                        @if(isSuperAdmin())
                                            <option value="exportByDate" {{ $filter === 'exportByDate' ? 'selected' : '' }}>Export by Date Range</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                    
                            <div class="col-md-1 searchBoxContainer">
                                <div class="input-group">
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
                    
                            @if(isSuperAdmin() || isAdmin())
                                <div class="col-md-4 exportByDateContainer" style="display: none;">
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
                                <div class="col-md-1 text-left">
                                    <a href="activity/logs" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
                                        <i class="uil uil-refresh"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                  </div>
               </div>
            </div>
            <div class="card-body actlog">
               <table class="table dt-responsive w-100 branchTable " id="activity-logs-table">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Performed By</th>
                        <th>Performed At</th>
                        <th>Log</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + 1;
                     @endphp
                     @foreach($activityLogs as $key => $arr)
                     @php
                     $collapseId = 'collapseExample' . $key;
                     $logId = 'log' . $key;
                     @endphp
                     <tr>
                        <td>{{ $serial++ }}</td>
                        <td>{{ $arr->module }}</td>
                        <td>{{ $arr->description }}</td>
                        <td>{{ getUserNameById('users', 'userID', $arr->userID, 'displayName') }}</td>
                        <td>{{ $arr->performedOn }}</td>
                        <td>
                           <button class="btn btn-outline-primary btn-sm" type="button"
                              data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                              aria-expanded="false" aria-controls="{{ $collapseId }}">
                           <i class="ri-arrow-down-s-line"></i>
                           </button>
                        </td>
                     </tr>
                    <tr>
                        <td colspan="6">
                            <div class="collapse" id="{{ $collapseId }}">
                                
                                 <div class="table-data">
                                    <strong>
                                       <pre id="{{ $logId }}">{{$arr->log}}</pre>
                                    </strong>
                                    <i class="mdi mdi-content-copy" onclick="copyToClipboard('{{ $logId }}')"></i>
                                 </div>
                              
                            </div>
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
               </table>
               <div class="row">
                  {{ $activityLogs->appends($queryParameters)->links('pagination::bootstrap-5') }}
               </div>
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
<div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title" id="standard-modalLabel">Log Details</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <pre id="logContent" style="white-space: pre-wrap;"></pre>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endsection
@section('custom-js')
 
  <script>
    $(document).ready(function() {
        // Loop through all pre elements with a specific id pattern
          // Function to update containers based on selected option
         // Function to update containers based on selected option
        function updateContainers(selectedOption) {
            // Hide all containers and disable inputs
            $('.dateRangeContainer, .exportAllContainer, .exportByDateContainer').hide();
            $('.singledaterange, .exportDateRange').prop('disabled', true);  // Disable all date inputs
            $('#searchButton').hide(); // Hide search button by default

            // Show the corresponding container and enable relevant inputs based on selected option
            if (selectedOption === 'sortByDate') {
                $('.dateRangeContainer').show();
                $('.singledaterange').prop('disabled', false);  // Enable the date input for date range
                $('#searchButton').hide();  // Show the search button for sorting by date
                $('.searchBoxContainer').hide();  // Show the search button for sorting by date
            } else if (selectedOption === 'exportAll') {
                $('.exportAllContainer').show();  // Show the export all container
                 $('#searchButton').hide();  // Show the search button for sorting by date
                $('.searchBoxContainer').hide();  // Show th
            } else if (selectedOption === 'exportByDate') {
                $('.exportByDateContainer').show();
                $('.exportDateRange').prop('disabled', false);  // Enable export date range input
                 $('#searchButton').hide();  // Show the search button for sorting by date
                $('.searchBoxContainer').hide();  // Show th
            } else if (selectedOption === 'sortByToday' || selectedOption === 'sortByWeek' || selectedOption === 'sortByThisMonth' || selectedOption === 'sortByLastMonth') {
                // Show the search button for these options
                $('#searchButton').show();
                $('.searchBoxContainer').show();
            } else {
                // Default behavior if no valid option selected or if selectedOption is blank
                selectedOption = 'sortByToday';  // Default to 'sortByToday'
                $('#searchButton').show();  // Show the search button
            }

            // Set the selected option in the dropdown
            $('.exportSelect').val(selectedOption);
        }

        // Initialize with default option (based on controller data or 'sortByToday')
        @php
            $selectedOption = !empty($filter) ? $filter : 'sortByToday';
        @endphp
        var selectedOption = '{{ $selectedOption }}';
        updateContainers(selectedOption);  // Call the updateContainers function on page load

        // Handle change event on the select dropdown
        $('.exportSelect').change(function() {
            var selectedOption = $(this).val();
            updateContainers(selectedOption);  // Update containers based on selected filter
        });

  
     
        // Function to copy text content to clipboard
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId).textContent;

            // Create a temporary textarea element to copy text
            var textarea = document.createElement("textarea");
            textarea.value = copyText;
            document.body.appendChild(textarea);
            textarea.select(); // Select the text
            document.execCommand("copy"); // Copy the text
            document.body.removeChild(textarea); // Remove the textarea

            // Optionally, provide feedback to the user
            alert("Copied to clipboard!");
        }

        // Attach the function to global window scope so it's accessible in the onclick
        window.copyToClipboard = copyToClipboard; 
    });
</script>
 

@endsection