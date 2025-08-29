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
                        <button type="button" class="btn btn-primary" id="addNewBtn" data-bs-toggle="modal" data-bs-target="#approvalMatrixAddEditModal" ><i class="uil uil-plus"></i> Add New</button>
                    </div>
                        <div class="card-body">
                            <table id="basic-datatable" class="table dt-responsive nowrap w-100 branchTable table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                        <th>Employees</th>
                                        <th>Range</th>
                                        <th>Added On</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($approvalMatrixList as $key => $arr)
                                     <tr>
                                        <td>{{++$key}}</td>
                                        <td>{{getUserNameById('lms_departments','departmentsId',$arr->department,'departmentsName')}}
                                        <td>{{getUserNameById('lms_designations','designationsId',$arr->designation,'designationsName')}}</td>
                                         <td>  
                                            @php $dbUsers = json_decode($arr->users); @endphp
                                            @foreach ($dbUsers as $index => $user)
                                                {{ getUserNameById('users', 'userID', $user, 'displayName') }}
                                                @if ($index < count($dbUsers) - 1)
                                                    ,
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{nf($arr->rangeFrom).' - '.nf($arr->rangeTo)}}</td>    
                                        <td>{{dft($arr->addedOn)}}</td>
                                        @if($arr->status=='1')
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->approvalID}}" checked data-switch="success" data-approvalID="{{$arr->approvalID}}" value="0" class="status-switch">
                                                <label for="switch{{$arr->approvalID}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @else
                                        <td>
                                            <div>
                                                <input type="checkbox" id="switch{{$arr->approvalID}}" data-switch="success" data-approvalID="{{$arr->approvalID}}" value="1" class="status-switch">
                                                <label for="switch{{$arr->approvalID}}" data-on-label="Yes" data-off-label="No" class="mb-0 d-block"></label>
                                            </div>
                                        </td>
                                        @endif
                                        <td>
                                            <a href="" class="text-info approvalIDEditBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="info-tooltip" data-bs-title="Edit" style="font-size: 18px;" data-approvalID="{{$arr->approvalID}}">
                                                <i class='mdi mdi-pencil'></i>
                                            </a> 
                                           @if(isSuperAdmin())    
                                            <a href="" class="text-danger approvalDeleteBtn" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="danger-tooltip" data-bs-title="Delete" style="font-size: 18px;margin-left: 5px;" data-approvalID="{{$arr->approvalID}}">
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
    <div id="approvalMatrixAddEditModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title" id="primary-header-modalLabel">Approval Matrix Details</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                   <form method="post" id="approvalMatrixAddEditModalForm">
                    @csrf
                    <div class="row">
                    <div class="mb-2">
                       <label class="form-label">Department</label>
                       <select class="form-control" id="department" name="department">
                          <option value="">Select Department</option>
                          @foreach($departmentsData as $arr)
                          <option value="{{$arr->departmentsId}}">{{$arr->departmentsName}}</option>
                          @endforeach
                       </select>
                       <span class="departmentErr"></span>
                    </div>
                     <div class="mb-2">
                       <label class="form-label">Designations</label>
                       <select class="form-control" name="designation" id="designationsReplace">
                          <option value="">Select Designations</option> 
                          @foreach($designationsData as $arr)
                          <option value="{{$arr->designationsId}}">{{$arr->designationsName }}</option>
                          @endforeach
                       </select>
                       <span class="designationErr"></span>
                    </div>
                    <div class="mb-2">
                       <label class="form-label">Employees</label>
                       <select class="form-control select2-multiple" data-toggle="select2" multiple="multiple" name="users[]" id="usersReplace" placeholder="Select Employees" >
                         @foreach($users as $arr)
                          <option value="{{$arr->userID}}">{{$arr->displayName }}</option>
                          @endforeach
                       </select>
                       <span class="usersErr"></span>
                    </div>

                    <div class="mb-2">
                        <div class="input-group p-2">
                            <label for="loanAmount" class="scroll-bar-label">
                                Loan Range: <span id="minAmountDisplay">₹40,000</span> - <span id="maxAmountDisplay">₹200,000</span>
                            </label>
                            <div class="range-slider">
                                <div class="range-slider-track"></div>
                                <div class="range-slider-fill" id="rangeFill"></div>
                                <input type="range" id="minLoanAmount" name="minLoanAmount" min="50000" max="1000000" value="50000" step="1000">
                                <input type="range" id="maxLoanAmount" name="maxLoanAmount" min="50000" max="1000000" value="100000" step="1000">
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="approvalID" name="approvalID">
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
                    <input type="hidden" id="approvalIdDelete">
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
           $('#approvalMatrixAddEditModalForm').trigger('reset');
        });
               

       $(document).ready(function() {
            const $minLoanAmount = $('#minLoanAmount');
            const $maxLoanAmount = $('#maxLoanAmount');
            const $minAmountDisplay = $('#minAmountDisplay');
            const $maxAmountDisplay = $('#maxAmountDisplay');
            const $rangeFill = $('#rangeFill');

            // Update display for loan amount sliders
            function updateRange() {
                let minVal = parseInt($minLoanAmount.val());
                let maxVal = parseInt($maxLoanAmount.val());

                // Prevent the minimum handle from crossing the maximum handle
                if (minVal > maxVal) {
                    $minLoanAmount.val(maxVal);
                    minVal = maxVal;
                }

                // Prevent the maximum handle from crossing the minimum handle
                if (maxVal < minVal) {
                    $maxLoanAmount.val(minVal);
                    maxVal = minVal;
                }

                $minAmountDisplay.text(`₹${minVal.toLocaleString()}`);
                $maxAmountDisplay.text(`₹${maxVal.toLocaleString()}`);

                // Calculate the percentage for the filled color
                const min = parseInt($minLoanAmount.attr('min'));
                const max = parseInt($maxLoanAmount.attr('max'));

                const fillLeft = ((minVal - min) / (max - min)) * 100;
                const fillRight = ((maxVal - min) / (max - min)) * 100;

                $rangeFill.css({
                    left: fillLeft + '%',
                    width: (fillRight - fillLeft) + '%'
                });
            }

            // Bind updateRange to input events
            $minLoanAmount.on('input', updateRange);
            $maxLoanAmount.on('input', updateRange);

            // Initialize values
            updateRange();
        });        

         $('#department').change(function() {
             var department = $(this).val();
             $.ajax({
                 url: '{{ route("getDesignations", ":department") }}'.replace(':department', department),
                 type: 'GET',
                 success: function(response) {
                      $('#designationsReplace').empty();
                      $('#designationsReplace').append('<option value="">Select Designations</option>');
                      $.each(response, function(key, value) {
                         $('#designationsReplace').append('<option value="' + key + '">' + value + '</option>');
                     });
                 }
             });
         }); 

          $('#designationsReplace').change(function() {

             var designations = $(this).val();
             $.ajax({
                 url: '{{ route("getUsersByDesignation", ":designations") }}'.replace(':designations', designations),
                 type: 'GET',
                 success: function(response) {
                      $('#usersReplace').empty();
                     // $('#usersReplace').append('<option value="">Select Employees</option>');
                      $.each(response, function(key, value) {
                         $('#usersReplace').append('<option value="' + key + '">' + value + '</option>');
                     });
                 }
             });
         }); 
         
 
          $(document).on('submit','#approvalMatrixAddEditModalForm',function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var approvalID = $('#approvalID').val();
                var url = (approvalID !== '') ? '{{ route('approvalMatrixUpdate') }}' : '{{ route('approvalMatrixAdd') }}';
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
                           $('#approvalMatrixAddEditModalForm').trigger('reset');
                           $('#approvalMatrixAddEditModal').modal('hide');
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


          $(document).on('change','.status-switch',function() {
                var status = $(this).val();
                var approvalID = $(this).attr('data-approvalID');
                $.ajax({
                   url: "{{ route('approvalMatrixStatusUpdate') }}",
                   type: "POST",
                   dataType:'json',
                   data: {
                     _token: "{{ csrf_token() }}",
                     approvalID:approvalID,
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

        $(document).on('click','.approvalDeleteBtn',function(e){
            e.preventDefault();  
            $('#approvalIdDelete').val($(this).attr('data-approvalID'));
            $('#info-alert-modal').modal('show');
        });

        $(document).on('click','#confirmYes',function(){
           var approvalID = $('#approvalIdDelete').val();
                $.ajax({
                   url: "{{ route('approvalMatrixDelete') }}",
                   type: "GET",      
                   data: {approvalID:approvalID},
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
        
        $(document).on('click','.approvalIDEditBtn',function(event) {
                event.preventDefault();
                var approvalID = $(this).attr('data-approvalID');
                $.ajax({
                   url: "{{ route('approvalMatrixEdit') }}",
                   type: "GET",      
                   data: {approvalID:approvalID},
                   success: function(data) {
                    
                    // Process the response data here
                        if(data.response=='success'){
                             $('#approvalMatrixAddEditModal').modal('show');

                            // Populate form fields
                            $('#department').val(data.values[0].department);
                            $('#approvalID').val(data.values[0].approvalID);
                            $('#designationsReplace').val(data.values[0].designation);
                            $('#minLoanAmount').val(data.values[0].rangeFrom);
                            $('#maxLoanAmount').val(data.values[0].rangeTo);
                            var userArray = JSON.parse(data.values[0].users); // If it's a JSON string
                            $('#usersReplace').val(userArray).trigger('change');

                            // Update the range slider display
                            updateRange();
                         } 
                   },
                   error: function(error) {
                       console.error(error);
                   }
               });
         });
     
        function updateRange() {
            let minVal = parseInt($('#minLoanAmount').val());
            let maxVal = parseInt($('#maxLoanAmount').val());

            if (minVal > maxVal) {
                $('#minLoanAmount').val(maxVal);
                minVal = maxVal;
            }

            if (maxVal < minVal) {
                $('#maxLoanAmount').val(minVal);
                maxVal = minVal;
            }

            $('#minAmountDisplay').text(`₹${minVal.toLocaleString()}`);
            $('#maxAmountDisplay').text(`₹${maxVal.toLocaleString()}`);

            const min = parseInt($('#minLoanAmount').attr('min'));
            const max = parseInt($('#maxLoanAmount').attr('max'));

            const fillLeft = ((minVal - min) / (max - min)) * 100;
            const fillRight = ((maxVal - min) / (max - min)) * 100;

            $('#rangeFill').css({
                left: fillLeft + '%',
                width: (fillRight - fillLeft) + '%'
            });
    }
         function printErrorMsg(msg){
            $('.errClr').remove();
            $.each(msg,function(key,value){
                $('.'+key+'Err').html('<div class="alert alert-danger errClr mt-1" role="alert"><i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>'+value+'</strong></div>');
            });
        }
       
    });
    

    
    
</script>

@endsection