@extends('layouts.master')
@section('page-title', $page_info['page_title'])
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
                                    <li class="breadcrumb-item"><a
                                            href="javascript: void(0);">{{$page_info['page_title']}}</a></li>
                                    <li class="breadcrumb-item active">{{$page_info['page_name']}}</li>
                                </ol>
                            </div>
                            <h4 class="page-title">{{$page_info['page_name']}}</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title -->
                <div class="row new-pro-res">
                    @if($profileData->redFlag == '1')
                        <div class="col-md-12">
                            <div class="alert alert-danger text-bg-danger border-0 fade show" role="alert">
                                <strong>Red Flag - </strong> {{$profileData->remarks}}
                            </div>
                        </div>
                    @endif
                    <div class="col-sm-4">
                        <!-- Profile -->
                        <div class="row pro-card">
                            <div class="col-md-12">
                                <div class="card text-center" id="card-height">
                                    <div class="card-body">
                                       @php $avatar = null; @endphp
                                              @if($profileData->gender == 'Male')
                                              @php
                                              $avatar = 'assets/images/users/avatar-man.png';
                                              @endphp
                                              @else
                                              @php
                                              $avatar = 'assets/images/users/avatar-women.png'; // Change this if you have a different avatar for females
                                              @endphp
                                              @endif
                                              <img src="{{ $avatar }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                                            
                                        <h4 class="mt-1 mb-2">
                                             {{$profileData->name}}  
                                            <div class="col-md-12">
                                                <div class="text-center mt-1">
                                                    <p class="text-muted font-14">
                                                        <span class="ms-2">
                                                            <strong><i
                                                      class="uil uil-envelope me-1 font-16"></i></strong>{{$profileData->email}}  
                                                        </span>
                                                    </p>
                                                    @if(!empty($profileData->alternate_email))
                                                        <p class="text-muted font-14">
                                                            <span class="ms-2">
                                                              <strong><i
                                                      class="uil uil-envelope me-1"></i></strong>{{ucwords($profileData->alternate_email)}}  
                                                            </span>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </h4>
                                        @if($profileData->redFlag == '1')
                                            <button type="button" class="btn btn-danger btn-sm mb-2">Red Flag</button>
                                        @else
                                            <button type="button"  class="btn btn-success border-success btn-sm mb-2">Active</button>
                                        @endif
                                        
                                        @if(role() == 'CRM Support' || role() == 'Credit Manager' || role() == 'Sr. Credit Manager' || isSuperAdmin() || isAdmin())
                                            @if(role() == 'Credit Manager' || role() == 'Sr. Credit Manager')
                                                @if(getUserID() == $profileData->cmID)
                                                    <button type="button" class="btn  btn-info btn-sm mb-2 open-modal"
                                                        data-contact-id="{{$profileData->contactID}}">
                                                        <i class="mdi mdi-account-edit text-white"></i>
                                                    </button>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-info btn-sm mb-2 open-modal"
                                                    data-contact-id="{{$profileData->contactID}}">
                                                    <i class="mdi mdi-account-edit text-white"></i>
                                                </button>
                                            @endif
                                        @endif
                                          <div class="row mt-3">
                                              <div class="col-md-6">
                                                <div class="text-start">
                                                    <p class="text-muted mb-2 font-13 d-flex justify-content-between">
                                                        <span><strong>CIF:</strong> {{$profileData->contactID}} </span>
                                                     
                                                    </p>
                                                    <p class="text-muted mb-2 font-13 d-flex justify-content-between">
                                                        <span><strong>Mobile:</strong> {{$profileData->mobile}} </span>
                                                     
                                                    </p>
                                                    <p class="text-muted mb-2 font-13 d-flex justify-content-between">
                                                        <span><strong>DOB:</strong> {{df($profileData->dob)}} </span>
                                                      
                                                    </p>
                                                     <p class="text-muted font-13 d-flex justify-content-between">
                                                        <span><strong>Aadhar:</strong> {{$profileData->aadharNo}} </span>
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    </p>
                                                    
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-start">
                                                    <p class="text-muted mb-2 font-13 d-flex justify-content-between">
                                                        <span><strong>Lead ID:</strong> {{$profileData->leadID}} </span>
                                                      
                                                    </p>
                                                    <p class="text-muted mb-2 font-13 d-flex justify-content-between">
                                                        <span><strong>Gender:</strong> {{$profileData->gender}} </span>
                                                       
                                                    </p>
                                                    <p class="text-muted mb-2 font-13 d-flex justify-content-between">
                                                        <span><strong>Created on:</strong> {{df($profileData->addedOn)}} </span>
                                                       
                                                    </p>
                                                   <p class="text-muted font-13 d-flex justify-content-between">
                                                        <span><strong>Pan:</strong> {{$profileData->pancard}} </span>
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-12 d-flex align-items-center justify-content-center text-center">
                                                <div class="col-auto">
                                                    @if(in_array($profileData->status, ['No Answer', 'Callback', 'Interested', 'Not Interested', 'Not Eligible', 'Incomplete Documents']))
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                            @if(role() == 'Relationship Manager' && getUserID() == $profileData->rmID)
                                                                <a href="#" class="btn btn-sm btn-warning action-button"
                                                                    data-url="{{ url('mail/send-status/' . $profileData->leadID) }}"
                                                                    data-message="Send status mail document."
                                                                    data-bs-custom-class="warning-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Send Document Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @else
                                                                <a href="#" class="btn btn-sm btn-warning action-button"
                                                                    data-url="{{ url('mail/send-status/' . $profileData->leadID) }}"
                                                                    data-message="Send status for this lead."
                                                                    data-bs-custom-class="warning-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Send Document Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                            
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end card-body -->
                                </div>
                                <!-- end card -->
                            </div>
                            <!-- end card-body/ profile-user-box-->
                        </div>
                        <!--end profile/ card -->
                    </div>
                    <!-- end col-->
                    <div class="col-sm-8">
                        <!-- Profile -->
                        <div class="card shadow m-0 for_hei_new pro-card" id="card-height">
                            <div class="card-body">
                                <!-- Checkout Steps -->
                                <ul class="nav nav-pills bg-nav-pills nav-justified mb-2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a href="#loan-information" data-bs-toggle="tab" class="nav-link rounded-0"
                                            id="loan-tab" aria-selected="true" role="tab">
                                            <span class="d-none d-lg-block"><i class="mdi mdi-form-select font-16"></i> Loan
                                                Applied</span>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="#timeline-information" data-bs-toggle="tab" class="nav-link rounded-0"
                                            id="timeline-tab" aria-selected="false" role="tab">
                                            <span class="d-none d-lg-block"><i class="mdi mdi-timeline-text font-16"></i>
                                                Timeline</span>
                                        </a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a href="#previous-information" data-bs-toggle="tab" class="nav-link rounded-0"
                                            id="previous-tab" aria-selected="false" role="tab">
                                            <span class="d-none d-lg-block"><i class="mdi mdi-timetable font-16"></i>
                                                Previous Leads</span>
                                        </a>
                                    </li>
                                </ul>
                                <!-- Steps Information -->
                                <div class="tab-content">
                                    <!-- Billing Content-->
                                    <div class="tab-pane active show" id="loan-information" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card-body">
                                                    <div class="row new-pro-res_new">
                                                        <div class="col-sm-3">
                                                            <div class="card tilebox-one bg-primary mb-2">
                                                                <div class="card-body" data-simplebar data-simplebar-lg
                                                                    style="max-height:100px;">
                                                                    {{-- <i
                                                                        class="ri-file-list-fill float-end text-muted"></i>
                                                                    --}}
                                                                    <h6 class="text-white text-uppercase mt-0">Loan Applied
                                                                    </h6>
                                                                    <h2 class="text-white m-b-20"
                                                                        style="font-size:25px!important;">
                                                                        {{$totalLoanApplied}}
                                                                    </h2>
                                                                    <!-- <span class="badge bg-primary"> +11% </span> <span class="text-muted">From previous period</span> -->
                                                                </div>
                                                                <!-- end card-body-->
                                                            </div>
                                                            <!--end card-->
                                                        </div>
                                                        <!-- end col -->
                                                        <div class="col-sm-3">
                                                            <div class="card tilebox-one bg-primary mb-2">
                                                                <div class="card-body" data-simplebar data-simplebar-lg
                                                                    style="max-height:100px;">
                                                                    {{-- <i
                                                                        class="ri-file-list-3-fill float-end text-muted"></i>
                                                                    --}}
                                                                    <h6 class="text-white text-uppercase mt-0">Loan
                                                                        Disbursed</h6>
                                                                    <h2 class="text-white m-b-20"
                                                                        style="font-size:25px!important;">
                                                                        {{$totalLoanDisbursed}}
                                                                    </h2>
                                                                </div>
                                                                <!-- end card-body-->
                                                            </div>
                                                            <!--end card-->
                                                        </div>
                                                        <!-- end col -->
                                                        <div class="col-sm-3">
                                                            <div class="card tilebox-one bg-primary mb-2">
                                                                <div class="card-body" data-simplebar data-simplebar-lg
                                                                    style="max-height:100px;">
                                                                    {{-- <i
                                                                        class="ri-error-warning-fill float-end text-muted"></i>
                                                                    --}}
                                                                    <h6 class="text-white text-uppercase mt-0">Loan Rejected
                                                                    </h6>
                                                                    <h2 class="text-white m-b-20"
                                                                        style="font-size:25px!important;">
                                                                        {{$totalLoanRejected}}
                                                                    </h2>
                                                                </div>
                                                                <!-- end card-body-->
                                                            </div>
                                                            <!--end card-->
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="card tilebox-one bg-primary mb-2">
                                                                <div class="card-body" data-simplebar data-simplebar-lg
                                                                    style="max-height:100px;">
                                                                    {{-- <i
                                                                        class="ri-error-warning-fill float-end text-muted"></i>
                                                                    --}}
                                                                    <h6 class="text-white text-uppercase mt-0 mb-1">Lead
                                                                        Status</h6>
                                                                    <h2 class="text-white m-b-20"
                                                                        style="font-size:14.5px!important;margin-top:12px!important;">
                                                                        {{ucwords($profileData->status)}}
                                                                    </h2>
                                                                </div>
                                                                <!-- end card-body-->
                                                            </div>
                                                            <!--end card-->
                                                        </div>
                                                        <!-- end col -->
                                                    </div>
                                                    <!--<h4 class="card-title">Loan Apply Details</h4>-->
                                                    <hr>
                                                    <table class="table" style="border: none; font-size:13.5px!important;">
                                                        <tbody>
                                                            <tr>
                                                                <td style="border: none;"><strong>Loan Required:</strong>
                                                                    {{ nf($profileData->loanRequired) }}</td>
                                                                <td style="border: none;"><strong>Monthly Income:</strong>
                                                                    {{ nf($profileData->monthlyIncome) }}</td>
                                                                <td style="border: none;"><strong>Source:</strong>
                                                                    {{ $profileData->utmSource }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: none;"><strong>State:</strong>
                                                                 {{ getUserNameById('lms_states', 'stateID', $profileData->state, 'stateName') }}</td>
                                                                <td style="border: none;"><strong>City:</strong>
                                                                 {{ getUserNameById('lms_cities', 'cityID', $profileData->city, 'cityName') }}</td>
                                                                <td style="border: none;"><strong>Pincode:</strong>
                                                                 {{ $profileData->pincode }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: none;"><strong>Loan Purpose:</strong>
                                                                    {{ $profileData->purpose }}</td>
                                                                <td style="border: none;"><strong>Assigned RM:</strong>
                                                                    {{ (!empty($profileData->rmID) ? getUserNameById('users', 'userID', $profileData->rmID, 'displayName') : '--') }}
                                                                </td>
                                                                <td style="border: none;"><strong>Assigned CM:</strong>
                                                                    {{ (!empty($profileData->cmID) ? getUserNameById('users', 'userID', $profileData->cmID, 'displayName') : '--') }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border: none;"><strong>Applied On:</strong>
                                                                    {{ dft($profileData->addedOn) }}</td>
                                                                <td style="border: none;"><strong>Tenure:</strong> {{$profileData->tenure}} Months</td>
                                                                <td style="border: none;"><strong>Residential Type:</strong> {{$profileData->residentialType}}</td>
                                                                <!-- Empty cells for alignment -->
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- end row-->
                                    </div>
                                    <!-- End Billing Information Content-->
                                    <!-- Shipping Content-->
                                    <div class="tab-pane" id="timeline-information" role="tabpanel">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="card" data-simplebar data-simplebar-md style="height:330px;">
                                                    <div class="card-header">
                                                        <div class="row align-items-center">
                                                            <div class="col">
                                                                <h4 class="card-title mb-0">Timeline</h4>
                                                            </div>
                                                            <div class="col-auto">
                                                                @if(role() == 'Relationship Manager' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(in_array($profileData->status, ['Fresh', 'No Answer', 'Callback', 'Interested', 'Not Interested', 'Not Eligible', 'Incomplete Documents', 'Self Employed', 'Switch Off', 'Less Salary', 'Reloan Fresh', 'Wrong Number', 'Loan Running']))
                                                                        @if(role() == 'Relationship Manager')
                                                                            @if(getUserID() == $profileData->rmID)
                                                                                <button class="btn btn-sm btn-primary ms-1" type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseExample" aria-expanded="false"
                                                                                    aria-controls="collapseExample">
                                                                                    <i class="uil uil-plus"></i>Add Call
                                                                                </button>
                                                                            @endif
                                                                        @elseif(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                                            @if(getUserID() == $profileData->cmID)
                                                                                <button class="btn btn-sm btn-primary ms-1" type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseExample" aria-expanded="false"
                                                                                    aria-controls="collapseExample">
                                                                                    <i class="uil uil-plus"></i>Add Call
                                                                                </button>
                                                                            @endif
                                                                        @else
                                                                            <button class="btn btn-sm btn-primary ms-1" type="button"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#collapseExample" aria-expanded="false"
                                                                                aria-controls="collapseExample">
                                                                                <i class="uil uil-plus"></i>Add Call
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                @endif

                                                            </div>
                                                        </div>
                                                        <hr>
                                                    </div>
                                                    <div class="card-body py-0">
                                                        <div class="collapse mb-3  @if(session('active_tab') == 'timeline') show @endif"
                                                            id="collapseExample">
                                                            <form action="profile/add-timeline" method="post"
                                                                id="branchAddEditModalForm">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-1 ">
                                                                            <select class="form-control select2 new_css"
                                                                                name="status" id="callStatus"
                                                                                data-toggle="select2"
                                                                                style="max-height:50px!important;">
                                                                                <option value="">Select Call Status</option>
                                                                                @php
                                                                                    $desiredStatuses = ['No Answer', 'Callback', 'Interested', 'Not Interested', 'Not Eligible', 'Document Received', 'Incomplete Documents', 'Loan Running', 'Self Employed', 'Switch Off', 'Less Salary', 'Duplicate', 'DNC', 'Out Of Range', 'Other'];
                                                                                   @endphp

                                                                                @foreach($desiredStatuses as $desiredStatus)
                                                                                    @php
                                                                                        // Check if the current desired status exists in the leadStatus array
                                                                                        $status = $leadStatus->firstWhere('name', $desiredStatus);
                                                                                    @endphp

                                                                                    @if($status)
                                                                                        @if(role() == 'Relationship Manager' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Admin' || isSuperAdmin())
                                                                                            @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                                                                @if(getUserID() == $profileData->cmID)
                                                                                                    @if($profileData->redFlag == '1' && $profileData->redFlagApproved == '1')
                                                                                                        {{-- If redFlag is approved, show all
                                                                                                        statuses including "Document Received" --}}
                                                                                                        <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                            {{ $status->name }}
                                                                                                        </option>
                                                                                                    @elseif($profileData->redFlag == '1' && $profileData->redFlagApproved == '0')
                                                                                                        {{-- If redFlag is not approved, show all
                                                                                                        except "Document Received" --}}
                                                                                                        @if($status->name !== 'Document Received')
                                                                                                            <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                                {{ $status->name }}
                                                                                                            </option>
                                                                                                        @endif
                                                                                                    @else
                                                                                                        {{-- If not redFlag, show all desired
                                                                                                        statuses including "Document Received" --}}
                                                                                                        <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                            {{ $status->name }}
                                                                                                        </option>
                                                                                                    @endif
                                                                                                @endif
                                                                                            @elseif(role() == 'Relationship Manager')
                                                                                                {{-- For Relationship Manager --}}
                                                                                                @if(getUserID() == $profileData->rmID)
                                                                                                    @if($profileData->redFlag == '1')
                                                                                                        @if($profileData->redFlagApproved == '1')
                                                                                                            {{-- If redFlag is approved, show all except
                                                                                                            "Document Received" --}}
                                                                                                            @if($status->name !== 'Document Received')
                                                                                                                <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                                    {{ $status->name }}
                                                                                                                </option>
                                                                                                            @endif
                                                                                                        @else
                                                                                                            {{-- If redFlag is not approved, show all
                                                                                                            except "Document Received" --}}
                                                                                                            @if($status->name !== 'Document Received')
                                                                                                                <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                                    {{ $status->name }}
                                                                                                                </option>
                                                                                                            @endif
                                                                                                        @endif
                                                                                                    @else
                                                                                                        {{-- If not redFlag, show all desired
                                                                                                        statuses except "Document Received" --}}
                                                                                                        @if($status->name !== 'Document Received')
                                                                                                            <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                                {{ $status->name }}
                                                                                                            </option>
                                                                                                        @endif
                                                                                                    @endif
                                                                                                @endif
                                                                                            @else
                                                                                                {{-- For other roles (like Admin or
                                                                                                SuperAdmin) --}}
                                                                                                @if($profileData->redFlag == '1' && $profileData->redFlagApproved == '1')
                                                                                                    {{-- If redFlag is approved, show all
                                                                                                    statuses including "Document Received" --}}
                                                                                                    <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                        {{ $status->name }}
                                                                                                    </option>
                                                                                                @elseif($profileData->redFlag == '1' && $profileData->redFlagApproved == '0')
                                                                                                    {{-- If redFlag is not approved, show all
                                                                                                    except "Document Received" --}}
                                                                                                    @if($status->name !== 'Document Received')
                                                                                                        <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                            {{ $status->name }}
                                                                                                        </option>
                                                                                                    @endif
                                                                                                @else
                                                                                                    {{-- If not redFlag, show all desired
                                                                                                    statuses including "Document Received" --}}
                                                                                                    <option value="{{ $status->name }}" {{ old('status') == $status->name ? 'selected' : '' }}>
                                                                                                        {{ $status->name }}
                                                                                                    </option>
                                                                                                @endif
                                                                                            @endif
                                                                                        @endif
                                                                                    @endif
                                                                                @endforeach
                                                                            </select>
                                                                            @error('status')
                                                                                <p class="text-danger m-1">
                                                                                    <i
                                                                                        class="ri-close-circle-line me-1 align-middle font-12"></i>
                                                                                    <strong>{{ $message }}</strong>
                                                                                </p>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-1">
                                                                            <textarea class="form-control" name="remarks"
                                                                                id="remarks" rows="1"
                                                                                placeholder="Remarks"></textarea>
                                                                            @error('remarks')
                                                                                <p class="text-danger m-1">
                                                                                    <i
                                                                                        class="ri-close-circle-line me-1 align-middle font-12"></i>
                                                                                    <strong>{{ $message }}</strong>
                                                                                </p>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="col-md-12 d-flex justify-content-center align-item-center pt-3">
                                                                        <input type="hidden" name="active_tab"
                                                                            value="timeline">
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Save</button>
                                                                    </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="timeline-alt pb-0 mt-2">
                                                        @if($timeline && count($timeline) > 0)
                                                            @foreach($timeline as $item)
                                                                <div class="timeline-item">
                                                                    <i
                                                                        class="mdi mdi-circle bg-primary-lighten text-primary timeline-icon"></i>
                                                                    <div class="timeline-item-info">
                                                                        <h5 class="mt-0 mb-1">{{ $item->status}}</h5>
                                                                        <p class="font-14">Call by:
                                                                            <strong>{{ getUserNameById('users', 'userID', $item->addedBy, 'displayName')}}</strong><span
                                                                                class="ms-2 font-12">{{$item->addedOn}}</span>
                                                                        </p>
                                                                        <p class="mt-0 mb-0 pb-3">{{ $item->remarks }}</p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="timeline-item">
                                                                <div class="timeline-item-info">
                                                                    <h5 class="mt-0 mb-3 mt-3 text-center">No Timeline Found
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end row-->
                                </div>
                                <!-- End Shipping Information Content-->
                                <!-- Payment Content-->
                                <div class="tab-pane" id="previous-information" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Previous Leads</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive" data-simplebar data-simplebar-lg
                                                        style="min-height:220px;">
                                                        <table id="basic-datatable" class="table  table-striped"
                                                            style="white-space:nowrap;">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                                                                        <th>Action</th>
                                                                    @endif
                                                                    <th>Status</th>
                                                                    <th>Loan Amount</th>
                                                                    <th>Admin Fee</th>
                                                                    <th>ROI</th>
                                                                    <th>Sanction Date</th>
                                                                    <th>Last Payment Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($prevLeads as $key => $prevLead)
                                                                    <tr>
                                                                        <td>{{++$key}}</td>
                                                                        @if(rolesAccess('Status Wise', 'view') || isSuperAdmin() || isAdmin())
                                                                            <td>
                                                                                <a target="_blank"
                                                                                    href="profile/{{$prevLead->leadID}}"
                                                                                    class="text-primary" data-bs-toggle="tooltip"
                                                                                    data-bs-placement="top"
                                                                                    data-bs-custom-class="primary-tooltip"
                                                                                    data-bs-title="Profile View"
                                                                                    style="font-size: 16px;margin-left: 6px;"><i
                                                                                        class='mdi mdi-eye'></i></a>
                                                                            </td>
                                                                        @endif
                                                                        <td>
                                                                            <span
                                                                                class="badge bg-success-lighten text-success">{{$prevLead->status}}</span>
                                                                        </td>
                                                                        <td>
                                                                            {{ !empty($prevLead->loanAmtApproved) ? nf($prevLead->loanAmtApproved) : '--' }}
                                                                        </td>
                                                                        <td>{{ !empty($prevLead->adminFee) ? nf($prevLead->adminFee) : '--' }}
                                                                        </td>
                                                                        <td>{{ !empty($prevLead->roi) ? $prevLead->roi . ' %' : '--' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ !empty($prevLead->createdDate) ? df($prevLead->createdDate) : '--' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ !empty($prevLead->collectedDate) ? df($prevLead->collectedDate) : '--' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        {{--
                                                        <div class="row">
                                                            {{ $prevLeads->links('pagination::bootstrap-5') }}
                                                        </div>
                                                        --}}
                                                    </div>
                                                </div>
                                                <!-- end card-body-->
                                            </div>
                                        </div>
                                        <!-- end col -->
                                    </div>
                                    <!-- end row-->
                                </div>
                                <!-- End Payment Information Content-->
                            </div>
                            <!-- end tab content-->
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- <h4 class="header-title mb-3">Additional Information</h4> -->
                            <ul class="nav nav-tabs nav-pills bg-nav-pills nav-justified mb-3">
                                <li class="nav-item">
                                    <a href="#customer" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                        <i class="ri-team-fill font-custom-size"></i>
                                        <span class="d-none d-lg-block">Customer Info</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#kyc" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                                        <i class="mdi mdi-cellphone-check font-custom-size"></i>
                                        <span class="d-none d-lg-block">KYC Details</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#checklist" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                                        <i class="mdi mdi-beaker-check font-custom-size"></i>
                                        <span class="d-none d-lg-block">Checklist</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sanction" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                                        <i class="ri-survey-line font-custom-size"></i>
                                        <span class="d-none d-lg-block">Sanction</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#communication" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                                        <i class="mdi mdi-email font-custom-size"></i>
                                        <span class="d-none d-lg-block">Communication</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="customer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="col-md-12">
                                                <div class="accordion custom-accordion mb-3"
                                                    id="custom-accordion-documents">
                                                    <div class="card mb-0">
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block"
                                                                    data-bs-toggle="collapse" href="#collapseDocuments"
                                                                    aria-expanded="true" aria-controls="collapseFour">
                                                                    Documents Details<i
                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div id="collapseDocuments" class="collapse"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#custom-accordion-documents">
                                                            <div class="card-body">
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager')
                                                                        @if(getUserID() == $profileData->rmID || getUserID() == $profileData->cmID)
                                                                            <div class="col-auto text-end">
                                                                                <button class="btn btn-sm btn-primary ms-1"
                                                                                    id="addDocumentsButton" type="button">
                                                                                    <i class="uil uil-plus"></i> Add
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="col-auto text-end">
                                                                            <button class="btn btn-sm btn-primary ms-1"
                                                                                id="addDocumentsButton" type="button">
                                                                                <i class="uil uil-plus"></i> Add
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                                <div class="row mt-2">
                                                                    <div class="collapse mb-3" id="collapseDocumentsForm">
                                                                        <form action="profile/add-documents" method="post"
                                                                            enctype="multipart/form-data"
                                                                            class="documents-form" autocomplete="off">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <select id="documentsType"
                                                                                            class="form-control"
                                                                                            name="documentsType">
                                                                                            <option value="">Choose Document
                                                                                                Type</option>
                                                                                            <option value="Cibil Report">
                                                                                                Cibil Report</option>
                                                                                            <option value="Pancard">Pancard
                                                                                            </option>
                                                                                            <option
                                                                                                value="Aadhar Back Image">
                                                                                                Aadhar Back Image</option>
                                                                                            <option
                                                                                                value="Aadhar Front Image">
                                                                                                Aadhar Front Image</option>
                                                                                            <option value="Bank Statement">
                                                                                                Bank Statement</option>
                                                                                            <option value="Selfie">Selfie
                                                                                            </option>
                                                                                            <option value="Salary Slip">
                                                                                                Salary Slip</option>
                                                                                            <option value="ID Card">ID Card
                                                                                            </option>
                                                                                            <option value="Cheque">Cheque
                                                                                            </option>
                                                                                            <option value="BSA Report">BSA Report
                                                                                            <option value="Profile Pic">Profile Pic
                                                                                            </option>
                                                                                             <option value="Water Bill">Water Bill
                                                                                            </option>
                                                                                            <option
                                                                                                value="Electricity Bill">
                                                                                                Electricity Bill</option>
                                                                                            <option value="Mobile Bill">
                                                                                                Mobile Bill</option>
                                                                                            <option value="Other">Other
                                                                                            </option>
                                                                                        </select>
                                                                                        <span
                                                                                            class="documentsTypeErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="documentsPassword"
                                                                                            id="documentsPassword"
                                                                                            placeholder="Enter Password/Remarks">
                                                                                        <span
                                                                                            class="documentsPasswordErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <input type="file"
                                                                                            class="form-control"
                                                                                            name="documents" id="documents">
                                                                                        <span class="documentsErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isSuperAdmin() || role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager' || isAdmin())
                                                                                    <div class="col-md-6">
                                                                                        <div class="mb-3">
                                                                                            <select id="documentsStatus"
                                                                                                class="form-control"
                                                                                                name="documentsStatus">
                                                                                                <option value="">Choose Status
                                                                                                </option>
                                                                                                <option value="Pending">Pending
                                                                                                </option>
                                                                                                <option value="Verified">
                                                                                                    Verified</option>
                                                                                                <option value="Rejected">
                                                                                                    Rejected</option>
                                                                                            </select>
                                                                                            <span
                                                                                                class="documentsStatusErr"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="mb-3">
                                                                                            <input type="text"
                                                                                                class="form-control"
                                                                                                name="docRemarks"
                                                                                                id="docRemarks"
                                                                                                placeholder="Enter remarks">
                                                                                            <span class="docRemarksErr"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                @endif
                                                                                <div
                                                                                    class="col-md-12 d-flex justify-content-center">
                                                                                    <input type="hidden" name="id"
                                                                                        id="documentsId">
                                                                                    <input type="hidden" name="oldDocument"
                                                                                        id="oldDocument">
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-danger font-16"
                                                                                        id="closeDocumentsButton"><i
                                                                                            class="uil uil-times icon"></i></button>
                                                                                    <button type="submit"
                                                                                        id="submitDocumentBtn"
                                                                                        class="btn btn-sm btn-primary font-16"
                                                                                        style="margin-left: 10px;"><i
                                                                                            class="uil uil-check icon"></i>
                                                                                    </button>
                                                                                </div>
                                                                                <!-- end col-md-12 -->
                                                                            </div>
                                                                            <!-- end row -->
                                                                        </form>
                                                                    </div>
                                                                  <div class="table-responsive" data-simplebar
                                                                        data-simplebar-primary>
                                                                        <table class="table table-striped">
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Documents Type</th>
                                                                            <th>Documents</th>
                                                                            <th>Remarks / Password</th>
                                                                            <th>PD Remarks</th>
                                                                            <th>Status</th>
                                                                            @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager' || isSuperAdmin() || isAdmin())
                                                                                <th>Action</th>
                                                                            @endif
                                                                        </tr>
                                                                        @if(!empty($documents))
                                                                            @foreach($documents as $key => $doc)
                                                                                <tr>
                                                                                    <td>{{ ++$key }}</td>
                                                                                    <td>{{ $doc->documentsType }}</td>
                                                                                    <td>
                                                                                        <a href="{{ Storage::url($doc->documents) }}"
                                                                                            target="_blank" class="text-success"
                                                                                            data-bs-toggle="tooltip"
                                                                                            data-bs-placement="top"
                                                                                            data-bs-custom-class="success-tooltip"
                                                                                            data-bs-title="View"
                                                                                            style="font-size: 18px;">
                                                                                            <i class='mdi mdi-eye'></i>
                                                                                        </a>
                                                                                        <a href="{{ Storage::url($doc->documents) }}"
                                                                                            target="_blank" class="text-primary"
                                                                                            data-bs-toggle="tooltip"
                                                                                            data-bs-placement="top"
                                                                                            data-bs-custom-class="primary-tooltip"
                                                                                            data-bs-title="Download"
                                                                                            style="font-size: 18px;margin-left: 10px;"
                                                                                            download>
                                                                                            <i class="mdi mdi-download"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                    <td>{{ $doc->documentsPassword ?? '--' }}</td>
                                                                                    <td>{{ $doc->docRemarks ?? '--' }}</td>
                                                                                    <td>
                                                                                        @if($doc->documentsStatus == 'Pending')
                                                                                            <span
                                                                                                class="badge bg-warning text-dark">Pending</span>
                                                                                        @elseif($doc->documentsStatus == 'Verified')
                                                                                            <span
                                                                                                class="badge bg-success text-white">Verified</span>
                                                                                        @elseif($doc->documentsStatus == 'Rejected')
                                                                                            <span
                                                                                                class="badge bg-danger text-white">Rejected</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'CRM Support' || role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager' || isSuperAdmin() || isAdmin())
                                                                                            <a href="#" data-id="{{ $doc->id }}"
                                                                                                class="btn-primary ms-1 editDocumentsBtn"
                                                                                                aria-controls="collapseExample"
                                                                                                data-bs-toggle="tooltip"
                                                                                                data-bs-placement="top"
                                                                                                data-bs-custom-class="info-tooltip"
                                                                                                data-bs-title="Edit"><i
                                                                                                    class="mdi mdi-square-edit-outline"></i></a>
                                                                                        @endif
                                                                                        @if(isSuperAdmin())
                                                                                            <a href=""
                                                                                                class="text-danger documentsDeleteBtn"
                                                                                                data-bs-toggle="tooltip"
                                                                                                data-bs-placement="top"
                                                                                                data-bs-custom-class="danger-tooltip"
                                                                                                data-bs-title="Delete"
                                                                                                style="font-size: 18px;margin-left: 5px;"
                                                                                                data-id="{{ $doc->id }}"><i
                                                                                                    class="mdi mdi-delete"></i></a>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="5" class="text-center">No record
                                                                                    found</td>
                                                                            </tr>
                                                                        @endif
                                                                    </table>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="accordion custom-accordion mb-3" id="custom-accordion-address">
                                                    <div class="card mb-0">
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block"
                                                                    data-bs-toggle="collapse" href="#collapseFour"
                                                                    aria-expanded="true" aria-controls="collapseFour">
                                                                    Address Details<i
                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div id="collapseFour" class="collapse"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#custom-accordion-documents">
                                                            <div class="card-body">
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager')
                                                                        @if(getUserID() == $profileData->rmID || getUserID() == $profileData->cmID)
                                                                            <div class="col-auto text-end">
                                                                                <button class="btn btn-sm btn-primary ms-1"
                                                                                    id="addAddressButton" type="button">
                                                                                    <i class="uil uil-plus"></i> Add
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="col-auto text-end">
                                                                            <button class="btn btn-sm btn-primary ms-1"
                                                                                id="addAddressButton" type="button">
                                                                                <i class="uil uil-plus"></i> Add
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                                <div class="row mt-2">
                                                                    <div class="collapse mb-3" id="collapseAddress">
                                                                        <form action="profile/add-address" method="post"
                                                                            class="address-form" id="address-form"
                                                                            autocomplete="off">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <select class="form-control"
                                                                                            name="addressType"
                                                                                            id="addressType">
                                                                                            <option value="">Select address
                                                                                                type</option>
                                                                                            <option value="Aadhar">Aadhar
                                                                                            </option>
                                                                                            <option value="Permanent">
                                                                                                Permanent</option>
                                                                                            <option value="Current">Current
                                                                                            </option>
                                                                                            <option value="Owned">Owned
                                                                                            </option>
                                                                                            <option value="Rented">Rented
                                                                                            </option>
                                                                                            <option value="Other">Other
                                                                                            </option>
                                                                                        </select>
                                                                                        <span class="addressTypeErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <input type="number"
                                                                                            class="form-control"
                                                                                            name="pincode" id="pincode"
                                                                                            placeholder="Pincode" min="0">
                                                                                        <div id="pincodeError"
                                                                                            class="text-danger"></div>
                                                                                        <span class="pincodeErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <input type="text"
                                                                                            class="form-control" readonly
                                                                                            name="addState" id="addState"
                                                                                            placeholder="State">
                                                                                        <span class="addStateErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="addCity" id="addCity"
                                                                                            placeholder="City" value="">
                                                                                        <span class="addCityErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <textarea class="form-control"
                                                                                            name="address" id="address"
                                                                                            placeholder="Address"
                                                                                            rows="1"></textarea>
                                                                                        <span class="addressErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <select class="form-control"
                                                                                            name="status" id="addStatus">
                                                                                            <option value="">Select Status
                                                                                            </option>
                                                                                            <option value="Pending">Pending
                                                                                            </option>
                                                                                            <option value="Verified">
                                                                                                Verified</option>
                                                                                            <option value="Not Verified">Not
                                                                                                Verified</option>
                                                                                        </select>
                                                                                        <span class="statusErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- end col-md-5 -->
                                                                                <div
                                                                                    class="col-md-12 d-flex justify-content-center">
                                                                                    <input type="hidden" name="id"
                                                                                        id="addressID">
                                                                                    <button type="button"
                                                                                        class="btn btn-danger font-16"
                                                                                        id="closeAddressButton"><i
                                                                                            class="uil uil-times icon"></i>
                                                                                    </button>
                                                                                    <button type="submit"
                                                                                        id="submitAddressBtn"
                                                                                        class="btn btn-primary font-16"
                                                                                        style="margin-left: 10px;"><i
                                                                                            class="uil uil-check icon"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <th>Address Type</th>
                                                                            <th>State</th>
                                                                            <th>City</th>
                                                                            <th>Pincode</th>
                                                                            <th>Address</th>
                                                                            <th>Status</th>
                                                                            @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                                <th>Action</th>
                                                                            @endif
                                                                        </tr>
                                                                        @if(count($address) > 0)
                                                                            @foreach($address as $key => $addr)
                                                                                <tr>
                                                                                    <td>{{ $addr->addressType }}</td>
                                                                                    <td>{{ getUserNameById('lms_states', 'stateID', $profileData->state, 'stateName')}}</td>
                                                                                    <td>{{ getUserNameById('lms_cities', 'cityID', $profileData->city, 'cityName') }}</td>
                                                                                    <td>{{ $addr->pincode }}</td>
                                                                                    <td>{{ $addr->address }}</td>
                                                                                    <td>
                                                                                        @if($addr->status == 'Pending')
                                                                                            <span
                                                                                                class="badge bg-warning text-dark">Pending</span>
                                                                                        @elseif($addr->status == 'Verified')
                                                                                            <span
                                                                                                class="badge bg-success text-white">Verified</span>
                                                                                        @elseif($addr->status == 'Not Verified')
                                                                                            <span class="badge bg-danger text-white">Not
                                                                                                Verified</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        @if(role() == 'CRM Support' || isSuperAdmin())
                                                                                            <a class="btn-primary ms-1 editAddressBtn"
                                                                                                data-id="{{ $addr->id }}"
                                                                                                data-bs-toggle="tooltip"
                                                                                                data-bs-placement="top"
                                                                                                data-bs-custom-class="info-tooltip"
                                                                                                data-bs-title="Edit"><i
                                                                                                    class="mdi mdi-square-edit-outline"></i></a>
                                                                                        @endif
                                                                                        @if(isSuperAdmin())
                                                                                            <a href=""
                                                                                                class="text-danger addressDeleteBtn"
                                                                                                data-bs-toggle="tooltip"
                                                                                                data-bs-placement="top"
                                                                                                data-bs-custom-class="danger-tooltip"
                                                                                                data-bs-title="Delete"
                                                                                                style="font-size: 18px;margin-left: 5px;"
                                                                                                data-id="{{ $addr->id }}"><i
                                                                                                    class="mdi mdi-delete"></i></a>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="7" class="text-center">No record
                                                                                    found</td>
                                                                            </tr>
                                                                        @endif
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="accordion custom-accordion mb-3" id="custom-accordion-account">
                                                    <div class="card mb-0">
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block"
                                                                    data-bs-toggle="collapse" href="#collapseAccount"
                                                                    aria-expanded="true" aria-controls="collapseFour">
                                                                    Account Details<i
                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div id="collapseAccount" class="collapse"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#custom-accordion">
                                                            <div class="card-body">
                                                                <table class="table table-striped">
                                                                    <tr>
                                                                        <th>Account No.</th>
                                                                        <th>Bank Name</th>
                                                                        <th>IFSC</th>
                                                                        <th>City</th>
                                                                        <th>Branch</th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $profileData->accountNo }}</td>
                                                                        <td>{{ $profileData->bankName }}</td>
                                                                        <td>{{ $profileData->ifscCode }}</td>
                                                                        <td>{{ $profileData->branchCity }}</td>
                                                                        <td>{{ $profileData->bankBranch }}</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="col-md-12">
                                                <div class="accordion custom-accordion mb-3" id="custom-accordion-company">
                                                    <div class="card mb-0">
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block"
                                                                    data-bs-toggle="collapse" href="#collapseCompany"
                                                                    aria-expanded="true" aria-controls="collapseFour">
                                                                    Company Details <i
                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div id="collapseCompany" class="collapse"
                                                            aria-labelledby="headingFour"
                                                            data-bs-parent="#custom-accordion-address">
                                                            <div class="card-body">
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager')
                                                                        @if(getUserID() == $profileData->rmID || getUserID() == $profileData->cmID)
                                                                            <div class="col-auto text-end">
                                                                                <button class="btn btn-sm btn-primary ms-1"
                                                                                    id="addCompanyButton" type="button">
                                                                                    <i class="uil uil-plus"></i> Add
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="col-auto text-end">
                                                                            <button class="btn btn-sm btn-primary ms-1"
                                                                                id="addCompanyButton" type="button">
                                                                                <i class="uil uil-plus"></i> Add
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                                <div class="row mt-2">
                                                                    <div class="collapse mb-3" id="collapseCompanyForm">
                                                                        <form action="profile/add-company" method="post"
                                                                            class="company-form" autocomplete="off">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="companyName"
                                                                                            id="companyName"
                                                                                            placeholder="Company Name"
                                                                                            value="{{ old('companyName') }}">
                                                                                        <span class="companyNameErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <textarea class="form-control"
                                                                                            name="companyAddress"
                                                                                            id="companyAddress"
                                                                                            placeholder="Company Address"
                                                                                            rows="1">{{ old('companyAddress') }}</textarea>
                                                                                        <span
                                                                                            class="companyAddressErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <select class="form-control"
                                                                                            name="status"
                                                                                            id="companyStatus">
                                                                                            <option value="">Select Status
                                                                                            </option>
                                                                                            <option value="Pending">Pending
                                                                                            </option>
                                                                                            <option value="Verified">
                                                                                                Verified</option>
                                                                                            <option value="Not Verified">Not
                                                                                                Verified</option>
                                                                                        </select>
                                                                                        <span class="statusErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="col-md-12 d-flex justify-content-center">
                                                                                    <input type="hidden" name="id"
                                                                                        id="companyId">
                                                                                    <button type="button"
                                                                                        class="btn btn-danger font-16"
                                                                                        id="closeCompanyButton"><i
                                                                                            class="uil uil-times icon"></i></button>
                                                                                    <button type="submit"
                                                                                        id="submitCompanyBtn"
                                                                                        class="btn btn-primary font-16"
                                                                                        style="margin-left: 10px;"><i
                                                                                            class="uil uil-check icon"></i>
                                                                                    </button>
                                                                                </div>
                                                                                <!-- end col-md-12 -->
                                                                            </div>
                                                                            <!-- end row -->
                                                                        </form>
                                                                    </div>
                                                                    <div class="table-responsive" data-simplebar
                                                                        data-simplebar-primary>
                                                                        <table class="table table-striped">
                                                                            <tr>
                                                                                <th>Name</th>
                                                                                <th>Type</th>
                                                                                <th>Designation</th>
                                                                                <!--<th>Experience</th>-->
                                                                                <th>Address</th>
                                                                                <th>Status</th>
                                                                                @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                                    <th>Action</th>
                                                                                @endif
                                                                            </tr>
                                                                            @if(count($company) > 0)
                                                                                @foreach($company as $key => $comp)
                                                                                    <tr>
                                                                                        <td>{{ $comp->companyName }}</td>
                                                                                        <td>{{ $comp->companyType }}</td>
                                                                                        <td>{{ $comp->designation }}</td>
                                                                                        <!--<td>{{ $comp->workExperience }}</td>-->
                                                                                        <td>{{ $comp->address }}</td>
                                                                                        <td>
                                                                                            @if($comp->status == 'Pending')
                                                                                                <span
                                                                                                    class="badge bg-warning text-dark">Pending</span>
                                                                                            @elseif($comp->status == 'Verified')
                                                                                                <span
                                                                                                    class="badge bg-success text-white">Verified</span>
                                                                                            @elseif($comp->status == 'Not Verified')
                                                                                                <span
                                                                                                    class="badge bg-danger text-white">Not
                                                                                                    Verified</span>
                                                                                            @endif
                                                                                        </td>
                                                                                        <td>
                                                                                            @if(role() == 'CRM Support' || isSuperAdmin())
                                                                                                <a href="#" data-id="{{ $comp->id }}"
                                                                                                    class="btn-primary ms-1 editCompanyBtn"
                                                                                                    data-bs-toggle="tooltip"
                                                                                                    data-bs-placement="top"
                                                                                                    data-bs-custom-class="info-tooltip"
                                                                                                    data-bs-title="Company Edit"><i
                                                                                                        class="mdi mdi-square-edit-outline"></i></a>
                                                                                            @endif
                                                                                            @if(isSuperAdmin())
                                                                                                <a href=""
                                                                                                    class="text-danger companyDeleteBtn"
                                                                                                    data-bs-toggle="tooltip"
                                                                                                    data-bs-placement="top"
                                                                                                    data-bs-custom-class="danger-tooltip"
                                                                                                    data-bs-title="Delete"
                                                                                                    style="font-size: 18px;margin-left: 5px;"
                                                                                                    data-id="{{ $comp->id }}"><i
                                                                                                        class="mdi mdi-delete"></i></a>
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            @else
                                                                                <tr>
                                                                                    <td colspan="5" class="text-center">No
                                                                                        record found</td>
                                                                                </tr>
                                                                            @endif
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="accordion custom-accordion mb-3"
                                                    id="custom-accordion-reference">
                                                    <div class="card mb-0">
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block"
                                                                    data-bs-toggle="collapse" href="#collapseReference"
                                                                    aria-expanded="true" aria-controls="collapseReference">
                                                                    Reference Details <i
                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div id="collapseReference" class="collapse"
                                                            aria-labelledby="collapseReference"
                                                            data-bs-parent="#custom-accordion-company">
                                                            <div class="card-body">
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager')
                                                                        @if(getUserID() == $profileData->rmID || getUserID() == $profileData->cmID)
                                                                            <div class="col-auto text-end">
                                                                                <button class="btn btn-sm btn-primary ms-1"
                                                                                    id="addReferenceButton" type="button">
                                                                                    <i class="uil uil-plus"></i> Add
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="col-auto text-end">
                                                                            <button class="btn btn-sm btn-primary ms-1"
                                                                                id="addReferenceButton" type="button">
                                                                                <i class="uil uil-plus"></i> Add
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                                <div class="row mt-2">
                                                                    <div class="collapse mb-3" id="collapseReferenceForm">
                                                                        <form action="profile/add-reference" method="post"
                                                                            class="reference-form" autocomplete="off">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <select id="referenceRelation"
                                                                                            class="form-control"
                                                                                            name="referenceRelation">
                                                                                            <option value="">Choose Relation
                                                                                            </option>
                                                                                            <option value="Father">Father
                                                                                            </option>
                                                                                            <option value="Mother">Mother
                                                                                            </option>
                                                                                            <option value="Brother">Brother
                                                                                            </option>
                                                                                            <option value="Sister">Sister
                                                                                            </option>
                                                                                            <option value="Spouse">Spouse
                                                                                            </option>
                                                                                            <option value="Relative">
                                                                                                Relative</option>
                                                                                            <option
                                                                                                value="Office colleague">
                                                                                                Office colleague</option>
                                                                                            <option value="Friend">Friend
                                                                                            </option>
                                                                                            <option value="Other">Other
                                                                                            </option>
                                                                                        </select>
                                                                                        <span
                                                                                            class="referenceRelationErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="referenceName"
                                                                                            id="referenceName"
                                                                                            placeholder="Enter Name">
                                                                                        <span
                                                                                            class="referenceNameErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <input type="number"
                                                                                            class="form-control"
                                                                                            name="referenceMobile"
                                                                                            id="referenceMobile"
                                                                                            placeholder="Enter Mobile"
                                                                                            min="0">
                                                                                        <span
                                                                                            class="referenceMobileErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                {{--
                                                                                <div class="col-md-4">
                                                                                    <div class="mb-3">
                                                                                        <select class="form-control"
                                                                                            name="status"
                                                                                            id="referenceStatus">
                                                                                            <option value="">Select Status
                                                                                            </option>
                                                                                            <option value="Verified">
                                                                                                Verified</option>
                                                                                            <option value="Not Verified">Not
                                                                                                Verified</option>
                                                                                        </select>
                                                                                        <span class="statusErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                --}}
                                                                                <div
                                                                                    class="col-md-12 d-flex justify-content-center">
                                                                                    <input type="hidden" name="id"
                                                                                        id="referenceId">
                                                                                    <button type="button"
                                                                                        class="btn btn-danger font-16"
                                                                                        id="closeReferenceButton"><i
                                                                                            class="uil uil-times icon"></i></button>
                                                                                    <button type="submit"
                                                                                        id="submitReferenceBtn"
                                                                                        class="btn btn-primary font-16"
                                                                                        style="margin-left: 10px;"><i
                                                                                            class="uil uil-check icon"></i>
                                                                                    </button>
                                                                                </div>
                                                                                <!-- end col-md-12 -->
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <th>Reference Relation</th>
                                                                            <th>Reference Name</th>
                                                                            <th>Reference Mobile</th>
                                                                            @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                                <th>Action</th>
                                                                            @endif
                                                                        </tr>
                                                                        @if(count($reference) > 0)
                                                                            @foreach($reference as $key => $ref)
                                                                                <tr>
                                                                                    <td>{{ $ref->referenceRelation }}</td>
                                                                                    <td>{{ $ref->referenceName }}</td>
                                                                                    <td>{{ $ref->referenceMobile }}</td>
                                                                                    <td>
                                                                                        @if(role() == 'CRM Support' || isSuperAdmin())
                                                                                            <a href="#" data-id="{{ $ref->id }}"
                                                                                                class="btn-primary ms-1 editReferenceBtn"
                                                                                                data-bs-toggle="collapse"
                                                                                                data-bs-toggle="tooltip"
                                                                                                data-bs-placement="top"
                                                                                                data-bs-custom-class="info-tooltip"
                                                                                                data-bs-title=" Edit"><i
                                                                                                    class="mdi mdi-square-edit-outline"></i></a>
                                                                                        @endif
                                                                                        @if(isSuperAdmin())
                                                                                            <a href=""
                                                                                                class="text-danger referenceDeleteBtn"
                                                                                                data-bs-toggle="tooltip"
                                                                                                data-bs-placement="top"
                                                                                                data-bs-custom-class="danger-tooltip"
                                                                                                data-bs-title="Delete"
                                                                                                style="font-size: 18px;margin-left: 5px;"
                                                                                                data-id="{{ $ref->id }}"><i
                                                                                                    class="mdi mdi-delete"></i></a>
                                                                                        @endif
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="5" class="text-center">No record
                                                                                    found</td>
                                                                            </tr>
                                                                        @endif
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($profileData->pdStart == 1)
                                            <div class="col-md-12">
                                                <div class="accordion custom-accordion mb-3" id="custom-accordion-pd">
                                                    <div class="card mb-0">
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block" data-bs-toggle="collapse" href="#collapsePd"
                                                                    aria-expanded="true" aria-controls="collapseReference">
                                                                    Field Verification
                                                                    <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                        <div id="collapsePd" class="collapse" aria-labelledby="collapsePd" data-bs-parent="#custom-accordion-pd">
                                                            <div class="card-body">
                                                                @if(role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager')
                                                                      @if($profileData->pdStart==1)
                                                                        @if(getUserID() == $approvalData->pdVerifiedBy)
                                                                            <div class="col-auto text-end">
                                                                                <button class="btn btn-sm btn-primary ms-1" id="addPdButton" type="button">
                                                                                    <i class="uil uil-plus"></i> Add
                                                                                </button>
                                                                            </div>
                                                                        @endif
                                                                      @endif    
                                                                    @else
                                                                        <div class="col-auto text-end">
                                                                            <button class="btn btn-sm btn-primary ms-1" id="addPdButton" type="button">
                                                                                <i class="uil uil-plus"></i> Add
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                            
                                                                <div class="row mt-2">
                                                                    <div class="collapse mb-3" id="collapsePdForm">
                                                                        <form action="profile/add-pd-verification" method="post" class="pd-form" autocomplete="off">
                                                                            @csrf
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <input type="file" class="form-control" name="pdImages[]" id="pdImages" multiple>
                                                                                        <span class="dpdImagesErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <textarea class="form-control" name="pdRemarks"
                                                                                            id="pdRemarks" placeholder="Enter remarks"></textarea>
                                                                                        <span class="pdRemarksErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <select class="form-control" name="pdStatus" id="pdStatus">
                                                                                            <option value="">Select Status</option>
                                                                                            <option value="1">Verified</option>
                                                                                            <option value="0">Rejected</option>
                                                                                        </select>
                                                                                        <span class="statusErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12 d-flex justify-content-center">
                                                                                    <input type="hidden" name="id" id="pdId">
                                                                                    <button type="button" class="btn btn-danger font-16" id="closePdButton">
                                                                                        <i class="uil uil-times icon"></i>
                                                                                    </button>
                                                                                    <button type="submit" id="submitPdBtn" class="btn btn-primary font-16"
                                                                                        style="margin-left: 10px;">
                                                                                        <i class="uil uil-check icon"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                 <div class="table-responsive" data-simplebar data-simplebar-primary>
                                                                     <table class="table table-striped ml-1">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Remarks</th>
                                                                            <th>Files</th>
                                                                            <th>City</th>
                                                                            <th>Location</th>
                                                                            <th>Status</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @php
                                                                        // Function to get the file icon based on extension
                                                                        function getFileIcon($file) {
                                                                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                                                                            return match (strtolower($ext)) {
                                                                                'pdf' => 'uil-file-pdf text-danger',
                                                                                'doc', 'docx' => 'uil-file-word text-primary',
                                                                                'xls', 'xlsx' => 'uil-file-excel text-success',
                                                                                'jpg', 'jpeg', 'png', 'gif', 'bmp' => 'uil-image text-info',
                                                                                default => 'uil-file text-muted',
                                                                            };
                                                                        }
                                                                    @endphp
                                                                    
                                                                   @foreach($pdVerificationData as $pdVerificationData)
                                                                    @if(!empty($pdVerificationData->pdRemarks))
                                                                        <tr>
                                                                            <td>
                                                                                {{ $pdVerificationData->pdRemarks }}
                                                                            </td>

                                                                            <td class="text-nowrap">
                                                                                @php
                                                                                    $documentPd = !empty($pdVerificationData->pdDocuments)
                                                                                        ? json_decode($pdVerificationData->pdDocuments, true)
                                                                                        : [];
                                                                                @endphp

                                                                                @if(!empty($documentPd))
                                                                                    <ul class="list-unstyled mb-0">
                                                                                        @foreach($documentPd as $index => $file)
                                                                                            <li class="mb-1">
                                                                                                <a href="{{ Storage::url($file) }}" target="_blank" class="text-decoration-none">
                                                                                                    <i class="uil {{ getFileIcon($file) }} me-2"></i> Doc {{ $index + 1 }}
                                                                                                </a>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                @else
                                                                                    <span class="text-muted">No files uploaded</span>
                                                                                @endif
                                                                            </td>

                                                                            <td>{{ $pdVerificationData->geoCity }}</td>
                                                                            <td>{{ $pdVerificationData->geoAddress }}</td>
                                                                            <td>
                                                                                @if($pdVerificationData->pdStatus == 1)
                                                                                    <span class="badge bg-success">Verified</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Not Verified</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach


                                                                    </tbody>
                                                                </table>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="kyc">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card mb-0 p-1">
                                                <div class="card-header">
                                                    <h5 class="m-0 text-center">E-Sign Details</h5>
                                                </div>
                                                <table class="table table-striped">
                                                    <tr>
                                                        <th>Status</th>
                                                        <th>Requested By</th>
                                                        <th>Documents</th>
                                                        <th>Requested On</th>
                                                        <th>Signed On</th>
                                                    </tr>

                                                    @if(!empty($esignDoc))
                                                        <tr>
                                                            <td>
                                                                @if($esignDoc->status == 'requested')
                                                                    <span class="badge bg-warning text-dark">Requested</span>
                                                                @elseif($esignDoc->status == 'signed')
                                                                    <span class="badge bg-success text-white">Signed</span>
                                                                @endif
                                                            </td>

                                                            <td>{{ $esignDoc->docRequestByName }}</td>

                                                            <td>
                                                                @if($esignDoc->status == 'requested')
                                                                    <a href="profile/esign-document-verify/{{ $profileData->leadID }}"
                                                                        class="text-white btn btn-sm btn-success"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        data-bs-custom-class="success-tooltip"
                                                                        data-bs-title="Check Status" style="font-size:12px;">
                                                                        Check status
                                                                    </a>
                                                                @elseif($esignDoc->status == 'signed')
                                                                    <a href="{{ Storage::url('documentData/' . $profileData->contactID . '/' . $esignDoc->fileName) }}"
                                                                        target="_blank" class="text-success"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        data-bs-custom-class="success-tooltip" data-bs-title="View"
                                                                        style="font-size: 18px;">
                                                                        <i class='mdi mdi-eye'></i>
                                                                    </a>
                                                                    <a href="{{ Storage::url('documentData/' . $profileData->contactID . '/' . $esignDoc->fileName) }}"
                                                                        target="_blank" class="text-primary"
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        data-bs-custom-class="primary-tooltip"
                                                                        data-bs-title="Download"
                                                                        style="font-size: 18px;margin-left: 10px;" download>
                                                                        <i class="mdi mdi-download"></i>
                                                                    </a>
                                                                @endif
                                                            </td>

                                                            <td>{{ dft($esignDoc->addedOn) }}</td>
                                                            <td>{{ $esignDoc->updatedOn ? dft($esignDoc->updatedOn) : '--' }}
 
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="6" class="text-center">No record found</td>
                                                        </tr>
                                                    @endif
                                                </table>


                                                <!--<div class="card-header">-->
                                                <!--    <h5 class="m-0 text-center">E-Stamp Details</h5>-->
                                                <!--</div>-->
                                                <!--<table class="table table-striped">-->
                                                <!--    <tr><th>Status</th>-->
                                                <!--        <th>Requested By</th>-->
                                                <!--        <th>Documents</th>-->
                                                <!--        <th>Requested On</th>-->
                                                <!--    </tr>-->
                                                <!--    @if(!empty($esigtampDoc->fileName))-->
                                                <!--        <tr>-->
                                                <!--            <td><span class="badge bg-success text-white">Genrated</span></td>-->
                                                <!--            <td>{{getUserNameById('users', 'userID', $esigtampDoc->addedBy, 'displayName');}}</td>-->
                                                <!--            <td>-->

                                                <!--                <a href="{{ Storage::url('documentData/' . $profileData->contactID . '/' . $esigtampDoc->fileName) }}"-->
                                                <!--                    target="_blank" class="text-success"-->
                                                <!--                    data-bs-toggle="tooltip" data-bs-placement="top"-->
                                                <!--                    data-bs-custom-class="success-tooltip" data-bs-title="View"-->
                                                <!--                    style="font-size: 18px;">-->
                                                <!--                    <i class='mdi mdi-eye'></i>-->
                                                <!--                </a>-->
                                                <!--                <a href="{{ asset('storage/documentData/' . $profileData->contactID . '/' . $esigtampDoc->fileName) }}"-->
                                                <!--                    target="_blank" class="text-primary"-->
                                                <!--                    data-bs-toggle="tooltip" data-bs-placement="top"-->
                                                <!--                    data-bs-custom-class="primary-tooltip"-->
                                                <!--                    data-bs-title="Download"-->
                                                <!--                    style="font-size: 18px; margin-left: 10px;" download>-->
                                                <!--                    <i class="mdi mdi-download"></i>-->
                                                <!--                </a>-->

                                                <!--            </td>-->
                                                <!--            <td>{{df($esigtampDoc->addedOn)}}</td>-->
                                                <!--        </tr>-->
                                                    
                                                <!--    @else-->
                                                    

                                                <!--    <tr>-->
                                                <!--        <td colspan="5" class="text-center">No record found</td>-->
                                                <!--    </tr>-->
                                                    
                                                <!--    @endif-->

                                                <!--</table>-->
                                                <div class="card-header">
                                                    <h5 class="m-0 text-center">Video Kyc Details</h5>
                                                </div>
                                                <table class="table table-striped">
                                                    <tr>
                                                        @if(!empty($videoKycDoc))
                                                            @if($videoKycDoc->status == 'requested')
                                                            @elseif($videoKycDoc->status == 'approved' || $videoKycDoc->status == 'approval_pending')
                                                                <th>Action</th>
                                                            @endif
                                                        @endif
                                                        <th>Status</th>
                                                        <th>Requested By</th>
                                                        <th>Video</th>
                                                        @if(!empty($videoKycDoc))
                                                            @if($videoKycDoc->status == 'approved' || $videoKycDoc->status == 'approval_pending')
                                                                <th>CM Approval</th>
                                                            @endif
                                                        @endif
                                                        <th>Requested On</th>
                                                        <th>Signed On</th>
                                                    </tr>
                                                    @if(!empty($videoKycDoc))
                                                        <tr>
                                                            @if($videoKycDoc->status == 'requested')
                                                            @elseif($videoKycDoc->status == 'approved' || $videoKycDoc->status == 'approval_pending')
                                                                <td>
                                                                    <a target="_blank"
                                                                        href="profile/kyc-details/{{$profileData->leadID}}"
                                                                        class="text-primary" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top"
                                                                        data-bs-custom-class="primary-tooltip"
                                                                        data-bs-title="KYC Details"
                                                                        style="font-size: 16px;margin-left: 6px;">
                                                                        <i class='mdi mdi-eye'></i>
                                                                    </a>
                                                                </td>
                                                            @endif
                                                            <td>
                                                                @if($videoKycDoc->status == 'requested')
                                                                    <span class="badge bg-warning text-dark">Requested</span>
                                                                @elseif($videoKycDoc->status == 'approved' || $videoKycDoc->status == 'approval_pending')
                                                                    <span class="badge bg-success text-white">Signed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ getUserNameById('users', 'userID', $videoKycDoc->requestBy, 'displayName') }}
                                                            </td>
                                                            <td id="videoShow">
                                                                <a href="javascript:void(0);"
                                                                    class="text-white btn btn-sm btn-success"
                                                                    data-leadID="{{$profileData->leadID}}" id="verifyButton"
                                                                    style="font-size:12px;"><i
                                                                        class="mdi mdi-eye-refresh"></i></a>
                                                            </td>
                                                            
                                                             @if(!empty($videoKycDoc))
                                                                @if($videoKycDoc->status == 'approved' || $videoKycDoc->status == 'approval_pending')
                                                                  @if($videoKycDoc->cmVerified == 0)
                                                                   <td><a href="javascript:void(0);" class="text-white btn btn-sm btn-danger" data-leadID="{{$profileData->leadID}}" id="approvedButton" style="font-size:12px;">Pending Approval</a></td>
                                                                  @else
                                                                    <td><span class="badge bg-success text-white">Approved</span></td>
                                                                  @endif
                                                                @endif
                                                             @endif
                                                            
                                                            <td>{{dft($videoKycDoc->addedOn)}}</td>
                                                            <td>{{ $videoKycDoc->updatedOn ? dft($videoKycDoc->updatedOn) : '--' }}
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td colspan="8" class="text-center">No record found</td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="checklist">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card mb-0">
                                                <div class="card-body">
                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager')
                                                            @if(getUserID() == $profileData->rmID || getUserID() == $profileData->cmID)
                                                                <div class="col-auto text-end">
                                                                    <button class="btn btn-sm btn-primary ms-1" id="addChecklistButton"
                                                                        type="button">
                                                                        <i class="uil uil-plus"></i> Add
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="col-auto text-end">
                                                                <button class="btn btn-sm btn-primary ms-1" id="addChecklistButton"
                                                                    type="button">
                                                                    <i class="uil uil-plus"></i> Add
                                                                </button>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    <div class="row mt-2">
                                                        <div class="collapse mb-3" id="collapseChecklistForm">
                                                            <form action="profile/add-checklist" method="post"
                                                                class="checklist-form" autocomplete="off">
                                                                @csrf
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <select id="documentType" class="form-control"
                                                                                name="documentType">
                                                                                <option value="">Choose Document Type
                                                                                </option>
                                                                                <option value="KYC Check">KYC Check</option>
                                                                                <option value="Aadhar Card">Aadhar Card
                                                                                </option>
                                                                                <option value="Pan Card">Pan Card</option>
                                                                                <option value="Residence Proof">Residence
                                                                                    Proof</option>
                                                                                <option value="Electricity Bill">Electricity
                                                                                    Bill</option>
                                                                                <option value="Gas Bill">Gas Bill</option>
                                                                                <option value="Rent Agreement">Rent
                                                                                    Agreement</option>
                                                                                <option value="Postpaid Mobile Bill">
                                                                                    Postpaid Mobile Bill</option>
                                                                                <option value="WiFi Bill">WiFi Bill</option>
                                                                                <option
                                                                                    value="Salary Slips (Latest 3 months)">
                                                                                    Salary Slips (Latest 3 months)</option>
                                                                                <option
                                                                                    value="Bank Statement (Latest 6 months)">
                                                                                    Bank Statement (Latest 6 months)
                                                                                </option>
                                                                                <option value="Employment Proof">Employment
                                                                                    Proof</option>
                                                                                <option value="Employee ID Card">Employee ID
                                                                                    Card</option>
                                                                                <option value="Offer Letter">Offer Letter
                                                                                </option>
                                                                            </select>
                                                                            <span class="documentsTypeErr"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <input type="text" class="form-control"
                                                                                name="remark" id="checklistRemark"
                                                                                placeholder="Enter Remarks">
                                                                            <span class="remarkErr"></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 d-flex justify-content-center">
                                                                        <input type="hidden" name="id" id="checklistId">
                                                                        <button type="button" class="btn btn-danger font-16"
                                                                            id="closeChecklistButton"><i
                                                                                class="uil uil-times icon"></i></button>
                                                                        <button type="submit" id="submitChecklistBtn"
                                                                            class="btn btn-primary font-16"
                                                                            style="margin-left: 10px;"><i
                                                                                class="uil uil-check icon"></i> </button>
                                                                    </div>
                                                                    <!-- end col-md-12 -->
                                                                </div>
                                                                <!-- end row -->
                                                            </form>
                                                        </div>
                                                        <table class="table table-striped">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Documents Type</th>
                                                                <th>Mark</th>
                                                                <th>Remarks</th>
                                                                @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                    <th>Action</th>
                                                                @endif
                                                            </tr>
                                                            @if(count($checklist) > 0)
                                                                @foreach($checklist as $key => $doc)
                                                                    <tr>
                                                                        <td>{{ ++$key }}</td>
                                                                        <td>{{ $doc->documentType }}</td>
                                                                        <td>
                                                                            <div class="form-check">
                                                                                <input type="checkbox" class="form-check-input"
                                                                                    checked disabled>
                                                                            </div>
                                                                        </td>
                                                                        <td>{{ $doc->remark ?? '--' }}</td>
                                                                        <td>
                                                                            @if(role() == 'CRM Support' || isSuperAdmin())
                                                                                <a href="#" data-id="{{ $doc->id }}"
                                                                                    class="btn-primary ms-1 editChecklistBtn"
                                                                                    aria-controls="collapseExample"
                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                    data-bs-custom-class="info-tooltip"
                                                                                    data-bs-title="Edit"><i
                                                                                        class="mdi mdi-square-edit-outline"></i></a>
                                                                            @endif
                                                                            @if(isSuperAdmin())
                                                                                <a href="" class="text-danger checklistDeleteBtn"
                                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                                    data-bs-custom-class="danger-tooltip"
                                                                                    data-bs-title="Delete"
                                                                                    style="font-size: 18px;margin-left: 5px;"
                                                                                    data-id="{{ $doc->id }}"><i
                                                                                        class="mdi mdi-delete"></i></a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="5" class="text-center">No record found</td>
                                                                </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="sanction">
                                    <div class="row">
                                        @if($profileData->status=='Document Received')
                                            @if(empty($sanctionData))
                                                @if(count($documents) > 0 && count($address) > 0)
                                                    <div class="accordion custom-accordion mb-3" id="custom-accordion-sanctionPreAdd">
                                                        <div class="card mb-0">
                                                            @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isSuperAdmin() || isAdmin())
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                                    @if(getUserID() == $profileData->cmID)
                                                                        <div class="card-header" id="headingFour">
                                                                            <h5 class="m-0">
                                                                                <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                                                    href="#collapaseAddSanction" aria-expanded="true"
                                                                                    aria-controls="collapseFour">
                                                                                    Loan Sanction<i
                                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                                </a>
                                                                            </h5>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <div class="card-header" id="headingFour">
                                                                        <h5 class="m-0">
                                                                            <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                                                href="#collapaseAddSanction" aria-expanded="true"
                                                                                aria-controls="collapseFour">
                                                                                Loan Sanction<i
                                                                                    class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                            </a>
                                                                        </h5>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                            <div id="collapaseAddSanction" class="collapse"
                                                                aria-labelledby="headingFour"
                                                                data-bs-parent="#custom-accordion-address">
                                                                <div class="card-body">
                                                                    <div class="row mt-2">
                                                                        <div class="collapse mb-3 show">
                                                                            <form action="profile/add-sanction" method="post"
                                                                                class="addPreSanction-form" id="address-form"
                                                                                autocomplete="off">
                                                                                @csrf
                                                                                <div class="row">
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Loan
                                                                                                Amount</label>
                                                                                            <input type="text" class="form-control"
                                                                                                name="loanAmtApproved"
                                                                                                id="loanAmtApproved"
                                                                                                value="{{ $profileData->loanRequired}}"
                                                                                                placeholder="Approved Loan Amount"
                                                                                                autocomplete="off">
                                                                                        </div>
                                                                                        <span class="loanAmtApprovedErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Confirm Loan
                                                                                                Amount</label>
                                                                                            <input type="password" class="form-control"
                                                                                                name="confirmLoanAmtApproved"
                                                                                                id="confirmLoanAmtApproved" value=""
                                                                                                placeholder="Confirm Loan Amount"
                                                                                                autocomplete="off">
                                                                                        </div>
                                                                                        <span class="confirmLoanAmtApprovedErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Branch</label>
                                                                                            <select id="branchSelect"
                                                                                                class="form-control" name="branch">
                                                                                                <option value="">Choose Branch</option>
                                                                                                @foreach($branches as $branch)
                                                                                                    <option value="{{ $branch->cityID }}">
                                                                                                        {{ $branch->cityName }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="branchErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">ROI</label>
                                                                                            <select id="roi" class="form-control"
                                                                                                name="roi">
                                                                                                <option value="">Select ROI</option>
                                                                                                <option value="24">24 %
                                                                                                </option>
                                                                                                <option value="27">27 %
                                                                                                </option>
                                                                                                <option value="30">30 %
                                                                                                </option>
                                                                                                <option value="33">33 %
                                                                                                </option>
                                                                                                <option value="36" selected>36 %
                                                                                                </option>
                                                                                            </select>
    
                                                                                        </div>
                                                                                        <span class="roiErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">EMI Date</label>
                                                                                            <select class="form-control mb-1"
                                                                                                name="emiDate">
                                                                                                <option value="" disabled selected>
                                                                                                    Select EMI Date</option>
                                                                                                <option value="2">2nd</option>
                                                                                                <option value="5">5th</option>
                                                                                                <option value="7">7th</option>
                                                                                                <option value="10">10th</option>
                                                                                            </select>
                                                                                            <span class="emiDateErr"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Loan
                                                                                                Tenure(Months)</label>
                                                                                            <select class="form-control mb-1"
                                                                                                name="loanTenure">
                                                                                                <option value="" disabled {{ old('loanTenure', $profileData->tenure) == '' ? 'selected' : '' }}>Loan
                                                                                                    Tenure
                                                                                                </option>
                                                                                                <option value="3" {{ old('loanTenure', $profileData->tenure) == 3 ? 'selected' : '' }}>3 Months
                                                                                                </option>
                                                                                                <option value="6" {{ old('loanTenure', $profileData->tenure) == 6 ? 'selected' : '' }}>6 Months
                                                                                                </option>
                                                                                                <option value="9" {{ old('loanTenure', $profileData->tenure) == 9 ? 'selected' : '' }}>9 Months
                                                                                                </option>
                                                                                                <option value="12" {{ old('loanTenure', $profileData->tenure) == 12 ? 'selected' : '' }}>12 Months
                                                                                                </option>
                                                                                                <option value="18" {{ old('loanTenure', $profileData->tenure) == 18 ? 'selected' : '' }}>18 Months
                                                                                                </option>
                                                                                                <option value="24" {{ old('loanTenure', $profileData->tenure) == 24 ? 'selected' : '' }}>24 Months
                                                                                                </option>
                                                                                                <option value="30" {{ old('loanTenure', $profileData->tenure) == 30 ? 'selected' : '' }}>30 Months
                                                                                                </option>
                                                                                                <option value="36" {{ old('loanTenure', $profileData->tenure) == 36 ? 'selected' : '' }}>36 Months
                                                                                                </option>

                                                                                            </select>
                                                                                            <span class="loanTenureErr"></span>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Official
                                                                                                Email</label>
                                                                                            <input type="email" class="form-control"
                                                                                                name="officialEmail" id="officialEmail"
                                                                                                value="{{ $profileData->officialEmail }}"
                                                                                                placeholder="Official Email"
                                                                                                autocomplete="off">
                                                                                        </div>
                                                                                        <span class="officialEmailErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Alternate
                                                                                                Mobile</label>
                                                                                            <input class="form-control" type="number"
                                                                                                name="alternateMobile"
                                                                                                id="alternateMobile"
                                                                                                placeholder="Alternate Mobile"
                                                                                                value="{{ $profileData->alternate_mobile ?? '' }}"
                                                                                                min="0" autocomplete="off">
                                                                                        </div>
                                                                                        <span class="alternateMobileErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">PF
                                                                                                Percentage</label>
                                                                                            <input type="text" id="pf"
                                                                                                class="form-control" name="pf"
                                                                                                placeholder="PF Percentage"
                                                                                                value="5"
                                                                                                >
                                                                                        </div>
                                                                                        <span class="pfErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Admin
                                                                                                Fee</label>
                                                                                            <input class="form-control" type="number"
                                                                                                name="adminFee" id="adminFee"
                                                                                                placeholder="Admin Fee"
                                                                                                value=""
                                                                                                min="0" autocomplete="off" readonly>
                                                                                        </div>
                                                                                        <span class="adminFeeErr"></span>
                                                                                    </div>
                                                                                    
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Monthly
                                                                                                Income</label>
                                                                                            <input class="form-control" type="number"
                                                                                                name="monthlyIncome" id="monthlyIncome"
                                                                                                value="{{ $profileData->monthlyIncome }}"
                                                                                                placeholder="Monthly Income" min="0">
                                                                                        </div>
                                                                                        <span class="monthlyIncomeErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Cibil
                                                                                                Score</label>
                                                                                            <input class="form-control" type="number"
                                                                                                name="cibilScore" id="cibilScore"
                                                                                                placeholder="Cibil Score" value=""
                                                                                                min="0" step="1"
                                                                                                aria-describedby="cibilScoreHelp">
                                                                                        </div>
                                                                                        <span class="cibilScoreErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Monthly
                                                                                                Obligation</label>
                                                                                            <input class="form-control" type="number"
                                                                                                name="monthlyObligation"
                                                                                                id="monthlyObligation" value=""
                                                                                                placeholder="Monthly Obligation"
                                                                                                min="0">
                                                                                        </div>
                                                                                        <span class="monthlyObligationErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Residential
                                                                                                Type</label>
                                                                                            <select id="residential"
                                                                                                class="form-control" name="residential">
                                                                                                <option value="">Select Residential Type
                                                                                                </option>
                                                                                                <option value="Owned" {{ isset($profileData->residentialType) && $profileData->residentialType == 'Owned' ? 'selected' : '' }}>Owned</option>
                                                                                                <option value="Rented" {{ isset($profileData->residentialType) && $profileData->residentialType == 'Rented' ? 'selected' : '' }}>Rented</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="residentialErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Employment
                                                                                                Type</label>
                                                                                            <select id="employeeType"
                                                                                                class="form-control"
                                                                                                name="employeeType">
                                                                                                <option value="">Select Employment Type
                                                                                                </option>
                                                                                                <option value="Salaried" {{ old('employeeType', $profileData->customerType ?? null) == 'Salaried' ? 'selected' : '' }}>Salaried</option>
                                                                                                <option value="Self Employed" {{ old('employeeType', $profileData->customerType ?? null) == 'Self Employed' ? 'selected' : '' }}>Self Employed</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="employeeTypeErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Loan
                                                                                                Purpose</label>
                                                                                            <select id="purpose" class="form-control"
                                                                                                name="purpose">
                                                                                                <option value="" selected>Select Loan
                                                                                                    Requirements</option>
                                                                                                <option value="Household fund shortage"
                                                                                                    {{ !empty($profileData->purpose) && $profileData->purpose == 'Household fund shortage' ? 'selected' : '' }}>
                                                                                                    Household fund shortage</option>
                                                                                                <option value="Travel fund shortage" {{ !empty($profileData->purpose) && $profileData->purpose == 'Travel fund shortage' ? 'selected' : '' }}>
                                                                                                    Travel fund shortage</option>
                                                                                                <option
                                                                                                    value="Meeting immediate commitment"
                                                                                                    {{ !empty($profileData->purpose) && $profileData->purpose == 'Meeting immediate commitment' ? 'selected' : '' }}>Meeting immediate commitment
                                                                                                </option>
                                                                                                <option value="Immediate purchase" {{ !empty($profileData->purpose) && $profileData->purpose == 'Immediate purchase' ? 'selected' : '' }}>
                                                                                                    Immediate purchase</option>
                                                                                                <option value="Loan to clear bills" {{ !empty($profileData->purpose) && $profileData->purpose == 'Loan to clear bills' ? 'selected' : '' }}>
                                                                                                    Loan to clear bills</option>
                                                                                                <option value="Loan repayment" {{ !empty($profileData->purpose) && $profileData->purpose == 'Loan repayment' ? 'selected' : '' }}>Loan
                                                                                                    repayment</option>
                                                                                                <option
                                                                                                    value="Loan for paying school fees"
                                                                                                    {{ !empty($profileData->purpose) && $profileData->purpose == 'Loan for paying school fees' ? 'selected' : '' }}>Loan for paying school fees
                                                                                                </option>
                                                                                                <option value="Medical emergency" {{ !empty($profileData->purpose) && $profileData->purpose == 'Medical emergency' ? 'selected' : '' }}>
                                                                                                    Medical emergency</option>
                                                                                                <option value="Buying gadgets" {{ !empty($profileData->purpose) && $profileData->purpose == 'Buying gadgets' ? 'selected' : '' }}>Buying
                                                                                                    gadgets</option>
                                                                                                <option value="Weddings expenses" {{ !empty($profileData->purpose) && $profileData->purpose == 'Weddings expenses' ? 'selected' : '' }}>
                                                                                                    Weddings expenses</option>
                                                                                                <option value="Home interiors" {{ !empty($profileData->purpose) && $profileData->purpose == 'Home interiors' ? 'selected' : '' }}>Home
                                                                                                    interiors</option>
                                                                                                <option value="Down-payment shortfall"
                                                                                                    {{ !empty($profileData->purpose) && $profileData->purpose == 'Down-payment shortfall' ? 'selected' : '' }}>
                                                                                                    Down-payment shortfall</option>
                                                                                                <option value="Personal" {{ !empty($profileData->purpose) && $profileData->purpose == 'Personal' ? 'selected' : '' }}>
                                                                                                    Personal</option>
                                                                                                <option value="Wedding" {{ !empty($profileData->purpose) && $profileData->purpose == 'Wedding' ? 'selected' : '' }}>
                                                                                                    Wedding</option>
                                                                                                <option value="Medical" {{ !empty($profileData->purpose) && $profileData->purpose == 'Medical' ? 'selected' : '' }}>
                                                                                                    Medical</option>
                                                                                                <option value="Travel" {{ !empty($profileData->purpose) && $profileData->purpose == 'Travel' ? 'selected' : '' }}>
                                                                                                    Travel</option>
                                                                                                <option value="Loan Payment" {{ !empty($profileData->purpose) && $profileData->purpose == 'Loan Payment' ? 'selected' : '' }}>Loan
                                                                                                    Payment</option>
                                                                                                <option value="Bill Payment" {{ !empty($profileData->purpose) && $profileData->purpose == 'Bill Payment' ? 'selected' : '' }}>Bill
                                                                                                    Payment</option>
                                                                                                <option value="Others" {{ !empty($profileData->purpose) && $profileData->purpose == 'Others' ? 'selected' : '' }}>
                                                                                                    Others</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="purposeErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Salary
                                                                                                Accounts</label>
                                                                                            <select
                                                                                                class="select2 form-control select2-multiple banksShow"
                                                                                                data-toggle="select2"
                                                                                                multiple="multiple" name="bank[]"
                                                                                                data-placeholder="Choose Salary Accounts">
                                                                                                <option value="">Choose Salary Accounts
                                                                                                </option>
                                                                                              
                                                                                                @foreach ($banks as $bank => $id)
                                                                                                    <option value="{{ $id }}">{{ $bank }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="bankErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Field Verification</label>
                                                                                            <select class="form-control" name="fiChoice"
                                                                                                id="fiChoice">
                                                                                                <option value="">Choose Field Verification</option>
                                                                                                <option value="0">With FV</option>
                                                                                                <option value="1">Without FV</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="fiChoiceErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3" id="pdPersonSection"
                                                                                        style="display: none;">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">PD
                                                                                                Person's</label>
                                                                                            <select class="form-control" name="pdPerson"
                                                                                                data-placeholder="Choose Field Verification Person">
                                                                                                <option value="">Choose Field Verification Person
                                                                                                </option>
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="pdPersonErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Remark</label>
                                                                                            <textarea name="remark" class="form-control"
                                                                                                placeholder="Remarks"
                                                                                                rows="2"></textarea>
                                                                                        </div>
                                                                                        <span class="remarkErr"></span>
                                                                                    </div>
                                                                                    <div
                                                                                        class="col-md-12 d-flex justify-content-center">
                                                                                        <button type="button"
                                                                                            class="btn btn-danger font-16"
                                                                                            id="closeAddSanctionButton"><i
                                                                                                class="uil uil-times icon"></i>
                                                                                        </button>
                                                                                        <button type="submit" id="submitAddSanctionBtn"
                                                                                            class="btn btn-primary font-16"
                                                                                            style="margin-left: 10px;"><i
                                                                                                class="uil uil-check icon"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion custom-accordion mb-3" id="custom-accordion-sanctionReject">
                                                        <div class="card mb-0">
                                                            @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isSuperAdmin() || isAdmin())
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                                    @if(getUserID() == $profileData->cmID)
                                                                        <div class="card-header" id="headingFour">
                                                                            <h5 class="m-0">
                                                                                <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                                                    href="#collapaseRejectSanction" aria-expanded="true"
                                                                                    aria-controls="collapseFour">
                                                                                    Loan Rejection<i
                                                                                        class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                                </a>
                                                                            </h5>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <div class="card-header" id="headingFour">
                                                                        <h5 class="m-0">
                                                                            <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                                                href="#collapaseRejectSanction" aria-expanded="true"
                                                                                aria-controls="collapseFour">
                                                                                Loan Rejection<i
                                                                                    class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                            </a>
                                                                        </h5>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                            <div id="collapaseRejectSanction" class="collapse"
                                                                aria-labelledby="headingFour"
                                                                data-bs-parent="#custom-accordion-address">
                                                                <div class="card-body">
                                                                    <div class="row mt-2">
                                                                        <div class="collapse mb-3 show">
                                                                            <form action="profile/reject-sanction" method="post"
                                                                                class="rejectSanction-form" id="address-form"
                                                                                autocomplete="off">
                                                                                @csrf
                                                                                <div class="row">
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Branches</label>
                                                                                            <select id="roi" class="form-control"
                                                                                                name="branch">
                                                                                                <option value="">Choose Branches
                                                                                                </option>
                                                                                                @foreach($branches as $branch)
                                                                                                    <option value="{{ $branch->cityID }}">
                                                                                                        {{ $branch->cityName }}
                                                                                                    </option>
                                                                                                @endforeach
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="branchErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Personal
                                                                                                Email</label>
                                                                                            <input type="email" class="form-control"
                                                                                                name="officialEmail" id="officialEmail"
                                                                                                placeholder="Personal Email" value="{{$profileData->email}}">
                                                                                        </div>
                                                                                        <span class="officialEmailErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Cibil
                                                                                                Score</label>
                                                                                            <input class="form-control" type="number"
                                                                                                name="cibilScore" id="cibilScore"
                                                                                                placeholder="Cibil Score" min="0"
                                                                                                step="1"
                                                                                                aria-describedby="cibilScoreHelp">
                                                                                        </div>
                                                                                        <span class="cibilScoreErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Rejection
                                                                                                Reasons</label>
                                                                                            <select
                                                                                                class="select2 form-control select2-multiple"
                                                                                                data-toggle="select2"
                                                                                                multiple="multiple"
                                                                                                name="rejectionReason[]" id=""
                                                                                                data-placeholder="Choose Rejection Reasons">
                                                                                                <option value="">Select Reason</option>
                                                                                                <option
                                                                                                    value="High Pay Day exposure">
                                                                                                    High Pay Day exposure</option>
                                                                                                <option
                                                                                                    value="Less Salary">
                                                                                                    Less Salary</option>
                                                                                                <option
                                                                                                    value="CIBIL < 720">
                                                                                                     CIBIL <7 20</option> 
                                                                                                <option
                                                                                                    value="Rented house without renewal agreement &amp; huge liabilities">
                                                                                                    Rented house without renewal
                                                                                                    agreement &amp; huge liabilities
                                                                                                </option>
                                                                                                <option
                                                                                                    value="Applicant residing in PG">
                                                                                                    Applicant residing in PG</option>
                                                                                                <option
                                                                                                    value="No proper current address proof">
                                                                                                    No proper current address proof
                                                                                                </option>
                                                                                                <option
                                                                                                    value="Low employment tenure also variations in salary">
                                                                                                    Low employment tenure also
                                                                                                    variations in salary</option>
                                                                                                <option
                                                                                                    value="CIBIL Bureau negative, Suit filed case, OD &amp; very high DPDs in recent tracks including PL">
                                                                                                    CIBIL Bureau negative, Suit filed
                                                                                                    case, OD &amp; very high DPDs in
                                                                                                    recent tracks including PL</option>
                                                                                                <option
                                                                                                    value="No proper employment proof">
                                                                                                    No proper employment proof</option>
                                                                                                <option
                                                                                                    value="Applicant not okay with ROI/processing fee/sanctioned amount">
                                                                                                    Applicant not okay with
                                                                                                    ROI/processing fee/sanctioned amount
                                                                                                </option>
                                                                                                <option
                                                                                                    value="Edited documents/fraudulent documents">
                                                                                                    Edited documents/fraudulent
                                                                                                    documents </option>
                                                                                                <option
                                                                                                    value="No proper response since long time">
                                                                                                    No proper response since long time
                                                                                                </option>
                                                                                                <option value="Repayment issue">
                                                                                                    Repayment issue</option>
                                                                                                <option
                                                                                                    value="Negative area/community dominated area">
                                                                                                    Negative area/community dominated
                                                                                                    area</option>
                                                                                                <option
                                                                                                    value="Many bounces in last few months in banking">
                                                                                                    Many bounces in last few months in
                                                                                                    banking</option>
                                                                                                <option
                                                                                                    value="Others">
                                                                                                    Others</option>    
                                                                                            </select>
                                                                                        </div>
                                                                                        <span class="rejectionReasonErr"></span>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="mb-2">
                                                                                            <label class="mb-1 font-12">Remark</label>
                                                                                            <textarea name="remarks"
                                                                                                class="form-control"
                                                                                                placeholder="Remarks"
                                                                                                rows="2"></textarea>
                                                                                        </div>
                                                                                        <span class="remarkErr"></span>
                                                                                    </div>
                                                                                    <div
                                                                                        class="col-md-12 d-flex justify-content-center">
                                                                                        <button type="button"
                                                                                            class="btn btn-danger font-16"
                                                                                            id="closeRejectSanctionButton"><i
                                                                                                class="uil uil-times icon"></i>
                                                                                        </button>
                                                                                        <button type="submit"
                                                                                            id="submitRejectSanctionBtn"
                                                                                            class="btn btn-primary font-16"
                                                                                            style="margin-left: 10px;"><i
                                                                                                class="uil uil-check icon"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-danger container justify-content-center w-100" role="alert"
                                                        style="width:81%!important;">
                                                        <strong>Important! - </strong> "You must provide document & address before
                                                        proceeding with the pre-sanction and the lead status should be document
                                                        received."
                                                    </div>
                                                @endif
                                            @endif
                                        @else
                                            @if(!empty($rejectionData))

                                            @elseif(empty($sanctionData))
                                                <div class="alert alert-danger container justify-content-center w-100" role="alert"
                                                    style="width:81%!important;text-align:center;">
                                                    <strong>Important! - </strong> " The lead status must be set to 'Document Received' in order to proceed."
                                                </div>
                                            @endif    
                                        @endif    
                                        @if(!empty($rejectionData))
                                            <div class="row">
                                                <div class="card-header" id="headingFour">
                                                    <div class="row">
                                                        <div class="col-12 text-end">
                                                            @if($profileData->status == 'Rejected')
                                                                @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isSuperAdmin() || isAdmin())
                                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                                        @if(getUserID() == $profileData->cmID)
                                                                            <a href="javascript:void(0);"
                                                                                class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                                data-lead-id="{{ $profileData->leadID }}"
                                                                                data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                                data-bs-placement="top" data-bs-title="Rejection Mail"><i
                                                                                    class="mdi mdi-email"></i></a>
                                                                        @endif
                                                                    @else
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                            data-lead-id="{{ $profileData->leadID }}"
                                                                            data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                            data-bs-placement="top" data-bs-title="Rejection Mail"><i
                                                                                class="mdi mdi-email"></i></a>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ml-auto">
                                                    <table class="table table-bordered ml-1">
                                                        <tr class="bg-primary">
                                                            <th colspan="7" class="text-center  text-white">Rejection Details
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>Branch</th>
                                                            <th>Official Email</th>
                                                            <th>Cibil Score</th>
                                                            <th>Rejection Reason</th>
                                                            <th>Remarks</th>
                                                            <th>Rejected By</th>
                                                            <th>Rejected Date</th>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                {{ getUserNameById('lms_cities', 'cityID', $rejectionData->branch, 'cityName')}}
                                                            </td>
                                                            <td>{{ $rejectionData->officialEmail}}</td>
                                                            <td>{{ $rejectionData->cibil ?? '-' }}</td>
                                                            <td>
                                                                @php $rejectReasons = json_decode($rejectionData->rejectionReason); @endphp
                                                                <ul>
                                                                    @foreach($rejectReasons as $key => $reasons)
                                                                        <li>{{ $key + 1 }}. {{ $reasons }}.</li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                            <td>{{ $rejectionData->remarks}}</td>
                                                            <td>
                                                                {{ getUserNameById('users', 'userID', $rejectionData->addedBy, 'displayName')}}
                                                            </td>
                                                            <td>{{ df($rejectionData->createdDate ?? '-') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        @if(!empty($sanctionData))
                                            <div class="col-12 text-end">
                                                @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Accounts Executive' || role() == 'Credit Manager' || isSuperAdmin() || isAdmin())
                                                    @if($profileData->status == 'Pending For Approval')
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                            @if(getUserID() == $profileData->cmID)
                                                                <button class="btn btn-sm btn-primary ms-1" id="updateSanctionButton"
                                                                    type="button">
                                                                    <i class="mdi mdi-square-edit-outline"></i>
                                                                </button>
                                                            @endif
                                                        @else
                                                            <button class="btn btn-sm btn-primary ms-1" id="updateSanctionButton"
                                                                type="button">
                                                                <i class="mdi mdi-square-edit-outline"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                    @if(!empty($sanctionData))
                                                       @if(role() != 'Recovery Executive' || role() != 'Recovery Manager' || role() != 'Sr. Recovery Manager')
                                                            <a href="profile-cashpey/loan/{{$profileData->leadID}}"
                                                            class="btn btn-sm btn-success" data-message="View Loan Profile"
                                                            data-bs-custom-class="success-tooltip" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" data-bs-title="View Loan Profile"><i
                                                                class="mdi mdi-eye"></i></a>
                                                        @endif
                                                    @endif
                                                    @if(!empty($esignDoc) && $esignDoc->loanStatus == 'Rejected' && $profileData->status == 'Rejected')
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                            @if(getUserID() == $profileData->cmID)
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                    data-lead-id="{{ $profileData->leadID }}"
                                                                    data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                data-lead-id="{{ $profileData->leadID }}"
                                                                data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                <i class="mdi mdi-email"></i>
                                                            </a>
                                                        @endif
                                                    @elseif($profileData->status == 'Rejected')
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                            @if(getUserID() == $profileData->cmID)
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                    data-lead-id="{{ $profileData->leadID }}"
                                                                    data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                data-lead-id="{{ $profileData->leadID }}"
                                                                data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                <i class="mdi mdi-email"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                                @if($profileData->status == 'Pending For Approval')
                                                    @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                        @if(getUserID() == $profileData->cmID)
                                                            <a href="javascript:void(0);" class="btn btn-sm btn-warning send-sanction-mail"
                                                                data-lead-id="{{ $profileData->leadID }}"
                                                                data-bs-custom-class="warning-tooltip" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Pre Approval Mail">
                                                                <i class="mdi mdi-email"></i>
                                                            </a>
                                                        @endif
                                                    @else
                                                        <a href="javascript:void(0);" class="btn btn-sm btn-warning send-sanction-mail"
                                                            data-lead-id="{{ $profileData->leadID }}"
                                                            data-bs-custom-class="warning-tooltip" data-bs-toggle="tooltip"
                                                            data-bs-placement="top" data-bs-title="Pre Approval Mail">
                                                            <i class="mdi mdi-email"></i>
                                                        </a>
                                                    @endif
                                                @endif

                                            </div>
                                            <div class="row">
                                                <div class="collapse mb-2" id="collapseSanctionUpdate">
                                                    <form action="profile/update-sanction" method="post"
                                                        class="updateSanction-form" autocomplete="off">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Loan Amount</label>
                                                                    <input type="text" class="form-control"
                                                                        name="loanAmtApproved" id="loanAmtApproved"
                                                                        placeholder="Approved Loan Amount"
                                                                        value="{{ $sanctionData->loanAmtApproved ?? '' }}">
                                                                </div>
                                                                <span class="loanAmtApprovedErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Confirm Loan Amount</label>
                                                                    <input type="password" class="form-control"
                                                                        name="confirmLoanAmtApproved"
                                                                        id="confirmLoanAmtApproved"
                                                                        placeholder="Confirm Loan Amount"
                                                                        value="{{ $sanctionData->loanAmtApproved ?? '' }}">
                                                                </div>
                                                                <span class="confirmLoanAmtApprovedErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Branch</label>
                                                                    <select class="form-control" name="branch">
                                                                        <option value="">Choose Branch</option>
                                                                        @foreach($branches as $branch)
                                                                            <option value="{{ $branch->cityID }}" {{ isset($sanctionData) && intval($sanctionData->branch) == intval($branch->cityID) ? 'selected' : '' }}>
                                                                                {{ $branch->cityName }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <span class="branchErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">ROI</label>
                                                                    <select step="any" class="form-control" name="roi">
                                                                        <option value="24" {{ old('roi', isset($sanctionData) && $sanctionData->roi == 24 ? 'selected' : '') }}>24 %
                                                                        </option>
                                                                        <option value="27" {{ old('roi', isset($sanctionData) && $sanctionData->roi == 27 ? 'selected' : '') }}>27 %
                                                                        </option>
                                                                        <option value="30" {{ old('roi', isset($sanctionData) && $sanctionData->roi == 30 ? 'selected' : '') }}>30 %
                                                                        </option>
                                                                        <option value="33" {{ old('roi', isset($sanctionData) && $sanctionData->roi == 33 ? 'selected' : '') }}>33 %
                                                                        </option>
                                                                        <option value="36" {{ old('roi', isset($sanctionData) && $sanctionData->roi == 36 ? 'selected' : '') }} selected>36 %
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <span class="roiErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">EMI Date</label>
                                                                    <select class="form-control mb-1" name="emiDate" required>
                                                                        <option value="" disabled selected>Select EMI Date
                                                                        </option>
                                                                        <option value="2" {{ $sanctionData->repayDay == '2' ? 'selected' : '' }}>
                                                                            2nd</option>
                                                                        <option value="5" {{ $sanctionData->repayDay == '5' ? 'selected' : '' }}>
                                                                            5th</option>
                                                                        <option value="7" {{ $sanctionData->repayDay == '7' ? 'selected' : '' }}>
                                                                            7th</option>
                                                                        <option value="10" {{ $sanctionData->repayDay == '10' ? 'selected' : '' }}>10th</option>
                                                                    </select>
                                                                    <span class="emiDateErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Loan Tenure(Months)</label>
                                                                    <select class="form-control mb-1" name="loanTenure"
                                                                        required>
                                                                        <option value="" disabled selected>Loan Tenure</option>
                                                                        <option value="3" {{ $sanctionData->tenure == '3' ? 'selected' : '' }}>3
                                                                            Months</option>
                                                                        <option value="6" {{ $sanctionData->tenure == '6' ? 'selected' : '' }}>6
                                                                            Months</option>
                                                                        <option value="9" {{ $sanctionData->tenure == '9' ? 'selected' : '' }}>9
                                                                            Months</option>
                                                                        <option value="12" {{ $sanctionData->tenure == '12' ? 'selected' : '' }}>
                                                                            12 Months</option>
                                                                        <option value="18" {{ $sanctionData->tenure == '18' ? 'selected' : '' }}>
                                                                            18 Months</option>     
                                                                        <option value="24" {{ $sanctionData->tenure == '24' ? 'selected' : '' }}>
                                                                            24 Months</option>
                                                                        <option value="30" {{ $sanctionData->tenure == '30' ? 'selected' : '' }}>
                                                                            30 Months</option>
                                                                        <option value="36" {{ $sanctionData->tenure == '36' ? 'selected' : '' }}>
                                                                            36 Months</option>    
                                                                    </select>
                                                                    <span class="loanTenureErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Official Email</label>
                                                                    <input type="email" class="form-control"
                                                                        name="officialEmail" id="officialEmail"
                                                                        placeholder="Official Email"
                                                                        value="{{ $sanctionData->officialEmail ?? '' }}">
                                                                </div>
                                                                <span class="officialEmailErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Alternate Mobile</label>
                                                                    <input class="form-control" type="number"
                                                                        name="alternateMobile" id="alternateMobile"
                                                                        placeholder="Alternate Mobile"
                                                                        value="{{ $sanctionData->alternateMobile ?? '' }}"
                                                                        min="0" <?php    if (role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isAdmin() || isSuperAdmin()): ?>
                                                                        value="{{ $sanctionData->alternateMobile ?? '' }}" <?php    else: ?> disabled <?php    endif; ?>>
                                                                </div>
                                                                <span class="alternateMobileErr"></span>
                                                            </div>
                                                             <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">PF Percentage</label>
                                                                    <input type="text" id="pf" class="form-control"
                                                                        name="pf" value="{{$sanctionData->pfPercentage}}">
                                                                </div>
                                                                <span class="pfErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Admin Fee</label>
                                                                    <input class="form-control" type="number" name="adminFee"
                                                                        id="adminFee" placeholder="Admin Fee"
                                                                        value="{{ $sanctionData->adminFee ?? '' }}" readonly min="0">
                                                                </div>
                                                                <span class="adminFeeErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Monthly Income</label>
                                                                    <input class="form-control" type="number"
                                                                        name="monthlyIncome" id="monthlyIncome"
                                                                        placeholder="Monthly Income"
                                                                        value="{{ $sanctionData->monthlyIncome ?? '' }}" min="0"
                                                                        <?php    if (role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isAdmin() || isSuperAdmin()): ?> readonly <?php    else: ?> disabled
                                                                        <?php    endif; ?>>
                                                                </div>
                                                                <span class="monthlyIncomeErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Cibil</label>
                                                                    <input class="form-control" type="number" name="cibilScore"
                                                                        id="cibilScore" placeholder="Cibil Score"
                                                                        value="{{ $sanctionData->cibil ?? '' }}" min="0" <?php    if (role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isAdmin() || isSuperAdmin()): ?> readonly <?php    else: ?> disabled
                                                                        <?php    endif; ?>>
                                                                </div>
                                                                <span class="cibilScoreErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Monthly Obligation</label>
                                                                    <input class="form-control" type="number"
                                                                        name="monthlyObligation" id="monthlyObligation"
                                                                        placeholder="Monthly Obligation"
                                                                        value="{{ $sanctionData->monthlyObligation ?? '' }}"
                                                                        min="0" <?php    if (role() == 'CRM Support' || isSuperAdmin()): ?> readonly <?php    else: ?> disabled
                                                                        <?php    endif; ?>>
                                                                </div>
                                                                <span class="monthlyObligationErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Residential Type</label>
                                                                    <select id="residential" class="form-control"
                                                                        name="residential" <?php    if (role() == 'CRM Support' || isSuperAdmin()): ?> <?php    else: ?> disabled <?php    endif; ?>>
                                                                        <option value="">Select Residential Type</option>
                                                                        <option value="Owned" {{ $sanctionData->residentialType ?? '' == 'Owned' ? 'selected' : '' }}>Owned</option>
                                                                        <option value="Rented" {{ $sanctionData->residentialType ?? '' == 'Rented' ? 'selected' : '' }}>Rented</option>
                                                                    </select>
                                                                </div>
                                                                <span class="residentialErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Employment Type</label>
                                                                    <select id="employeeType" class="form-control"
                                                                        name="employeeType" <?php    if (role() == 'CRM Support' || isSuperAdmin()): ?> <?php    else: ?> disabled <?php    endif; ?>>
                                                                        <option value="">Select Employment Type</option>
                                                                        <option value="Salaried" {{ $sanctionData->employed ?? '' == 'Salaried' ? 'selected' : '' }}>Salaried
                                                                        </option>
                                                                        <option value="Self" {{ $sanctionData->employed ?? '' == 'Self' ? 'selected' : '' }}>Self</option>
                                                                    </select>
                                                                </div>
                                                                <span class="employeeTypeErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Loan Purpose</label>
                                                                    <select id="purpose" class="form-control" name="purpose"
                                                                        <?php    if (role() == 'CRM Support' || isSuperAdmin()): ?>
                                                                        <?php    else: ?> disabled <?php    endif; ?>>
                                                                        <option value="">Select Loan Requirements</option>
                                                                        <option value="Household fund shortage" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Household fund shortage' ? 'selected' : '' }}>Household fund
                                                                            shortage</option>
                                                                        <option value="Travel fund shortage" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Travel fund shortage' ? 'selected' : '' }}>Travel fund shortage
                                                                        </option>
                                                                        <option value="Meeting immediate commitment" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Meeting immediate commitment' ? 'selected' : '' }}>Meeting
                                                                            immediate commitment</option>
                                                                        <option value="Immediate purchase" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Immediate purchase' ? 'selected' : '' }}>Immediate purchase
                                                                        </option>
                                                                        <option value="Loan to clear bills" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Loan to clear bills' ? 'selected' : '' }}>Loan to clear bills
                                                                        </option>
                                                                        <option value="Loan repayment" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Loan repayment' ? 'selected' : '' }}>Loan repayment</option>
                                                                        <option value="Loan for paying school fees" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Loan for paying school fees' ? 'selected' : '' }}>Loan for paying
                                                                            school fees</option>
                                                                        <option value="Medical emergency" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Medical emergency' ? 'selected' : '' }}>Medical emergency
                                                                        </option>
                                                                        <option value="Buying gadgets" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Buying gadgets' ? 'selected' : '' }}>Buying gadgets</option>
                                                                        <option value="Weddings expenses" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Weddings expenses' ? 'selected' : '' }}>Weddings expenses
                                                                        </option>
                                                                        <option value="Home interiors" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Home interiors' ? 'selected' : '' }}>Home interiors</option>
                                                                        <option value="Down-payment shortfall" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Down-payment shortfall' ? 'selected' : '' }}>Down-payment
                                                                            shortfall</option>
                                                                        <option value="Personal" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Personal' ? 'selected' : '' }}>
                                                                            Personal</option>
                                                                        <option value="Wedding" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Wedding' ? 'selected' : '' }}>
                                                                            Wedding</option>
                                                                        <option value="Medical" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Medical' ? 'selected' : '' }}>
                                                                            Medical</option>
                                                                        <option value="Travel" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Travel' ? 'selected' : '' }}>
                                                                            Travel</option>
                                                                        <option value="Loan Payment" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Loan Payment' ? 'selected' : '' }}>Loan Payment</option>
                                                                        <option value="Bill Payment" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Bill Payment' ? 'selected' : '' }}>Bill Payment</option>
                                                                        <option value="Others" {{ !empty($sanctionData->loanRequirePurpose) && $sanctionData->loanRequirePurpose == 'Others' ? 'selected' : '' }}>
                                                                            Others</option>
                                                                    </select>
                                                                </div>
                                                                <span class="purposeErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Salary Accounts</label>
                                                                    <select
                                                                        class="select2 form-control select2-multiple banksShow"
                                                                        data-toggle="select2" name="bank[]"
                                                                        id="reportingManagers"
                                                                        data-placeholder="Choose Salary Accounts" <?php    if (role() == 'CRM Support' || isSuperAdmin()): ?> <?php    else: ?> disabled <?php    endif; ?> multiple="multiple">
                                                                        <option value="">Choose Salary Accounts</option>
                                                                        @php $bankNames = explode(',', $sanctionData->bankName ?? ''); @endphp
                                                                        @foreach ($banks as $bank => $id)
                                                                            <option value="{{ $id }}" {{ in_array($id, $bankNames) ? 'selected' : '' }}>{{ $bank }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <span class="bankErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Credit Status</label>
                                                                    <select id="creditStatus" class="form-control"
                                                                        name="creditStatus">
                                                                        <option value="" {{ empty($sanctionData->creditStatus) ? 'selected' : '' }}>Choose Credit Status</option>
                                                                        @if($sanctionData->creditStatus != 'Pending For Approval' || isSuperAdmin())
                                                                            <option value="Approved" {{ !empty($sanctionData->creditStatus) && $sanctionData->creditStatus == 'Approved' ? 'selected' : '' }} {{ role() != 'Super Admin' ? 'readonly' : '' }}>
                                                                                Approved
                                                                            </option>
                                                                        @endif
                                                                        <option value="Rejected" {{ !empty($sanctionData->creditStatus) && $sanctionData->creditStatus == 'Rejected' ? 'selected' : '' }}>
                                                                            Rejected
                                                                        </option>
                                                                        @if($sanctionData->creditStatus == 'Pending For Approval' || isSuperAdmin())
                                                                            <option value="Pending For Approval" readonly {{ !empty($sanctionData->creditStatus) && $sanctionData->creditStatus == 'Pending For Approval' ? 'selected' : '' }}>
                                                                                Pending For Approval
                                                                            </option>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <span class="creditStatusErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">FI By</label>
                                                                    <select class="form-control" name="fiChoice" id="fiChoice">
                                                                        <option value="">Choose FI By</option>
                                                                        <option value="0" {{ (isset($sanctionData->pdVerification) && $sanctionData->pdVerification == 0) ? 'selected' : '' }}>With FV
                                                                        </option>
                                                                        <option value="1" {{ (isset($sanctionData->pdVerification) && $sanctionData->pdVerification == 1) ? 'selected' : '' }}>Without FV
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <span class="fiChoiceErr"></span>
                                                            </div>
                                                            <div class="col-md-3" id="pdPersonSection">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">FV Person's</label>
                                                                    <select class="form-control" name="pdPerson"
                                                                        data-placeholder="Choose FV persons">
                                                                        <option value="">Choose FV Person</option>
                                                                        @foreach ($pdTeamUsers as $user)
                                                                            <option value="{{ $user->userID }}" {{ isset($sanctionData) && $user->userID == $sanctionData->pdVerifiedBy ? 'selected' : '' }}>
                                                                                {{ $user->displayName }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <span class="pdPersonErr"></span>
                                                            </div>
                                                            <div id="rejectionReasonDiv" class="col-md-3"
                                                                style="display: none;">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Rejection Reason</label>
                                                                    <select class="select2 form-control select2-multiple"
                                                                        data-toggle="select2" multiple="multiple"
                                                                        name="rejectionReason[]"
                                                                        data-placeholder="Choose Rejection Reasons">
                                                                        <option
                                                                            value="Rented house without renewal agreement &amp; huge liabilities">
                                                                            Rented house without renewal agreement &amp; huge
                                                                            liabilities</option>
                                                                        <option value="Applicant residing in PG">Applicant
                                                                            residing in PG</option>
                                                                        <option value="No proper current address proof">No
                                                                            proper current address proof</option>
                                                                        <option
                                                                            value="Low employment tenure also variations in salary">
                                                                            Low employment tenure also variations in salary
                                                                        </option>
                                                                        <option
                                                                            value="CIBIL Bureau negative, Suit filed case, OD &amp; very high DPDs in recent tracks including PL">
                                                                            CIBIL Bureau negative, Suit filed case, OD &amp;
                                                                            very high DPDs in recent tracks including PL
                                                                        </option>
                                                                        <option value="No proper employment proof">No proper
                                                                            employment proof</option>
                                                                        <option
                                                                            value="Applicant not okay with ROI/processing fee/sanctioned amount">
                                                                            Applicant not okay with ROI/processing
                                                                            fee/sanctioned amount</option>
                                                                        <option value="Edited documents/fraudulent documents">
                                                                            Edited documents/fraudulent documents </option>
                                                                        <option value="No proper response since long time">No
                                                                            proper response since long time</option>
                                                                        <option value="Repayment issue">Repayment issue</option>
                                                                        <option value="Negative area/community dominated area">
                                                                            Negative area/community dominated area</option>
                                                                        <option
                                                                            value="Many bounces in last few months in banking">
                                                                            Many bounces in last few months in banking</option>
                                                                    </select>
                                                                </div>
                                                                <span class="rejectionReasonErr"></span>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="mb-2">
                                                                    <label class="mb-1 font-12">Remark</label>
                                                                    <textarea name="remark" class="form-control"
                                                                        placeholder="Remarks"
                                                                        rows="2">{{ $sanctionData->remark ?? ''}}</textarea>
                                                                </div>
                                                                <span class="remarkErr"></span>
                                                            </div>
                                                            <div class="col-md-12 d-flex justify-content-center">
                                                                <button type="button" class="btn btn-danger font-16"
                                                                    id="closeSanctionUpdateButton"><i
                                                                        class="uil uil-times icon"></i> </button>
                                                                <button type="submit" id="submitSanctionUpdateBtn"
                                                                    class="btn btn-primary font-16"
                                                                    style="margin-left: 10px;"><i
                                                                        class="uil uil-check icon"></i> </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="ml-auto">
                                                    <div class="table-responsive" data-simplebar data-simplebar-primary>
                                                        <table class="table table-bordered ml-1">
                                                            <tr class="bg-primary">
                                                                <th colspan="7" class="text-center text-white">Sanction Details
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Approval Amount</th>
                                                                <th>PF. %</th>
                                                                <th>Admin Fee</th>
                                                                <th>GST of PF.</th>
                                                                <th>Stamp Duty</th>
                                                                <th>PRE-EMI Interest</th>
                                                                <th>Amount to be Disbursed</th>
                                                                
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    {{ !empty($sanctionData->loanAmtApproved) ? nf($sanctionData->loanAmtApproved) : '-' }}
                                                                </td>
                                                                <td>{{ $sanctionData->pfPercentage ?? '-' }} %</td>
                                                                <td>{{ !empty($sanctionData->adminFee) ? nf($sanctionData->adminFee) : '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ !empty($sanctionData->adminGstAmount) ? nf($sanctionData->adminGstAmount) : '-' }}
                                                                </td>
                                                                <td>{{$sanctionData->stampDuty}}</td>
                                                                @php
                                                                    $preEmi = 0;
                                                                    $preEmiText = '';

                                                                    if ($sanctionData->preEmiInterestDaysDiff > 30) {
                                                                        $preEmiText = $sanctionData->preEmiInterest . ' (Recieveable)';
                                                                    } else {
                                                                        $preEmiText = $sanctionData->preEmiInterest . ' (Payable)';
                                                                    }

                                                                    $daysLabel = ($sanctionData->preEmiInterestDays == 1) ? 'Day' : 'Days';
                                                                @endphp

                                                                <td>{{ $preEmiText }} {{ $sanctionData->preEmiInterestDays }} {{ $daysLabel }}</td>
                                                                <td>{{ nf($sanctionData->disbursementAmount) }}</td>
                                                               
                                                            </tr>
                                                            <tr>
                                                                <th>Monthly Income</th>
                                                                <th>Monthly Obligation</th>
                                                                <th>Sanction Date</th>
                                                                <th>Sanction By</th>
                                                                <th>Branch</th>
                                                                <th>Employed</th>
                                                                <th>Official Email</th>
                                                              
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    {{ !empty($sanctionData->monthlyIncome) ? nf($sanctionData->monthlyIncome) : '-' }}
                                                                </td>
                                                                 <td>
                                                                    {{ !empty($sanctionData->monthlyObligation) ? nf($sanctionData->monthlyObligation) : '-' }}
                                                                </td>
                                                               
                                                                <td>{{ isset($sanctionData->addedOn) ? dft($sanctionData->addedOn) : '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ getUserNameById('users', 'userID', $sanctionData->creditedBy, 'displayName')}}
                                                                </td>
                                                                <td>
                                                                    {{ getUserNameById('lms_cities', 'cityID', $sanctionData->branch, 'cityName') }}
                                                                </td>
                                                                <td>{{ $sanctionData->employed ?? '-' }}</td>
                                                                <td>{{ $sanctionData->officialEmail ?? '-' }}</td>
                                                                
                                                            </tr>
                                                            <tr>
                                                                  <th>Alternate Mobile</th>
                                                                <th>Loan Required for</th>
                                                                <th>Cibil Score</th>
                                                                <th>Residential Type</th>
                                                                <th>FI</th>
                                                                <th>FI Assigned</th>
                                                                <th>Bank</th>
                                                                {{-- <th>Tenure</th> --}}
                                                            </tr>
                                                            <tr>

                                                                <td>{{ $sanctionData->alternateMobile ?? '-' }}</td>
                                                                <td>{{ $sanctionData->loanRequirePurpose ?? '-' }}</td>
                                                                <td>{{ $sanctionData->cibil ?? '-' }}</td>
                                                                <td>{{ $sanctionData->residentialType ?? '-' }}</td>
                                                                <td>
                                                                    @if($sanctionData->pdVerification == '0')
                                                                        {{ 'With FV' }}
                                                                    @else
                                                                        {{ 'Without FV' }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ getUserNameById('users', 'userID', $sanctionData->pdVerifiedBy, 'displayName')}}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $bankNames = !empty($sanctionData->bankName) ? explode(',', $sanctionData->bankName) : [];
                                                                       @endphp
                                                                    @if(!empty($bankNames) && count($bankNames) > 0)
                                                                        @foreach ($bankNames as $key => $id)
                                                                            {{ ++$key . '. ' . getUserNameById('lms_banks', 'id', $id, 'bank') }}<br>
                                                                        @endforeach
                                                                    @else
                                                                        {{ '-' }}
                                                                    @endif
                                                                </td>
                                                                {{-- <td>{{ $sanctionData->tenure ?? '-' }}</td> --}}
                                                            </tr>
                                                            <tr>
                                                                <th>Sanction Status</th>
                                                                <th>Sanction Remarks</th>
                                                                <th>Final Remarks</th>
                                                                <th>Rejection Reason</th>
                                                                <th>Matrix Status</th>
                                                                <th>Matrix Approved By</th>
                                                                <th>Matrix Remarks</th>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ $sanctionData->status ?? '-' }}</td>
                                                                <td>{{ $sanctionData->remark ?? '-' }}</td>
                                                                <td>{{ $sanctionData->finalRemarks ?? '-' }}</td>
                                                                <td>
                                                                    @foreach((array) json_decode($sanctionData->rejectionReason, true) as $reason)
                                                                        {{ $loop->iteration }}. {{ $reason }}.<br>
                                                                    @endforeach
                                                                    @if(empty($sanctionData->rejectionReason))
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $sanctionData->creditStatus ?? '-' }}</td>
                                                                <td>
                                                                    {{ (!empty($sanctionData->approvalID) ? getUserNameById('users', 'userID', $sanctionData->matrixApprovalBy, 'displayName') : '-') }}
                                                                </td>
                                                                <td>{{ $sanctionData->approvalRemarks ?? '-' }}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane" id="pd-verfication">
                                    <div class="row">
                                        @if(count($documents) > 0 && count($address) > 0)
                                            <div class="accordion custom-accordion mb-3"
                                                id="custom-accordion-pdVerficationDocumentAdd">
                                                <div class="card mb-0">
                                                    @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                                    href="#collapaseAddDocuments" aria-expanded="true"
                                                                    aria-controls="collapseFour">
                                                                    Documents<i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                    @endif
                                                    <div id="collapaseAddDocuments" class="collapse"
                                                        aria-labelledby="headingFour"
                                                        data-bs-parent="#custom-accordion-address">
                                                        <div class="card-body">
                                                            <div class="row mt-2">
                                                                <div class="collapse mb-3 show">
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Documents Type</th>
                                                                            <th>Documents</th>
                                                                            <th>Password</th>
                                                                            <th>Status</th>
                                                                            @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                                <th>Remarks</th>
                                                                                <th>Action</th>
                                                                            @endif
                                                                        </tr>
                                                                        @if(!empty($documents))
                                                                            @foreach($documents as $key => $doc)
                                                                                <tr>
                                                                                    <td>{{ ++$key }}</td>
                                                                                    <td>{{ $doc->documentsType ?? '' }}</td>
                                                                                    <td>
                                                                                        <a href="{{ Storage::url($doc->documents) }}"
                                                                                            target="_blank" class="text-success"
                                                                                            data-bs-toggle="tooltip"
                                                                                            data-bs-placement="top"
                                                                                            data-bs-custom-class="success-tooltip"
                                                                                            data-bs-title="View"
                                                                                            style="font-size: 18px;">
                                                                                            <i class='mdi mdi-eye'></i>
                                                                                        </a>
                                                                                        <a href="{{ Storage::url($doc->documents) }}"
                                                                                            target="_blank" class="text-primary"
                                                                                            data-bs-toggle="tooltip"
                                                                                            data-bs-placement="top"
                                                                                            data-bs-custom-class="primary-tooltip"
                                                                                            data-bs-title="Download"
                                                                                            style="font-size: 18px;margin-left: 10px;"
                                                                                            download>
                                                                                            <i class="mdi mdi-download"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                    <td>{{ $doc->documentsPassword ?? '--' }}</td>
                                                                                    <td>
                                                                                        <select
                                                                                            class="form-select form-select-sm status-select"
                                                                                            name="documentsStatus"
                                                                                            id="status_{{ $doc->id }}">
                                                                                            <option value="">Select Status</option>
                                                                                            <option value="Pending" {{ $doc->documentsStatus == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                                                            <option value="Verified" {{ $doc->documentsStatus == 'Verified' ? 'selected' : '' }}>Verified</option>
                                                                                            <option value="Rejected" {{ $doc->documentsStatus == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <textarea class="form-control remarks-textarea"
                                                                                            id="remarks_{{ $doc->id }}"
                                                                                            rows="1">{{ $doc->docRemarks ?? '' }}</textarea>
                                                                                    </td>
                                                                                    <td>
                                                                                        <button type="button"
                                                                                            class="btn btn-success btn-sm update-btn-doc"
                                                                                            data-id="{{ $doc->id }}"><i
                                                                                                class="ri-checkbox-circle-line"></i></button>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="5" class="text-center">No record found
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion custom-accordion mb-3"
                                                id="custom-accordion-pdVerficationAddressAdd">
                                                <div class="card mb-0">
                                                    @if(role() == 'Recovery Executive' || isSuperAdmin() || isAdmin())
                                                        <div class="card-header" id="headingFour">
                                                            <h5 class="m-0">
                                                                <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                                    href="#collapaseAddAddress" aria-expanded="true"
                                                                    aria-controls="collapseFour">
                                                                    Address <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                                </a>
                                                            </h5>
                                                        </div>
                                                    @endif
                                                    <div id="collapaseAddAddress" class="collapse" aria-labelledby="headingFour"
                                                        data-bs-parent="#custom-accordion-address">
                                                        <div class="card-body">
                                                            <div class="row mt-2">
                                                                <div class="collapse mb-3 show">
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <th>Address Type</th>
                                                                            <th>State</th>
                                                                            <th>City</th>
                                                                            <th>Pincode</th>
                                                                            <th>Address</th>
                                                                            <th>Status</th>
                                                                            <th>Remarks</th>
                                                                            <th>Image</th>
                                                                            <!-- Add column for image -->
                                                                            @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'Relationship Manager' || isSuperAdmin() || isAdmin())
                                                                                <th>Action</th>
                                                                            @endif
                                                                        </tr>
                                                                        @if(!empty($address))
                                                                            @foreach($address as $key => $addr)
                                                                                <tr id="address-row-{{ $addr->id }}">
                                                                                    <td>{{ $addr->addressType }}</td>
                                                                                    <td>{{ getUserNameById('lms_states', 'stateID', $profileData->state, 'stateName')}}</td>
                                                                                    <td>{{ getUserNameById('lms_cities', 'cityID', $profileData->city, 'cityName') }}</td>
                                                                                    <td>{{ $addr->pincode }}</td>
                                                                                    <td>{{ $addr->address }}</td>
                                                                                    <td>
                                                                                        <select
                                                                                            class="form-select form-select-sm status-select"
                                                                                            id="status_{{ $addr->id }}">
                                                                                            <option value="">Select Status</option>
                                                                                            <option value="Pending" {{ $addr->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                                                            <option value="Verified" {{ $addr->status == 'Verified' ? 'selected' : '' }}>Verified</option>
                                                                                            <option value="Rejected" {{ $addr->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <textarea class="form-control remarks-textarea"
                                                                                            id="remarks_{{ $addr->id }}"
                                                                                            rows="1">{{ $addr->addRemarks ?? '' }}</textarea>
                                                                                    </td>
                                                                                    <td>
                                                                                        <!-- Image input for uploading images -->
                                                                                        <input type="file"
                                                                                            class="form-control form-control-sm w-75"
                                                                                            id="image_{{ $addr->id }}"
                                                                                            name="image_{{ $addr->id }}">
                                                                                    </td>
                                                                                    <td>
                                                                                        <button type="button"
                                                                                            class="btn btn-success btn-sm update-btn-add"
                                                                                            data-id="{{ $addr->id }}">
                                                                                            <i class="ri-checkbox-circle-line"></i>
                                                                                        </button>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @else
                                                                            <tr>
                                                                                <td colspan="7" class="text-center">No record found
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-danger container justify-content-center w-100" role="alert"
                                                style="width:81%!important;">
                                                <strong>Important! - </strong> "You must provide document & address before
                                                proceeding with the PD-Verification."
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane" id="sanction">
                                    <div class="row">
                                        @if(!empty($sanctionData))
                                            <div class="col-12 text-end">
                                                @if(role() == 'CRM Support' || role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || isSuperAdmin() || isAdmin())
                                                    @if(!empty($esignDoc) && $esignDoc->loanStatus == 'Rejected' && $profileData->status == 'Rejected')
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                            @if(getUserID() == $profileData->cmID)
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                    data-lead-id="{{ $profileData->leadID }}"
                                                                    data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                data-lead-id="{{ $profileData->leadID }}"
                                                                data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                <i class="mdi mdi-email"></i>
                                                            </a>
                                                        @endif
                                                    @elseif($profileData->status == 'Rejected')
                                                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                            @if(getUserID() == $profileData->cmID)
                                                                <a href="javascript:void(0);"
                                                                    class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                    data-lead-id="{{ $profileData->leadID }}"
                                                                    data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @endif
                                                        @else
                                                            <a href="javascript:void(0);"
                                                                class="btn btn-sm btn-danger send-sanction-rejection-mail"
                                                                data-lead-id="{{ $profileData->leadID }}"
                                                                data-bs-custom-class="danger-tooltip" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" data-bs-title="Rejection Mail">
                                                                <i class="mdi mdi-email"></i>
                                                            </a>
                                                        @endif
                                                    @else
                                                        @if($profileData->status == 'Pending For Approval')
                                                            @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                                                                @if(getUserID() == $profileData->cmID)
                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-warning send-sanction-mail"
                                                                        data-lead-id="{{ $profileData->leadID }}"
                                                                        data-bs-custom-class="warning-tooltip" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-title="Pre Approval Mail">
                                                                        <i class="mdi mdi-email"></i>
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <a href="javascript:void(0);" class="btn btn-sm btn-warning send-sanction-mail"
                                                                    data-lead-id="{{ $profileData->leadID }}"
                                                                    data-bs-custom-class="warning-tooltip" data-bs-toggle="tooltip"
                                                                    data-bs-placement="top" data-bs-title="Pre Approval Mail">
                                                                    <i class="mdi mdi-email"></i>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="row mt-2">
                                                <div class="ml-auto">
                                                    <div class="table-responsive" data-simplebar data-simplebar-primary>
                                                        <table class="table table-bordered ml-1">
                                                            <tr class="bg-primary">
                                                                <th colspan="7" class="text-center  text-white">Sanction Details
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Approval Amount</th>
                                                                <th>Tenure</th>
                                                                <th>ROI</th>
                                                                <th>Pre-EMI Interest Days</th>
                                                                <th>Pre-EMI Interest</th>
                                                                <th>Pre-EMI Interest Diff Days</th>
                                                                <th>Total Interest</th>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    {{ !empty($sanctionData->loanAmtApproved) ? nf($sanctionData->loanAmtApproved) : '-' }}
                                                                </td>
                                                                <td>{{ $sanctionData->tenure ?? '-' }} Months</td>
                                                                <td>{{ $sanctionData->interestRate ?? '-' }} %</td>
                                                                <td>{{ $sanctionData->preEmiInterestDays ?? '-' }} Days</td>
                                                                <td>{{ $sanctionData->preEmiInterest ?? '-' }}</td>
                                                                <td>{{ $sanctionData->preEmiInterestDaysDiff ?? '-' }} Days</td>
                                                                <td>{{ $sanctionData->totalInterestAmount ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>EMI Amount</th>
                                                                <th>PF. %</th>
                                                                <th>Admin Fee</th>
                                                                <th>GST of PF.</th>
                                                                <th>Monthly Income</th>
                                                                <th>Monthly Obligation</th>
                                                                <th>Amount to be Disbursed</th>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ $sanctionData->emi ?? '-' }}</td>
                                                                <td>{{ $sanctionData->pfPercentage ?? '-' }} %</td>
                                                                <td>{{ !empty($sanctionData->adminFee) ? nf($sanctionData->adminFee) : '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ !empty($sanctionData->adminGstAmount) ? nf($sanctionData->adminGstAmount) : '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ !empty($sanctionData->monthlyIncome) ? nf($sanctionData->monthlyIncome) : '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ !empty($sanctionData->monthlyObligation) ? nf($sanctionData->monthlyObligation) : '-' }}
                                                                </td>
                                                                <td>{{ nf($sanctionData->disbursementAmount) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Sanction Date</th>
                                                                <th>Sanction By</th>
                                                                <th>Branch</th>
                                                                <th>Employed</th>
                                                                <th>Official Email</th>
                                                                <th>Alternative Mobile</th>
                                                                <th>Loan Required for</th>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ isset($sanctionData->addedOn) ? dft($sanctionData->addedOn) : '-' }}
                                                                </td>
                                                                <td>
                                                                    {{ getUserNameById('users', 'userID', $sanctionData->creditedBy, 'displayName')}}
                                                                </td>
                                                                <td>
                                                                    {{ getUserNameById('lms_cities', 'cityID', $sanctionData->branch, 'cityName') }}
                                                                </td>
                                                                <td>{{ $sanctionData->employed ?? '-' }}</td>
                                                                <td>{{ $sanctionData->officialEmail ?? '-' }}</td>
                                                                <td>{{ $sanctionData->alternativeMobile ?? '-' }}</td>
                                                                <td>{{ $sanctionData->loanRequirePurpose ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Cibil Score</th>
                                                                <th>Residential Type</th>
                                                                <th colspan="2">Bank</th>
                                                                <th>Matrix Status</th>
                                                                <th>Matrix Approved By</th>
                                                                <th>Matrix Remarks</th>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ $sanctionData->cibil ?? '-' }}</td>
                                                                <td>{{ $sanctionData->residentialType ?? '-' }}</td>
                                                                <td colspan="2">
                                                                    @php
                                                                        $bankNames = !empty($sanctionData->bankName) ? explode(',', $sanctionData->bankName) : [];
                                                                       @endphp
                                                                    @if(!empty($bankNames) && count($bankNames) > 0)
                                                                        @foreach ($bankNames as $key => $id)
                                                                            {{ ++$key . '. ' . getUserNameById('lms_banks', 'id', $id, 'bank') }}<br>
                                                                        @endforeach
                                                                    @else
                                                                        {{ '-' }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $sanctionData->status ?? '-' }}</td>
                                                                <td>
                                                                    {{ (!empty($sanctionData->approvalID) ? getUserNameById('users', 'userID', $sanctionData->matrixApprovalBy, 'displayName') : '-') }}
                                                                </td>
                                                                <td>{{ $sanctionData->approvalRemarks ?? '-' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <th>Sanction Status</th>
                                                                <th colspan="2">Sanction Remarks</th>
                                                                <th>Rejection Reason</th>
                                                                <th></th>
                                                                <th></th>
                                                                <th></th>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ $sanctionData->creditStatus ?? '-' }}</td>
                                                                <td colspan="2">{{ $sanctionData->remark ?? '-' }}</td>
                                                                <td>
                                                                    @foreach((array) json_decode($sanctionData->rejectionReason, true) as $reason)
                                                                        {{ $loop->iteration }}. {{ $reason }}.<br>
                                                                    @endforeach
                                                                    @if(empty($sanctionData->rejectionReason))
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-danger container justify-content-center w-75" role="alert">
                                                <strong>Important! - </strong> "You must pre-sanction the loan before proceeding
                                                with the sanction."
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="tab-pane show" id="communication">
                                    <div class="row">
                                        <div class="accordion custom-accordion mb-3" id="custom-accordion-communication">
                                            <div class="card mb-0">
                                                <div class="card-header" id="headingFour">
                                                    <h5 class="m-0">
                                                        <a class="custom-accordion-title d-block" data-bs-toggle="collapse"
                                                            href="#collapseFour" aria-expanded="true"
                                                            aria-controls="collapseFour">
                                                            Add/View <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                                        </a>
                                                    </h5>
                                                </div>
                                                <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                                    data-bs-parent="#custom-accordion-
                                     s">
                                                    <div class="card-body">
                                                        <div class="row mt-2">
                                                            <div class="collapse mb-3 show" id="collapseCommunication">
                                                                @if($profileData->status == 'Disbursed' || $profileData->status == 'Part Payment' || $profileData->status == 'Closed' || $profileData->status == 'Payday Preclose' || $profileData->status == 'Settlement' || $profileData->status == 'EMI Running')
                                                                    <form action="" method="post"
                                                                        class="communication-mail-form" id="address-form"
                                                                        autocomplete="off">
                                                                        @csrf
                                                                        <div class="row">
                                                                            <div class="col-md-7 row">
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <select class="form-control select2"
                                                                                            name="operation" id=""
                                                                                            data-toggle="select2">
                                                                                            <option value="">Select template
                                                                                                type</option>
                                                                                            @if(role() == 'CRM Support' || role() == 'Sr. Recovery Manager' || role() == 'Recovery Manager' || role() == 'Recovery Executive' || isSuperAdmin() || isAdmin())
                                                                                                @if($profileData->status == 'Disbursed' || $profileData->status == 'Part Payment' || $profileData->status == 'EMI Running')
                                                                                                    <!-- <option value="Tomorrow Reminder">
                                                                                                        Tomorrow Reminder</option> -->
                                                                                                    <!--<option value="Special Reminder">Special Reminder</option>-->
                                                                                                    <option
                                                                                                        value="Loan Repayment Reminder">
                                                                                                        Loan Repayment Reminder</option>
                                                                                                    <option value="E-Mandate Bounce">
                                                                                                        E-Mandate Bounce</option>
                                                                                                    <!--<option value="Benefits of Repaying">Benefits of Repaying</option>-->
                                                                                                    <option value="Don't Cash Payment">
                                                                                                        Don't Cash Payment</option>
                                                                                                    <!--<option value="Normal Reminder">Normal Reminder</option>-->
                                                                                                    <!--<option value="E-Mandate Mail">E-Mandate Mail</option>-->
                                                                                                    <option
                                                                                                        value="Repeated Commitments">
                                                                                                        Repeated Commitments</option>
                                                                                                    <!--<option value="Align Visit">Align Visit</option>-->
                                                                                                    <option value="No Answer">No Answer
                                                                                                    </option>
                                                                                                   <!--  <option value="Credit Reminder">
                                                                                                        Credit Reminder</option> -->
                                                                                                @endif
                                                                                            @endif
                                                                                            @if(role() == 'CRM Support' || role() == 'Accounts Manager' || role() == 'Accounts Executive' || role() == 'Sr. Recovery Manager' || isSuperAdmin() || isAdmin())
                                                                                                @if($profileData->status == 'Settlement')
                                                                                                    <option value="Settlement">
                                                                                                        Settlement</option>
                                                                                                @endif
                                                                                                @if($profileData->status == 'Closed' || $profileData->status == 'Payday Preclose')
                                                                                                    <option value="NOC">NOC</option>
                                                                                                @endif
                                                                                            @endif
                                                                                        </select>
                                                                                        <span class="operationErr"></span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="mb-3">
                                                                                        <input type="hidden"
                                                                                            name="communicationType"
                                                                                            value="Mail">
                                                                                        <button type="submit"
                                                                                            id="mailSendCommBtn"
                                                                                            class="btn btn-primary"><i
                                                                                                class="mdi mdi-email-send-outline"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                @endif
                                                                <table class="table table-striped">
                                                                    <tr>
                                                                        <th>Communication Type</th>
                                                                        <th>Operation</th>
                                                                        <th>Performed On</th>
                                                                        <th>Performed By</th>
                                                                    </tr>
                                                                    @if(count($communicationData) > 0)
                                                                        @foreach($communicationData as $key => $comm)
                                                                            <tr>
                                                                                <td>{{ $comm->communicationType }}</td>
                                                                                <td>{{ $comm->operation }}</td>
                                                                                <td>{{ dft($comm->addedOn) }}</td>
                                                                                <td>
                                                                                    {{ getUserNameById('users', 'userID', $comm->addedBy, 'displayName') }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">No communication
                                                                                found</td>
                                                                        </tr>
                                                                    @endif
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end card-body -->
            </div>
        </div>
    </div>
    <!-- end card -->
    <div id="addressDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-alert-line h1 text-warning"></i>
                        <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
                        <p class="mt-3"></p>
                        <input type="hidden" id="idDelete">
                        <button type="button" class="btn btn-success my-2" id="confirmYes">Yes</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div id="companyDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-alert-line h1 text-warning"></i>
                        <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
                        <p class="mt-3"></p>
                        <input type="hidden" id="idDelete">
                        <button type="button" class="btn btn-success my-2" id="confirmYesCompany">Yes</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div id="referenceDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-alert-line h1 text-warning"></i>
                        <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
                        <p class="mt-3"></p>
                        <input type="hidden" id="idDelete">
                        <button type="button" class="btn btn-success my-2" id="confirmYesReference">Yes</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- Modal Structure -->
    <div id="info-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-alert-line h1 text-warning"></i>
                        <h4 class="mt-2">Are You Sure You Want To Proceed?</h4>
                        <p class="mt-3"></p>
                        <button type="button" class="btn btn-success my-2 confirm-yes">Yes</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div id="documentsDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-alert-line h1 text-warning"></i>
                        <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
                        <p class="mt-3"></p>
                        <input type="hidden" id="idDelete">
                        <button type="button" class="btn btn-success my-2" id="confirmYesDocuments">Yes</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div id="checklistDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="ri-alert-line h1 text-warning"></i>
                        <h4 class="mt-2">Are You Sure Want To Delete ?</h4>
                        <p class="mt-3"></p>
                        <input type="hidden" id="idDelete">
                        <button type="button" class="btn btn-success my-2" id="confirmYesChecklist">Yes</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div id="right-modal" class="modal fade profile-update-model" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-right">
            <div class="modal-content">
                <div class="modal-header border-0 text-center">
                    <h4 class="modal-title" style="margin: 0 auto;" id="primary-header-modalLabel">Profile Update Form</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form class="ps-1 pe-1" id="leadAddEditModalForm" action="#" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="pancard" class="form-label">Pancard Number</label>
                                    <input class="form-control" type="text" name="pancard" readonly id="pancard"
                                        placeholder="Pancard Number" min="0" {{ !(role() == 'CRM Support' || isSuperAdmin()) ? 'readonly' : '' }}>
                                    <span class="pancardErr"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="nameOnPancard" class="form-label">Name on Pancard</label>
                                    <input class="form-control" type="text" name="nameOnPancard" id="nameOnPancard"
                                        placeholder="Name on Pancard" {{ !(role() == 'CRM Support' || isSuperAdmin()) ? 'disabled' : '' }}>
                                    <span class="nameOnPancardErr"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="adharNumber" class="form-label">Adhar Number</label>
                                    <input class="form-control" type="text" name="adharNumber" id="adharNumber"
                                        placeholder="Adhar Number" min="0" {{ !(role() == 'CRM Support' || isSuperAdmin()) ? 'readonly' : '' }}>
                                    <span class="adharNumberErr"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="email" class="form-label">Email</label>
                                    <input class="form-control " type="email" name="email" id="email" placeholder="Email" {{ !(role() == 'CRM Support' || isSuperAdmin() || isAdmin()) ? 'disabled' : '' }}>
                                    <span class="emailErr"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label for="mobile" class="form-label">Mobile</label>
                                    <input class="form-control " type="text" name="mobile" id="mobile" placeholder="Mobile"
                                        {{ !(role() == 'CRM Support' || isSuperAdmin() || isAdmin()) ? 'disabled' : '' }}>
                                    <span class="mobileErr"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <label class="mb-1 font-12">Date Of Birth</label>
                                    <div class="position-relative" id="datepicker4">
                                        <input type="text" class="form-control " name="dob" id="dob"
                                            data-provide="datepicker" placeholder="Date Of Birth" data-date-autoclose="true"
                                            {{ !(role() == 'CRM Support' || isSuperAdmin() || isAdmin()) ? 'disabled' : '' }}>
                                    </div>
                                    <span class="dobErr"></span>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-2">
                                    <label class="form-label">Gender</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                                             class="form-check-label" for="male" {{ !(role() == 'CRM Support' || isSuperAdmin()) ? 'disabled' : '' }}>Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female"
                                            value="Female"  class="form-check-label" for="female" {{ !(role() == 'CRM Support' || isSuperAdmin()) ? 'disabled' : '' }}>Female</label>
                                    </div>
                                    <span class="genderErr"></span>
                                </div>
                            </div>
                            @if(role() == 'Credit Manager' || role() == 'Sr. Credit Manager' || isSuperAdmin() || isAdmin())
                                <div class="col-md-5">
                                    <div class="mb-2">
                                        <label class="mb-1 font-12">Red Flag</label>
                                        <select class="form-control redFlag" name="redFlag" id="redFlag">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            @if(role() == 'CRM Support' || isSuperAdmin() || isAdmin())
                                                <option value="0">No</option>
                                            @endif
                                        </select>
                                        <span class="redFlagErr"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label class="mb-1 font-12">Remarks</label>
                                        <textarea class="form-control" name="remarks" id="redFlagRemarks" rows="1"></textarea>
                                        <span class="remarksErr"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="mb-2 text-center">
                            <input type="hidden" name="contactID" id="contactID" value="">
                            <input type="submit" class="btn btn-primary form-control" value="Save">
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
    <script type="text/javascript">


        $(document).ready(function () {
            let redirectUrl; // Variable to store the URL

            // When an action button is clicked
            $('.action-button').on('click', function (event) {
                event.preventDefault(); // Prevent default behavior
                redirectUrl = $(this).data('url'); // Get the URL from data attribute
                const message = $(this).data('message'); // Get the message for the modal

                // Update modal message dynamically
                $('#info-alert-modal .modal-body p').text(message);

                // Show the modal
                $('#info-alert-modal').modal('show');
            });

            // When the Yes button in the modal is clicked
            $('.confirm-yes').on('click', function () {
                // Redirect to the stored URL
                var activeTab = localStorage.getItem('activeTab');
                if (activeTab) {
                    $('.tab-pane').removeClass('show active');
                    $('.nav-link').removeClass('active');
                    $(activeTab).addClass('show active');
                    $('a[href="' + activeTab + '"]').addClass('active');
                    localStorage.removeItem('activeTab');
                }
                window.location.href = redirectUrl;
            });
        });

        $(document).ready(function () {
            // Get the status from the Blade view
            var status = '{{ $profileData->status }}';

            // Default active tab (if status is not 'Approved')
            var activeTab = '#customer'; // Default to profile tab

            // Check if status is 'Approved'
            if (status === 'Approved' || status === 'Pending For Approval') {
                // Remove 'active' and 'show' classes from all tabs and links
                $('.tab-pane').removeClass('show active');
                $('.nav-link').removeClass('active');

                // Set active tab to 'Sanction' if status is 'Approved'
                activeTab = '#sanction';
            }

            // Add the 'show' and 'active' classes to the correct tab and link
            $(activeTab).addClass('show active');
            $('a[href="' + activeTab + '"]').addClass('active');
        });


        $(document).ready(function () {
            // fetchAddress(); 
            // fetchCompany(); 
            // fetchReference(); 
            // fetchDocuments(); 




            function handleCreditStatusChange() {
                var creditStatus = $('#creditStatus').val();

                if (creditStatus == 'Approved') {
                    $('#rejectionReason').html('');
                }
                // Show or hide the textarea based on whether 'Rejected' is selected
                $('#rejectionReasonDiv').toggle(creditStatus === 'Rejected');

            }

            // Initial check to handle pre-selected values
            handleCreditStatusChange();

            // $(document).ready(function() {
            //      $('#pancard').on('keyup', function() {
            //          var pancardValue = $(this).val();

            //          // Exit if input is empty
            //          if (!$.trim(pancardValue)) {
            //              $('.pancardErr').text('');
            //              return;
            //          }

            //          $.ajax({
            //              url: '{{route('verifyPancard')}}', // URL to your Laravel endpoint
            //              type: 'POST',
            //              data: { pan: pancardValue , _token: '{{ csrf_token() }}'},
            //              success: function(response) {

            //                $('#email').val(response.emailId);
            //                $('#mobile').val(response.mobileNumber);
            //                 // Convert uppercase response value to Capitalized format
            //                  var capitalizeFirstLetter = function(string) {
            //                      return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
            //                  };
            //                  var genderValue = capitalizeFirstLetter(response.gender);
            //                   var name = capitalizeFirstLetter(response.name);
            //                   $('#nameOnPancard').val(name);
            //                  // Set the gender radio button
            //                  if (genderValue) {
            //                      $('input[name="gender"][value="' + genderValue + '"]').prop('checked', true);
            //                  }

            //                $('input[name="gender"][value="' + genderValue + '"]').prop('checked', true);
            //                var parts = response.dateOfBirth.split('-'); // Split by '-'
            //                var formattedDate = parts[1] + '/' + parts[0] + '/' + parts[2]; // Format as "m/d/y"
            //                $('#dob').val(formattedDate);
            //              },
            //              error: function(xhr, status, error) {
            //                  // Handle any errors here
            //                  $('.pancardErr').text('Error: Invalid Pancard' );
            //              }
            //          });
            //      });
            //  });


            var leadID = "{{ $profileData->leadID }}";
            var contactID = "{{ $profileData->contactID }}";

            $('form').each(function () {
                // Check if the form does not already have the leadID input
                if (!$(this).find('input[name="leadID"]').length) {
                    // Append the hidden leadID field to the form
                    $(this).append('<input type="hidden" name="leadID" value="' + leadID + '">');
                }

                // Check if the form does not already have the contactID input
                if (!$(this).find('input[name="contactID"]').length) {
                    // Append the hidden contactID field to the form
                    $(this).append('<input type="hidden" name="contactID" value="' + contactID + '">');
                }
            });



            // Attach the change event handler to the select element
            $('#creditStatus').on('change', handleCreditStatusChange);


            //   $(document).ready(function() {
            //         function calculatePFPercentage() {
            //             var loanAmt = parseFloat($('#loanAmtApproved').val());
            //             var adminFee = parseFloat($('#adminFee').val());

            //             if (!isNaN(loanAmt) && loanAmt > 0 && !isNaN(adminFee) && adminFee >= 0) {
            //                 var percentage = (adminFee / loanAmt) * 100;
            //                 $('#pf').val(percentage.toFixed(2));
            //             } else {
            //                 $('#pf').val('');
            //             }
            //         }
            //         $('#loanAmtApproved, #adminFee').on('input', calculatePFPercentage);
            //     });

            $(document).ready(function () {
                $('#redFlag').change(function () {
                    if ($(this).val() === '') {
                        $('#redFlagRemarks').val('');
                    }
                });
            });

            $(document).ready(function () {
                var activeTab = localStorage.getItem('activeTab');
                if (activeTab) {
                    $('.tab-pane').removeClass('show active');
                    $('.nav-link').removeClass('active');
                    $(activeTab).addClass('show active');
                    $('a[href="' + activeTab + '"]').addClass('active');
                    localStorage.removeItem('activeTab');
                }
            });


            $(document).ready(function () {
                // Function to get the date plus a number of days in YYYY-MM-DD format
                function getDatePlusDays(days) {
                    var today = new Date();
                    today.setDate(today.getDate() + days);
                    var day = ("0" + today.getDate()).slice(-2);
                    var month = ("0" + (today.getMonth() + 1)).slice(-2);
                    var year = today.getFullYear();
                    return year + '-' + month + '-' + day;
                }

                // Initialize the datepicker
                $('#emiDate').datepicker({
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    startDate: new Date(), // Allow selection of dates from today
                    endDate: getDatePlusDays({{repayDays(40)}}) // Limit selection to 40 days from today
                });

                // Optionally set default date to 40 days from today
                //  $('#emiDate').datepicker('setDate', getDatePlusDays(40));
            });


            // Address Delete Button Click
            $(document).on('click', '.addressDeleteBtn', function (e) {
                e.preventDefault();
                $('#idDelete').val($(this).attr('data-id'));
                $('#addressDeleteModal').modal('show');
            });

            // Company Delete Button Click
            $(document).on('click', '.companyDeleteBtn', function (e) {
                e.preventDefault();
                $('#idDelete').val($(this).attr('data-id'));
                $('#companyDeleteModal').modal('show');
            });

            // Reference Delete Button Click
            $(document).on('click', '.referenceDeleteBtn', function (e) {
                e.preventDefault();
                $('#idDelete').val($(this).attr('data-id'));
                $('#referenceDeleteModal').modal('show');
            });

            // Documents Delete Button Click
            $(document).on('click', '.documentsDeleteBtn', function (e) {
                e.preventDefault();
                $('#idDelete').val($(this).attr('data-id'));
                $('#documentsDeleteModal').modal('show');
            });

            // Checklist Delete Button Click
            $(document).on('click', '.checklistDeleteBtn', function (e) {
                e.preventDefault();
                $('#idDelete').val($(this).attr('data-id'));
                $('#checklistDeleteModal').modal('show');
            });

            // Call Status Change


            // Function to show/hide credit manager select based on status
            function toggleCreditManagerSelect() {
                var selectedStatus = $('#callStatus').val();
                if (selectedStatus === 'Document Received') {
                    $('#creditManagerSelect').show();
                } else {
                    $('#creditManagerSelect').hide();
                }
            }

            // Run on page load
            toggleCreditManagerSelect();

            // Run on change
            $('#callStatus').change(function () {
                toggleCreditManagerSelect();
            });

            // Confirm Delete for Address
            $(document).on('click', '#confirmYes', function () {
                var id = $('#idDelete').val();
                $.ajax({
                    url: "{{ route('addressDelete') }}",
                    type: "GET",
                    data: { id: id },
                    success: function (data) {
                        if (data.response == 'success') {
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            $('#addressDeleteModal').modal('hide');
                            fetchAddress();
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });

            // Confirm Delete for Company
            $(document).on('click', '#confirmYesCompany', function () {
                var id = $('#idDelete').val();
                $.ajax({
                    url: "{{ route('companyDelete') }}",
                    type: "GET",
                    data: { id: id },
                    success: function (data) {
                        if (data.response == 'success') {
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            $('#companyDeleteModal').modal('hide');
                            fetchCompany();
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });

            // Confirm Delete for Reference
            $(document).on('click', '#confirmYesReference', function () {
                var id = $('#idDelete').val();
                $.ajax({
                    url: "{{ route('referenceDelete') }}",
                    type: "GET",
                    data: { id: id },
                    success: function (data) {
                        if (data.response == 'success') {
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            $('#referenceDeleteModal').modal('hide');
                            fetchReference();
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });

            // Confirm Delete for Address
            $(document).on('click', '#confirmYesDocuments', function () {
                var id = $('#idDelete').val();
                $.ajax({
                    url: "{{ route('documentsDelete') }}",
                    type: "GET",
                    data: { id: id },
                    success: function (data) {
                        if (data.response == 'success') {
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            $('#documentsDeleteModal').modal('hide');
                            fetchDocuments();
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });


            // Checklist Delete for Address
            $(document).on('click', '#confirmYesChecklist', function () {
                var id = $('#idDelete').val();
                $.ajax({
                    url: "{{ route('checklistDelete') }}",
                    type: "GET",
                    data: { id: id },
                    success: function (data) {
                        if (data.response == 'success') {
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            $('#checklistDeleteModal').modal('hide');
                            setTimeout(function () { window.location.reload(); }, 1000);
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });


            // Pincode Validation and Fetching
            $('#pincode').on('keyup', function () {
                var pincode = $(this).val();
                if (pincode.length < 6) {
                    $('#addState').val('');
                    $('#addCity').val('');
                    $('#pincodeError').text('Pincode must be 6 digits long').show();
                } else if (pincode.length === 6) {
                    $('#pincodeError').hide().text('');
                    fetchPincodeDetails(pincode);
                }
            });

            function fetchPincodeDetails(pincode) {
                $.ajax({
                    url: 'https://api.postalpincode.in/pincode/' + pincode,
                    type: 'GET',
                    success: function (response) {
                        if (response && response[0] && response[0].PostOffice && response[0].PostOffice.length > 0) {
                            var postOffice = response[0].PostOffice[0];
                            $('#addState').val(postOffice.State);
                            $('#addCity').val(postOffice.District);
                            $('#pincodeError').hide().text('');
                        } else {
                            $('#addState').val('');
                            $('#addCity').val('');
                            $('#pincodeError').text('No data found for this pincode').show();
                        }
                    },
                    error: function () {
                        $('#addState').val('');
                        $('#addCity').val('');
                        $('#pincodeError').text('Error fetching data. Please try again later.').show();
                    }
                });
            }


            $('#ifscCode').on('keyup', function () {

                var ifscCode = $(this).val().trim();
                if (ifscCode.length !== 11) {
                    $('#branch').val('');
                    $('#ifscCodeErr').text('IFSC code must be 11 characters long').show();
                    return;
                }
                $('#ifscCodeErr').hide().text('');
                fetchIfscDetails(ifscCode);
            });


            $('#ifscCodeUpdate').on('keyup', function () {

                var ifscCode = $(this).val().trim();
                if (ifscCode.length !== 11) {
                    $('#branch').val('');
                    $('#ifscCodeErrUpdate').text('IFSC code must be 11 characters long').show();
                    return;
                }
                $('#ifscCodeErrUpdate').hide().text('');
                fetchIfscDetailsUpdate(ifscCode);
            });


            function fetchIfscDetails(ifscCode) {
                $.ajax({
                    url: 'https://ifsc.razorpay.com/' + ifscCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response && response.STATE && response.CITY) {
                            $('#branch').val(toTitleCase(response.BRANCH + ", " + response.CITY + ", " + response.STATE));
                            $('#bank').val(toTitleCase(response.BANK));
                            $('.branchErr').hide().text('');
                        } else {
                            $('#branch').val('');
                            $('#bank').val('');
                            $('.branchErr').text('No data found for this IFSC code').show();
                        }
                    },
                    error: function () {
                        $('#branch').val('');
                        $('#bank').val('');
                        $('.branchErr').text('Error fetching data. Please try again later.').show();
                    }
                });
            }
            
            function toTitleCase(str) {
              return str.toLowerCase().replace(/\b\w/g, function (char) {
                return char.toUpperCase();
              });
            }

            function fetchIfscDetailsUpdate(ifscCode) {
                $.ajax({
                    url: 'https://ifsc.razorpay.com/' + ifscCode,
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response && response.STATE && response.CITY) {
                            $('#branchUpdate').val(response.BRANCH + ", " + response.CITY + ", " + response.STATE);
                            $('#bankUpdate').val(response.BANK);
                            $('.branchErrUpdate').hide().text('');
                        } else {
                            $('#branchUpdate').val('');
                            $('#bankUpdate').val('');
                            $('.branchErrUpdate').text('No data found for this IFSC code').show();
                        }
                    },
                    error: function () {
                        $('#branchUpdate').val('');
                        $('#bankUpdate').val('');
                        $('.branchErr').text('Error fetching data. Please try again later.').show();
                    }
                });
            }

     
 
            // Edit Address
            $(document).on('click', '.editAddressBtn', function () {
                var id = $(this).data('id');
                $('#collapseAddress').collapse('show');
                $('#addAddressButton').hide();
                $('#submitAddressBtn').prop('disabled', false);
                $.ajax({
                    url: "{{ route('editProfileAddress') }}",
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            var addressData = response.data;
                            $('#addressType').val(addressData.addressType);
                            $('#pincode').val(addressData.pincode);
                            $('#addState').val(addressData.state);
                            $('#addCity').val(addressData.city);
                            $('#address').val(addressData.address);
                            $('#addressID').val(addressData.id);
                            $('#addStatus').val(addressData.status);
                        } else {
                            alert('Failed to fetch address details: ' + response.message);
                        }
                    },
                });
            });

            // Edit Reference
            $(document).on('click', '.editReferenceBtn', function (e) {
                e.preventDefault();
                $('#collapseReferenceForm').collapse('show');
                $('#submitReferenceBtn').prop('disabled', false);
                $('#addReferenceButton').hide();

                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('editProfileReference') }}",
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            var referenceData = response.data;
                            $('#referenceRelation').val(referenceData.referenceRelation);
                            $('#referenceName').val(referenceData.referenceName);
                            $('#referenceMobile').val(referenceData.referenceMobile);
                            $('#referenceId').val(referenceData.id);
                            $('#referenceStatus').val(referenceData.status);
                        } else {
                            alert('Failed to fetch reference details: ' + response.message);
                        }
                    },
                });
            });

            // Edit Company
            $(document).on('click', '.editCompanyBtn', function (e) {
                e.preventDefault();
                $('#collapseCompanyForm').collapse('show');
                $('#submitCompanyBtn').prop('disabled', false);
                $('#addCompanyButton').hide();
                var id = $(this).data('id');
                $.ajax({
                    url: "{{ route('editProfileCompany') }}",
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            var companyData = response.data;
                            $('#companyName').val(companyData.companyName);
                            $('#companyAddress').val(companyData.address);
                            $('#companyId').val(companyData.id);
                            $('#companyStatus').val(companyData.status);
                        } else {
                            alert('Failed to fetch company details: ' + response.message);
                        }
                    },
                });
            });

            // Edit Documents
            $(document).on('click', '.editDocumentsBtn', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#collapseDocumentsForm').collapse('show');
                $('#addDocumentsButton').hide();
                $('#submitDocumentBtn').prop('disabled', false);

                $.ajax({
                    url: "{{ route('editProfileDocument') }}",
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            var documentsData = response.data;

                            const userRole = "{{ role() }}"; // this is your PHP helper returning the role

                            const restrictedRoles = ['Recovery Executive', 'Recovery Manager', 'Sr. Recovery Manager'];

                            if (restrictedRoles.includes(userRole)) {
                                const select = document.getElementById('documentsType');
                                select.style.pointerEvents = 'none'; // disable selection
                                select.style.backgroundColor = '#e9ecef'; // optional: grey out
                                select.style.cursor = 'not-allowed'; // optional: UI feedback
                            }

                            const passwordInput = document.getElementById('documentsPassword');
                            if (passwordInput) {
                                passwordInput.readOnly = true;
                                passwordInput.style.backgroundColor = '#e9ecef';
                                passwordInput.style.cursor = 'not-allowed';
                            }

                            // Prevent interaction with file input
                            const fileInput = document.getElementById('documents');
                            if (fileInput) {
                                fileInput.style.pointerEvents = 'none'; // disables click
                                fileInput.style.backgroundColor = '#e9ecef';
                                fileInput.style.cursor = 'not-allowed';
                            }

                            $('#documentsType').val(documentsData.documentsType);
                            $('#documentsPassword').val(documentsData.documentsPassword);
                            $('#docRemarks').val(documentsData.docRemarks);
                            $('#documentsStatus').val(documentsData.documentsStatus);
                            $('#oldDocument').val(documentsData.documents);
                            $('#documentsId').val(documentsData.id);
                        } else {
                            alert('Failed to fetch document details: ' + response.message);
                        }
                    },
                });
            });


            // Edit Checklist
            $(document).on('click', '.editChecklistBtn', function (e) {
                e.preventDefault();
                var id = $(this).data('id');

                // Show the checklist form and hide the add button
                $('#collapseChecklistForm').collapse('show');
                $('#addChecklistButton').hide();
                $('#submitChecklistBtn').prop('disabled', false);

                // AJAX request to fetch checklist details
                $.ajax({
                    url: "{{ route('editProfileChecklist') }}", // Ensure this route is correctly defined
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            var documentsData = response.data;

                            // Set the values of the form fields with the response data
                            $('#documentType').val(documentsData.documentType);
                            $('#checklistRemark').val(documentsData.remark);
                            $('#checklistId').val(documentsData.id);
                        } else {
                            // Handle the failure case by alerting the message
                            alert('Failed to fetch document details: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle AJAX request errors (optional but good practice)
                        alert('An error occurred while fetching the document details. Please try again.');
                    }
                });
            });

            $(document).on('click', '.editCollectionBtn', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#collapseCollectionUpdate').collapse('show');
                $.ajax({
                    url: "{{ route('editProfileCollection') }}",
                    type: 'GET',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            var collectionData = response.data;
                            $('#collectedAmountUpdate').val(collectionData.collectedAmount);
                            $('#penaltyAmountUpdate').val(collectionData.penaltyAmount);
                            $('#collectionModeUpdate').val(collectionData.collectedMode);
                            $('#collectionUtrNoUpdate').val(collectionData.collectionUtrNo);
                            $('#collectionDateUpdate').val(collectionData.collectedDate);
                            $('#waveOffUpdate').val(collectionData.discountAmount);
                            $('#settlementAmountUpdate').val(collectionData.settlementAmount);
                            $('#statusUpdate').val(collectionData.status);
                            $('#collectionSourceUpdate').val(collectionData.collectionSource);
                            $('#remarkUpdate').val(collectionData.remark);
                            $('#collectionID').val(collectionData.collectionID);
                        } else {
                            alert('Failed to fetch collection details: ' + response.message);
                        }
                    },
                });
            });


            // Address Form Submission
            $(document).on('submit', '.address-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("profile/add-address") }}',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitAddressBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $('.address-form').trigger('reset');
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                $('#collapseAddress').collapse('hide');
                                $('#addAddressButton').show();
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });


            // Fetch address list
            function fetchAddress() {
                $.ajax({
                    type: 'GET',
                    url: '{{ url("profile/get-address") }}',
                    success: function (data) {
                        if (data) {
                            $('#replaceAddress').html(data);
                        }
                    },
                });
            }
            // Fetch address list ends


            // Company Form Submission
            $(document).on('submit', '.company-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("profile/add-company") }}',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitCompanyBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $('.company-form').trigger('reset');
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                $('#collapseCompanyForm').collapse('hide');
                                $('#addCompanyButton').show();
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });

            // Fetch company list
            function fetchCompany() {
                $.ajax({
                    type: 'GET',
                    url: '{{ url("profile/get-company") }}',
                    success: function (data) {
                        if (data) {
                            $('#replaceCompany').html(data);
                        }
                    },
                });
            }
            // Fetch company list ends


            // Reference Form Submission
            $(document).on('submit', '.reference-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("profile/add-reference") }}',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitReferenceBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $('.reference-form').trigger('reset');
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                $('#collapseReferenceForm').collapse('hide');
                                $('#addReferenceButton').show();
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });
            
             // Reference Form Submission
        $(document).on('submit', '.pd-form', function (e) {
           e.preventDefault();
    
            var form = this;
            var formData = new FormData(form);
        
            var activeTab = $('.nav-tabs .nav-link.active').attr('href');
            localStorage.setItem('activeTab', activeTab);
        
            // Show confirmation modal
            $('#info-alert-modal').modal('show');
        
            $('.confirm-yes').off('click').on('click', function () {
        
                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        // Get latitude & longitude
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;
        
                        // Append to FormData
                        formData.append('latitude', latitude);
                        formData.append('longitude', longitude);
        
                        // Proceed with AJAX
                        $.ajax({
                            type: 'POST',
                            url: '{{ url("profile/add-pd-verification") }}',
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function (data) {
                                if (data.response == 'success') {
                                    $('#submitPdBtn').prop('disabled', true);
                                    $('.errClr').html('').hide();
                                    $('.pd-form').trigger('reset');
                                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                    $('#collapsePdForm').collapse('hide');
                                    $('#addPdButton').show();
                                    setTimeout(function () { window.location.reload(); }, 1000);
                                } else {
                                    printErrorMsg(data.error);
                                }
                            }
                        });
        
                        $('#info-alert-modal').modal('hide');
        
                    }, function (error) {
                        alert('Geolocation failed: ' + error.message);
                        $('#info-alert-modal').modal('hide');
                    });
                } else {
                    alert('Geolocation is not supported by your browser.');
                    $('#info-alert-modal').modal('hide');
                }
            });
        });


            // Fetch reference list
            function fetchReference() {
                $.ajax({
                    type: 'GET',
                    url: '{{ url("profile/get-reference") }}',
                    success: function (data) {
                        if (data) {
                            $('#replaceReference').html(data);
                        }
                    },
                });
            }
            // Fetch reference list ends


            // Fetch reference list
            function fetchChecklist() {
                $.ajax({
                    type: 'GET',
                    url: '{{ url("profile/get-checklist") }}',
                    success: function (data) {
                        if (data) {
                            $('#replaceChecklist').html(data);
                        }
                    },
                });
            }
            // Fetch reference list ends

            // Documents Form Submission
            $(document).on('submit', '.documents-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                var formData = new FormData(this);

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);
                console.log('hi')
                var ajaxUrl = '{{ url("profile/add-documents") }}'; // default

                if (documentsType === 'Aadhar Back Image') {
                    ajaxUrl = '{{ url("profile/add-aadhaar") }}';
                }

                // Proceed with the AJAX request
                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitDocumentsBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.documents-form').trigger('reset');
                            $('#collapseDocumentsForm').collapse('hide');
                            $('#addReferenceButton').show();
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                   error: function (xhr, status, error) {
                        console.group("AJAX Error Debug Info");
                        console.log("Status Code:", xhr.status);
                        console.log("Status Text:", status);
                        console.log("Thrown Error:", error);
                    
                        // Try to parse and display JSON if possible
                        try {
                            const jsonResponse = xhr.responseJSON || JSON.parse(xhr.responseText);
                            console.log("Parsed Response JSON:", jsonResponse);
                        } catch (e) {
                            console.log("Raw Response Text:", xhr.responseText);
                        }
                        console.groupEnd();
                    
                        let errorMessage = "Something went wrong. Please try again.";
                    
                        // Custom error messages
                        if (xhr.status === 404) {
                            errorMessage = "Requested resource not found (404).";
                        } else if (xhr.status === 500) {
                            errorMessage = "Internal server error (500).";
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                    
                        $.NotificationApp.send("Error", errorMessage, "bottom-right", "rgba(0,0,0,0.2)", "error");
                    }

                });

            });


            // Fetch documents list
            function fetchDocuments() {
                $.ajax({
                    type: 'GET',
                    url: '{{ url("profile/get-documents") }}',
                    success: function (data) {
                        if (data) {
                            $('#replaceDocuments').html(data);
                        }
                    },
                });
            }
            // Fetch documents list ends


            // Documents Form Submission
            $(document).on('submit', '.checklist-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Collect form values
                var formValues = $(this).serialize();

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        method: 'POST',
                        url: '{{ url("profile/add-checklist") }}',
                        data: formValues,
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitChecklistBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $('.checklist-form').trigger('reset');
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                $('#collapseChecklistForm').collapse('hide');
                                $('#addChecklistButton').show();
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                        error: function (xhr) {
                            console.error('An error occurred:', xhr.responseText);
                        }
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });




            // Sanction Form Submission
            $(document).on('submit', '.addSanction-form', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);


                $.ajax({
                    type: 'POST',
                    url: '{{ url("profile/add-sanction") }}',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitAddSanctionBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.addSanction-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } if (data.response == 'exist') {
                            //$('.AddSanctionExistErr').html(data.error);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });


            $(document).on('submit', '.addPreSanction-form', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                $.ajax({
                    type: 'POST',
                    url: '{{ url("profile/add-sanction") }}',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitAddPreSanctionBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.addPreSanction-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } if (data.response == 'exist') {
                            //$('.AddSanctionExistErr').html(data.error);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });


            // Sanction Rejection Form Submission
            $(document).on('submit', '.rejectSanction-form', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);


                $.ajax({
                    type: 'POST',
                    url: '{{ url("profile/reject-sanction") }}',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitRejectSanctionBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.rejectSanction-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } if (data.response == 'exist') {
                            //$('.AddSanctionExistErr').html(data.error);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });


            // Fetch sanction list
            function fetchSanction() {
                $.ajax({
                    type: 'GET',
                    url: '{{ url("profile/get-sanction") }}',
                    success: function (data) {
                        if (data) {
                            $('#replaceSanction').html(data);
                        }
                    },
                });
            }
            // Fetch sanction list ends



            $(document).on('submit', '.communication-mail-form', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);
                $.ajax({
                    type: 'POST',
                    url: '{{ url("mail/add-communication-mail") }}',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#mailSendCommBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.communication-mail-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } if (data.response == 'exist') {
                            //$('.AddSanctionExistErr').html(data.error);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });


            $(document).on('submit', '.updateSanction-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("profile/update-sanction") }}',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitUpdateSanctionBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else if (data.response == 'failed') {
                                printErrorMsg(data.error);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });


            $(document).on('submit', '.addDisbursed-form', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);
                $.ajax({
                    type: 'POST',
                    url: '{{ url("profile/add-disbursed") }}',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitAddDisbursedBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.addDisbursed-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } if (data.response == 'exist') {
                            //$('.AddSanctionExistErr').html(data.error);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });


            $(document).on('submit', '.addCollection-form', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);
                $.ajax({
                    type: 'POST',
                    url: '{{ url("profile/add-collection") }}',
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitAddCollectionBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.addCollection-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } if (data.response == 'exist') {
                            //$('.AddSanctionExistErr').html(data.error);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });

            $(document).on('submit', '.updateDisbursed-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("profile/update-disbursed") }}',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitUpdateDisbursedBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else if (data.response == 'exist') {
                                // Handle existing case if needed
                                // $('.AddSanctionExistErr').html(data.error);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });

            $(document).on('submit', '.updateCollection-form', function (e) {
                e.preventDefault(); // Prevent the default form submission

                // Show the confirmation modal
                $('#info-alert-modal').modal('show');

                var formData = $(this).serialize();

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Proceed with the AJAX request
                    $.ajax({
                        type: 'POST',
                        url: '{{ url("profile/update-collection") }}',
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            if (data.response == 'success') {
                                $('#submitUpdateCollectionBtn').prop('disabled', true);
                                $('.errClr').html('').hide();
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else if (data.response == 'exist') {
                                // Handle existing case if needed
                                // $('.AddSanctionExistErr').html(data.error);
                            } else {
                                printErrorMsg(data.error);
                            }
                        },
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });
    
    
       $('#approvedButton').on('click', function() {
            // Get the leadID from the data attribute of the clicked button
            var leadID = $(this).data('leadid');
            
            // Send AJAX POST request
            $.ajax({
                url: 'profile/videokyc-cm-approval', // Your Laravel route to handle this request
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // CSRF token for Laravel protection
                    leadID: leadID // Send the leadID to the server
                },
                success: function(data) {
                    // Check if the response indicates failure
                    if (data.response == 'failed') {
                        // Display a notification with the message from the server
                        $.NotificationApp.send("KYC Approval Failed", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                         setTimeout(function() { window.location.reload(); }, 1000);
                        // Optionally, you can also set the message in the videoShow div
                    } else {
                        // Replace the spinner with the video content in #videoShow
                      $.NotificationApp.send("Well Done", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                         setTimeout(function() { window.location.reload(); }, 1000); 
                    }
                },
            });
        });

            @if(!empty($loanData->repayDate))
                $(document).ready(function () {

                    var repayDateStr = '{{ $loanData->repayDate}}';

                    var repayDate = new Date(repayDateStr);

                    function updateStatusOptions() {

                        var collectionDateStr = $('#collectionDate').val();

                        var collectionDateParts = collectionDateStr.split('/');
                        var collectionDate = new Date(collectionDateParts[2], collectionDateParts[0] - 1, collectionDateParts[1]);

                        var $statusDropdown = $('.statusDropdown');
                        $statusDropdown.empty();
                        if (collectionDate <= repayDate) {
                            $statusDropdown.append('<option value="Part Payment">Part Payment</option>');
                            $statusDropdown.append('<option value="Payday Preclose">Payday Preclose</option>');
                        } else {
                            $statusDropdown.append('<option value="Part Payment">Part Payment</option>');
                            $statusDropdown.append('<option value="Closed">Closed</option>');
                            $statusDropdown.append('<option value="Settled To Closed">Settled to Closed</option>');
                            $statusDropdown.append('<option value="Settlement">Settlement</option>');
                            $statusDropdown.append('<option value="Bad Debts">Bad Debts</option>');
                            $statusDropdown.append('<option value="Write Off">Write Off</option>');
                        }
                        $statusDropdown.prepend('<option value="">Choose Status</option>'); // Add default option
                    }

                    $('#collectionDate').datepicker({
                        format: 'mm/dd/yyyy',
                        autoclose: true
                    }).on('changeDate', function () {
                        updateStatusOptions();
                    });
                    updateStatusOptions();
                });

            @endif

            @if(!empty($loanData->repayDate))
                $(document).ready(function () {

                    var repayDateStr = '{{ $loanData->repayDate ?? ''}}';


                    var repayDate = new Date(repayDateStr);

                    function updateStatusOptions() {
                        var collectionDateStr = $('#collectionDateUpdate').val();

                        var collectionDateParts = collectionDateStr.split('/');
                        var collectionDate = new Date(collectionDateParts[2], collectionDateParts[0] - 1, collectionDateParts[1]);

                        var $statusDropdown = $('.statusDropdown');
                        $statusDropdown.empty();
                        if (repayDate <= collectionDate) {
                            $statusDropdown.append('<option value="Part Payment">Part Payment</option>');
                            $statusDropdown.append('<option value="Payday Preclose">Payday Preclose</option>');
                        } else {
                            $statusDropdown.append('<option value="Part Payment">Part Payment</option>');
                            $statusDropdown.append('<option value="Closed">Closed</option>');
                            $statusDropdown.append('<option value="Settled To Closed">Settled to Closed</option>');
                            $statusDropdown.append('<option value="Settlement">Settlement</option>');
                            $statusDropdown.append('<option value="Bad Debts">Bad Debts</option>');
                            $statusDropdown.append('<option value="Write Off">Write Off</option>');
                        }
                        $statusDropdown.prepend('<option value="">Choose Status</option>'); // Add default option
                    }

                    $('#collectionDateUpdate').datepicker({
                        format: 'mm/dd/yyyy',
                        autoclose: true
                    }).on('changeDate', function () {
                        updateStatusOptions();
                    });
                    updateStatusOptions();
                });

            @endif


            $('.open-modal').click(function (event) {
                event.preventDefault(); // Prevent default link behavior

                var contactID = $(this).data('contact-id'); // Get the leadID from data attribute
                $('#right-modal').modal('show');
                // Make AJAX request to fetch data using leadID
                $.ajax({
                    url: "{{ route('profileInfoEdit') }}", // Replace with your route URL
                    type: 'GET',
                    data: { contactID: contactID },
                    success: function (response) {
                        // Populate modal with fetched data

                        $('#nameOnPancard').val(response.name);
                        $('#email').val(response.email);
                        $('#mobile').val(response.mobile);
                        $('#pancard').val(response.pancard);
                        $('#adharNumber').val(response.aadharNo);
                        $('#redFlag').val(response.redFlag);
                        $('#redFlagRemarks').val(response.remarks || '');
                        var parts = response.dob.split('-'); // Split by '-'
                        var formattedDate = parts[1] + '/' + parts[2] + '/' + parts[0]; // Format as "m/d/y"

                        // if (response.redFlag == '1') {
                        //     $('#remarksSection').show();

                        // }  

                        if (response.redFlag !== null && response.redFlag !== '') {
                            $('#redFlag option[value=""]').remove(); // Remove the "Select" option
                        }

                        $('#dob').val(formattedDate);
                        $('input[name="gender"][value="' + response.gender + '"]').prop('checked', true);
                        $('#contactID').val(response.contactID);
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            });



            $(document).on('submit', '#leadAddEditModalForm', function (event) {
                event.preventDefault();

                var formData = $(this).serialize();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var leadID = $('#contactID').val();

                $.ajax({
                    url: "{{ route('profileInfoUpdate') }}",
                    type: "POST",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: formData,
                    success: function (data) {
                        if (data.response == 'success') {
                            $('.errClr').html('').hide();
                            $('#leadAddEditModalForm').trigger('reset');
                            $('#right-modal').modal('hide');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });


            $(document).on('click', '.send-sanction-mail', function (event) {
                event.preventDefault(); // Prevent the default link behavior

                var leadID = $(this).data('lead-id');

                // Show the existing confirmation modal
                $('#info-alert-modal').modal('show');

                // Set up the confirmation action
                $('.confirm-yes').off('click').on('click', function () {
                    // Store the active tab's identifier
                    var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                    localStorage.setItem('activeTab', activeTab);

                    // Proceed with the AJAX request
                    $.ajax({
                        url: 'mail/sanctionApproval',
                        type: 'POST',
                        data: {
                            leadID: leadID,
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function (data) {
                            // Handle success response
                            if (data.response === 'success') {
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else {
                                $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                            }
                        },
                        error: function (xhr) {
                            // Handle error response
                            console.log(xhr);
                        }
                    });

                    // Close the modal
                    $('#info-alert-modal').modal('hide');
                });
            });

            $(document).on('click', '.send-sanction-rejection-mail', function () {
                var leadID = $(this).data('lead-id');

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                $.ajax({
                    url: 'mail/sanctionRejection',
                    type: 'POST',
                    data: {
                        leadID: leadID,
                        _token: '{{ csrf_token() }}' // Include CSRF token
                    },
                    success: function (data) {
                        // Handle success response
                        if (data.response === 'success') {
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } else {
                            $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    error: function (xhr) {
                        // Handle error response
                        console.log(xhr);
                    }
                });
            });


            $(document).ready(function () {
                // Click event for the update button
                $('.update-btn-doc').on('click', function () {

                    // Store the active tab's identifier
                    var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                    localStorage.setItem('activeTab', activeTab);

                    var docId = $(this).data('id');
                    var status = $('#status_' + docId).val();
                    var remarks = $('#remarks_' + docId).val();

                    // Make AJAX request to update the status and remarks
                    $.ajax({
                        url: 'profile/update-doc-pd',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token for security
                            status: status,
                            remarks: remarks,
                            docId: docId
                        },
                        success: function (data) {
                            if (data.response === 'success') {
                                $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                                setTimeout(function () { window.location.reload(); }, 1000);
                            } else {
                                $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            alert('An error occurred: ' + error);
                        }
                    });
                });
            });


            $(document).ready(function () {
                $(".update-btn-add").on('click', function () {
                    var addressId = $(this).data('id');  // Get the address ID

                    // Capture the status and remarks values for the corresponding row
                    var status = $('#status_' + addressId).val();
                    var remarks = $('#remarks_' + addressId).val();

                    // Capture the image from the file input field
                    var image = $('#image_' + addressId)[0].files[0];  // Get the file from the input

                    // Check if the image is selected and log it
                    console.log('Image selected: ', image);  // Check if image is captured correctly

                    var formData = new FormData();
                    formData.append('address_id', addressId);
                    formData.append('status', status);
                    formData.append('remarks', remarks);

                    // If an image is selected, append it to formData
                    if (image) {
                        formData.append('image', image);
                    } else {
                        console.log('No image selected');
                    }

                    // Log the FormData before sending it to the server
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }

                    // Send the data to the server via AJAX
                    $.ajax({
                        url: 'profile/update-add-pd',  // Replace with your actual endpoint URL
                        type: 'POST',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Add CSRF token to headers
                        },
                        processData: false,  // Important for sending FormData
                        contentType: false,  // Important for sending FormData
                        success: function (response) {
                            // Handle the response from the server (success or error)
                            if (response.success) {
                                alert('Data updated successfully!');
                            } else {
                                alert('Error occurred!');
                            }
                        },
                        error: function (xhr, status, error) {
                            // Handle any AJAX errors
                            alert('Error: ' + error);
                        }
                    });
                });
            });



            $(document).on('submit', '.customer-remarks-form', function (e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Store the active tab's identifier
                var activeTab = $('.nav-tabs .nav-link.active').attr('href');
                localStorage.setItem('activeTab', activeTab);

                $.ajax({
                    type: 'POST',
                    url: '{{ url("profile/add-customer-remarks") }}',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.response == 'success') {
                            $('#submitCustomRemarksBtn').prop('disabled', true);
                            $('.errClr').html('').hide();
                            $('.customer-remarks-form').trigger('reset');
                            $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            $('#collapseCustomerRemarks').collapse('hide');
                            $('#addCustomerRemarksButton').show();
                            setTimeout(function () { window.location.reload(); }, 1000);
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                });
            });

            $('#verifyButton').on('click', function () {
                // Get the leadID from the data attribute of the clicked button
                var leadID = $(this).data('leadid');

                // Show the loader inside the #videoShow td only when the button is clicked
                $('#videoShow').html('<div class="d-flex justify-content-center align-items-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

                // Send AJAX POST request
                $.ajax({
                    url: 'profile/videokyc-download', // Your Laravel route to handle this request
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // CSRF token for Laravel protection
                        leadID: leadID // Send the leadID to the server
                    },
                    success: function (data) {
                        // Check if the response indicates failure
                        if (data.response == 'failed') {
                            // Display a notification with the message from the server
                            $.NotificationApp.send("KYC Status", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                            setTimeout(function () { window.location.reload(); }, 1000);
                            // Optionally, you can also set the message in the videoShow div
                        } else {
                            // Replace the spinner with the video content in #videoShow
                            $('#videoShow').html(data);
                        }
                    },
                    error: function (xhr, status, error) {
                        // In case of error, show an error message instead of the video
                        $('#videoShow').html('<p class="text-danger">An error occurred: ' + error + '</p>');
                    }
                });
            });

            if ($('#fiChoice').val() === '0') {
                $('#pdPersonSection').show();
            } else {
                $('#pdPersonSection').hide();
            }

            // Add event listener for changes to the #fiChoice dropdown
            $('#fiChoice').on('change', function () {
                if ($(this).val() === '0') {
                    // Show the PD Person's section if "With PD" is selected
                    $('#pdPersonSection').show();
                } else {
                    // Hide the PD Person's section if "Without PD" is selected
                    $('#pdPersonSection').hide();
                }
            });

            // Toggle Address Collapse
            $('#addAddressButton').click(function () {
                $('#collapseAddress').collapse('show');
                $('#addAddressButton').hide();
                $('#closeAddressButton').show();
                $('.address-form').trigger('reset');
            });

            $('#closeAddressButton').click(function () {
                $('#collapseAddress').collapse('hide');
                $('#addAddressButton').show();
            });


            // Toggle Company Collapse
            $('#addCompanyButton').click(function () {
                $('#collapseCompanyForm').collapse('show');
                $('#addCompanyButton').hide();
                $('#closeCompanyButton').show();
                $('.company-form').trigger('reset');
            });

            $('#closeCompanyButton').click(function () {
                $('#collapseCompanyForm').collapse('hide');
                $('#addCompanyButton').show();
            });

            // Toggle Reference Collapse
            $('#addReferenceButton').click(function () {
                $('#collapseReferenceForm').collapse('show');
                $('#addReferenceButton').hide();
                $('#closeReferenceButton').show();
                $('.reference-form').trigger('reset');
            });

            $('#closeReferenceButton').click(function () {
                $('#collapseReferenceForm').collapse('hide');
                $('#addReferenceButton').show();
            });

             // Toggle pd Collapse
            $('#addPdButton').click(function () {
                $('#collapsePdForm').collapse('show');
                $('#addPdButton').hide();
                $('#closePdButton').show();
                $('.pd-form').trigger('reset');
            });

            $('#closePdButton').click(function () {
                $('#collapsePdForm').collapse('hide');
                $('#addPdButton').show();
            });
 
 
            // Toggle Documents Collapse
            $('#addDocumentsButton').click(function () {
                $('#collapseDocumentsForm').collapse('show');
                $('#addDocumentsButton').hide();
                $('#closeDocumentsButton').show();
                $('.documents-form').trigger('reset');
            });

            $('#closeDocumentsButton').click(function () {
                $('#collapseDocumentsForm').collapse('hide');
                $('#addDocumentsButton').show();
            });

            // Toggle Documents Collapse
            $('#addChecklistButton').click(function () {
                $('#collapseChecklistForm').collapse('show');
                $('#addChecklistButton').hide();
                $('#closeChecklistButton').show();
                $('.checklist-form').trigger('reset');
            });

            $('#closeChecklistButton').click(function () {
                $('#collapseChecklistForm').collapse('hide');
                $('#addChecklistButton').show();
            });


            // Toggle Sanction Collapse
            $('#addSanctionButton').click(function () {
                $('#collapaseAddSanction').collapse('show');
                $('#addSanctionButton').hide();
                $('#closeSanctionButton').show();
                $('.sanctionAdd-form').trigger('reset');
            });

            $('#closeAddSanctionButton').click(function () {
                $('#collapaseAddSanction').collapse('hide');
                $('#addSanctionButton').show();
            });


            // Toggle Sanction Collapse
            // 	 $('#addPreSanctionButton').click(function() {
            // 		  $('#collapaseAddPreSanction').collapse('show');
            // 		  $('#addPreSanctionButton').hide();
            // 		  $('#closePreSanctionButton').show();
            // 		  $('.sanctionAdd-form').trigger('reset');
            // 	 });

            // 	 $('#closeAddPreSanctionButton').click(function() {
            // 		  $('#collapaseAddPreSanction').collapse('hide');
            // 		  $('#addPreSanctionButton').show();
            // 	 });

            // Toggle Sanction Rejection Collapse
            $('#rejectSanctionButton').click(function () {
                $('#collapaseRejectSanction').collapse('show');
                $('#rejectSanctionButton').hide();
                $('#closeSanctionButton').show();
                $('.sanctionReject-form').trigger('reset');
            });

            $('#closeRejectSanctionButton').click(function () {
                $('#collapaseRejectSanction').collapse('hide');
                $('#rejectSanctionButton').show();
            });


            $('#updateSanctionButton').click(function () {
                $('#collapseSanctionUpdate').collapse('show');
                $('#updateSanctionButton').hide();
                $('#closeSanctionUpdateButton').show();
            });

            $('#closeSanctionUpdateButton').click(function () {
                $('#collapseSanctionUpdate').collapse('hide');
                $('#updateSanctionButton').show();
            });


            $('#closeAddDisbursedButton').click(function () {
                $('#collapseDisbursedAdd').collapse('hide');
                $('#updateDisbursedButton').show();
            });


            $('#updateDisbursedButton').click(function () {
                $('#collapseDisbursedUpdate').collapse('show');
                $('#updateDisbursedButton').hide();
                $('#closeDisbursedUpdateButton').show();
            });

            $('#closeDisbursedUpdateButton').click(function () {
                $('#collapseDisbursedUpdate').collapse('hide');
                $('#updateDisbursedButton').show();
            });


            $('#closeAddCollectionButton').click(function () {
                $('#collapseCollection').collapse('hide');
                // $('#updateCollectionButton').show();
            });

            $('#closeUpdateCollectionButton').click(function () {
                $('#collapseCollectionUpdate').collapse('hide');
                $('#updateCollectionButton').show();
            });

            $('#addCustomerRemarksButton').click(function () {
                $('#addCustomerRemarksForm').collapse('show');
                $('#addCustomerRemarksButton').hide();
                $('#closeCustomerRemarksButton').show();
                $('.customer-remarks-form').trigger('reset');
            });

            $('#closeCustomerRemarksButton').click(function () {
                $('#addCustomerRemarksForm').collapse('hide');
                $('#addCustomerRemarksButton').show();
            });

            $(document).ready(function () {
                // Manually activate the 'Loan Applied' tab
                $('#loan-tab').addClass('active'); // Add 'active' class to the tab
                $('#loan-information').addClass('active show'); // Add 'active' and 'show' classes to the tab content

                // Optionally, you can trigger the Bootstrap tab show event to activate it
                $('#loan-tab').tab('show');
            });
           



        	// Function to load PD persons based on the selected branch
            function loadPdPersons(branchID) {
                if (branchID) {
                    // Send an AJAX request to fetch the PD persons for the selected branch
                    $.ajax({
                        url: "{{ route('getPdPerson') }}",  // Your route for fetching PD persons
                        type: 'GET',
                        data: {
                            branchID: branchID  // Pass the selected branch ID
                        },
                        success: function (response) {
                            // Clear the PD persons dropdown and add the default option
                            $('select[name="pdPerson"]').empty().append('<option value="">Choose FV Person</option>');

                            // Check if PD persons are present in the response
                            if (response.success == true) {
                                // Loop through the PD persons and add them to the dropdown
                                response.pdPersons.forEach(function (person) {
                                    $('select[name="pdPerson"]').append('<option value="' + person.userID + '">' + person.displayName + '</option>');
                                });
                            } else {
                                // If no PD persons are found, add a "No PD person found" option
                                $('select[name="pdPerson"]').append('<option value="" selected>No FV person found</option>');
                            }
                        }
                    });
                } else {
                    // Hide the PD Person's section if no branch is selected
                    $('#pdPersonSection').hide();
                }
            }

            // Trigger the function when the branch is changed
            $('#branchSelect').on('change', function () {
                var branchID = $(this).val();
                loadPdPersons(branchID);

                // Show the PD Person's section if a branch is selected
                if (branchID) {
                    $('#pdPersonSection').show();
                } else {
                    $('#pdPersonSection').hide();
                }
            });

            // On page load, trigger the function for the selected branch
            var initialBranchID = $('#branchSelect').val();
            loadPdPersons(initialBranchID); // This will fetch PD persons for the selected branch on load

            // Show/hide PD Person's section based on page load selection
            if (initialBranchID) {
                $('#pdPersonSection').show();
            } else {
                $('#pdPersonSection').hide();
            }

            $('.futureDate').datepicker({
                autoclose: true,
                todayHighlight: true,
                endDate: new Date() // Restricts selection to today and earlier
            });

            $(".banksShow").select2({
                tags: true
            });

            $('#basic-datatable-2,#basic-datatable-3,#basic-datatable-4').DataTable();

            $(".banksShow").on("select2:select", function (evt) {
                var element = evt.params.data.element;
                var $element = $(element);

                $element.detach();
                $(this).append($element);
                $(this).trigger("change");
            });


                   // Admin Fee and Loan Amount Validation
         function calculate() {
                var loanAmtApproved = parseFloat($('#loanAmtApproved').val());
                var pf = parseFloat($('#pf').val());

                // Check if the loanAmtApproved and pf are valid numbers
                if (!isNaN(loanAmtApproved) && !isNaN(pf)) {
                    var adminFee;

                    // If adminFee is not provided, calculate from loanAmtApproved using default 5%
                    if ($('#adminFee').val() === "") {
                        adminFee = loanAmtApproved * 0.05; // Default 5% admin fee
                        $('#adminFee').val(adminFee.toFixed(0));
                    } else {
                        adminFee = parseFloat($('#adminFee').val());
                    }

                    // Calculate pf from loanAmtApproved and adminFee if pf is empty
                    if ($('#pf').val() === "" && loanAmtApproved > 0) {
                        pf = (adminFee / loanAmtApproved) * 100; // Calculate percentage based on adminFee and loanAmtApproved
                        $('#pf').val(pf.toFixed(2));
                    }

                    // If pf is changed, update adminFee accordingly
                    if (!isNaN(pf) && pf > 0 && loanAmtApproved > 0) {
                        adminFee = (loanAmtApproved * pf) / 100;
                        $('#adminFee').val(adminFee.toFixed()); // Update adminFee based on pf
                    }

                    // Validate minimum admin fee (5% of loanAmtApproved)
                    var minAdminFee = loanAmtApproved * 0.05;
                    if (adminFee < minAdminFee) {
                        $('.pfErr').text('Minimum 5% of the Loan Amount.');
                        $('.pfErr').addClass('text-danger');
                        $('#pf').addClass('is-invalid');
                    } else {
                        $('.pfErr').text('');
                        $('#pf').removeClass('is-invalid');
                    }

                } else {
                    // Handle the case when any field is invalid or empty
                    $('#adminFee').removeClass('is-invalid');
                    $('.adminFeeErr').text('');
                }
            }

            // Trigger the function when the document is ready (on load)
            calculate();

            // Trigger the function on keyup event for loanAmtApproved and pf
            $('#loanAmtApproved, #pf').on('keyup', function () {
                calculate();
            });

            // Print Error Message
            function printErrorMsg(msg) {
                $('.errClr').remove();
                $.each(msg, function (key, value) {
                    $('.' + key + 'Err').html('<p class="text-danger mt-1 errClr font-12"><i class="ri-close-circle-line me-1 align-middle font-12"></i><strong>' + value + '</strong></p>');
                });
            }
        });
    </script>
@endsection