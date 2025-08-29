
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->
<!--  Select2 Plugin Js -->

<script src="assets/vendor/select2/js/select2.min.js"></script>
<!-- Daterangepicker Plugin js -->
<script src="assets/vendor/daterangepicker/moment.min.js"></script>

<script src="assets/vendor/daterangepicker/daterangepicker.js"></script>
<!-- Bootstrap Datepicker Plugin js -->
<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap Timepicker Plugin js -->
<script src="assets/vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<!-- Input Mask Plugin js -->
<script src="assets/vendor/jquery-mask-plugin/jquery.mask.min.js"></script>
<!-- Bootstrap Touchspin Plugin js -->
<script src="assets/vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
<!-- Bootstrap Maxlength Plugin js -->
<script src="assets/vendor/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
<!-- Typehead Plugin js -->
<script src="assets/vendor/handlebars/handlebars.min.js"></script>
<script src="assets/vendor/typeahead.js/typeahead.bundle.min.js"></script>
<!-- Flatpickr Timepicker Plugin js -->
<script src="assets/vendor/flatpickr/flatpickr.min.js"></script>
<!-- Typehead Demo js -->
<script src="assets/js/pages/demo.typehead.js"></script>
<!-- Timepicker Demo js -->
<script src="assets/js/pages/demo.timepicker.js"></script>
@if($page_info['page_name']=='Branch List' || $page_info['page_name']=='Modules List' || $page_info['page_name']=='Departments List' || $page_info['page_name']=='Designations List' || $page_info['page_name']=='Users List' || $page_info['page_name']=='Branch Target' || $page_info['page_name']=='Sanction Target' || $page_info['page_name'] == 'Fresh Leads' || $page_info['page_name'] == 'States List' || $page_info['page_name'] == 'Cities List' || $page_info['page_name'] == 'Leads Status' || $page_info['page_name'] == 'ROI' || $page_info['page_name'] == 'Profile' || $page_info['page_name']=='Activity Logs' || $page_info['page_name']=='Approval Pending List' || $page_info['page_name'] =='Relationship Managers List' || $page_info['page_name'] =='Credit Managers List')
<!-- Datatables js -->
<script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

<!-- Datatable Init js -->
<script src="assets/js/pages/demo.datatable-init.js"></script>
@endif
<script src="assets/vendor/jquery-toast-plugin/jquery.toast.min.js"></script>
@if($page_info['page_name']=='Tickets' || $page_info['page_name']=='View Ticket')
<script src="assets/vendor/simplemde/simplemde.min.js"></script>
<!-- SimpleMDE demo -->
<script src="assets/js/pages/demo.simplemde.js"></script>
@endif
<!-- init js -->
@if($page_info['page_name']=='Dashboard')
<!-- Apex Charts js -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<!-- Vector Map Js -->
<script src="assets/vendor/jsvectormap/js/jsvectormap.min.js"></script>
<script src="assets/vendor/jsvectormap/maps/world-merc.js"></script>
<script src="assets/vendor/jsvectormap/maps/world.js"></script>
<!-- Chart js -->
<script src="assets/vendor/chart.js/chart.min.js"></script>
{{-- <script src="assets/js/pages/demo.dashboard.js"></script> --}}
<!-- Projects Analytics Dashboard App js -->
<script src="assets/js/pages/demo.dashboard-projects.js"></script>
<!-- Dashboard App js -->
@endif
<!-- Toastr Demo js -->
<script src="assets/js/pages/demo.toastr.js"></script>
<script src="assets/js/app.min.js"></script>

</body>
</html>