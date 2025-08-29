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
         <ul class="nav nav-tabs mb-1">
            <li class="nav-item">
               <a href="reporting/list/disbursed" class="nav-link @if ($activeTab == 'disbursed') active @endif">
               <i class="mdi mdi-home-variant d-md-none d-block"></i>
               <span class="d-none d-md-block">Disbursed</span>
               </a>
            </li>
            <li class="nav-item">
               <a href="reporting/list/collection" class="nav-link @if ($activeTab == 'collection') active @endif">
               <i class="mdi mdi-home-variant d-md-none d-block"></i>
               <span class="d-none d-md-block">Collection</span>
               </a>
            </li>
         </ul>
      <div class="tab-content">
         <div class="card">
            <div class="card-header">
               <div class="row">
                  <div class="col-md-12">
                     <form action="{{ route('reporting.filter', ['reportingType' => $activeTab]) }}" method="get">
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
                                 @if(rolesAccess('All Reporting Data', 'export') || isSuperAdmin() || isAdmin())
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
                        @if(rolesAccess('All Reporting Data', 'export') || isSuperAdmin() || isAdmin())
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
                              <a href="reporting/list/{{$activeTab}}" class="btn btn-outline-primary" data-toggle="tooltip" data-original-title="success">
                                 <i class="uil uil-refresh"></i>
                              </a>
                           </div>
                           @endif
                        </div>
                     </form>
                  </div>
               </div>
            </div>
           @if($activeTab == 'disbursed') 
            <div class="tab-pane show active" id="disbursed">
                  <div class="card-body" style="overflow-x:auto;">
                        <div class="table-responsive"  data-simplebar data-simplebar-lg>
                   <table  class="table w-100 table-striped" style="white-space: nowrap;">
                     <thead>
                     <tr style="font-size:14px;">
                        <th>#</th>
                        <th>Action</th>
                        <th>LeadID</th>
                        <th>Loan No</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Credit By</th>
                        <th>PD By</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Pancard</th>
                        <th>Aadhar No</th>
                        <th>Employed</th>
                        <th>Monthly Income</th>
                        <th>Monthly Obligation</th>
                        <th>Loan Amount</th>
                        <th>EMI Amount</th>
                        <th>Tenure</th>
                        <th>ROI</th>
                        <th>AccountNo</th>
                        <th>Bank IFSC</th>
                        <th>Bank</th>
                        <th>Bank Branch</th>
                        <th>Enach Details</th>
                        <th>Disbursal Reference No</th>
                        <th>Disbursed By Bank</th>
                        <th>Disbursal Date</th>
                        <th>Admin Fee</th>
                        <th>Cibil</th>
                        <th>GSTOfAdminFee</th>
                        <th>UTM Source</th>
                        <th>State</th>
                        <th>Red Flag</th>
                        <th>Status</th>
                        <th>Lead Coming Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                     @endphp
                     @if ($leads->isEmpty())
                     <tr>
                        <td colspan="40" class="text-center">No data found</td>
                     </tr>
                     @else
                     @foreach($leads as $key => $arr)
                     <tr style="font-size:15px;">
                        <td>{{ $serial++ }}</td>
 <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                                    <i class='mdi mdi-eye'></i>
                                 </a>
                              </td>
                                                <td>{{ $arr->leadID }}</td>
                        <td>{{ $arr->loanNo }}</td>
                        <td>{{ getUserNameById('lms_cities', 'cityID', $arr->branch, 'cityName')}}</td>
                        <td><a href="profile/{{ $arr->leadID }}" class="text-primary">{{ ucwords($arr->name) }}</a></td>
                        <td>{{ getUserNameById('users','userID',$arr->creditedBy ,'displayName')}}</td>
                        <td>{{ getUserNameById('users','userID',$arr->pdVerifiedBy ,'displayName')}}</td>
                        <td>{{ $arr->gender }}</td>
                        <td>{{ df($arr->dob) }}</td>
                        <td>{{ $arr->email }}</td>
                        <td>{{ $arr->mobile }}</td>
                        <td>{{ $arr->pancard }}</td>
                        <td>{{ $arr->aadharNo }}</td>
                        <td>{{ $arr->employed }}</td>
                        <td>{{ nf($arr->monthlyIncome)}}</td>
                        <td>{{ nf($arr->monthlyObligation) }}</td>
                        <td>{{ nf($arr->loanAmtApproved) }}</td>
                        <td>{{ nf($arr->emi) }}</td>
                        <td>{{ $arr->tenure }}</td>
                        <td>{{ $arr->roi }} %</td>
                        <td>{{ $arr->accountNo }}</td>
                        <td>{{ $arr->ifscCode }}</td>
                        <td>{{ $arr->bank }}</td>
                        <td>{{ $arr->bankBranch }}</td>
                        <td>{{ $arr->enachID }}</td>
                        <td>{{ $arr->disbursalUtrNo }}</td>
                        <td>{{ $arr->bank }}</td>
                        <td>{{ df($arr->disbursalDate) }} {{$arr->disburseTime}}</td>
                        <td>{{ nf($arr->adminFee) }}</td>
                        <td>{{ $arr->cibil }}</td>
                        <td>{{ nf($arr->adminGstAmount)}}</td>
                        <td>{{ $arr->utmSource ?? '-' }}</td>
                        <td>{{ getUserNameById('lms_cities', 'cityID', $arr->branch, 'cityName')}}</td>
                        <td>{{ $arr->redFlag == 0 ? 'No' : 'Yes' }}</td>
                        <td><span class="badge bg-light text-dark">{{$arr->status}}</span></td>
                        <td>{{ df($arr->commingLeadsDate)}}</td>
                        
                     </tr>
                     @endforeach
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>LeadID</th>
                        <th>Loan No</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Credit By</th>
                        <th>PD By</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Pancard</th>
                        <th>Aadhar No</th>
                        <th>Employed</th>
                        <th>Monthly Income</th>
                        <th>Monthly Obligation</th>
                        <th>Loan Amount</th>
                        <th>EMI Amount</th>
                        <th>Tenure</th>
                        <th>ROI</th>
                        <th>AccountNo</th>
                        <th>Bank IFSC</th>
                        <th>Bank</th>
                        <th>Bank Branch</th>
                        <th>Enach Details</th>
                        <th>Disbursal Reference No</th>
                        <th>Disbursed By Bank</th>
                        <th>Disbursal Date</th>
                        <th>Admin Fee</th>
                        <th>Cibil</th>
                        <th>GSTOfAdminFee</th>
                        <th>UTM Source</th>
                        <th>State</th>
                        <th>Red Flag</th>
                        <th>Status</th>
                        <th>Lead Coming Date</th>
                     </tr>
                  </tfoot>
                  </table>
                     <div class="row">
                        {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
                     </div>
                  </div>
                  </div>
           @endif
            @if($activeTab == 'collection')   
            <div class="tab-pane show active" id="collection">
                  <div class="card-body" style="overflow-x:auto;">
                      <div class="table-responsive"  data-simplebar data-simplebar-lg>
                   <table  class="table w-100 table-striped" style="white-space: nowrap;">
                  <thead>
                     <tr style="font-size:14px;">
                        <th>#</th>
                        <th>Action</th>
                        <th>LeadID</th>
                        <th>Loan No</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Pancard</th>
                        <th>Loan Amount</th>
                        <th>Collected Amount</th>
                        <th>Collected Mode</th>
                        <th>Reference No</th>
                        <th>Remark</th>
                        <th>Collection Source</th>
                        <th>Status</th>
                        <th>Collected Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($leads->currentPage() - 1) * $leads->perPage() + 1;
                     @endphp
                     @if ($leads->isEmpty())
                     <tr>
                        <td colspan="22" class="text-center">No data found</td>
                     </tr>
                     @else
                     @foreach($leads as $key => $arr)
                     <tr style="font-size:15px;">
                          <td>{{ $serial++ }}</td>
                            <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;">
                                    <i class='mdi mdi-eye'></i>
                                 </a>
                              </td>
                          <td>{{ $arr->leadID }}</td>
                          <td>{{ $arr->loanNo }}</td>
                          <td>{{ getUserNameById('lms_cities', 'cityID', $arr->branch, 'cityName')}}</td>
                          <td><a href="profile/collection/{{ $arr->leadID }}" class="text-primary">{{ ucwords($arr->name) }}</a></td>
                          <td>{{ $arr->email }}</td>
                          <td>{{ $arr->mobile }}</td>
                          <td>{{ $arr->pancard }}</td>
                          <td>{{ nf($arr->loanAmtApproved) }}</td>
                          <td>{{ nf($arr->collectedAmount) }}</td>
                          <td>{{ $arr->collectedMode }}</td>
                          <td>{{ $arr->collectionUtrNo }}</td>
                          <td>{{ $arr->remark }}</td>
                          <td>{{ $arr->collectionSource }}</td>
                          <td><span class="badge bg-light text-dark">{{$arr->status}}</span></td>
                          <td>{{ dft($arr->collectedDate) }}</td>
                     </tr>
                     @endforeach
                     @endif
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                       <th>Action</th>
                        <th>LeadID</th>
                        <th>Loan No</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Pancard</th>
                        <th>Loan Amount</th>
                        <th>Collected Amount</th>
                        <th>Collected Mode</th>
                        <th>Reference No</th>
                        <th>Remark</th>
                        <th>Collection Source</th>
                        <th>Status</th>
                        <th>Collected Date</th>
                     </tr>
                  </tfoot>
               </table>
               <div class="row">
                  {{ $leads->appends($queryParameters)->links('pagination::bootstrap-5') }}
               </div>
                  </div>
                  </div>
               </div>
            </div>   
           @endif   
         </div>
         <!-- end card-->
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
        } else if (selectedOption === 'sortByToday' || selectedOption === 'sortByWeek' || selectedOption === 'sortByThisMonth' || selectedOption === 'sortByLastMonth') {
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