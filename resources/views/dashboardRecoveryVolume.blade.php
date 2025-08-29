@extends('layouts.master')
@section('page-title',$page_info['page_title'])
@section('main-section')
 
<div class="content-page">
<div class="content">
<!-- Start Content-->
<div class="container-fluid">
   <div class="row">
      <div class="col-12">
         <div class="page-title-box">
            <div class="page-title-right">
               <form class="d-flex">
                  <a href="{{route('dashboard')}}" class="btn btn-primary ms-2">
                  <i class="mdi mdi-autorenew"></i>
                  </a>
                  <a href="javascript: void(0);" class="btn btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#dashboard-modal">
                  <i class="mdi mdi-filter-variant"></i>
                  </a>
               </form>
            </div>
            <h4 class="page-title">{{$page_info['page_name']}}</h4>
         </div>
      </div>
   </div>
   
   <div class="row">
      <div class="col-lg-12">
         <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
               <h4 class="header-title mb-3">Recovery 360º Sanction wise {{$filterShow}}</h4>
            </div>
            <div class="card-body pt-0">
               <div class="table-responsive">
                 <table class="table table-centered table-nowrap table-hover mb-0">
                <thead>
                    <tr class="bg-success">
                        <th class="text-white">Sanction Officer</th>
                        <th class="text-white">Due Cases</th>
                        <th class="text-white">Closed Cases</th>
                        <th class="text-white">Pending Cases</th>
                        <th class="text-white">Ratio %</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($recoverySanctionVolumeData) > 0)
                        @php
                            $totalDueCases = 0;
                            $totalClosedCases = 0;
                            $totalPendingCases = 0;
                            $totalRatio = 0;
                        @endphp
                        @foreach($recoverySanctionVolumeData as $data)
                            @php
                                // Aggregating the totals
                                $totalDueCases += $data->DueCases;
                                $totalClosedCases += $data->ClosedCases;
                                $totalPendingCases += $data->PendingCases;
                                $totalRatio += $data->Ratio;
                            @endphp
                            <tr>
                                <td class="table-user">
                                     <img src="{{ Storage::url($data->profile) }}" alt="table-user" class="me-2 rounded-circle" />
                                       {{ getUserNameById('users','userID',$data->creditedBy,'displayName') }}
                                </td>
                                <td>
                                    <span class="font-14">{{ number_format($data->DueCases) }}</span>
                                </td>
                                <td>
                                    <span class="font-14">{{ number_format($data->ClosedCases) }}</span>
                                </td>
                                <td>
                                    <span class="font-14">{{ number_format($data->PendingCases) }}</span>
                                </td>
                                <td>
                                    <span class="font-14">{{ number_format($data->Ratio, 2) }}%</span>
                                </td>
                            </tr>
                        @endforeach
            
                        <!-- Last row for totals -->
                        <tr style="font-weight: bold;">
                            <td class="text-left">Total</td>
                            <td>
                                <span class="font-14">{{ number_format($totalDueCases) }}</span>
                            </td>
                            <td>
                                <span class="font-14">{{ number_format($totalClosedCases) }}</span>
                            </td>
                            <td>
                                <span class="font-14">{{ number_format($totalPendingCases) }}</span>
                            </td>
                            <td>
                                <span class="font-14">{{ number_format($totalRatio / count($recoverySanctionVolumeData), 2) }}%</span>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5" class="text-center">No Records Found</td>
                        </tr>
                    @endif
                </tbody>
            </table>

               </div>
            </div>
         </div>
      </div>
      <div class="col-lg-12">
         <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
               <h4 class="header-title mb-3">Recovery 360º Branch wise {{$filterShow}}</h4>
            </div>
            <div class="card-body pt-0">
               <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
              <thead>
    <tr class="bg-success">
        <th class="text-white">Branch</th>
        <th class="text-white">Due Cases</th>
        <th class="text-white">Closed Cases</th>
        <th class="text-white">Pending Cases</th>
        <th class="text-white">Ratio %</th>
    </tr>
</thead>
<tbody>
    @if(count($recoveryBranchVolumeData) > 0)
        @php
            $totalDueCases = 0;
            $totalClosedCases = 0;
            $totalPendingCases = 0;
            $totalRatio = 0;
        @endphp
        @foreach($recoveryBranchVolumeData as $data)
            @php
                // Aggregating the totals
                $totalDueCases += $data->DueCases;
                $totalClosedCases += $data->ClosedCases;
                $totalPendingCases += $data->PendingCases;
                $totalRatio += $data->Ratio;
            @endphp
            <tr>
                <td class="table-user">
                   {{ getUserNameById('lms_cities','cityID',$data->branch,'cityName') }}
                </td>
                <td>
                    <span class="font-14">{{ number_format($data->DueCases) }}</span>
                </td>
                <td>
                    <span class="font-14">{{ number_format($data->ClosedCases) }}</span>
                </td>
                <td>
                    <span class="font-14">{{ number_format($data->PendingCases) }}</span>
                </td>
                <td>
                    <span class="font-14">{{ number_format($data->Ratio, 2) }}%</span>
                </td>
            </tr>
        @endforeach

        <!-- Last row for totals -->
        <tr style="font-weight: bold;">
            <td class="text-left">Total</td>
            <td>
                <span class="font-14">{{ number_format($totalDueCases) }}</span>
            </td>
            <td>
                <span class="font-14">{{ number_format($totalClosedCases) }}</span>
            </td>
            <td>
                <span class="font-14">{{ number_format($totalPendingCases) }}</span>
            </td>
            <td>
                <span class="font-14">{{ number_format($totalRatio / count($recoverySanctionVolumeData), 2) }}%</span>
            </td>
        </tr>
    @else
        <tr>
            <td colspan="5" class="text-center">No Records Found</td>
        </tr>
    @endif
