<base href="{{url('')}}">
<!-- App favicon -->
<link rel="shortcut icon" href="assets/images/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Daterangepicker css -->
<!-- Bootstrap Datepicker css -->

<link href="assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Daterangepicker css -->
<link href="assets/vendor/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
<!-- Bootstrap Datepicker css -->
<link href="assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<!-- Bootstrap Timepicker css -->
<link href="assets/vendor/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<!-- Flatpickr Timepicker css -->
<link href="assets/vendor/flatpickr/flatpickr.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
<!-- Include jQuery UI CSS for drag-and-drop styling -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<!-- Vector Map css -->
{{-- 
<link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css">
--}}

<link rel="stylesheet" href="assets/vendor/jquery-toast-plugin/jquery.toast.min.css">
<!-- Theme Config Js -->
<script src="assets/js/hyper-config.js"></script>
<!-- App css -->
<link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
<!-- Icons css -->
<link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
<!-- Datatables css -->
@if($page_info['page_name']=='Branch List' || $page_info['page_name']=='Modules List' || $page_info['page_name']=='Departments List' || $page_info['page_name']=='Designations List'  || $page_info['page_name']=='Users List' || $page_info['page_name']=='Branch Target' || $page_info['page_name']=='Sanction Target' || $page_info['page_name'] == 'Fresh Leads' || $page_info['page_name'] == 'States List' || $page_info['page_name'] == 'Cities List' || $page_info['page_name'] == 'Leads Status' || $page_info['page_name'] == 'ROI' || $page_info['page_name'] == 'Profile' || $page_info['page_name']=='Activity Logs' || $page_info['page_name']=='Approval Pending List' || $page_info['page_name'] =='Relationship Managers List' || $page_info['page_name'] =='Credit Managers List')
<link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endif
@if($page_info['page_name']=='Tickets' || $page_info['page_name']=='View Ticket')
<!-- Quill css -->
<link href="assets/vendor/simplemde/simplemde.min.css" rel="stylesheet" type="text/css" />      
@endif