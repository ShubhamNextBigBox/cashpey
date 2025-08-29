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
                                 @if(rolesAccess('Approved', 'export') || isSuperAdmin() || isAdmin())
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
                        @if(rolesAccess('Approved', 'export') || isSuperAdmin() || isAdmin())
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
                              <a href="sanction/sanction-rejected" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
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
                        <th>Action</th>
                        <th>Lead ID</th>
                        <th>Branch</th>
                        <th>Sanction By</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Pancard</th>
                        <th>Employed</th>
                        <th>Loan Amount</th>
                        <th>Tenure</th>
                        <th>ROI</th>
                        <th>Admin Fee</th>
                        <th>Monthly Income</th>
                        <th>Cibil</th>
                        <th>Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                     @endphp
                     @if ($leads->isEmpty())
                     <tr>
                        <td colspan="18" class="text-center">No data found</td>
                     </tr>
                     @else
                     @foreach($leads as $key => $arr)
                     <tr style="font-size:15px;">
                        <td>{{$serial++}}</td>
                        <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;"><i class='mdi mdi-eye'></i>
                            </a>
                        </td>
                        <td>{{$arr->leadID}}</a></td>
                        <td>{{getUserNameById('lms_cities', 'cityID', $arr->branch, 'cityName')}}</td>
                        <td>{{getUserNameById('users','userID',$arr->creditedBy,'displayName')}}</td>
                        <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                        <td>{{$arr->email}}</td>
                        <td>{{$arr->mobile}}</td>
                        <td>{{$arr->pancard}}</td>
                        <td>{{$arr->employed}}</td>
                        <td>{{nf($arr->loanAmtApproved)}}</td>
                        <td>{{$arr->tenure}}</td>
                        <td>{{ $arr->roi ? $arr->roi.'%' : '--' }} </td>
                        <td>{{nf($arr->adminGstAmount)}}</td>
                        <td>{{nf($arr->monthlyIncome)}}</td>
                        <td>{{$arr->cibil}}</td>
                        <!-- DF(date-format) is a custom helper -->
                        <td>{{df($arr->addedOn)}}</td>
                     </tr>
                     @endforeach
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Lead ID</th>
                        <th>Branch</th>
                        <th>Sanction By</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Pancard</th>
                        <th>Employed</th>
                        <th>Loan Amount</th>
                        <th>Tenure</th>
                        <th>ROI</th>
                        <th>Admin Fee</th>
                        <th>Monthly Income</th>
                        <th>Cibil</th>
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