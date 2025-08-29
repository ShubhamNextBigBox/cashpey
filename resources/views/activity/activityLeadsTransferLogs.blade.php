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
            <div class="card-body actlog">
               <table class="table dt-responsive nowrap w-100 branchTable">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>LeadID</th>
                        <th>Assigned RM</th>
                        <th>Assigned CM</th>
                        <th>Transfer RM</th>
                        <th>Transfer CM</th>
                        <th>Transfer By</th>
                        <th>Transfer Date Time</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $serial = ($activityLeadsTransferLogs->currentPage() - 1) * $activityLeadsTransferLogs->perPage() + 1;
                     @endphp
                     @foreach($activityLeadsTransferLogs as $key => $arr)
                     @php
                     $collapseId = 'collapseExample' . $key;
                     $logId = 'log' . $key;
                     @endphp
                     <tr>
                        <td>{{ $serial++ }}</td>
                        <td><a target="_blank" href="profile/{{strtolower($arr->loanTypeName)}}/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                           data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;"><i class='mdi mdi-eye'></i>
                           </a>
                        </td>
                        <td>{{ getUserNameById('users', 'userID', $arr->assignedRM, 'displayName') }}</td>
                        <td>{{ getUserNameById('users', 'userID', $arr->assignedCM, 'displayName') }}</td>
                        <td>{{ getUserNameById('users', 'userID', $arr->transferRM, 'displayName') }}</td>
                        <td>{{ getUserNameById('users', 'userID', $arr->transferCM, 'displayName') }}</td>
                        <td>{{ getUserNameById('users', 'userID', $arr->transferBy, 'displayName') }}</td>
                        <td>{{ dft($arr->addedOn) }}</td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
               <div class="row">
                  {{ $activityLeadsTransferLogs->links('pagination::bootstrap-5') }}
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