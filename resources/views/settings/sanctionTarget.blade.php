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
                        <button type="button" class="btn btn-primary" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#designationsAddEditModal" ><i class="uil uil-plus"></i> Add New</button>
                    </div>
                        <div class="card-body">
                            <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Target</th>
                                        <!-- <th>Added By</th> -->
                                        <th>Added On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                              @foreach($sanction_target as $key => $arr)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{getUserNameById('users','userID',$arr->userID,'displayName')}}</td> 
                                        <td>{{$arr->target}}</td>
                                        <td>{{dft($arr->addedOn)}}</td>
                                        <td>
                                            <a href="" class="text-info sanctionTargetEditBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="info-tooltip" data-bs-title="Edit" style="font-size: 18px;" data-targetID="{{$arr->targetID}}">
                                                <i class='mdi mdi-pencil'></i>
                                            </a> 
                                         @if(isSuperAdmin())        
                                            <a href="" class="text-danger sanctionTargetDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="danger-tooltip" data-bs-title="Delete" style="font-size: 18px;margin-left: 5px;" data-targetID="{{$arr->targetID}}">
                                                <i class='mdi mdi-delete'></i>
                                            </a>
                                        @endif    
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

            {{-- Modal for add modules  --}}
            <div id="designationsAddEditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h4 class="modal-title" id="primary-header-modalLabel">Sanction Target Details</h4>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                           <form method="post" id="designationsAddEditModalForm">
                            @csrf
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Employee:</label>
                                 <select class="form-control" name="userId" id="userID">
                                    <option value="">Select Employee</option>
                                    @foreach($users as $arr)
                                     <option value="{{$arr->userID}}">{{$arr->displayName}}</option>
                                    @endforeach
                                </select>
                                <span class="userIdErr"></span>
                            </div>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Sanction Target:</label>
                                <input type="text" class="form-control" name="target" id="target" placeholder="Enter sanction target">
                                <span class="targetErr"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" id="targetID" name="targetID">
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
                    <input type="hidden" id="targetIDDelete">
                    <button type="button" class="btn btn-success my-2" id="confirmYes">Yes</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
    {{-- Modal for add modules ends --}}
                    @endsection


@section('custom-js')
                  
 <script type="text/javascript">
    
       $(document).ready(function() {

        $(document).on('click','#addNewBtn',function(){
           $('#designationsAddEditModalForm').trigger('reset');
        });
               
          

          $(document).on('submit','#designationsAddEditModalForm',function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var targetID = $('#targetID').val();
 
                var url = (targetID !== '') ? '{{ route('sanctionTargetUpdate') }}' : '{{ route('sanctionTargetAdd') }}';
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
                           $('#designationsAddEditModalForm').trigger('reset');
                           $('#designationsAddEditModal').modal('hide');
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

      $(document).on('click','.sanctionTargetEditBtn',function(event) {
                event.preventDefault();
                var targetID = $(this).attr('data-targetID');

                $.ajax({
                   url: "{{ route('sanctionTargetEdit') }}",
                   type: "GET",      
                   data: {targetID:targetID},
                   success: function(data) {
                    
                    // Process the response data here
                        if(data.response=='success'){
                            $('#designationsAddEditModal').modal('show');
                            $('#userID').val(data.values[0].userID);
                            $('#targetID').val(data.values[0].targetID);
                            $('#target').val(data.values[0].target);
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });
         

        $(document).on('click','.sanctionTargetDeleteBtn',function(e){
            e.preventDefault();  
            $('#targetIDDelete').val($(this).attr('data-targetID'));
            $('#info-alert-modal').modal('show');
        });

        $(document).on('click','#confirmYes',function(){
           var targetID = $('#targetIDDelete').val();
                $.ajax({
                   url: "{{ route('sanctionTargetDelete') }}",
                   type: "GET",      
                   data: {targetID:targetID},
                   success: function(data) {
                    // Process the response data here
                        if(data.response=='success'){
                            $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                            setTimeout(function(){window.location.reload();}, 1000);
                            $('#info-alert-modal').modal('hide');
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
                $('.'+key+'Err').html('<div class="alert alert-danger errClr mt-1" role="alert"><i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>'+value+'</strong></div>');
            });
        }
       
    });
    

    
    
</script>

@endsection