</tbody>
</table>

               </div>
            </div>
         </div>
      </div>
      
   </div>
 
</div>
<!-- container -->
<div id="dashboard-modal" class="modal fade dashboard-update-modal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-right">
      <div class="modal-content align-item-start">
         <div class="modal-header border-0 text-center">
            <h4 class="modal-title" style="color:#0acf97; margin: 0 auto;" id="primary-header-modalLabel">Dashboard Filter</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1 mb-5" action="{{route('dashboard')}}" method="GET">
               <div class="row mb-5">
                
                  <div class="col-md-12">
                        <div class="mb-2">
                            <label for="reportType" class="form-label">Report Type</label>
                             <select class="form-select" name="reportType" id="reportType">
                               <option value="">Select Report Type</option>
                               @if (isSuperAdmin() || isAdmin())
                            <option value="Business Overview" {{ $reportType === 'Business Overview' ? 'selected' : '' }}>Business Overview</option>
                          <!--    <option value="Recovery Value" {{ $reportType === 'Recovery Value' ? 'selected' : '' }}>Recovery Value</option>
                            <option value="Recovery Volume" {{ $reportType === 'Recovery Volume' ? 'selected' : '' }}>Recovery Volume</option> -->
                             <option value="Sanction 360" {{ $reportType === 'Sanction 360' ? 'selected' : '' }}>Sanction 360<sup>°</sup></option>
                           @endif   
                           @if (role() === 'Sr. Credit Manager' || role() === 'Credit Manager')
                           <option value="Sanction 360" {{ $reportType === 'Sanction 360' ? 'selected' : '' }}>Sanction 360<sup>°</sup></option>
                           @endif
                           @if (role()=='Sr. Recovery Manager' || role()=='Recovery Manager' || role()=='Recovery Executive')
                            <!-- <option value="Recovery Value" {{ $reportType === 'Recovery Value' ? 'selected' : '' }}>Recovery Value</option>
                            <option value="Recovery Volume" {{ $reportType === 'Recovery Volume' ? 'selected' : '' }}>Recovery Volume</option> -->
                           @endif   
                            </select>
                            <span class="reportTypeErr"></span>
                        </div>
                    </div>

                  <div class="col-md-12">
                     <div class="mb-2">
                        <div class="input-group">
                           <span class="input-group-text">
                           <i class="uil uil-filter"></i>
                           </span>
                           <select class="form-select exportSelect" name="filter" id="filter">
                           <option value="sortByToday"  {{ $filter === 'sortByToday' ? 'selected' : '' }}>Sort by Today</option>
                           <option value="sortByWeek"  {{ $filter === 'sortByWeek' ? 'selected' : '' }}>Sort by Week</option>
                           <option value="sortByThisMonth"  {{ $filter === 'sortByThisMonth' || empty($filter) ? 'selected' : '' }}>Sort by This Month</option>
                           <option value="sortByLastMonth"  {{ $filter === 'sortByLastMonth' ? 'selected' : '' }}>Sort by Prev Month</option>
                           <option value="sortByDate" {{ $filter === 'sortByDate' ? 'selected' : '' }}>Sort by Date</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12" id="dateRange" style="display: none;">
                     <div class="mb-3">
                        <div class="input-group">
                           <span class="input-group-text">
                           <i class="uil uil-calender"></i>
                           </span>
                           <input type="text" class="form-control date" name="searchRange"  data-toggle="date-picker" data-cancel-class="btn-warning">
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12 mb-5">
                     <div class="mb-5 text-center">
                        <input type="submit" class="btn btn-primary form-control" value="Apply">
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
@endsection
@section('custom-js')
 <script>
       $(document).ready(function() {
        // Function to toggle date range visibility
        function toggleDateRange() {
            if ($('#filter').val() === 'sortByDate') {
                $('#dateRange').show();
            } else {
                $('#dateRange').hide();
            }
        }

        // Initial check on page load
        toggleDateRange();

        // Event listener for filter change
        $('#filter').change(function() {
            toggleDateRange();
        });
    });
 </script>
@endsection

