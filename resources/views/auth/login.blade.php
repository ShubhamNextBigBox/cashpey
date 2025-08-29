<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Log In - {{cmp()->companyName}}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">
        <link rel="stylesheet" href="assets/vendor/jquery-toast-plugin/jquery.toast.min.css">
        <!-- Theme Config Js -->
        <script src="assets/js/hyper-config.js"></script>
        <!-- App css -->
        <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
        <!-- Icons css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    </head>
    
    <body class="authentication-bg position-relative">
        <div class="position-absolute start-0 end-0 start-0 bottom-0 w-100 h-100">
           <svg xmlns='http://www.w3.org/2000/svg' width='100%' height='100%' viewBox='0 0 800 800'>
                <g fill-opacity='0.22'>
                    <circle style="fill: rgba(71, 67, 250, 0.1);" cx='400' cy='400' r='600'/>
                    <circle style="fill: rgba(71, 67, 250, 0.2);" cx='400' cy='400' r='500'/>
                    <circle style="fill: rgba(71, 67, 250, 0.3);" cx='400' cy='400' r='300'/>
                    <circle style="fill: rgba(71, 67, 250, 0.4);" cx='400' cy='400' r='200'/>
                    <circle style="fill: rgba(71, 67, 250, 0.5);" cx='400' cy='400' r='100'/>
                </g>
            </svg>
        </div>
         @if(Session::has('LoginError')) 
             <div id="danger-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content modal-filled bg-danger">
                            <div class="modal-body p-2">
                                <div class="text-center">
                                    <i class="ri-close-circle-line h1"></i>
                                    <h4 class="mt-1">Oh snap!</h4>
                                    <p class="mt-2">{{Session::get('LoginError')}}</p>
                                    <button type="button" class="btn btn-light my-1" data-bs-dismiss="modal">Retry</button>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div> 
                <button  type="button" id="alertBtn" style="visibility: hidden;"  data-bs-toggle="modal" data-bs-target="#danger-alert-modal"> </button>
        @endif  
        <div class="account-pages pt-2 pt-sm-5 pb-3 pb-sm-5 position-relative">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-lg-5">
                        <div class="card">
                            <!-- Logo -->
                            <div class="card-header py-3 text-center bg-primary">
                                <a href="index.html">
                                    <span><img src="{{Storage::url('logo/front-logo.png')}}" alt="logo" height="50"></span>
                                </a>
                            </div>
                            <div class="card-body p-3">
                                <div class="text-center w-75 m-auto">
                                    <h2 class="text-dark-50 text-center pb-0 fw-bold">CRM LOGIN</h2>
                                    <p class="text-muted mb-2">Enter your username and password to access admin panel.</p>
                                </div>
                                <form action="login" method="post">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="emailaddress" class="form-label">Username</label>
                                        <input class="form-control" type="text" id="" name="userName" autocomplete="off" placeholder="Enter your username">
                                    </div>
                                    @error('userName')
                                    <div class="alert alert-danger" role="alert">
                                        <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                    </div>
                                    @enderror
                                    <div class="mb-3">
                                        <a href="pages-recoverpw.html" class="text-muted float-end"><small>Forgot your password?</small></a>
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" name="password" id="password" autocomplete="off" class="form-control" placeholder="Enter your password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                    @error('password')
                                    <div class="alert alert-danger" role="alert">
                                        <i class="ri-close-circle-line me-1 align-middle font-16"></i><strong>{{$message}}</strong>
                                    </div>
                                    @enderror
                                   {{--  <div class="mb-3 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                                        </div>
                                    </div> --}}
                                    <div class="mb-3 mb-0 text-center">
                                        <button class="btn btn-primary form-control" type="submit"> Log In </button>
                                    </div>
                                </form>
                                </div> <!-- end card-body -->
                            </div>
                            <!-- end card -->
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end container -->
                </div>
                <!-- end page -->
                <footer class="footer footer-alt">
                    <script>document.write(new Date().getFullYear())</script> © Powered by - NextBigBox
                </footer>
                <!-- Vendor js -->
                <script src="assets/js/vendor.min.js"></script>
                
                <script src="assets/vendor/jquery-toast-plugin/jquery.toast.min.js"></script>
                <!-- Toastr Demo js -->
                <script src="assets/js/pages/demo.toastr.js"></script>
                <!-- App js -->
                <script src="assets/js/app.min.js"></script>
            </body>
            
        </html>

<script type="text/javascript">
    @if(Session::has('success'))
        $.NotificationApp.send("Well Done!","{{Session::get('success')}}", "bottom-right", "rgba(0,0,0,0.2)", "success"); 
    @endif
    @if(Session::has('error'))
     $.NotificationApp.send("Oh snap!","{{Session::get('error')}}", "bottom-right", "rgba(0,0,0,0.2)", "error");
    @endif

    @if(Session::has('LoginSuccess'))
        $('#succBtn').trigger('click'); 
    @endif
    @if(Session::has('LoginError'))
        $('#alertBtn').trigger('click');
    @endif
 
                                                
</script>