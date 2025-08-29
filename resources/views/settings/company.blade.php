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
                <div class="col-9">
                    <div class="card">
                        <div class="card-header text-end">
                            
                        </div>
                        <div class="card-body">
                                           
                           <ul class="nav nav-tabs mb-3">
                                <li class="nav-item">
                                    <a href="#organisation" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                        <i class="mdi mdi-home-variant d-md-none d-block"></i>
                                        <span class="d-none d-md-block">Organisation</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#apiDetails" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                                        <i class="mdi mdi-account-circle d-md-none d-block"></i>
                                        <span class="d-none d-md-block">API's / Payment Gateway / SMS API</span>
                                    </a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a href="#target" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                                        <span class="d-none d-md-block">Target`</span>
                                    </a>
                                </li> -->
                            </ul>
 
                            <div class="tab-content">
                                <div class="tab-pane active" id="organisation">
                                    <form action="settings/organisation-add" method="post" enctype="multipart/form-data">
                                        @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Company Name</label>
                                            <input type="text" id="simpleinput" name="companyName" class="form-control mb-1" placeholder="Company Name" value="{{$cmpData->companyName ?? ''}}">
                                            @error('companyName')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Company Domain</label>
                                            <input type="text" id="simpleinput" name="domain" class="form-control mb-1" placeholder="Company Domain" value="{{$cmpData->domain ?? ''}}">
                                            @error('domain')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Company Email</label>
                                            <input type="text" id="simpleinput" name="email" class="form-control mb-1" placeholder="Company Email" value="{{$cmpData->email ?? ''}}">
                                            @error('email')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Employee ID Prefix</label>
                                            <input type="text" id="simpleinput" name="empIdPrefixes" class="form-control mb-1" placeholder="e.g. EMP101" value="{{$cmpData->empIdPrefixes ?? ''}}">
                                            @error('empIdPrefixes')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Loan No. Prefix</label>
                                            <input type="text" id="simpleinput" name="loanNoPrefixes" class="form-control mb-1" placeholder="e.g. ABC" value="{{$cmpData->loanNoPrefixes ?? ''}}">
                                            @error('loanNoPrefixes')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">GST No.</label>
                                            <input type="text" id="simpleinput" name="gst" class="form-control mb-1" placeholder="GST No." value="{{$cmpData->gst ?? ''}}">
                                            @error('gst')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Account No.</label>
                                            <input type="text" id="simpleinput" name="accountNo" class="form-control mb-1" placeholder="Account No" value="{{$cmpData->accountNo ?? ''}}">
                                            @error('accountNo')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Account Name</label>
                                            <input type="text" id="simpleinput" name="accountName" class="form-control mb-1" placeholder="Account Name" value="{{$cmpData->accountName ?? ''}}">
                                            @error('accountName')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Bank Name</label>
                                            <input type="text" id="simpleinput" name="bankName" class="form-control mb-1" placeholder="Bank Name" value="{{$cmpData->bankName ?? ''}}">
                                            @error('bankName')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Bank IFSC</label>
                                            <input type="text" id="simpleinput" name="bankIfsc" class="form-control mb-1" placeholder="Bank IFSC" value="{{$cmpData->bankIfsc ?? ''}}">
                                            @error('bankIfsc')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Company Address</label>
                                            <textarea id="simpleinput" rows="9" name="address" class="form-control mb-1" placeholder="Company Address">{{$cmpData->address ?? ''}}</textarea> 
                                            @error('address')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2 dropzone">
                                            <label for="simpleinput" class="form-label">Company Logo</label>
                                              <input name="logo" type="file" id="file" style="visibility: hidden;"/>
                                            <a href="javascript:void(0)"  id="uploadIcon"> 
                                                 <div class="dz-message needsclick">
                                                    <i class="h1 text-muted ri-upload-cloud-2-line"></i>
                                                    <h3>Click to upload Logo.</h3>
                                                  </div>
                                            </a>
                                            @error('logo')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                         @if(!empty($cmpData->logo))
                                             <div class="row">
                                               <img src="{{ Storage::url($cmpData->logo) }}" alt="Logo" style="width:250px;">
                                             </div>
                                          @endif                                      
                                        <div class="col-md-12 mb-2 text-end">
                                            <input type="hidden" name="id" value="{{$cmpData->id ?? ''}}">
                                            <input type="hidden" name="oldLogo" value="{{$cmpData->logo ?? ''}}">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                         </div>
                                    </div>
                                </form>
                                </div>
                                <div class="tab-pane show " id="apiDetails">
                                    <form action="settings/api-add" method="post">
                                        @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label for="productType" class="form-label">Product Type</label>
                                            <select id="productType" name="productType" class="form-select mb-1">
                                                <option value="">Select Product Type</option>
                                                <option value="SMS">SMS API</option>
                                                <option value="BANK">Bank API</option>
                                                <option value="Payment Gateway">Payment Gateway</option>
                                                <!-- Add more options here as needed -->
                                            </select>
                                            @error('productType')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Product Name</label>
                                            <input type="text" id="simpleinput" name="name" class="form-control mb-1" placeholder="Product Name" value="">
                                           @error('name')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">API</label>
                                            <input type="text" id="simpleinput" name="api" class="form-control mb-1" placeholder="Api" value="">
                                           @error('api')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Test Key</label>
                                            <input type="text" id="simpleinput" name="testKey" class="form-control mb-1" placeholder="Test Key" value="">
                                           @error('testKey')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Live Key</label>
                                            <input type="text" id="simpleinput" name="liveKey" class="form-control mb-1" placeholder="Live Key" value="">
                                           @error('liveKey')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2 text-end">
                                            <input type="hidden" name="id" value="{{$cmpPrefixes->id ?? ''}}">
                                          <button type="submit" class="btn btn-primary">Save changes</button>
                                         </div>
                                    </div>
                                </form>
                                </div>
                                <div class="tab-pane" id="target">
                                    <form action="settings/prefixes-add" method="post">
                                        @csrf
                                    <div class="row">
                                         <div class="col-md-6 mb-2">
                                            <label for="simpleinput" class="form-label">Live Key</label>
                                            <input type="text" id="simpleinput" name="liveKey" class="form-control mb-1" placeholder="Live Key" value="Test Key">
                                           @error('liveKey')
                                            <div class="alert alert-danger" role="alert">
                                                <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-12 mb-2 text-end">
                                            <input type="hidden" name="id" value="{{$cmpPrefixes->id ?? ''}}">
                                          <button type="submit" class="btn btn-primary">Save changes</button>
                                         </div>
                                    </div>
                                </form>
                                </div>
                                <div class="tab-pane" id="settings1">v
                                   
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            @endsection


@section('custom-js')

<script>
$('#uploadIcon').click(function() {
    $('#file').click();
});
</script>
@endsection