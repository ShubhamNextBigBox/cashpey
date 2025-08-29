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
                    <div class="card-header text-end">
                        <button type="button" class="btn btn-primary" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#branchAddEditModal" ><i class="uil uil-plus"></i> Add New</button>
                    </div>
                        <div class="card-body">
                            <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Branch Name</th>
                                        <!-- <th>Added By</th> -->
                                        <th>Added On</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                              @foreach($branch_list as $key => $arr)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$arr->branchName}}</td>
                                         {{-- Custom Helper for getting name from id check helper for more info--}}
                                         <!-- <td>{{getUserNameById('users','userID',$arr->addedBy,'displayName')}}</td> -->
                                         {{-- Custom Helper for getting name from id ends--}}
                                        <td>{{date('d, M Y',strtotime($arr->addedOn))}}</td>
                                        @if($arr->status=='1')
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->branchId}}" checked data-switch="success" data-branchId="{{$arr->branchId}}" value="0" class="status-switch">
                                                <label for="switch{{$arr->branchId}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @else
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->branchId}}" data-switch="success" data-branchId="{{$arr->branchId}}" value="1" class="status-switch">
                                                <label for="switch{{$arr->branchId}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @endif
                                        <td>
                                            <a href="" class="text-info branchEditBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="info-tooltip" data-bs-title="Edit" style="font-size: 18px;" data-branchId="{{$arr->branchId}}">
                                                <i class='mdi mdi-pencil'></i>
                                            </a> 
                                            <a href="" class="text-danger branchDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="danger-tooltip" data-bs-title="Delete" style="font-size: 18px;margin-left: 5px;" data-branchId="{{$arr->branchId}}">
                                                <i class='mdi mdi-delete'></i>
                                            </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div> <!-- end card-body -->
                        </div> <!-- end card-->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                    </div> <!-- container -->

    {{-- Modal for add branch  --}}
    <div id="branchAddEditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="primary-header-modalLabel">Branch Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                   <form method="post" id="branchAddEditModalForm">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Branch Name:</label>
                        <input type="text" class="form-control" name="branchName" id="branchName" placeholder="Enter branch name">
                        <span class="branchNameErr"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="branchId" name="branchId">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
    {{-- Modal for add branch ends --}}
                    @endsection


