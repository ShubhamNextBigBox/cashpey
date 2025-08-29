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
            <div class="row mt-5 justify-content-center">
               <div class="col-md-4 mt-5">
                  <a href="support/tickets/crm-support" class="card-link">
                     <div class="card cta-box bg-primary text-white">
                        <div class="card-body">
                           <div class="d-flex align-items-start align-items-center">
                              <div class="w-100 overflow-hidden">
                                 <h2 class="mt-0 text-reset"><i class="mdi mdi-bullhorn-outline"></i>&nbsp;</h2>
                                 <h3 class="m-0 fw-normal cta-box-title text-reset">CRM<b> SUPPORT</b>   </h3>
                              </div>
                              <img class="ms-3" src="assets/images/svg/1.png" width="120" alt="Generic placeholder image">
                           </div>
                        </div>
                        <!-- end card-body -->
                     </div>
                  </a>
               </div>
               <div class="col-md-4 mt-5">
                  <a href="support/tickets/it-helpdesk" class="card-link">
                     <div class="card cta-box bg-primary text-white">
                        <div class="card-body">
                           <div class="d-flex align-items-start align-items-center">
                              <div class="w-100 overflow-hidden">
                                 <h2 class="mt-0 text-reset"><i class="mdi mdi-bullhorn-outline"></i>&nbsp;</h2>
                                 <h3 class="m-0 fw-normal cta-box-title text-reset">IT<b> HELPDESK</b>
                              </div>
                              <img class="ms-3" src="assets/images/svg/2.png" width="120" height="120"  alt="Generic placeholder image">
                           </div>
                        </div>
                        <!-- end card-body -->
                     </div>
                  </a>
               </div>
            </div>
         </div>
         @endsection