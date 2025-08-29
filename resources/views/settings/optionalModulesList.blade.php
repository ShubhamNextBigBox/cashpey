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
                        <div class="card-body">
                            <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Modules Name</th>
                                        <th>Status</th>
                                        <th>Updated By</th>
                                        <th>Updated On</th>
                                    </tr>
                                </thead>
                                <tbody>
                              @foreach($optionalModulesList as $key => $arr)
                                    <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{$arr->module}}</td>
                                        @if($arr->status=='1')
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->id}}" checked data-switch="success" data-id="{{$arr->id}}" value="0" class="status-switch">
                                                <label for="switch{{$arr->id}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @else
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->id}}" data-switch="success" data-id="{{$arr->id}}" value="1" class="status-switch">
                                                <label for="switch{{$arr->id}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @endif
                                        <td>{{getUserNameById('users','userID',$arr->updatedBy,'displayName')}}</td>
                                        <td>{{ $arr->updatedOn ? dft($arr->updatedOn) : 'N/A' }}</td>
                                        
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
                    @endsection


@section('custom-js')
                  
 <script type="text/javascript">
    
       $(document).ready(function() {
               
          $(document).on('change','.status-switch',function() {
                var status = $(this).val();
                var id = $(this).attr('data-id');
                $.ajax({
                   url: "{{ route('optionalModulesStatusUpdate') }}",
                   type: "POST",
                   dataType:'json',
                   data: {
                     _token: "{{ csrf_token() }}",
                     id:id,
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
    });
    

    
    
</script>

@endsection