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
                <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5 mt-2">
                    <div class="card">
                        <div class="card-body p-3">
 

                            <form action="settings/update-password" method="post">
                                @csrf
                                 <div class="mb-3">
                                    <label for="password" class="form-label">Current Password</label>
                                    <div class="input-group input-group-merge mb-1">
                                        <input type="password" name="currentPass" id="password" class="form-control" placeholder="Enter your password" value="{{old('currentPass')}}">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                    @error('currentPass')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                 <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group input-group-merge  mb-1">
                                        <input type="password" name="newPass" id="password" class="form-control" placeholder="Enter your password" value="{{old('newPass')}}">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                    @error('newPass')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Confirm New Password</label>
                                    <div class="input-group input-group-merge  mb-1">
                                        <input type="password" name="confPass" id="password" class="form-control" placeholder="Enter your password" value="{{old('confPass')}}">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                    @error('confPass')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
 

                                <div class="mb-2 text-center">
                                    <button class="btn btn-sm btn-primary" type="submit"> Change Password <i class="mdi mdi-shield-lock-open font-18"></i></button>
                                </div>
                                <div class="col-12 text-center">
                            <p class="text-muted">Forget current password ? <a href="pages-login.html" class="text-muted "><b>Generate Ticket</b></a></p>
                        </div> <!-- end col-->
                            </form>
                           
                        
                    </div>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            </div>
            <!-- end card-->
         </div>
         <!-- end col -->
      </div>
      <!-- end row -->
   </div>
   <!-- container -->
</div>
@endsection
 