<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <title>@yield('page-title')</title>
      @include('layouts.headerlinks')
   </head>
   <body>
      <!-- Begin page -->
      <div class="wrapper">
      <!-- ========== Topbar Start ========== -->
      <div class="navbar-custom">
         <div class="topbar container-fluid">
            <div class="d-flex align-items-center gap-lg-2 gap-1">
               <!-- Topbar Brand Logo -->
               <div class="logo-topbar">
                  <!-- Logo light -->
                  <a href="" class="logo-light">
                  <span class="logo-lg">
                  <img src="assets/images/logo.png" alt="logo">
                  </span>
                  <span class="logo-sm">
                  <img src="assets/images/logo-sm.png" alt="small logo">
                  </span>
                  </a>
                  <!-- Logo Dark -->
                  <a href="" class="logo-dark">
                  <span class="logo-lg">
                  <img src="assets/images/logo-dark.png" alt="dark logo">
                  </span>
                  <span class="logo-sm">
                  <img src="assets/images/logo-dark-sm.png" alt="small logo">
                  </span>
                  </a>
               </div>
               <!-- Sidebar Menu Toggle Button -->
               <button class="button-toggle-menu">
               <i class="mdi mdi-menu"></i>
               </button>
               <!-- Horizontal Menu Toggle Button -->
               <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                  <div class="lines">
                     <span></span>
                     <span></span>
                     <span></span>
                  </div>
               </button>
               <!-- Topbar Search Form -->
               <div class="app-search dropdown d-none d-lg-block">
                  {{-- <form>
                     <div class="input-group">
                        <input type="search" class="form-control dropdown-toggle" placeholder="Search..." id="top-search">
                        <span class="mdi mdi-magnify search-icon"></span>
                        <button class="input-group-text btn btn-primary" type="submit">Search</button>
                     </div>
                  </form> --}}
                  {{-- <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                     <!-- item-->
                     <div class="dropdown-header noti-title">
                        <h5 class="text-overflow mb-2">Found <span class="text-danger">17</span> results</h5>
                     </div>
                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                     <i class="uil-notes font-16 me-1"></i>
                     <span>Analytics Report</span>
                     </a>
                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                     <i class="uil-life-ring font-16 me-1"></i>
                     <span>How can I help you?</span>
                     </a>
                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                     <i class="uil-cog font-16 me-1"></i>
                     <span>User profile settings</span>
                     </a>
                     <!-- item-->
                     <div class="dropdown-header noti-title">
                        <h6 class="text-overflow mb-2 text-uppercase">Users</h6>
                     </div>
                     <div class="notification-list">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                           <div class="d-flex">
                              <img class="d-flex me-2 rounded-circle" src="assets/images/users/avatar-2.jpg" alt="Generic placeholder image" height="32">
                              <div class="w-100">
                                 <h5 class="m-0 font-14">Erwin Brown</h5>
                                 <span class="font-12 mb-0">UI Designer</span>
                              </div>
                           </div>
                        </a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                           <div class="d-flex">
                              <img class="d-flex me-2 rounded-circle" src="assets/images/users/avatar-5.jpg" alt="Generic placeholder image" height="32">
                              <div class="w-100">
                                 <h5 class="m-0 font-14">Jacob Deo</h5>
                                 <span class="font-12 mb-0">Developer</span>
                              </div>
                           </div>
                        </a>
                     </div>
                  </div> --}}
               </div>
            </div>
            <ul class="topbar-menu d-flex align-items-center gap-3">
               <li class="dropdown d-lg-none">
                  <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                  <i class="ri-search-line font-22"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                     <form class="p-3">
                        <input type="search" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                     </form>
                  </div>
               </li>
               <li class="d-none d-sm-inline-block">
                  {{attendanceBtn()}}
               </li>
               <li class="dropdown notification-list">
                      <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                      <i class="ri-notification-3-line font-22"></i>
                      <span class="badge bg-danger float-end"  id="notiCount"></span>
                      </a>
                      <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                         <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                            <div class="row align-items-center">
                               <div class="col">
                                  <h6 class="m-0 font-16 fw-semibold"> Notification</h6>
                               </div>
                            </div>
                         </div>
                         {{getTicketNotification()}}
                        @php
                            // Determine the URL based on the role
                            if (role() == 'CRM Support') {
                                $url = 'support/tickets/crm-support';
                            } elseif (role() == 'Technical Support') {
                                $url = 'support/tickets/it-helpdesk';  // Assuming this is the correct URL for this role as well
                            } else {
                                $url = 'support/tickets'; // Default URL if needed
                            }
                        @endphp
                        
                        <!--<a href="{{ url($url) }}" class="dropdown-item text-center text-primary notify-item border-top py-2">-->
                        <!--    View All-->
                        <!--</a>-->
                       
                      </div>
                   </li>
               <li class="d-none d-sm-inline-block">
                  <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left" title="Theme Mode">
                     <i class="ri-moon-line font-22"></i>
                  </div>
               </li>
               <li class="d-none d-md-inline-block">
                  <a class="nav-link" href="#" data-toggle="fullscreen">
                  <i class="ri-fullscreen-line font-22"></i>
                  </a>
               </li>
               <li class="d-none d-sm-inline-block">
                        <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                            <i class="ri-settings-3-line font-22"></i>
                        </a>
                    </li>
               <li class="dropdown">
                  <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                     <span class="account-user-avatar">
                     <img src="{{Storage::url(profilePic())}}" alt="user-image" width="32" class="rounded-circle">
                     </span>
                     <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">@if(Session::has('name')){{Session::get('name')}}@endif</h5>
                        <h6 class="my-0 fw-normal">@if(Session::has('role')){{Session::get('role')}} @endif</h6>
                     </span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                     <!-- item-->
                     <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome @if(Session::has('name')){{Session::get('name')}} ! @endif</h6>
                     </div>
                     <a href="settings/change-password" class="dropdown-item">
                     <i class="mdi mdi-shield-lock-open me-1"></i>
                     <span>Change Password</span>
                     </a>
                     <!-- item-->
                     @if(optionalModules('Support Process'))
                     <a href="support/tickets-category" class="dropdown-item">
                     <i class="mdi mdi-lifebuoy me-1"></i>
                     <span>Support</span>
                     </a>
                     @endif
                     @if(optionalModules('Attendance Process'))
                     <a href="attendance/list" class="dropdown-item">
                     <i class="uil-calender me-1"></i>
                     <span>Attendance</span>
                     </a>
                     @endif    
                     <a href="logout" class="dropdown-item">
                     <i class="mdi mdi-logout me-1"></i>
                     <span>Logout</span>
                     </a>
                  </div>
               </li>
            </ul>
         </div>
      </div>
      <!-- ========== Topbar End ========== -->
      @include('layouts.navigation')