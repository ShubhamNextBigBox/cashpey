@extends('layouts.master')

@section('page-title', $page_info['page_title'])

@section('main-section')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $page_info['page_title'] }}</a></li>
                                <li class="breadcrumb-item active">{{ $page_info['page_name'] }}</li>
                            </ol>
                        </div>
                        <h4 class="page-title">{{ $page_info['page_name'] }}</h4>
                    </div>
                </div>
            </div>

            <!-- Table displaying KYC details -->
            <div class="row">
                <table class="table table-bordered table-success">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Request ID</th>
                            <th>Customer Name</th>
                            <th>Email Id</th>
                            <th>Status</th>
                            <th>ID's Found</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $kycRequest->kycRequestID }}</td>
                            <td>{{ ucwords($kycRequest->customer_name) }}</td>
                            <td>{{ $kycRequest->customer_identifier }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $kycRequest->status)) }}</td>
                            <td>Video, Aadhar, Pancard</td>
                            <td>{{ $kycRequest->updatedOn }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tabs for Video, Aadhar, and Pancard details -->
            <div class="container">
                <div class="row">
                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                        <li class="nav-item">
                            <a href="#home1" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 active">
                                <span class="d-none d-md-block">Video Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#profile1" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0">
                                <span class="d-none d-md-block">Aadhar Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#settings1" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0">
                                <span class="d-none d-md-block">Pancard Details</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane show active" id="home1">
                            <div class="row  d-flex justify-content-center align-items-center">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Video Geolocation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $kycRequest->kycLocation }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3" id="video-container">
                                     <div class="spinner-border text-success"></div><span> Loading Video...</span>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="profile1">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Aadhar No.</th>
                                                <th>Name</th>
                                                <th>Father's Name</th>
                                                <th>DOB</th>
                                                <th>Address</th>
                                                <th>ID Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $kycRequest->aadharNO }}</td>
                                                <td>{{ ucwords($kycRequest->aadharCustomerName) }}</td>
                                                <td>{{ ucwords($kycRequest->aadharFatherName) }}</td>
                                                <td>{{ $kycRequest->aadharDob }}</td>
                                                <td>{{ $kycRequest->aadharAddress }}</td>
                                                <td>{{ $kycRequest->aadharIDTypes }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Aadhar images (Front and Back) in 3x3 grid -->
                                <div class="col-md-12">
                                    <div class="row justify-content-center">
                                        <!-- Front Aadhar -->
                                        <div class="col-md-3 mb-3" id="front-aadhar-container">
                                             <div class="spinner-border text-success"></div><span> Loading Adhar Card Front...</span>
                                        </div>
                                        <!-- Back Aadhar -->
                                        <div class="col-md-3 mb-3" id="back-aadhar-container">
                                           <div class="spinner-border text-success"></div><span> Loading Adhar Card Back...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="settings1">
                            <div class="row d-flex justify-content-center align-items-center">
                                <div class="col-md-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Pancard No.</th>
                                                <th>Name</th>
                                                <th>Father's Name</th>
                                                <th>DOB</th>
                                                <th>ID Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $kycRequest->panNO }}</td>
                                                <td>{{ ucwords($kycRequest->panCustomerName) }}</td>
                                                <td>{{ ucwords($kycRequest->panFatherName) }}</td>
                                                <td>{{ $kycRequest->panDob }}</td>
                                                <td>{{ $kycRequest->panIDTypes }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-3" id="pan-container">
                                    <div class="spinner-border text-success"></div><span> Loading Pancard...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End container -->
    </div>
</div>
@endsection

@section('custom-js')
<script>
    $(document).ready(function() {
        var videoFileId = '{{ $kycRequest->videoFileID }}';
        var frontAadharId = '{{ $kycRequest->frontAadharCard }}';
        var backAadharId = '{{ $kycRequest->backAadharCard }}';
        var panCardId = '{{ $kycRequest->panCard }}';

        // Fetch video details via the proxy route
        $.ajax({
            url: "profile/proxy/" + videoFileId, // Proxy route
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    var videoContent = `<video controls style="width:300px; height: 250px;">
                                        <source src="data:video/mp4;base64,${data.body}" type="video/mp4">
                                      </video>`;
                    $("#video-container").html(videoContent);
                } else {
                    $("#video-container").html('<p>No video available.</p>');
                }
            },
            error: function() {
                $("#video-container").html('<p>Error fetching video.</p>');
            }
        });

        // Fetch Front Aadhar details via the proxy route
        $.ajax({
            url: "profile/proxy/" + frontAadharId, // Proxy route
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    var imgAadhar = `<img src="data:image/jpeg;base64,${data.body}" alt="Front Aadhar Card" class="img-fluid">`;
                    $("#front-aadhar-container").html(imgAadhar);
                } else {
                    $("#front-aadhar-container").html('<p>No Front Aadhar available.</p>');
                }
            },
            error: function() {
                $("#front-aadhar-container").html('<p>Error fetching Front Aadhar.</p>');
            }
        });

        // Fetch Back Aadhar details via the proxy route
        $.ajax({
            url: "profile/proxy/" + backAadharId, // Proxy route
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    var imgAadhar = `<img src="data:image/jpeg;base64,${data.body}" alt="Back Aadhar Card" class="img-fluid">`;
                    $("#back-aadhar-container").html(imgAadhar);
                } else {
                    $("#back-aadhar-container").html('<p>No Back Aadhar available.</p>');
                }
            },
            error: function() {
                $("#back-aadhar-container").html('<p>Error fetching Back Aadhar.</p>');
            }
        });

        // Fetch Pancard details via the proxy route
        $.ajax({
            url: "profile/proxy/" + panCardId, // Proxy route
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    var imgPancard = `<img src="data:image/jpeg;base64,${data.body}" alt="Pancard" class="img-fluid">`;
                    $("#pan-container").html(imgPancard);
                } else {
                    $("#pan-container").html('<p>No Pancard available.</p>');
                }
            },
            error: function() {
                $("#pan-container").html('<p>Error fetching Pancard.</p>');
            }
        });
    });
</script>
@endsection