@section('custom-js')`
                  
 <script type="text/javascript">
    
       $(document).ready(function() {
       // getAllBranches();

        $(document).on('click','#addNewBtn',function(){
           $('#branchAddEditModalForm').trigger('reset');
        });
               
          $(document).on('change','.status-switch',function() {
                var status = $(this).val();
                var branchId = $(this).attr('data-branchId');
                $.ajax({
                   url: "{{ route('branchStatusUpdate') }}",
                   type: "POST",
                   dataType:'json',
                   data: {
                     _token: "{{ csrf_token() }}",
                     branchId:branchId,
                     status:status
                   },
                   success: function(data) {
                       if(data.response=='success'){
                         $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                        setTimeout(function(){window.location.reload();}, 1000);
                         //getAllBranches();
                       }else{
                         $.NotificationApp.send("Oh snap!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                       }
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });   

       

          $(document).on('submit','#branchAddEditModalForm',function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var branchId = $('#branchId').val();
                var url = (branchId !== '') ? '{{ route('branchUpdate') }}' : '{{ route('branchAdd') }}';
                $.ajax({
                   url: url,
                   type: "POST",
                   dataType:'json',
                   headers: {
                     'X-CSRF-TOKEN': csrfToken 
                   },
                   data: formData,
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                           $('.errClr').html('').hide();
                           $('#branchAddEditModalForm').trigger('reset');
                           $('#branchAddEditModal').modal('hide');
                           $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                          // getAllBranches();
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

      $(document).on('click','.branchEditBtn',function(event) {
                event.preventDefault();
                var branchId = $(this).attr('data-branchId');
                $.ajax({
                   url: "{{ route('branchEdit') }}",
                   type: "GET",      
                   data: {branchId:branchId},
                   success: function(data) {
                    
                    // Process the response data here
                        if(data.response=='success'){
                            $('#branchAddEditModal').modal('show');
                            $('#branchName').val(data.values[0].branchName);
                            $('#branchId').val(data.values[0].branchId);
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });
         

        $(document).on('click','.branchDeleteBtn',function(e){
            e.preventDefault();  
            $('#branchIdDelete').val($(this).attr('data-branchId'));
            $('#info-alert-modal').modal('show');
        });

        $(document).on('click','#confirmYes',function(){
           var branchId = $('#branchIdDelete').val();
                $.ajax({
                   url: "{{ route('branchDelete') }}",
                   type: "GET",      
                   data: {branchId:branchId},
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                            $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                          //  getAllBranches();
                            setTimeout(function(){window.location.reload();}, 1000);
                            $('#info-alert-modal').modal('hide');
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
            });
        });
            
       
       // function getAllBranches(){
       //      $.ajax({
       //             url: "{{ route('getAllBranches') }}",
       //             type: "GET", 
       //              dataType: "json", 
       //             success: function(data) {
                 
       //                   if ($.fn.DataTable.isDataTable('#basic-datatable')) {
       //                      $('#basic-datatable').DataTable().destroy();
       //                      $('#basic-datatable tbody').empty();
       //                  }
       //                    // Populate DataTable with received data
       //                  $.each(data, function(index, item) {

       //                      var statusCheckbox = '';
       //                    var formattedAddedOn = new Date(item.addedOn).getDate() + ', ' + new Date(item.addedOn).toLocaleString('default', { month: 'short' }) + ' ' + new Date(item.addedOn).getFullYear();

       //                      // Add condition to decide checkbox status
       //                      if (item.status=='1') {
       //                          statusCheckbox = '<div><input type="checkbox" id="switch'+item.branchId+'" checked data-switch="success" data-branchId="'+item.branchId+'" value="0" class="status-switch"><label for="switch'+item.branchId+'" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label></div>';
       //                      } else {
       //                          statusCheckbox = '<div><input type="checkbox" id="switch'+item.branchId+'" data-switch="success" data-branchId="'+item.branchId+'" value="1" class="status-switch"><label for="switch'+item.branchId+'" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label></div>';
       //                      }
       //                      var newRow = '<tr>' +
       //                          '<td>' + (index + 1) + '</td>' +
       //                          '<td>' + item.branchName + '</td>' +
       //                          '<td>' + item.addedBy + '</td>' +
       //                          '<td>' + formattedAddedOn + '</td>' +
       //                          '<td>' + statusCheckbox +'</td>' +
       //                          '<td><a href="javascript:void(0)" class="text-info branchEditBtn" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="info-tooltip" data-bs-title="Edit Branch" style="font-size: 18px;" data-branchId="'+item.branchId+'"> <i class="mdi mdi-pencil"></i></a> <a href="javascript:void(0)" class="text-danger branchDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="danger-tooltip" data-bs-title="Delete Branch" style="font-size: 18px;margin-left: 5px;" data-branchId="'+item.branchId+'"><i class="mdi mdi-delete"></i></a></td></tr>';
       //                      $('#basic-datatable tbody').append(newRow);
       //                  });

       //                  // Initialize or redraw DataTable after data is updated
       //                  $('#basic-datatable').DataTable({
       //                      "paging": true, // Enable pagination
       //                      "lengthChange": true, // Enable length change
       //                      "searching": true, // Enable search
       //                      "ordering": true, // Enable ordering
       //                      "info": true, // Enable info
       //                      "autoWidth": false, // Disable auto width
       //                      "responsive": true, // Enable responsiveness
       //                      // Add any other configurations as needed
       //                  });
       //                }      
                   
       //      });
       //  }    
 
         function printErrorMsg(msg){
            $('.errClr').remove();
            $.each(msg,function(key,value){
                $('.'+key+'Err').html('<div class="alert alert-danger errClr mt-1" role="alert"><i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>'+value+'</strong></div>');
            });
        }
       
    });
    

    
    
</script>

@endsection