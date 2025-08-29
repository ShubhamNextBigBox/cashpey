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
            <div class="card-body" style="overflow-x:auto;">
               <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                  <thead>
                     <tr style="font-size:14px;">
                        <th>#</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Customer Name</th>
                        <th>Loan Amount</th>
                        <th>Requested By</th>
                        <th>Date</th>
                     </tr>
                  </thead>
                  <tbody>
                     
                     @foreach($leads as $key => $arr)
                     <tr style="font-size:15px;">
                        <td>{{++$key}}</td>
                        <td><a target="_blank" href="profile/{{$arr->leadID}}" class="text-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="primary-tooltip" data-bs-title="Profile View" style="font-size: 16px;margin-left: 6px;"><i class='mdi mdi-eye'></i>
                            </a>
                             <a href="#" class="text-info open-modal" data-lead-id="{{$arr->leadID}}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="info-tooltip" data-bs-title="Lead Edit" style="font-size: 16px;margin-left: 6px;">
                               <i class='mdi mdi-square-edit-outline'></i>
                           </a>
                        </td>
                        <td><span class="badge bg-danger">{{$arr->creditStatus}}</span></span></td>
                        <td><a href="profile/{{$arr->leadID}}" class="text-primary">{{ucwords($arr->name)}}</a></td>
                        <td>{{$arr->loanTypeName}}</td>
                        <td>{{nf($arr->loanAmtApproved)}}</td>
                        <td>{{getUserNameById('users', 'userID',$arr->addedBy, 'displayName')}}</td>
                        <td>{{df($arr->addedOn)}}</td>
                     </tr>
                     @endforeach
                   
                  </tbody>
                  <tfoot>
                     <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Customer Name</th>
                        <th>Loan Amount</th>
                        <th>Requested By</th>
                        <th>Date</th>
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
<!-- /.modal -->
 
<div id="success-header-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="success-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title text-white" id="success-header-modalLabel">Approval Details</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form class="ps-1 pe-1" id="leadAddEditModalForm" action="#">
               @csrf
               <div class="row">
                
                  <div class="col-md-12">
                     <div class="mb-2">
                        <label for="state" class="form-label">Credit Status</label>
                        <select class="form-select" name="creditStatus" id="creditStatus">
                           <option value="">Select Credit Status</option>
                           <option value="Approved">Approved</option>
                           <option value="Rejected">Rejected</option>
                           <option value="Pending For Approval">Pending For Approval</option>
                        </select>
                        <span class="creditStatusErr"></span>
                     </div>
                  </div>
                   <div class="col-md-12">
                     <div class="mb-2">
                        <label for="state" class="form-label">Remarks</label>
                        <textarea class="form-control" name="approvalRemarks" id="approvalRemarks" rows="2"></textarea>
                        <span class="approvalRemarksErr"></span>
                     </div>
                  </div>
               </div>
         
            </div>
            <div class="modal-footer">
                <input type="hidden" name="leadID" id="leadID" value="">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save changes</button>
            </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 
@endsection

@section('custom-js')

<script type="text/javascript">

    

      
    
 
        $('.open-modal').click(function(event) {
            event.preventDefault(); // Prevent default link behavior
            
            var leadID = $(this).data('lead-id'); // Get the leadID from data attribute

            // Make AJAX request to fetch data using leadID
            $.ajax({
                url: "{{ route('pendingApprovalEdit') }}", // Replace with your route URL
                type: 'GET',
                data: { leadID: leadID },
                success: function(response) {
                    // Populate modal with fetched data
                    $('#creditStatus').val(response.creditStatus);
                    $('#approvalRemarks').val(response.approvalRemarks);
                    $('#leadID').val(response.leadID);
                },
            });
             $('#success-header-modal').modal('show');
        });
   
 

   $(document).on('submit','#leadAddEditModalForm',function(event) {
        event.preventDefault();
        
        var formData = $(this).serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var leadID = $('#leadID').val();
       
        $.ajax({
           url: "{{ route('pendingApprovalUpdate') }}",
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
    });
      

       function printErrorMsg(msg){
            $('.errClr').remove();
            $.each(msg,function(key,value){
                $('.'+key+'Err').html('<div class="text-danger errClr mt-1" style="font-size:12px;"><i class="ri-close-circle-line me-1 align-left font-12"></i><strong>'+value+'</strong></div>');
            });
        } 
   
</script>
@endsection