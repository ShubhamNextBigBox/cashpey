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
            <h4 class="page-title">{{$page_info['page_name']}}  ({{$filterShow}})</h4>
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
                                 <input type="text" name="search" class="form-control searchInput" placeholder="No search...">
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
                              <a href="marketing/salary-wise" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
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
 <table class="table table-centered table-nowrap table-hover mb-0">
    <thead>
        <tr class="bg-primary">
            <th class="text-white">Monthly Income Range</th>
            <th class="text-white">Total Leads</th>
            <th class="text-white">Percentage</th>
        </tr>
    </thead>
    <tbody>
        @forelse($incomeWiseData as $data)
            <tr>
                <td>{{ $data->income_range }}</td>
                <td>{{ $data->total_count }}</td>
                <td>{{ $data->percentage }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">No records found.</td>
            </tr>
        @endforelse
    </tbody>
    <!-- Total row -->
    <tfoot>
        <tr class="bg-light">
            <td><strong>Grand Total</strong></td>
            <td><strong>{{ $totalCount }}</strong></td>
            <td><strong>100%</strong></td>
        </tr>
    </tfoot>
</table>
 
            </div>
         </div>
      </div>
      <!-- end col -->
   </div>
   <!-- end row -->
</div>
<!-- container -->
 
@endsection

@section('custom-js')

<script type="text/javascript">
 
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
       $selectedOption = !empty($filter) ? $filter : 'sortByThisMonth';
      @endphp
    var selectedOption = '{{ $selectedOption }}';
    updateContainers(selectedOption);

    // Handle change event on the select dropdown
    $('.exportSelect').change(function() {
        var selectedOption = $(this).val();
        updateContainers(selectedOption);
    });
});
 
</script>
@endsection