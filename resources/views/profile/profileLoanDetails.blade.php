@extends('layouts.master')
@section('page-title',$page_info['page_title'])
@section('main-section')
<div class="content-page main_product main_profile">
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
   <div class="row main_profi">
      <div class="col-md-12 col-lg-12">
         <div class="profile_det_main">   
         <div class="row">  
            <div class="col-md-12">
                 <div class="mb-2 d-flex justify-content-end align-items-center gap-2">  
                     @if($profileData->status=='Customer Approved')
                          @php $color = 'warning'; @endphp
                     @elseif($profileData->status=='Approved')
                          @php $color = 'primary'; @endphp
                     @elseif($profileData->status=='Pending For Disburse')
                          @php $color = 'info'; @endphp
                     @elseif($profileData->status=='Disbursed')
                          @php $color = 'success'; @endphp      
                     @else
                          @php $color = 'secondary'; @endphp
                     @endif
                     @if($approvalData->pdVerification == 0)
                         <span class="badge bg-{{ $profileData->pdStatus == 1 ? 'success' : 'danger' }} font-14 mr-2"> 
                           @if($profileData->pdStatus == '1')
                                {{'FV Verified'}}
                           @elseif($profileData->pdStatus == '0')
                                {{'FV Rejected'}}
                           @else
                                {{'FV Pending'}}
                           @endif             
                         </span>
                     @endif 
                     <span class="badge bg-{{$color}} font-14 mr-2"> {{$profileData->status}}</span>
                   </div>
                    
            </div>
            <div class="col-md-1 kycBtns">
                 
                   @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager' || role() == 'CRM Support' || isSuperAdmin() || isAdmin())
                        @if(role() == 'Sr. Credit Manager' || role() == 'Credit Manager')
                            @if(getUserID() == $profileData->cmID)
                                @if($profileData->status == 'Approved')
                                    <div id="estampActionContainer_{{ $profileData->leadID }}">
                                        @if($profileData->status == 'Approved' && (empty($esigtampDoc) || empty($esigtampDoc->requestId)))
                                            {{-- Generate eStamp --}}
                                            <a href="#" class="btn btn-sm btn-info action-button"
                                                data-url="{{ url('profile/get-estamp/' . $profileData->leadID) }}"
                                                data-message="Send E-Stamp Request" data-bs-toggle="tooltip"
                                                data-bs-placement="top"  data-bs-custom-class="info-tooltip" title="Generate Estamp">
                                                <i class="mdi mdi-file-document"></i>
                                            </a>
                                        @elseif(!empty($esigtampDoc->requestId) && empty($esigtampDoc->fileName) && $profileData->status == 'Approved')
                                            {{-- Check & Fetch eStamp --}}
                                            <a href="javascript:void(0);"
                                                class="btn btn-sm btn-warning  mb-2 estamp-action"
                                                data-url="{{ url('profile/check-estamp/' . $profileData->leadID) }}"
                                                data-lead="{{ $profileData->leadID }}"
                                                data-action="check" data-message="Check Estamp"
                                                data-bs-toggle="tooltip"  data-bs-custom-class="warning-tooltip" data-bs-placement="top"
                                                title="Check and Download Estamp">
                                                <i class="mdi mdi-file-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                @endif
                                
                                @if(!empty($esignDoc) && $esignDoc->loanStatus == 'Rejected' && $profileData->status == 'Approved' && !empty($esigtampDoc->fileName))
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-esign-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send E-Sign Request Mail." data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send E-Sign Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif(empty($esignDoc) && $profileData->status == 'Approved' && !empty($esigtampDoc->fileName))
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-esign-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send E-Sign Request Mail." data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send E-Sign Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif($profileData->status == 'Approved' && !empty($esigtampDoc->fileName))
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="E-Sign Request Mail Already Sent">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @endif
                                
                                {{-- Video-KYC Mail --}}
                                @if(!empty($videoKycDoc) && $videoKycDoc->status == 'rejected' && $profileData->status == 'Approved')
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-videokyc-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send Video-KYC Request Mail"  data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send Video-KYC Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif(empty($videoKycDoc) && $profileData->status == 'Approved')
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-videokyc-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send Video-KYC Request Mail" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send Video-KYC Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif($profileData->status == 'Approved')
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip"  data-bs-custom-class="warning-tooltip"  data-bs-placement="top"
                                        title="Video-KYC Request Already Sent">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @endif
                                
                                {{-- E-NACH Mail --}}
                                @if($profileData->status == 'Approved')
                                    <a href="#" class="btn btn-sm btn-info action-button"
                                        data-url="profile/send-enach-request/{{$profileData->leadID}}"
                                        data-message="Send E-Nach Request Mail."  data-bs-custom-class="info-tooltip"  data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send E-Nach Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @endif
                            @endif
                        @else
                         
                             @if($profileData->status == 'Approved')
                                    <div id="estampActionContainer_{{ $profileData->leadID }}">
                                        @if(empty($esigtampDoc) || empty($esigtampDoc->requestId))
                                            <a href="#" class="btn btn-sm btn-info action-button"
                                                data-url="{{ url('profile/get-estamp/' . $profileData->leadID) }}"
                                                data-message="Send E-Stamp Request"  data-bs-custom-class="info-tooltip" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Generate Estamp">
                                                <i class="mdi mdi-file-document"></i>
                                            </a>
                                        @elseif(!empty($esigtampDoc->requestId) && empty($esigtampDoc->fileName))
                                            <a href="javascript:void(0);" class="btn btn-sm btn-warning estamp-action"
                                                data-url="{{ url('profile/check-estamp/' . $profileData->leadID) }}"
                                                data-lead="{{ $profileData->leadID }}"  data-bs-custom-class="warning-tooltip" data-action="check"
                                                data-message="Check Estamp" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Check and Download Estamp">
                                                <i class="mdi mdi-file-download"></i>
                                            </a>
                                        @endif
                                    </div> 
                                @endif
                                
                                {{-- E-Sign Mail --}}
                                @if(!empty($esignDoc) && $esignDoc->loanStatus == 'Rejected' && $profileData->status == 'Approved' && !empty($esigtampDoc->fileName))
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-esign-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send E-Sign Request Mail." data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send E-Sign Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                               @elseif(empty($esignDoc) && $profileData->status == 'Approved' && !empty($esigtampDoc->fileName))
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-esign-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send E-Sign Request Mail." data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send E-Sign Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif($profileData->status == 'Approved' && !empty($esigtampDoc->fileName))
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="E-Sign Request Mail Already Sent">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @endif
                                
                                {{-- Video-KYC Mail --}}
                                @if(!empty($videoKycDoc) && $videoKycDoc->status == 'rejected' && $profileData->status == 'Approved')
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-videokyc-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send Video-KYC Request Mail"  data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send Video-KYC Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif(empty($videoKycDoc) && $profileData->status == 'Approved')
                                    <a href="#" class="btn btn-sm btn-success action-button"
                                        data-url="profile/send-videokyc-request/{{$profileData->leadID}}/{{$profileData->contactID}}"
                                        data-message="Send Video-KYC Request Mail" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send Video-KYC Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @elseif($profileData->status == 'Approved')
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning"
                                        data-bs-toggle="tooltip"  data-bs-custom-class="warning-tooltip"  data-bs-placement="top"
                                        title="Video-KYC Request Already Sent">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @endif
                                
                                {{-- E-NACH Mail --}}
                                @if($profileData->status == 'Approved')
                                    <a href="#" class="btn btn-sm btn-info action-button"
                                        data-url="profile/send-enach-request/{{$profileData->leadID}}"
                                        data-message="Send E-Nach Request Mail."  data-bs-custom-class="info-tooltip"  data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Send E-Nach Request Mail">
                                        <i class="mdi mdi-email"></i>
                                    </a>
                                @endif
                         
                        @endif
                    @endif   
                
                <div class="d-flex justify-content-center align-items-center">
                    @if($profileData->status == 'Pending For Disburse' || $profileData->status == 'Disbursed')
                        @if($kycValidation==1)
                        <a href="#" class="btn btn-sm btn-success mt-3 text-center action-button" data-url="mail/send-welcome-letter/{{$profileData->leadID}}" data-message="Send Welcome Letter." data-bs-custom-class="success-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Send Welcome Letter">
                            <i class="mdi mdi-email"></i>
                        </a>
                        @endif
                    @endif
                </div>
            </div>    
            <div class="col-md-10">
                <div class="main_pro_det">
                   <div class="pro_det_new">
                      <div class="loan_det">
                         <p><strong>Lead ID :</strong>{{$profileData->leadID}}</p>
                         <p><strong>CIF ID :</strong>{{$profileData->contactID}}</p>
                         <p><strong>Client Name : </strong>{{$profileData->name}}</p>
                         <p><strong>Loan Purpose :</strong>{{$approvalData->loanRequirePurpose}}</p>
                      </div>
                   </div>
                   <div class="pro_det_new">
                      <div class="loan_det">
                         <p><strong>Loan Amount :</strong>{{ nf($approvalData->loanAmtApproved)}}</p>
                         <p><strong>Loan No. :</strong>{{$approvalData->loanNo ?? '-'}}</p>
                         <p><strong>Sanction Date:  </strong>{{empty($approvalData->creditedBy) ? '--' :  dft($approvalData->addedOn)}}</p>
                         <p><strong>Sanction By:  </strong>{{getUserNameById('users','userID',$approvalData->creditedBy,'displayName')}}</p>
                      </div>
                   </div>
                   <div class="pro_det_new">
                      <div class="loan_det">
                         <p><strong>Sheet Send Date: </strong>@if(!empty($loanData->sheetSendDate) && !empty($loanData->sheetSendTime))
                                {{ dft($loanData->sheetSendDate . ' ' . $loanData->sheetSendTime) }}
                            @else
                                -
                            @endif
                            </p>
                         <p><strong>Sheet Send By:  </strong>{{ getUserNameById('users', 'userID', optional($loanData)->addedBy, 'displayName') ?? '--' }} </p>
                         <p><strong>Disbursed Date:  </strong>
                            @if(!empty($loanData->disbursalDate) && !empty($loanData->disburseTime))
                                {{ dft($loanData->disbursalDate . ' ' . $loanData->disburseTime) }}
                            @else
                                --
                            @endif
                         </p>
                         <p><strong>Disbursed By: </strong>{{getUserNameById('users','userID',optional($loanData)->disbursedBy,'displayName')}} </p>
                      </div>
                   </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="main_pro_det">
                    <div class="pro_det_new">
                      <div class="main_toggle">
                         <!-- Hamburger Icon -->
                         <div class="dropdown" id="sidebar">
                            <a href="#" class="dropdown-toggle arrow-none card-drop menu-icon" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-menu"></i>
                            </a>
                            <div class="sidebar">
                               <div class="dropdown-menu dropdown-menu-end" style="">
                                  <!-- item-->
                                  <ul>
                                     @if($profileData->status=='Customer Approved')
                                    {{--  <li>
                                        <a href="#" class="action-button" data-url="{{ route('approvedToSanction',['leadID'=>$profileData->leadID]) }}" data-message="Approved To Sanction" data-bs-title="Approved To Sanction">
                                        <i class="ri-checkbox-circle-fill font-18 text-white"></i> Final Sanction
                                        </a>
                                     </li> --}}
                                    <li>
                                        <a href="javascript:void(0);" 
                                           data-bs-toggle="modal" 
                                           data-bs-target="#finalSanctionModal">
                                           <i class="ri-checkbox-circle-fill font-18 text-white"></i> Final Sanction
                                        </a>
                                    </li>

                                     @elseif($profileData->status=='Approved')
                                        @if($kycValidation==1)
                                            @if(empty($pennyData))
                                             <li>
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#penny-modal"> <i class="ri-checkbox-circle-fill font-18 text-white"></i> Penny Drop</a>
                                             </li>
                                            @endif
                                             <li>
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#disbursal-modal"> <i class="ri-checkbox-circle-fill font-18 text-white"></i> Pending For Disburse</a>
                                             </li>
                                        @else
                                            <li>
                                             <div class="text-white" role="alert">
                                                <i class="ri-alert-line text-white align-middle font-16"></i>KYC Pending!
                                             </div>
                                            </li>
                                        @endif 
                                     @elseif($profileData->status=='Pending For Disburse')
                                         
                                        <li>
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#disbursal-modal"> <i class="ri-checkbox-circle-fill font-18 text-white"></i> Disburse</a>
                                        </li>
                                         
                                     @elseif($profileData->status=='Disbursed' || $profileData->status=='EMI Running')
                                     <li>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#repayment-modal">  <i class="font-18 text-white">₹</i> Add Repayment</a>
                                     </li>
                                       @if ($approvalData->preEmiInterestDaysDiff < 30) 
                                            @if($preEmiAmountExists < 1)
                                                <li>
                                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#pre-emi-interest-modal">  <i class="font-18 text-white">₹</i> Add Pre-EMI Amount</a>
                                                </li> 
                                            @endif
                                       @endif  
                                    
                                     @else
                                     <li>
                                         <div class="text-white" role="alert">
                                            <i class="ri-alert-line text-white align-middle font-16"></i>Customer Approval Required!
                                         </div>
                                     </li>
                                     @endif
                                  </ul>
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
   <!-- end row-->
   <div class="row tab_con_pro">
      <div class="col-xl-12 col-lg-12">
         <div class="main_profile_tab_new">
            <div class="tab-navigation">
               <button class="scroll-left"><i class="fa fa-angle-left"></i></button>
               <div class="nav-tabs-container">
                  <ul class="nav nav-tabs new_desing_tab" id="mainTab">
                     <li class="nav-item"><a href="#general" data-bs-toggle="tab" class="nav-link active">
                        <img src="assets/images/img-pro/General.png" alt="user-image"><span>General</span></a>
                     </li>
                     <li class="nav-item"><a href="#repaymentschedule" data-bs-toggle="tab" class="nav-link"> <img src="assets/images/img-pro/Repayment.png" alt="user-image"><span>Repayment Schedule</span></a></li>
                     <li class="nav-item"><a href="#disburseddetails" data-bs-toggle="tab" class="nav-link"> <img src="assets/images/img-pro/Acount.png" alt="user-image"><span>Disbursement Details</span></a></li>
                     <li class="nav-item"><a href="#transactions" data-bs-toggle="tab" class="nav-link"> <img src="assets/images/img-pro/Transaction.png" alt="user-image"><span>Transactions</span></a></li>
                     <li class="nav-item"><a href="#soageneration" data-bs-toggle="tab" class="nav-link"> <img src="assets/images/img-pro/Soa.png" alt="user-image"><span>SOA Generation</span></a></li>
                  </ul>
               </div>
               <button class="scroll-right"><i class="fa fa-angle-right"></i></button>
            </div>
            <!-- Tab Content -->
            <div class="tab-content tab_con_in">
               <div class="tab-pane active" id="general">
                   <div class="table-responsive">
                     <table class="table custom-table">
                        <div class="main_head_tb">
                           <h3>Performance History</h3>
                        </div>
                        <tbody>
                           <tr>
                              <td><strong>Approved Amt :</strong></td>
                              <td>{{ nf($approvalData->loanAmtApproved)}}</td>
                              <td><strong>Disburse Amt :</strong></td>
                              <td>{{ nf($approvalData->disbursementAmount) ?? '-' }}</td>
                              <td><strong>PRE-EMI Amt :</strong></td>
                              @php
                                $preEmi = 0;
                                $preEmiText = '';

                                if ($approvalData->preEmiInterestDaysDiff > 30) {
                                    $preEmiText = $approvalData->preEmiInterest . ' (Recieveable)';
                                } else {
                                    $preEmiText = $approvalData->preEmiInterest . ' (Payable)';
                                }

                                $daysLabel = ($approvalData->preEmiInterestDays == 1) ? 'Day' : 'Days';
                            @endphp
                              <td>{{ $preEmiText }} {{ $approvalData->preEmiInterestDays }} {{ $daysLabel }}</td>
                              <td><strong>Total EMI's :</strong></td>
                              <td>{{$approvalData->tenure}}</td>
                              <td><strong>EMI Start:</strong></td>
                              <td>{{df($approvalData->paymentStartDate)}}</td>
                              <td><strong>EMI End:</strong></td>
                              <td>{{df($approvalData->paymentEndDate)}}</td>
                           </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <div class="main_head_tb">
                                <h3>Penny Verification Details</h3>
                            </div>
                            <thead>
                                <tr>
                                    <th>Customer Name </th>
                                    <th>Bank Name </th>
                                    <th>Account No.</th>
                                    <th>IFSC Code</th>
                                    <th>Status</th>
                                    <th>Requested By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                              @if ($pennyData)
                                <tr>
                                    <td>{{ $pennyData->customerName ?? '-' }}</td>
                                    <td>{{ $pennyData->bankName ?? '-' }}</td>
                                    <td>{{ $pennyData->accountNo ?? '-' }}</td>
                                    <td>{{ $pennyData->ifscCode ?? '-' }}</td>
                                    <td>
                                        @php $status = $pennyData->status ?? '-' @endphp
                                        @if($status === 'Verified')
                                            <span class="badge bg-success font-12 mr-2">{{ $status }}</span>
                                        @else
                                            <span class="badge bg-danger font-12 mr-2">{{ $status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ getUserNameById('users', 'userID', $pennyData->addedBy ?? null, 'displayName') ?? '-' }}</td>
                                    <td>{{ $pennyData->addedOn ? df($pennyData->addedOn) : '-' }}</td>
                                </tr>
                                @else
                                <tr>
                                    <td colspan="7" class="text-center">No data available</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @php
                        $totalOpening = 0;
                        $totalEMI = 0;
                        $totalPrincipal = 0;
                        $totalInterest = 0;
                        $totalClosing = 0;
                    @endphp
                    <div class="table-responsive">
                        <table class="table custom-table">
                            <div class="main_head_tb">
                                <h3>Pre-Repayment Schedule</h3>
                            </div>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Opening Balance</th>
                                    <th>EMI Amount</th>
                                    <th>Principal Amount</th>
                                    <th>Interest Amount</th>
                                    <th>Closing Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repaymentScheduleSanction as $key => $repayment)
                                    @php
                                        $totalOpening += $repayment->openingBalance;
                                        $totalEMI += $repayment->emiAmount;
                                        $totalPrincipal += $repayment->principalAmount;
                                        $totalInterest += $repayment->interestAmount;
                                        $totalClosing += $repayment->closingBalance;
                                    @endphp
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ df($repayment->paymentDate) }}</td>
                                        <td>{{ nf($repayment->openingBalance) }}</td>
                                        <td>{{ nf($repayment->emiAmount) }}</td>
                                        <td>{{ nf($repayment->principalAmount) }}</td>
                                        <td>{{ nf($repayment->interestAmount) }}</td>
                                        <td>{{ round($repayment->closingBalance) }}</td>
                                    </tr>
                                @endforeach
                    
                                <!-- Total Row -->
                                <tr style="font-weight: bold;background:#4743fa;">
                                    <td colspan="2" class="text-white">Total</td>
                                    <td class="text-white">{{ nf($totalOpening) }}</td>
                                    <td class="text-white">{{ nf($totalEMI) }}</td>
                                    <td class="text-white">{{ nf($totalPrincipal) }}</td>
                                    <td class="text-white">{{ nf($totalInterest) }}</td>
                                    <td class="text-white">{{ nf($totalClosing) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

               </div>
               <div class="tab-pane" id="repaymentschedule">
                    <div class="table-responsive">
                     <table class="table custom-table">
                        <div class="main_head_tb mt-2">
                        </div>
                        <thead>
                         <tr>
                            <th>#</th>
                            <th>Payment Date</th>
                            <th>Opening Balance</th>
                            <th>EMI Amount</th>
                            <th>Principal Amount</th>
                            <th>Interest Amount</th>
                            <th>Closing Balance</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($repaymentScheduleDisbursed as $key => $repayment)
                            <tr>
                                <td>{{ ++$key }}</td>
                                 <td>{{ df($repayment->paymentDate) }}</td>
                                <td>{{ nf($repayment->openingBalance) }}</td>
                                <td>{{ nf($repayment->emiAmount) }}</td>
                                <td>{{ nf($repayment->principalAmount) }}</td>
                                <td>{{ nf($repayment->interestAmount) }}</td>
                                <td>{{ round($repayment->closingBalance) }}</td>
                                <td>
                                    <span class="badge {{ $repayment->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $repayment->status == 1 ? 'Paid' : 'Due' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
                <div class="tab-pane" id="disburseddetails">
                  <div class="table-responsive">
                      <table class="table custom-table">
                        <div class="main_head_tb mt-2">
                        </div>
                        <thead>
                         <tr>
                            <th colspan="6" class="text-center">Disbursed Details</th>
                        </tr>
                        </thead>
                        @if(optional($loanData)->status=='Disbursed')
                        <tbody class="black-head">
                            <tr>
                                <th>Loan No.</th>
                                <th>Sheet Send Date</th>
                                <th>Disbursal Date</th>
                                <th>Disbursal Amount</th>
                                <th>Repay Amount</th>
                                <th>Repay Day</th>
                            </tr>
                            <tr>
                                <td>{{$loanData->loanNo ?? '-'}}</td>
                                <td>{{ optional($loanData)->sheetSendDate ? df(optional($loanData)->sheetSendDate) : '-' }} {{ optional($loanData)->sheetSendTime}}</td>
                                <td>{{ optional($loanData)->disbursalDate ? df(optional($loanData)->disbursalDate) : '-' }} {{ optional($loanData)->disburseTime}}</td>
                                <td>{{$loanData->disbursalAmount ?? '-'}}</td>
                                <td>{{$repaymentScheduleDisbursed[0]->emiAmount ?? '-'}}</td>
                                <td>{{$approvalData->repayDay}} of every month</td>
                            </tr>
                            <tr>
                                <th>Account No.</th>
                                <th>Bank Name</th>
                                <th>Bank Branch</th>
                                <th>IFSC Code</th>
                                <th>eNach ID</th>
                                <th>Loan UTR No.</th>
                            </tr>
                            <tr>
                                <td>{{$loanData->accountNo ?? '-'}}</td>
                                <td>{{$loanData->bank ?? '-'}}</td>
                                <td>{{$loanData->bankBranch ?? '-'}}</td>
                                <td>{{$loanData->ifscCode ?? '-'}}</td>
                                <td>{{$loanData->enachID ?? '-'}}</td>
                                <td>{{$loanData->disbursalUtrNo ?? '-'}}</td>     
                            </tr>
                            <tr>
                                <th>FI Done By</th>
                                <th>Disbursed By</th>
                                <th>Remarks</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>{{$approvalData->pdVerifedBy ?? 'Without PD'}}</td>
                                <td>{{getUserNameById('users','userID',optional($loanData)->disbursedBy,'displayName')}}</td>
                                <td>{{$loanData->remarks ?? '-'}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        @endif
                     </table>
                  </div>
               </div>
               <div class="tab-pane" id="transactions">
                  <div class="table-responsive">
                     <table class="table custom-table">
                        <div class="main_head_tb  mt-2">
                        </div>
                        <thead>
                           <tr>
                              <th>Installment #</th>
                              <th>Transaction Date</th>
                              <th>UTR/eNach No.</th>
                              <th>Transaction Type</th>
                              <th>Payment Mode</th>
                              <th>Amount</th>
                              <th>Remarks</th>
                           </tr>
                        </thead>
                        <tbody>
                          
                            @foreach($transactions as $trans)
                             <tr class="bold-row">
                              <td class="text-center">{{$trans->installmentNo}}</td>
                              <td>{{dft($trans->collectedDate)}}</td>
                              <td>{{$trans->enachID}}</td>
                              <td>{{$trans->paymentType}}</td>
                              <td>{{$trans->collectedMode}}</td>
                              <td>{{$trans->collectedAmount}}</td>
                              <td>{{$trans->remark}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                     </table>
                  </div>
               </div>
               <div class="tab-pane" id="soageneration">
                  <div class="table-responsive" data-simplebar data-simplebar-lg style="max-height:500px;">
                   <table class="table custom-table">
                        <div class="main_head_tb mt-2 mb-2 d-flex justify-content-end">
                            @if(optional($loanData)->status=='Disbursed')
                              <a href="soa/generate-soa/{{$profileData->leadID}}" class="btn btn-sm btn-success" target="_blank" data-bs-custom-class="success-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Generate SOA PDF"><i class="mdi mdi-eye"></i></a>
                            @endif
                        </div>
                  <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Enach No.</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Closing Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                                @if(optional($loanData)->status == 'Disbursed')
                                    @php
                                        // Initialize variables to calculate totals
                                        $totalDebit = 0;
                                        $totalCredit = 0;
                                        $runningBalance = 0;
                                        $isFirstPayment = 1;  
                                        $collectedAmount = 0;
                                    @endphp
                                    
                                    @php 
                                        $adminFeeWithGst = $approvalData->adminFee + $approvalData->adminGstAmount;  
                                        $stampDuty = $approvalData->stampDuty;
                                        $gapInterest = $approvalData->preEmiInterest;
                                        
                                        // Calculate footer amounts
                                        $amtPaidToCust = $approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty - $gapInterest;
                                        $totalDebit += $amtPaidToCust;
                                        
                                        if($approvalData->preEmiInterestDaysDiff < 30) {
                                            $totalDebit += $gapInterest;
                                            $amtPaidToCust+=$gapInterest;
                                        }
                                        
                                        $totalDebit += $stampDuty;
                                        $totalDebit += $adminFeeWithGst;
                                        $totalCredit += $approvalData->loanAmtApproved;
                                        
                                        $closing1 = $approvalData->loanAmtApproved - $adminFeeWithGst;
                                        $closing2 = $approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty;
                                        $closing3 = $approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty - $gapInterest;
                                        $closing = $totalDebit + $closing1 + $closing2 + $closing3;
                                    @endphp
                            
                                    @foreach($paidSchedule as $key => $repayment)
                                        @php 
                                            $installmentNo = $loop->count - $loop->iteration + 1;
                                           
                                        @endphp
                                        
                                        @php
                                            // Update total debit
                                            $totalDebit += $repayment->emiAmount;
                            
                                            // If payment exists, show Payment Received
                                            if (isset($collections[$installmentNo])) {
                                                // Payment Received Row (if exists)
                                                $collectedAmount = $collections[$installmentNo]->collectedAmount;
                                                $remainingAmount = $repayment->emiAmount - $collectedAmount;
                                                $totalCredit += $collectedAmount;
                                                $runningBalance += $remainingAmount;
                            
                                                // Only set to false after the first payment is received
                                            } else {
                                                // If no payment is received, the remaining balance will just be the full due amount
                                                $remainingAmount = $repayment->emiAmount;
                                                $runningBalance += $remainingAmount;
                                            }
                                        @endphp
                            
                                        <!-- Display Pre EMI Interest only after the first payment is received -->
                                        @if($approvalData->preEmiInterestDaysDiff < 30)
                                            @if($installmentNo==1)
                                                @if($preEmiAmountExists > 0)
                                                    <tr>
                                                        <td>{{ df($collections[$installmentNo]->collectedDate ?? '--') }}</td>
                                                        <td>Pre Emi Interest (paid to customer)</td>
                                                        <td>{{ optional($loanData)->enachID }}</td>
                                                        <td>{{ nf($gapInterest) }}</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td>{{ df($collections[$installmentNo]->collectedDate ?? '--') }}</td>
                                                    <td>Pre Emi Interest (excess - payable to customer)</td>
                                                    <td>--</td>
                                                    <td>0</td>
                                                    <td>{{ nf($gapInterest) }}</td>
                                                    <td>-{{ nf($gapInterest) }}</td>
                                                </tr>
                                            @endif    
                                        @endif
                                       
                                        <!-- Row for Payment Received (if exists) -->
                                        @if(isset($collections[$installmentNo]))
                                           @if($collectedAmount > 0)
                                            <tr>
                                                <td>{{ df($collections[$installmentNo]->collectedDate) }}</td>
                                                <td>Payment Received {{ $installmentNo }}</td>
                                                <td>{{ $collections[$installmentNo]->enachID ?? '-' }}</td>
                                                <td>0</td>
                                                <td>{{ nf($collectedAmount) }}</td>
                                                <td>{{ nf($remainingAmount) }}</td>
                                            </tr>
                                           @endif
                                        @endif
                                        
                                        <!-- Row for Installment Due -->
                                        <tr>
                                            <td>{{ df($repayment->paymentDate) }}</td>
                                            <td>Due for Installment {{ $installmentNo }}</td>
                                            <td>--</td>
                                            <td>{{ nf($repayment->emiAmount) }}</td>
                                            <td>0</td>
                                            <td>{{ nf($repayment->emiAmount) }}</td>
                                        </tr>
                                         @php $isFirstPayment = 0; @endphp 
                                    @endforeach
                             
                            </tbody>
 
                        <tfoot>
                            
                           
                            @if($approvalData->preEmiInterestDaysDiff < 30)
                              <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Amount Paid to Customer</td>
                                <td>{{optional($loanData)->enachID}}</td>
                                <td>{{ nf($amtPaidToCust) }}</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            @else
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Amount Paid to Customer</td>
                                <td>{{optional($loanData)->enachID}}</td>
                                <td>{{ nf($amtPaidToCust) }}</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Pre EMI interest</td>
                                <td>--</td>
                                <td>{{ nf($gapInterest) }}</td>
                                <td>0</td>
                                <td>-{{ nf($approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty - $gapInterest) }}</td>
                            </tr>
                            
                            @endif

                            
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Due for Stamp Duty</td>
                                <td>--</td>
                                <td>{{ nf($stampDuty) }}</td>
                                <td>0</td>
                                <td>-{{ nf($approvalData->loanAmtApproved - $adminFeeWithGst - $stampDuty) }}</td>
                            </tr>
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Due for Processing Fees from Customer</td>
                                <td>--</td>
                                <td>{{ nf($adminFeeWithGst) }}</td>
                                <td>0</td>
                                <td>-{{ nf($approvalData->loanAmtApproved - $adminFeeWithGst) }}</td>
                            </tr>
                            <tr>
                                <td>{{ df(optional($loanData)->disbursalDate) }}</td>
                                <td>Amt Financed - Payable</td>
                                <td>--</td>
                                <td>0</td>
                                <td>{{ nf($approvalData->loanAmtApproved) }}</td>
                                <td>-{{ nf($approvalData->loanAmtApproved) }}</td>
                            </tr>
                            <tr style="background:#4743fa;position:sticky;">
                                <td colspan="3" class="text-white"><strong>Total</strong></td>
                                <td class="text-white"><strong>{{ nf($totalDebit) }}</strong></td>
                                <td class="text-white"><strong>{{ nf($totalCredit) }}</strong></td>
                                <td class="text-white"><strong>-{{ nf($closing) }}</strong></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                  </div>
               </div>
            </div>
         </div>
         <!-- end card -->
      </div>
   </div>
</div>
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
<div id="penny-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h4 class="modal-title" id="disbursal-modalLabel">Penny Drop Verification Form</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form id="penny-form" method="post" autocomplete="off">
               @csrf   
                <div class="row">
                   <div class="col-md-6">
                     <div class="mb-1">
                        <label class="form-label">Customer Name</label>
                        <input type="text" class="form-control" name="pennycustName"  value="{{$profileData->name ?? ''}}"  placeholder="Bank name" readonly>
                        <span class="pennycustNameErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" name="pennyMobile"  value="{{$profileData->mobile ?? ''}}" placeholder="Mobile">
                        <span id="ifscCodeErr" class="text-danger font-12"></span>
                        <span class="pennyMobileErr"></span>
                     </div>
                  </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" name="pennyIfscCode"  value="{{$profileData->ifscCode ?? ''}}" placeholder="IFSC code">
                        <span id="ifscCodeErr" class="text-danger font-12"></span>
                        <span class="pennyIfscCodeErr"></span>
                     </div>
                  </div>
                   <div class="col-md-6">
                     <div class="mb-1">
                        <label class="form-label">Bank Name</label>
                        <input type="text" class="form-control" name="pennyBankName" id="bank"  value="{{$profileData->bankName ?? ''}}"  placeholder="Bank name">
                        <span class="pennyBankNameErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Account No.</label>
                        <input type="text" class="form-control" name="pennyAccountNo" value="{{$profileData->accountNo ?? ''}}" placeholder="Account no.">
                        <span class="pennyAccountNoErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Confirm Account No.</label>
                        <input type="password" class="form-control" name="pennyCnfrmAccountNo" value="" oncopy="return false"  onpaste="return false" oncut="return false" placeholder="Account no.">
                        <span class="pennyCnfrmAccountNoErr"></span>
                     </div>
                  </div>
                  <div class="col-md-12 d-flex justify-content-center">
                     <input type="hidden" name="leadID" id="leadID" value="{{$profileData->leadID}}">
                     <input type="hidden" name="contactID" id="contactID" value="{{$profileData->contactID}}">
                     <button type="button" id="pennyBtn" class="btn btn-sm btn-primary">Verify</button>
                  </div>
                </div>
            </form>
         </div>
       </div>
    </div>
</div>

<div id="disbursal-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h4 class="modal-title" id="disbursal-modalLabel">Disbursal Form</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form id="disbursal-form" method="post">
               @csrf   
                <div class="row">
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Approval Amount</label>
                        <input type="text" class="form-control" name="disburseAmount" value="{{$approvalData->loanAmtApproved}}" readonly>
                        <span class="disburseAmountErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Disbursal Amount</label>
                        <input type="text" class="form-control" name="disburseAmount" value="{{$approvalData->disbursementAmount}}" readonly>
                        <span class="disburseAmountErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Account No.</label>
                        <input type="text" class="form-control" name="accountNo" value="{{optional($pennyData)->accountNo ?? ''}}" placeholder="Account no.">
                        <span class="accountNo"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" name="ifscCode" id="ifscCode" value="{{optional($pennyData)->ifscCode ?? ''}}" placeholder="IFSC code">
                        <span id="ifscCodeErr" class="text-danger font-12"></span>
                        <span class="ifscCodeErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-1">
                        <label class="form-label">Bank Name</label>
                        <input type="text" class="form-control" name="bankName" id="bank"  value="{{optional($pennyData)->bankName ?? ''}}" placeholder="Bank name" >
                        <span class="bankNameErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Branch</label>
                        <input type="text" class="form-control" name="branch" id="branch" value="{{$loanData->bankBranch ?? $profileData->bankBranch}}" placeholder="Branch" readonly>
                        <span class="branchErr"></span>
                     </div>
                  </div>
                   <div class="col-md-6">
                     <div class="mb-1">
                        <label class="form-label">Enach ID</label>
                        <input type="text" class="form-control" name="enachID" id="enachID"  value="{{$loanData->enachID ?? ''}}"  placeholder="Enach ID" @if(!empty($loanData->enachID)) readonly @endif>
                        <span class="enachIDErr"></span>
                     </div>
                  </div>
                  @if($profileData->status=='Pending For Disburse')
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Disbursal UTR No.</label>
                        <input type="text" class="form-control" name="disbursalUtrNo" id="disbursalUtrNo" value="{{$loanData->disbursalUtrNo ?? ''}}" placeholder="Disbursal utr no.">
                        <span class="disbursalUtrNoErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <label class="form-label">Remarks</label>
                     <input type="text" class="form-control" name="remarks" id="remarks" value="{{$loanData->remarks ?? ''}}"  placeholder="Remarks">
                     <span class="remarksErr"></span>
                  </div>
                  @endif
                  <div class="col-md-12 d-flex justify-content-center">
                     <input type="hidden" name="leadID" id="leadID" value="{{$profileData->leadID}}">
                     @if($profileData->status=='Approved')
                        <button type="button" id="sheetSendBtn" class="btn btn-sm btn-primary">Submit</button>
                     @else
                        <button type="button" id="disburseBtn" class="btn btn-sm btn-primary">Submit</button>
                     @endif
                  </div>
                </div>
            </form>
         </div>
       </div>
    </div>
</div>

<div class="modal fade" id="finalSanctionModal" tabindex="-1" role="dialog" aria-labelledby="finalSanctionModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Final Sanction</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form id="finalSanctionForm">
               <input type="hidden" name="leadID" id="finalSanctionLeadID" value="{{$profileData->leadID}}">
               <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-control" name="status" id="sanctionStatus">
                     <option value="">-- Select --</option>
                     <option value="Approved">Approved</option>
                     <option value="Rejected">Rejected</option>
                  </select>
                  <span class="text-danger statusErr"></span>
               </div>
               <div class="mb-3">
                  <label class="form-label">Remarks</label>
                  <textarea class="form-control" name="remarks" id="sanctionRemarks" rows="3" placeholder="Enter remarks..."></textarea>
                  <span class="text-danger remarksErr"></span>
               </div>
               <div class="text-center">
                  <button type="button" id="finalSanctionBtn" class="btn btn-sm btn-primary">Submit</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<div id="repayment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h4 class="modal-title" id="disbursal-modalLabel">Repayment Form</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form id="repayment-form" method="post">
               @csrf   
                <div class="row">
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input type="text" class="form-control" name="repayAmount" id="dueDate" value="{{df(optional($lastPaymentDetails)->paymentDate)}}" readonly>
                        <span class="dueDateErr"></span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Repayment Amount</label>
                        <input type="text" class="form-control" name="repayAmount" id="repayAmount" value="{{optional($lastPaymentDetails)->emiAmount}}" readonly>
                        <span class="repayAmountErr"></span>
                     </div>
                  </div>
                  
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Penal Charges</label>
                        <input type="text" class="form-control" name="penalCharges"  id="penalCharges" value="" placeholder="Penal charges">
                        <span class="penalChargesErr"></span>
                     </div>
                  </div>
                   <div class="col-md-6">
                        <div class="mb-2">
                            <label class="mb-1 font-12">Collected Date</label>
                             <input type="date" class="form-control " name="collectedDate" id="collectedDate">    
                            <span class="collectedDateErr"></span>
                        </div>
                    </div>
                  <div class="col-md-6">
                      <div class="mb-3">
                         <label class="form-label">Collection Mode</label>
                         <select id="collectionMode" class="form-control valid" name="collectionMode">
                            <option value="">Choose Collection Mode</option>
                            <option value="E-Nach">E-Nach</option>
                            <option value="Cash">Cash</option>
                            <option value="Account">Account</option>
                            <option value="UPI">UPI</option>
                         </select>
                            <span class="collectionModeErr"></span>
                      </div>
                   </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">UTR/eNach No.</label>
                        <input type="text" class="form-control" name="collectionUtrNo" id="collectionUtrNo" value="" placeholder="UTR/eNach No.">
                        <span class="collectionUtrNoErr"></span>
                     </div>
                  </div>
                   <div class="col-md-6">
                        <div class="mb-3">
                        <label class="form-label">Collection Source</label>
                          <select class="form-control" name="collectionSource" id="collectionSource">
                            <option value="Collection">Collection</option>
                            <option value="Recovery">Recovery</option>
                            <option value="Legal">Legal</option>
                            <option value="NPA">NPA</option>
                            <option value="Others">Others</option>
                         </select>
                         <span class="collectionSourceErr"></span>
                         </div>
                  </div>
                   <div class="col-md-6">
                      <div class="mb-3">
                         <label class="form-label">Status</label>
                          <select id="collectionStatus" class="form-control valid" name="status">
                            <option value="">Choose Status</option>
                            <option value="EMI Running">EMI Running</option>
                            {{-- <option value="Part Payment">Part Payment</option> --}}
                            <option value="Settlement">Settlement</option>
                            <option value="Pre Closed">Pre Closed</option>
                            <option value="Closed">Closed</option>
                          </select>
                           <span class="statusErr"></span>
                      </div>
                   </div>
                  <div class="col-md-12">
                     <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" name="remark"  id="remark" placeholder="Remarks">
                        <span class="remarkErr"></span>
                     </div>
                  </div>
                  
                  <div class="col-md-12 d-flex justify-content-center">
                        <input type="hidden" id="repaymentID" value="{{optional($lastPaymentDetails)->id}}">
                        <input type="hidden" id="installmentNo" value="{{optional($lastPaymentDetails)->installment}}">
                        <button type="button" id="repaymentBtn" class="btn btn-sm btn-primary">Add Payment</button>
                  </div>
                </div>
            </form>
         </div>
       </div>
    </div>
</div>


<div id="pre-emi-interest-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
   <div class="modal-dialog  modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header bg-primary text-white">
            <h4 class="modal-title" id="disbursal-modalLabel">Pre EMI Interest Form</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form id="pre-emi-interest-form" method="post">
               @csrf   
                <div class="row">
                  <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="text" class="form-control" name="interestAmount" id="interestAmount" value="{{$approvalData->preEmiInterest}}" readonly>
                        <span class="repayAmountErr"></span>
                     </div>
                  </div>
               
                   <div class="col-md-6">
                        <div class="mb-2">
                            <label class="mb-1 font-12">Payment Date</label>
                             <input type="date" class="form-control " name="paymentDate" id="paymentDate">    
                            <span class="paymentDateErr"></span>
                        </div>
                    </div>
                  <div class="col-md-6">
                      <div class="mb-3">
                         <label class="form-label">Payment Mode</label>
                         <select id="paymentMode" class="form-control valid" name="paymentMode">
                            <option value="">Choose Payment Mode</option>
                            <option value="E-Nach">E-Nach</option>
                            <option value="Cash">Cash</option>
                            <option value="Account">Account</option>
                            <option value="UPI">UPI</option>
                         </select>
                            <span class="paymentModeErr"></span>
                      </div>
                   </div>
                   <div class="col-md-6">
                     <div class="mb-3">
                        <label class="form-label">UTR/eNach No.</label>
                        <input type="text" class="form-control" name="paymentUtrNo" id="paymentUtrNo" value="" placeholder="UTR no.">
                        <span class="paymentUtrNoErr"></span>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <input type="text" class="form-control" name="remark"  id="intRemark" placeholder="Remarks">
                        <span class="remarkErr"></span>
                     </div>
                  </div>
                  
                  <div class="col-md-12 d-flex justify-content-center">
                        <input type="hidden" name="leadID" id="intEmileadID" value="{{$profileData->leadID}}">
                        <button type="button" id="emiInterestBtn" class="btn btn-sm btn-primary">Add Payment</button>
                  </div>
                </div>
            </form>
         </div>
       </div>
    </div>
</div>
</div>
@endsection
@section('custom-js')
<script>
$(document).ready(function() {
    let redirectUrl; // Variable to store the URL

    // When an action button is clicked
    $('.action-button').on('click', function(event) {
        event.preventDefault(); // Prevent default behavior
        redirectUrl = $(this).data('url'); // Get the URL from data attribute
        const message = $(this).data('message'); // Get the message for the modal

        // Update modal message dynamically
        $('#info-alert-modal .modal-body p').text(message);
        
        // Show the modal
        $('#info-alert-modal').modal('show');
    });

    // When the Yes button in the modal is clicked
    $('.confirm-yes').on('click', function() {
        window.location.href = redirectUrl;
    });
    
    $('#ifscCode').on('keyup', function() {
        var ifscCode = $(this).val().trim();
        if (ifscCode.length !== 11) {
            $('#branch').val('');
            $('#ifscCodeErr').text('IFSC code must be 11 characters long').show();
            return;
        }  
        $('#ifscCodeErr').hide().text('');
        fetchIfscDetails(ifscCode);
    });
    
    $('#sheetSendBtn').click(function() {
        // Create FormData object from the form
        var formData = new FormData($('#disbursal-form')[0]);

        // AJAX request
        $.ajax({
            type: 'POST',
            url: "{{ route('pendingToDisburse') }}",  // URL for the backend route
            data: formData,
            processData: false,  // Don't process the data
            contentType: false,  // Don't set content type
            success: function(data) {
                if (data.response === 'success') {
                    // Show notification or success message
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    // Close the modal
                    $('#disbursal-modal').modal('hide');
                    // Optionally reset the form fields
                    $('#disbursal-form').trigger('reset');
                    setTimeout(function() { window.location.reload(); }, 1000); 
                } else if (data.response === 'exist') {
                    $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                } else {
                    // Handle error (display error messages for specific fields)
                    printErrorMsg(data.error);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);  // Handle AJAX error
            }
        });
    });
    
    $('#disburseBtn').click(function() {
        // Get values from the form
        var disbursalUtrNo = $('#disbursalUtrNo').val();
        var remarks = $('#remarks').val();
        var leadID = $('#leadID').val();
      
        // AJAX request
        $.ajax({
            url: "{{ route('approvedToDisburse') }}",  // URL for the backend route
            type: 'POST',
            data: {
                disbursalUtrNo: disbursalUtrNo,
                remarks: remarks,
                leadID: leadID
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Add CSRF token directly to the header
            }, 
            success: function(data) { 
                if (data.response === 'success') {
                    // Show notification or success message
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    
                    // Close the modal
                    $('#disbursal-modal').modal('hide');
                    
                    // Optionally reset the form fields
                    $('#disbursal-form').trigger('reset');
                    
                    // Reload the page after a short delay
                    setTimeout(function() { window.location.reload(); }, 1000); 
                } else if (data.response === 'success') {
                    // This condition is duplicate of the first one
                } else {
                    // Handle error (display error messages for specific fields)
                    printErrorMsg(data.error);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);  // Handle AJAX error
            }
        });
    });
    
    // Fill leadID when modal opens
        $(document).on('click', '.action-button', function () {
            let leadID = $(this).data('leadid');
            $('#finalSanctionLeadID').val(leadID);
        });

        $('#finalSanctionBtn').click(function () {
            let leadID = $('#finalSanctionLeadID').val();
            let status = $('#sanctionStatus').val();
            let remarks = $('#sanctionRemarks').val();

            
            $.ajax({
                url: "{{ route('approvedToFinal') }}", // Adjust to your actual route
                type: 'POST',
                data: {
                    leadID: leadID,
                    status: status,
                    remarks: remarks,
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (data) {
                    if (data.response === 'success') {
                        $.NotificationApp.send("Success", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");

                        // Optional: Reset error messages
                        $('.sanctionStatusErr').text('');
                        $('.sanctionRemarksErr').text('');

                        $('#finalSanctionModal').modal('hide');
                        $('#finalSanctionForm').trigger('reset');

                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    } else {
                          // Handle error (display error messages for specific fields)
                          printErrorMsg(data.error);
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

    
   $('#pennyBtn').click(function () {
        var formData = $('#penny-form').serialize(); // Serialize the complete form
    
        $.ajax({
            url: "{{ route('pennyVerification') }}",
            type: 'POST',
            data: formData, // send all form fields
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (data) {
                if (data.response === 'success') {
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
    
                    $('#penny-modal').modal('hide'); // corrected modal ID
                    $('#disbursal-form').trigger('reset');
    
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    printErrorMsg(data.error);
                }
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    });

    
   $('#repaymentBtn').click(function() {
        // Get values from the form
        var dueDate = $('#dueDate').val();
        var repayAmount = $('#repayAmount').val();
        var penalCharges = $('#penalCharges').val();
        var installmentNo = $('#installmentNo').val();
        var collectionMode = $('#collectionMode').val();
        var collectionUtrNo = $('#collectionUtrNo').val();
        var collectionSource = $('#collectionSource').val();
        var collectedDate = $('#collectedDate').val();
        var collectionStatus = $('#collectionStatus').val();
        var remark = $('#remark').val();
        var leadID = $('#leadID').val();
        var repaymentID = $('#repaymentID').val();
        
        // AJAX request
        $.ajax({
            url: "{{ route('addRepayment') }}",  // URL for the backend route
            type: 'POST',
            data: {
                dueDate: dueDate,
                repayAmount: repayAmount,
                penalCharges: penalCharges,
                installmentNo: installmentNo,
                repaymentID: repaymentID,
                collectionMode: collectionMode,
                collectionSource: collectionSource,
                collectionUtrNo: collectionUtrNo,
                collectedDate: collectedDate,
                status: collectionStatus,
                remark: remark,
                leadID: leadID
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'  // Add CSRF token directly to the header
            }, 
            success: function(data) { 
                if (data.response === 'success') {
                    // Show notification or success message
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                    
                    // Close the modal
                    $('#disbursal-modal').modal('hide');
                    
                    // Optionally reset the form fields
                    $('#disbursal-form').trigger('reset');
                    
                    // Reload the page after a short delay
                    setTimeout(function() { window.location.reload(); }, 1000); 
                } else if (data.response === 'exist') {
                    $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                } else {
                    // Handle error (display error messages for specific fields)
                    printErrorMsg(data.error);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);  // Handle AJAX error
            }
        });
    });


   $('#emiInterestBtn').click(function () {
        // Get values from form
        var interestAmount = $('#interestAmount').val();
        var paymentDate = $('#paymentDate').val();
        var paymentMode = $('#paymentMode').val();
        var paymentUtrNo = $('#paymentUtrNo').val();
        var remark = $('#intRemark').val();
        var leadID = $('#intEmileadID').val();

     
        // AJAX request
        $.ajax({
            url: "{{ route('addEmiInterest') }}",
            type: 'POST',
            data: {
                interestAmount: interestAmount,
                paymentDate: paymentDate,
                paymentMode: paymentMode,
                paymentUtrNo: paymentUtrNo,
                remark: remark,
                leadID: leadID,
                _token: '{{ csrf_token() }}'
            },
            success: function (data) {
                if (data.response === 'success') {
                    $.NotificationApp.send("Well Done!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");

                    $('#pre-emi-interest-modal').modal('hide');
                    $('#pre-emi-interest-form').trigger('reset');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else if (data.response === 'exist') {
                    $.NotificationApp.send("Oh Snap!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "error");
                } else {
                    printErrorMsg(data.error);
                }
            },
            error: function (xhr, status, error) {
                console.log('AJAX Error:', error);
            }
        });
    });
 

    function fetchIfscDetails(ifscCode) {
        $.ajax({
            url: 'https://ifsc.razorpay.com/' + ifscCode,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.STATE && response.CITY) {
                    $('#branch').val(capitalizeFirstLetter(response.BRANCH) + ", " + capitalizeFirstLetter(response.CITY) + ", " + capitalizeFirstLetter(response.DISTRICT));
                    $('#bank').val(response.BANK);
                    $('.branchErr').hide().text('');
                } else {
                    $('#branch').val('');
                    $('#bank').val('');
                    $('.branchErr').text('No data found for this IFSC code').show();
                }
            },
            error: function() {
                $('#branch').val('');
                $('#bank').val('');
                $('.branchErr').text('Error fetching data. Please try again later.').show();
            }
        });
    } 
    

     $(document).on('click', '.estamp-action', function () {
                var $btn = $(this);
                var url = $btn.data('url');
                var leadID = $btn.data('lead');
                var action = $btn.data('action');

                var $container = $('#estampActionContainer_' + leadID);

                // Show loading spinner
                $container.html(`
                <div class="d-flex justify-content-left align-items-center mb-2">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        $('#estampSpinner').hide();
                        $('#initiateEstampBtn').prop('disabled', false);
            
                        if (data.response === 'success') {
                            $.NotificationApp.send("Success!", data.message, "bottom-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function () { location.reload(); }, 1000);
                        } else {
                            $.NotificationApp.send("Failed!", data.message || "Unexpected error", "bottom-right", "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    error: function (xhr) {
                        $('#estampSpinner').hide();
                        $('#initiateEstampBtn').prop('disabled', false);
            
                        let errorMessage = xhr.responseJSON?.message || 'Something went wrong.';
                        $.NotificationApp.send("Error", errorMessage, "bottom-right", "rgba(0,0,0,0.2)", "error");
                    }
                });
            });

       
    // Helper function to capitalize only the first letter of the string
    function capitalizeFirstLetter(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }    
    
    function printErrorMsg(msg) {
        $('.errClr').remove();
        $.each(msg, function(key, value) {
            $('.' + key + 'Err').html('<p class="text-danger mt-1 errClr font-12"><i class="ri-close-circle-line me-1 align-middle font-12"></i><strong>' + value + '</strong></p>');
        });
    }
});
</script>
@endsection