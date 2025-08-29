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
                        <button type="button" class="btn btn-primary" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#modulesAddEditModal" ><i class="uil uil-plus"></i> Add New</button>
                    </div>
                        <div class="card-body">
                            <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Modules Name</th>
                                        <th>Modules Parent</th>
                                        <!-- <th>Added By</th> -->
                                        <th>Added On</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                              @foreach($modulesData as $key => $arr)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$arr->modulesName}}</td>
                                         {{-- Custom Helper for getting name from id check helper for more info--}}
                                        <td>
                                            @if(!empty($arr->modulesParentId))
                                             {{getUserNameById('lms_modules','modulesId',$arr->modulesParentId,'modulesName')}}
                                            @else
                                                {{'N/A'}}
                                            @endif
                                            </td>
                                         {{-- Custom Helper for getting name from id ends--}}
                                        <!-- <td>{{getUserNameById('users','userID',$arr->addedBy,'displayName')}}</td> -->
                                        <td>{{dft($arr->addedOn)}}</td>
                                        @if($arr->status=='1')
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->modulesId}}" checked data-switch="success" data-modulesId="{{$arr->modulesId}}" value="0" class="status-switch">
                                                <label for="switch{{$arr->modulesId}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @else
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->modulesId}}" data-switch="success" data-modulesId="{{$arr->modulesId}}" value="1" class="status-switch">
                                                <label for="switch{{$arr->modulesId}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @endif
                                        <td>
                                            <a href="" class="text-info modulesEditBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="info-tooltip" data-bs-title="Edit" style="font-size: 18px;" data-modulesId="{{$arr->modulesId}}">
                                                <i class='mdi mdi-pencil'></i>
                                            </a> 
                                         @if(isSuperAdmin())        
                                            <a href="" class="text-danger moduleDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="danger-tooltip" data-bs-title="Delete" style="font-size: 18px;margin-left: 5px;" data-modulesId="{{$arr->modulesId}}">
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
    <div id="modulesAddEditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="primary-header-modalLabel">Modules Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                   <form method="post" id="moduleAddEditModalForm">
                    @csrf
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Modules Parent:</label>
                         <select class="form-control" name="moduleParent" id="moduleParent">
                            <option value="">Select Modules Parent</option>
                            @foreach($modulesData as $arr)
                             <option value="{{$arr->modulesId}}">{{$arr->modulesName}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Modules Name:</label>
                        <input type="text" class="form-control" name="modulesName" id="modulesName" placeholder="Enter module name">
                        <span class="modulesNameErr"></span>
                    </div>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Modules URL:</label>
                        <input type="text" class="form-control" name="modulesUrl" id="modulesUrl" placeholder="Enter module url">
                        <span class="modulesUrlErr"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="modulesId" name="modulesId">
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
                    <input type="hidden" id="modulesIdDelete">
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
        function slugToText(slug) {
            return slug.replace(/-/g, ' ');
        }
        $(document).on('click','#addNewBtn',function(){
           $('#moduleAddEditModalForm').trigger('reset');
        });
               
          $(document).on('change','.status-switch',function() {
                var status = $(this).val();
                var modulesId = $(this).attr('data-modulesId');
                $.ajax({
                   url: "{{ route('modulesStatusUpdate') }}",
                   type: "POST",
                   dataType:'json',
                   data: {
                     _token: "{{ csrf_token() }}",
                     modulesId:modulesId,
                     status:status
                   },
                   success: function(data) {
                       if(data.response=='success'){
                         $.NotificationApp.send("Well Done!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "success"); 
                        setTimeout(function(){window.location.reload();}, 1000);
                       }else{
                         $.NotificationApp.send("Oh snap!",data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                       }
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });   


          $(document).on('submit','#moduleAddEditModalForm',function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var modulesId = $('#modulesId').val();
                var url = (modulesId !== '') ? '{{ route('modulesUpdate') }}' : '{{ route('modulesAdd') }}';
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
                           $('#moduleAddEditModalForm').trigger('reset');
                           $('#modulesAddEditModal').modal('hide');
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

      $(document).on('click','.modulesEditBtn',function(event) {
                event.preventDefault();
                var modulesId = $(this).attr('data-modulesId');
                $.ajax({
                   url: "{{ route('modulesEdit') }}",
                   type: "GET",      
                   data: {modulesId:modulesId},
                   success: function(data) {
                    
                    // Process the response data here
                        if(data.response=='success'){
                            $('#modulesAddEditModal').modal('show');
                            $('#modulesName').val(data.values[0].modulesName);
                            $('#modulesId').val(data.values[0].modulesId);
                            $('#moduleParent').val(data.values[0].modulesParentId);
                            $('#modulesUrl').val(data.values[0].modulesUrl);
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });
         

        $(document).on('click','.moduleDeleteBtn',function(e){
            e.preventDefault();  
            $('#modulesIdDelete').val($(this).attr('data-modulesId'));
            $('#info-alert-modal').modal('show');
        });

        $(document).on('click','#confirmYes',function(){
           var modulesId = $('#modulesIdDelete').val();
                $.ajax({
                   url: "{{ route('modulesDelete') }}",
                   type: "GET",      
                   data: {modulesId:modulesId},
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