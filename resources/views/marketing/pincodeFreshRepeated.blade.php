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
                                 <input type="text" name="search" class="form-control searchInput" placeholder="Pincode...">
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
               <table class="table table-centered table-nowrap table-hover mb-0">
                   <thead>
                        <tr class="bg-primary">
                           <th class="text-white">Pincode</th>
                           <th class="text-white">Fresh Cases</th>
                           <th class="text-white">Fresh Loan Amount</th>
                           <th class="text-white">Repeat Cases</th>
                           <th class="text-white">Repeat Loan Amount</th>
                           <th class="text-white">Grand Total Cases</th>
                           <th class="text-white">Grand Total Loan Amount</th>
                       </tr>
                   </thead>
                   <tbody>
                       @php
                           $totalFreshCases = 0;
                           $totalFreshLoanAmount = 0;
                           $totalRepeatCases = 0;
                           $totalRepeatLoanAmount = 0;
                       @endphp

                       @if(count($pincodeFreshRepeatData) > 0)
                           @foreach($pincodeFreshRepeatData as $arr)
                               @php
                                   $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                                   $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;

                                   // Accumulate totals
                                   $totalFreshCases += $arr->freshCases;
                                   $totalFreshLoanAmount += $arr->freshLoanAmount;
                                   $totalRepeatCases += $arr->repeatCases;
                                   $totalRepeatLoanAmount += $arr->repeatLoanAmount;
                               @endphp

                               <tr>
                                   <td class="table-user">
                                       {{$arr->pincode}}
                                   </td>
                                   <td>
                                       <span class="font-14">{{ $arr->freshCases }}</span>  
                                   </td>
                                   <td>
                                       <span class="font-14">{{ nf($arr->freshLoanAmount) }}</span>  
                                   </td>
                                   <td>
                                       <span class="font-14">{{ $arr->repeatCases }}</span>  
                                   </td>
                                   <td>
                                       <span class="font-14">{{ nf($arr->repeatLoanAmount) }}</span>  
                                   </td>
                                   <td>{{ $grandTotalCases }}</td>
                                   <td>{{ nf($grandTotalLoanAmount) }}</td>
                               </tr>
                           @endforeach
                       @else
                           <tr>
                               <td colspan="7" class="text-center">No Records Found</td>
                           </tr>
                       @endif
                       @if(count($pincodeFreshRepeatData) > 0)
                          <tfoot>
                              <tr>
                                  <th>Total</th>
                                  <th>{{ $totalFreshCases }}</th>
                                  <th>{{ nf($totalFreshLoanAmount) }}</th>
                                  <th>{{ $totalRepeatCases }}</th>
                                  <th>{{ nf($totalRepeatLoanAmount) }}</th>
                                  <th>{{ $totalFreshCases + $totalRepeatCases }}</th>
                                  <th>{{ nf($totalFreshLoanAmount + $totalRepeatLoanAmount) }}</th>
                              </tr>
                          </tfoot>
                       @endif
                   </tbody>
               </table>
               <div class="row mt-1">
                  {{ $pincodeFreshRepeatData->appends($queryParameters)->links('pagination::bootstrap-5') }}
               </div>
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
 
</script>
@endsection