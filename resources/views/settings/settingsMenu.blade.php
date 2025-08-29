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
 
<div class="container">
    <div class="row">
        {{-- <div class="col-md-3 mt-3">
            <a href="settings/branches" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-office-building"></i> Branches</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div> --}}
         @if(role()=='CRM Support' || isSuperAdmin())      
        <div class="col-md-3 mt-3">
            <a href="settings/departments" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-briefcase-account"></i> Departments</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mt-3">
            <a href="settings/designations" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-account-tie"></i> Designations</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif
      
      @if(isSuperAdmin())  
        <div class="col-md-3 mt-3">
            <a href="settings/modules" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-view-dashboard"></i> Modules</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
         <div class="col-md-3 mt-3">
            <a href="settings/optional-modules" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-view-dashboard"></i> Optional Modules</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
      @endif   
        @if(role()=='CRM Support' || isSuperAdmin())
        <div class="col-md-3 mt-3">
            <a href="settings/draggable-menu" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-drag"></i> Dragg Menu</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
       @endif      
      @if(role()=='CRM Support' || isAdmin() || isSuperAdmin())      
        <div class="col-md-3 mt-3">
            <a href="settings/branch-target" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-target"></i> Branches Target</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mt-3">
            <a href="settings/sanction-target" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-flag-checkered"></i> Sanctions Target</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif    
     @if(isSuperAdmin())      
        <div class="col-md-3 mt-3">
            <a href="settings/leads-status" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-chart-line"></i> Leads Status</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
      @endif    
      
        @if(role()=='CRM Support' || isSuperAdmin())      
        <div class="col-md-3 mt-3">
            <a href="settings/roi" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-percent"></i> ROI's</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
         @endif    
         @if(role()=='CRM Support' || isAdmin() || isSuperAdmin())       
        <div class="col-md-3 mt-3">
            <a href="settings/approval-matrix-list" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-database"></i>Approval Matrix</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
          @endif    
         @if(role()=='CRM Support' || isSuperAdmin())        
        <div class="col-md-3 mt-3">
            <a href="settings/states" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-map-marker"></i> States</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mt-3">
            <a href="settings/cities" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-city"></i> Cities</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 mt-3">
            <a href="settings/organisation" class="card-link">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="toll-free-box text-center w-100">
                            <h4 class="text-reset"><i class="mdi mdi-domain"></i> Company</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>
</div>

         <!-- container -->
         {{-- Modal for add branch ends --}}
         @endsection