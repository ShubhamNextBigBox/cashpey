<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Punch In - {{cmp()->companyName}}</title>
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
                    <circle style="fill: rgba(var(--ct-success-rgb), 0.1);" cx='400' cy='400' r='600'/>
                    <circle style="fill: rgba(var(--ct-success-rgb), 0.2);" cx='400' cy='400' r='500'/>
                    <circle style="fill: rgba(var(--ct-success-rgb), 0.3);" cx='400' cy='400' r='300'/>
                    <circle style="fill: rgba(var(--ct-success-rgb), 0.4);" cx='400' cy='400' r='200'/>
                    <circle style="fill: rgba(var(--ct-success-rgb), 0.5);" cx='400' cy='400' r='100'/>
                </g>
            </svg>
        </div>
        
        <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-lg-5">
                        <div class="card">
                            <!-- Logo -->
                            <div class="card-header py-3 text-center bg-primary">
                                <div class="row">
                                <div class="col-md-12">
                                    <a href="index.html">
                                        <span><img src="{{Storage::url(cmp()->logo)}}" alt="logo" height="55"></span>
                                    </a>
                                </div>
                                
                                </div>
                               
                            </div>
                            <div class="card-body p-3">
                                <div class="text-center w-75 m-auto">
                                    <img src="{{Storage::url(profilePic())}}" height="100" alt="user-image" class="rounded-circle shadow">
                                    <h4 class="text-dark-50 text-center mt-2 fw-bold">Hi ! @if(Session::has('name')){{ Session::get('name') }}@endif </h4>
                                    <p class="text-muted mb-3">Would You Like To Punch In?</p>
                                </div>
                                
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="mb-0">
                                        <a href="{{route('dashboard')}}" class="btn btn-danger" type="submit">No</a>
                                    </div>
                                    <div class="mb-0 offset-1">
                                        <button class="btn btn-success" type="button" id="punchIN" data-punchType="punchIN">Yes</button>
                                    </div>
                                </div>
                            
                            </div> <!-- end card-body-->
                        </div>
                        <!-- end card-->
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- end container -->
        </div>

        <!-- Include jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

       <script type="text/javascript">
$(document).ready(function() {
    $(document).on('click', '#punchIN', function () {
        var punchType = $(this).attr('data-punchType');

        // Check if the Geolocation API is available in the browser
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                $.ajax({
                    url: '{{route('punchInOut')}}', // Replace with your server endpoint
                    type: 'POST',
                    data: {
                        latitude: latitude,
                        longitude: longitude,
                        punchType: punchType,
                        _token: '{{ csrf_token() }}' // Include CSRF token if using Laravel
                    },
                    dataType: 'json',
                    success: function (data) {
                      if(data.response=='success'){
                           window.location.href = 'dashboard';

                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle error
                        alert('An error occurred: ' + error);
                    }
                });

            }, function (error) {
                // Handle errors from the Geolocation API
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        alert("User denied the request for Geolocation.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Location information is unavailable.");
                        break;
                    case error.TIMEOUT:
                        alert("The request to get user location timed out.");
                        break;
                    case error.UNKNOWN_ERROR:
                        alert("An unknown error occurred.");
                        break;
                }
            });
        } else {
            alert("Geolocation is not available in this browser.");
        }
    });
});
</script>

    </body>
</html>

<script type="text/javascript">
 const time = document.querySelector(".hours");
const secHand = document.querySelector(".second");
const minHand = document.querySelector(".minute");
const hourHand = document.querySelector(".hour");

for (let i = 1; i <= 60; i++) {
    if (i % 5 == 0) {
        time.innerHTML += "<div class='hour-number'><div>" + (i / 5) + "</div></div>";
        let hours = document.getElementsByClassName("hour-number");
        hours[hours.length - 1].style.transform = `translateX(-50%) rotate(${i*6}deg)`;
        hours[hours.length - 1].firstChild.style.transform = `rotate(${i*-6}deg)`;
    } else {
        time.innerHTML += "<div class='minute-bar'></div>";
        let bars = document.getElementsByClassName("minute-bar");
        bars[bars.length - 1].style.transform = `translateX(-50%) rotate(${i*6}deg)`;
    }
}

function startClock() {
    const now = new Date();
    const seconds = now.getSeconds();
    const minutes = now.getMinutes();
    const hours = now.getHours();

    let secDeg = seconds * (360 / 60) + minutes * 360;
  let minDeg = minutes * (360 / 60) + seconds / 12;
    let hourDeg = hours * (360 / 12) + (minutes / 12) * (360 / 60);
    secHand.style.transform = `translateX(-50%) rotate(${secDeg}deg)`;
    minHand.style.transform = `translateX(-50%) rotate(${minDeg}deg)`;
    hourHand.style.transform = `translateX(-50%) rotate(${hourDeg}deg)`;
}
setInterval(startClock, 1000);
startClock();


</script>