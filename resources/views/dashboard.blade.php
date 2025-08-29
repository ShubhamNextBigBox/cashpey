@extends('layouts.master')
@section('page-title',$page_info['page_title'])
@section('main-section')
<div class="content-page">
<div class="content">
<!-- Start Content-->
<div class="container-fluid">
   <div class="row">
      <div class="col-12">
         <div class="page-title-box">
            <div class="page-title-right">
               <form class="d-flex">
                  <a href="{{route('dashboard')}}" class="btn btn-primary ms-2">
                  <i class="mdi mdi-autorenew"></i>
                  </a>
                  <a href="javascript: void(0);" class="btn btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#dashboard-modal">
                  <i class="mdi mdi-filter-variant"></i>
                  </a>
               </form>
            </div>
            <h4 class="page-title">Dashboard</h4>
         </div>
      </div>
   </div>
   @if(isSuperAdmin() || isAdmin())
   <div class="row">
      <div class="col-xl-5 col-lg-6">
         <div class="row">
            <div class="col-sm-6">
               <div class="card widget-flat">
                  <div class="card-body">
                     <div class="float-end">
                        <i class="mdi mdi-account-multiple widget-icon"></i>
                     </div>
                     <h5 class="text-muted fw-normal mt-0" title="Number of Fresh Leads">Total Fresh Leads</h5>
                     <h3 class="mt-3 mb-3">{{$freshLeadsCount ?? 0}}</h3>
                     <p class="mb-0 text-muted">
                        <span class="text-nowrap">Since ({{$filterShow}})</span>
                     </p>
                  </div>
                  <!-- end card-body-->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
            <div class="col-sm-6">
               <div class="card widget-flat">
                  <div class="card-body">
                     <div class="float-end">
                        <i class="mdi mdi-cart-plus widget-icon"></i>
                     </div>
                     <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Total Sanction Leads</h5>
                     <h3 class="mt-3 mb-3">{{$sanctionLeadsCount ?? 0}}</h3>
                     <p class="mb-0 text-muted">
                        <span class="text-nowrap">Since ({{$filterShow}})</span>
                     </p>
                  </div>
                  <!-- end card-body-->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
         </div>
         <!-- end row -->
         <div class="row">
            <div class="col-sm-6">
               <div class="card widget-flat">
                  <div class="card-body">
                     <div class="float-end">
                        <i class="mdi mdi-currency-usd widget-icon"></i>
                     </div>
                     <h5 class="text-muted fw-normal mt-0" title="Average Revenue">Total Disbursed Leads</h5>
                     <h3 class="mt-3 mb-3">{{$disbursedLeadsCount ?? 0}}</h3>
                     <p class="mb-0 text-muted">
                        <span class="text-nowrap">Since ({{$filterShow}})</span>
                     </p>
                  </div>
                  <!-- end card-body-->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
            <div class="col-sm-6">
               <div class="card widget-flat">
                  <div class="card-body">
                     <div class="float-end">
                        <i class="mdi mdi-pulse widget-icon"></i>
                     </div>
                     <h5 class="text-muted fw-normal mt-0" title="Growth">Total Rejected Leads</h5>
                     <h3 class="mt-3 mb-3">{{$rejectedLeadsCount ?? 0}}</h3>
                     <p class="mb-0 text-muted">
                        <span class="text-nowrap">Since ({{$filterShow}})</span>
                     </p>
                  </div>
                  <!-- end card-body-->
               </div>
               <!-- end card-->
            </div>
            <!-- end col-->
         </div>
         <!-- end row -->
      </div>
      <!-- end col -->
      <div class="col-xl-7 col-lg-6">
         <div class="card card-h-100">
            <div class="d-flex card-header justify-content-between align-items-center">
               <!--<h4 class="header-title">Loan Vs Collection Amount (FY / {{$currentYear}}-{{$nextYear}})</h4>-->
               <h4 class="header-title">Loan Vs Collection Amount (FY / {{$currentYear}})</h4>
            </div>
            <div class="card-body pt-0">
               <div dir="ltr">
                  <div id="high-performing-product" class="apex-charts" data-colors="#727cf5,#91a6bd40"></div>
               </div>
            </div>
            <!-- end card-body-->
         </div>
         <!-- end card-->
      </div>
      <!-- end col -->
   </div>
   <div class="row">
      <div class="col-lg-8">
         <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
               <!--<h4 class="header-title">Income Before Taxes vs After Taxes (FY / {{$currentYear}}-{{$nextYear}})</h4>-->
               <h4 class="header-title">Income Before Taxes vs After Taxes (FY / {{$currentYear}})</h4>
            </div>
            <div class="card-body pt-0">
               <div class="chart-content-bg">
                  <div class="row text-center">
                     <div class="col-sm-6">
                        <p class="text-muted mb-0 mt-1">Before Taxes</p>
                        <h2 class="fw-normal mb-1">
                           <small class="mdi mdi-checkbox-blank-circle text-primary align-middle"></small>
                           <span>{{nf($queryAdminFeeAmountShow)}}</span>
                        </h2>
                     </div>
                     <div class="col-sm-6">
                        <p class="text-muted mb-0 mt-1">After Taxes</p>
                        <h2 class="fw-normal mb-1">
                           <small class="mdi mdi-checkbox-blank-circle text-success align-middle"></small>
                           <span>{{nf($queryInterestAmountShow)}}</span>
                        </h2>
                     </div>
                  </div>
               </div>
               <div class="dash-item-overlay d-none d-md-block" dir="ltr">
               </div>
               <div dir="ltr">
                  <div id="revenue-chart" class="apex-charts mt-3" data-colors="#727cf5,#0acf97"></div>
               </div>
            </div>
            <!-- end card-body-->
         </div>
         <!-- end card-->
      </div>
      <!-- end col-->
      <div class="col-xl-4 col-lg-6 order-lg-1">
         <div class="card">
            <div class="d-flex card-header justify-content-between align-items-center">
               <h4 class="header-title">Overview Since ({{$filterShow}})</h4>
            </div>
            <div class="card-body pt-2">
               <div id="average-sales" class="apex-charts mb-1 mt-1" data-colors="#727cf5,#fa5c7c,#0acf97,#ffc35a,#39afd1"></div>
               <div class="chart-widget-list">
                  <p>
                     <i class="mdi mdi-square text-primary"></i> Loan Amount
                     <span class="float-end">{{nf($sumLoanApprovedAmount)}} </span>
                  </p>
                  <p>
                     <i class="mdi mdi-square text-danger"></i> Disbursal Amount
                     <span class="float-end">{{nf($sumDisbursalAmount)}} </span>
                  </p>
                  <p>
                     <i class="mdi mdi-square text-success"></i> Collection
                     <span class="float-end">{{nf($sumCollectionAmount)}}</span>
                  </p>
                  <p>
                     <i class="mdi mdi-square text-warning"></i> Admin Fee
                     <span class="float-end">{{nf($sumAdminFeeAmount)}}</span>
                  </p>
                  <p>
                     <i class="mdi mdi-square text-secondary"></i> Interest Amount
                     <span class="float-end">{{nf($sumInterestAmount)}}</span>
                  </p>
                  <p>
                     <i class="mdi mdi-square text-info"></i> GST Fee
                     <span class="float-end">{{nf($sumGstAmount)}}</span>
                  </p>
               </div>
            </div>
            <!-- end card-body-->
         </div>
         <!-- end card-->
      </div>
      <!-- end col-->
   </div>
   <div class="row">
      <div class="col-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h4 class="header-title">All Leads Since ({{$filterShow}})</h4>
            </div>
            <div class="card-body">
               <div dir="ltr">
                  <div class="chartjs-chart" style="height: 320px;">
                     <canvas id="task-area-chart" data-bgColor="#727cf5" data-borderColor="#727cf5"></canvas>
                  </div>
               </div>
            </div>
            <!-- end card body-->
         </div>
         <!-- end card -->
      </div>
      <!-- end col-->
   </div>
   <!-- end row -->
   <!-- end row -->
   <div class="row">
      <div class="col-xl-4 col-lg-6">
         <div class="card" style="min-height:450px;">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h4 class="header-title">Relationship Managers Since ({{$filterShow}})</h4>
            </div>
            <div class="card-body pt-2">
               @if(count($totalPerformanceCountRM) > 0)
               @foreach($totalPerformanceCountRM as $key => $arr)
               @if($key > 0)
               @php $mt ='mt-3'; @endphp 
               @endif
               <div class="d-flex align-items-start {{$mt ?? ''}}">
                  <img class="me-3 rounded-circle" src="{{Storage::url($arr->profile)}}" width="40" height="40" alt="Generic placeholder image">
                  <div class="w-100 overflow-hidden">
                     <span class="badge badge-success-lighten float-end">#{{++$key}} Position</span>
                     <h5 class="mt-0 mb-1">{{$arr->displayName}}</h5>
                     <span class="font-13">{{$arr->email}}</span>
                  </div>
               </div>
               @endforeach 
               @endif    
            </div>
            <!-- end card-body -->
         </div>
         <!-- end card-->
      </div>
      <div class="col-xl-4 col-lg-6">
         <div class="card" style="min-height:450px;">
            <div class="card-header d-flex justify-content-between align-items-center">
               <h4 class="header-title">Credit Managers Since ({{$filterShow}})</h4>
            </div>
            <div class="card-body pt-2">
               @if(count($totalPerformanceCountCM) > 0)
               @foreach($totalPerformanceCountCM as $key => $arr)
               @if($key > 0)
               @php $mt2 ='mt-3'; @endphp 
               @endif
               <div class="d-flex align-items-start {{$key}} {{$mt2 ?? ''}}">
                  <img class="me-3 rounded-circle" src="{{Storage::url($arr->profile)}}" width="40" height="40" alt="Generic placeholder image">
                  <div class="w-100 overflow-hidden">
                     <span class="badge badge-success-lighten float-end">#{{++$key}} Position</span>
                     <h5 class="mt-0 mb-1">{{$arr->displayName}}</h5>
                     <span class="font-13">{{$arr->email}}</span>
                  </div>
               </div>
               @endforeach 
               @endif    
            </div>
            <!-- end card-body -->
         </div>
         <!-- end card-->
      </div>
      <div class="col-xl-4 col-lg-12">
         <div class="card" style="min-height: 450px;">
            <div class="d-flex card-header justify-content-between align-items-center">
               <h4 class="header-title">Collection Branches Since ({{$filterShow}})</h4>
            </div>
            <div class="card-body pt-0">
               <div id="views-min" class="apex-charts" data-colors="#0acf97"></div>
               <div class="table-responsive mt-3">
                  <table class="table table-sm mb-0 font-13">
                     <thead>
                        <tr>
                           <th>Branches</th>
                           <th>Position</th>
                           <th>Collection</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($totalPerformanceCountCB as $key => $arr) 
                        <tr>
                           <td>{{$arr->branch}}</td>
                           <td><span class="badge badge-success-lighten">#{{++$key}} Position</span></td>
                           <td>{{nf($arr->amount_count)}}</td>
                        </tr>
                        @endforeach 
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- end card-body-->
         </div>
         <!-- end card-->
      </div>
      <!-- end col-->
   </div>
   <!-- end row -->
</div>
<!-- container -->
<div id="dashboard-modal" class="modal fade dashboard-update-modal" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-right">
      <div class="modal-content align-item-start">
         <div class="modal-header border-0 text-center">
            <h4 class="modal-title" style="color:#0acf97; margin: 0 auto;" id="primary-header-modalLabel">Dashboard Filter</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
         </div>
         <div class="modal-body">
            <form class="ps-1 pe-1 mb-5" action="{{route('dashboard')}}" method="GET">
               <div class="row mb-5">
                  <div class="col-md-12">
                     <div class="mb-2">
                        <label for="state" class="form-label">Report Type</label>
                       <select class="form-select" name="reportType" id="reportType">
                           <option value="">Select Report Type</option>
                               @if(isSuperAdmin() || isAdmin() || role()=='CRM Support' || role()=='Sr. Recovery Manager' || role()=='Recovery Manager' || role()=='Recovery Executive')
                                 <option value="Business Overview" {{ $reportType === 'Business Overview' ? 'selected' : '' }}>Business Overview</option>
                                <!--  <option value="Recovery Value" {{ $reportType === 'Recovery Value' ? 'selected' : '' }}>Recovery Value</option>
                                 <option value="Recovery Volume" {{ $reportType === 'Recovery Volume' ? 'selected' : '' }}>Recovery Volume</option> -->
                               @endif   
                                 <option value="Sanction 360" {{ $reportType === 'Sanction 360' ? 'selected' : '' }}>Sanction 360<sup>°</sup></option>
                        </select>
                        <span class="reportTypeErr"></span>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <div class="mb-2">
                        <div class="input-group">
                           <span class="input-group-text">
                           <i class="uil uil-filter"></i>
                           </span>
                           <select class="form-select exportSelect" name="filter" id="filter">
                           <option value="sortByToday"  {{ $filter === 'sortByToday' ? 'selected' : '' }}>Sort by Today</option>
                           <option value="sortByWeek"  {{ $filter === 'sortByWeek' ? 'selected' : '' }}>Sort by Week</option>
                           <option value="sortByThisMonth"  {{ $filter === 'sortByThisMonth' || empty($filter) ? 'selected' : '' }}>Sort by This Month</option>
                           <option value="sortByLastMonth"  {{ $filter === 'sortByLastMonth' ? 'selected' : '' }}>Sort by Prev Month</option>
                           <option value="sortByDate" {{ $filter === 'sortByDate' ? 'selected' : '' }}>Sort by Date</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12" id="dateRange" style="display: none;">
                     <div class="mb-3">
                        <div class="input-group">
                           <span class="input-group-text">
                           <i class="uil uil-calender"></i>
                           </span>
                           <input type="text" class="form-control date" name="searchRange"  data-toggle="date-picker" data-cancel-class="btn-warning">
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12 mb-5">
                     <div class="mb-5 text-center">
                        <input type="submit" class="btn btn-primary form-control" value="Apply">
                     </div>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
@endif
@endsection
@section('custom-js')

<script type="text/javascript">
 @if(Session::has('LoginSuccess'))
   $('#succBtn').trigger('click'); 
 @endif
</script>
<script type="text/javascript">
    !(function (o) {
        "use strict";

        function ChartManager() {
            this.$body = o("body");
            this.charts = [];
        }

        ChartManager.prototype.respChart = function (chartElement, chartType, chartData, options) {
            Chart.defaults.color = "#8fa2b3";
            Chart.defaults.borderColor = "rgba(133, 141, 152, 0.1)";

            var context = chartElement.get(0).getContext("2d");
            chartElement.attr("width", o(chartElement).parent().width());

            let chartInstance;
            switch (chartType) {
                case "Line":
                    chartInstance = new Chart(context, { type: "line", data: chartData, options: options });
                    break;
                case "Bar":
                    chartInstance = new Chart(context, { type: "bar", data: chartData, options: options });
                    break;
                case "Doughnut":
                    chartInstance = new Chart(context, { type: "doughnut", data: chartData, options: options });
                    break;
                default:
                    console.error("Invalid chart type: " + chartType);
            }
            return chartInstance;
        };

        ChartManager.prototype.initCharts = function () {
            var chartsArray = [];
            var data = @json($allLeadsCountSum); // Ensure this returns valid data
            console.log(data); // Check the data

            const leadStatus = data.map(item => item.status); // Extract status
            const leadCount = data.map(item => item.total);   // Extract counts
            var filterShow = @json($filterShow);    

            // Task Area Chart
            if (o("#task-area-chart").length > 0) {
                var taskChartData = {
                    labels: leadStatus,
                    datasets: [{
                        label: filterShow,
                        backgroundColor: o("#task-area-chart").data("bgcolor") || "#727cf5",
                        borderColor: o("#task-area-chart").data("bordercolor") || "#727cf5",
                        data: leadCount,
                    }],
                };

                chartsArray.push(
                    this.respChart(o("#task-area-chart"), "Bar", taskChartData, {
                        maintainAspectRatio: false,
                        barPercentage: 0.7,
                        categoryPercentage: 0.5,
                        plugins: {
                            filler: { propagate: false },
                            legend: { display: false },
                            tooltip: { intersect: false },
                            hover: { intersect: true }
                        },
                        scales: {
                            x: { grid: { color: "rgba(0,0,0,0.05)" } },
                            y: {
                                ticks: { stepSize: 10, display: false },
                                min: 0,
                                max: Math.max(...leadCount) + 10,
                                display: true,
                                borderDash: [5, 5],
                                grid: { color: "rgba(0,0,0,0)", fontColor: "#fff" }
                            }
                        },
                    })
                );
            }

            return chartsArray;
        };

        ChartManager.prototype.init = function () {
            var self = this;
            Chart.defaults.font.family = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';
            self.charts = self.initCharts();

            o(window).on("resizeEnd", function () {
                o.each(self.charts, function (index, chart) {
                    try {
                        chart.destroy();
                    } catch (error) {
                        console.error("Chart destroy error:", error);
                    }
                });
                self.charts = self.initCharts();
            });

            o(window).resize(function () {
                this.resizeTO && clearTimeout(this.resizeTO);
                this.resizeTO = setTimeout(function () {
                    o(this).trigger("resizeEnd");
                }, 500);
            });
        };

        o.ChartJs = new ChartManager();
        o.ChartJs.Constructor = ChartManager;

    })(window.jQuery);

    (function () {
        "use strict";
        $(document).ready(function() {
            window.jQuery.ChartJs.init();
        });
    })();
</script>

<script type="text/javascript">
  !(function (o) {
    "use strict";
    function e() {
        (this.$body = o("body")), (this.charts = []);
    }
    (e.prototype.initCharts = function () {
        window.Apex = { chart: { parentHeightOffset: 0, toolbar: { show: !1 } }, grid: { padding: { left: 0, right: 0 } }, colors: ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"] };

        var data = @json($disbursalPerMonth); // Pass data from controller
        const month = data.map(item => item.month); // Get the loan amounts
        const loanAmounts = data.map(item => item.total_approved); // Get the loan amounts
        const disbursalAmounts = data.map(item => item.total_disbursed);
        const adminFee = data.map(item => item.total_adminFee);
        const collectionAmounts = data.map(item => item.total_collection);
        const interestAmounts = data.map(item => item.total_interest);
        
        var e = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"],
            t = o("#revenue-chart").data("colors"),
            r = {
                chart: { height: 370, type: "line", dropShadow: { enabled: !0, opacity: 0.2, blur: 7, left: -7, top: 7 } },
                dataLabels: { enabled: !1 },
                stroke: { curve: "smooth", width: 5 },
                series: [
                    { name: "Admin Fee", data: adminFee },
                    { name: "Interest Amount", data: interestAmounts },
                ],
                colors: (e = t ? t.split(",") : e),
                zoom: { enabled: !1 },
                legend: { show: !1 },
                xaxis: { type: "string", categories: month, tooltip: { enabled: !1 }, axisBorder: { show: !1 } },
                grid: { strokeDashArray: 7 },
                yaxis: {
                    labels: {
                        formatter: function (e) {
                            return  e ;
                        },
                        offsetX: -15,
                    },
                },
            },
            e = (new ApexCharts(document.querySelector("#revenue-chart"), r).render(), ["#727cf5", "#e3eaef"]),
            r = {
                chart: { height: 256, type: "bar", stacked: !0 },
                plotOptions: { bar: { horizontal: !1, columnWidth: "20%" } },
                dataLabels: { enabled: !1 },
                stroke: { show: !0, width: 0, colors: ["transparent"] },
                series: [
                    { name: "Collection Amount", data: collectionAmounts },
                    { name: "Loan Amount", data: loanAmounts },
                ],
                zoom: { enabled: !1 },
                legend: { show: !1 },
                colors: (e = (t = o("#high-performing-product").data("colors")) ? t.split(",") : e),
                xaxis: { categories: month, axisBorder: { show: !1 } },
                yaxis: {
                    labels: {
                        formatter: function (e) {
                            return e;
                        },
                        offsetX: -15,
                    },
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: {
                        formatter: function (e) {
                            return  e ;
                        },
                    },
                },
            },
            e = (new ApexCharts(document.querySelector("#high-performing-product"), r).render(), ["#727cf5", "#fa5c7c", "#0acf97", "#ffc35a", "#39afd1", "#6c757d", "#eef2f7"]),
            r = {
                chart: { height: 202, type: "donut" },
                legend: { show: !1 },
                stroke: { width: 0 },
                series: [{{$sumLoanApprovedAmount}}, {{$sumDisbursalAmount}}, {{$sumCollectionAmount}}, {{$sumAdminFeeAmount}}, {{$sumGstAmount}}],
                labels: ["Loan Amount", "Disbursal Amount", "Collection", "Admin Fee", "GST Fee"],
                colors: (e = (t = o("#average-sales").data("colors")) ? t.split(",") : e),
                responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: "bottom" } } }],
            };
        new ApexCharts(document.querySelector("#average-sales"), r).render();
    }),
    (e.prototype.init = function () {
        o("#dash-daterange").daterangepicker({ singleDatePicker: !0 }), this.initCharts();
    }),
    (o.Dashboard = new e()),
    (o.Dashboard.Constructor = e);
})(window.jQuery),
(function (t) {
    "use strict";
    t(document).ready(function (e) {
        t.Dashboard.init();
    });
})(window.jQuery);

</script>

<script type="text/javascript">
    !(function (s) {
    "use strict";
    function e() {
        (this.$body = s("body")), (this.charts = []);
    }
    (e.prototype.initCharts = function () {
        window.Apex = { chart: { parentHeightOffset: 0, toolbar: { show: !1 } }, grid: { padding: { left: 0, right: 0 } }, colors: ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"] };

            var data = @json($totalPerformanceCountCB); // Pass data from controller
            const branches = data.map(item => item.branch); // Get the loan amounts
            const collectionAmounts = data.map(item => item.amount_count); // Get the loan amounts

        for (
            var e = new Date(),
                e = (function (e, t) {
                    for (var a = new Date(t, e, 1), o = [], r = 0; a.getMonth() === e && r < 15; ) {
                        var s = new Date(a);
                        o.push(s.getDate() + " " + s.toLocaleString("en-us", { month: "short" })), a.setDate(a.getDate() + 1), (r += 1);
                    }
                    return o;
                })(e.getMonth() + 1, e.getFullYear()),
                t = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"],
                a = s("#sessions-overview").data("colors"),
                e = {
                    chart: { height: 309, type: "area" },
                    dataLabels: { enabled: !1 },
                    stroke: { curve: "smooth", width: 4 },
                    series: [{ name: "Sessions", data: [10, 20, 5, 15, 10, 20, 15, 25, 20, 30, 25, 40, 30, 50, 35] }],
                    zoom: { enabled: !1 },
                    legend: { show: !1 },
                    colors: (t = a ? a.split(",") : t),
                    xaxis: { type: "string", categories: e, tooltip: { enabled: !1 }, axisBorder: { show: !1 }, labels: {} },
                    yaxis: {
                        labels: {
                            formatter: function (e) {
                                return e ;
                            },
                            offsetX: -15,
                        },
                    },
                    fill: { type: "gradient", gradient: { type: "vertical", shadeIntensity: 1, inverseColors: !1, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
                },
                o = (new ApexCharts(document.querySelector("#sessions-overview"), e).render(), []),
                r = 10;
            1 <= r;
            r--
        )
            o.push('');
        (t = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"]),
            (a = s("#views-min").data("colors")) && (t = a.split(",")),
            (e = {
                chart: { height: 150, type: "bar", stacked: !0 },
                plotOptions: { bar: { horizontal: !1, endingShape: "rounded", columnWidth: "22%", dataLabels: { position: "top" } } },
                dataLabels: { enabled: !0, offsetY: -24, style: { fontSize: "12px", colors: ["#8a969c"] } },
                series: [
                    {
                        name: 'Collections',
                        data: collectionAmounts,
                    },
                ],
                zoom: { enabled: !1 },
                legend: { show: !1 },
                colors: t,
                xaxis: { categories: o, labels: { show: !1 }, axisTicks: { show: !1 }, axisBorder: { show: !1 } },
                yaxis: { labels: { show: !1 } },
                fill: { type: "gradient", gradient: { inverseColors: !0, shade: "light", type: "horizontal", shadeIntensity: 0.25, gradientToColors: void 0, opacityFrom: 1, opacityTo: 1, stops: [0, 100, 100, 100] } },
                tooltip: {
                    y: {
                        formatter: function (e) {
                            return  e;
                        },
                    },
                },
            }),
            new ApexCharts(document.querySelector("#views-min"), e).render(),
            (t = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"]),
            (e = {
                chart: { height: 345, type: "radar" },
                series: [{ name: "Usage", data: [80, 50, 30, 40, 60, 20] }],
                labels: ["Chrome", "Firefox", "Safari", "Opera", "Edge", "Explorer"],
                plotOptions: { radar: { size: 130, polygons: { strokeColor: "#e9e9e9", fill: { colors: ["#f8f8f8", "#fff"] } } } },
                colors: (t = (a = s("#sessions-browser").data("colors")) ? a.split(",") : t),
                yaxis: {
                    labels: {
                        formatter: function (e) {
                            return e + "%";
                        },
                    },
                },
                dataLabels: { enabled: !0 },
                markers: { size: 4, colors: ["#fff"], strokeColor: t[0], strokeWidth: 2 },
            }),
            new ApexCharts(document.querySelector("#sessions-browser"), e).render(),
            (t = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"]),
            (e = {
                chart: { height: 320, type: "bar" },
                plotOptions: { bar: { horizontal: !0 } },
                colors: (t = (a = s("#country-chart").data("colors")) ? a.split(",") : t),
                dataLabels: { enabled: !1 },
                series: [{ name: "Sessions", data: [90, 75, 60, 50, 45, 36, 28, 20, 15, 12] }],
                xaxis: {
                    categories: ["India", "China", "United States", "Japan", "France", "Italy", "Netherlands", "United Kingdom", "Canada", "South Korea"],
                    axisBorder: { show: !1 },
                    labels: {
                        formatter: function (e) {
                            return e + "%";
                        },
                    },
                },
                grid: { strokeDashArray: [5] },
            }),
            new ApexCharts(document.querySelector("#country-chart"), e).render(),
            (t = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"]),
            (e = {
                chart: { height: 269, type: "radialBar" },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: { fontSize: "22px" },
                            value: { fontSize: "16px" },
                            total: {
                                show: !0,
                                label: "OS",
                                formatter: function (e) {
                                    return 8541;
                                },
                            },
                        },
                    },
                },
                colors: (t = (a = s("#sessions-os").data("colors")) ? a.split(",") : t),
                series: [44, 55, 67, 83],
                labels: ["Windows", "Macintosh", "Linux", "Android"],
            });
        new ApexCharts(document.querySelector("#sessions-os"), e).render();
    }),
        (e.prototype.initMaps = function () {
            0 < s("#world-map-markers").length &&
                s("#world-map-markers").vectorMap({
                    map: "world_mill_en",
                    normalizeFunction: "polynomial",
                    hoverOpacity: 0.7,
                    hoverColor: !1,
                    regionStyle: { initial: { fill: "#91a6bd40" } },
                    series: { regions: [{ values: { KR: "#91a6bd40", CA: "#b3c3ff", GB: "#809bfe", NL: "#4d73fe", IT: "#1b4cfe", FR: "#727cf5", JP: "#e7fef7", US: "#e7e9fd", CN: "#8890f7", IN: "#727cf5" }, attribute: "fill" }] },
                    backgroundColor: "transparent",
                    zoomOnScroll: !1,
                });
        }),
        (e.prototype.init = function () {
            s("#dash-daterange").daterangepicker({ singleDatePicker: !0 }),
                this.initCharts(),
                this.initMaps(),
                window.setInterval(function () {
                    var e = Math.floor(600 * Math.random() + 150);
                    s("#active-users-count").text(e), s("#active-views-count").text(Math.floor(Math.random() * e + 200));
                }, 2e3);
        }),
        (s.AnalyticsDashboard = new e()),
        (s.AnalyticsDashboard.Constructor = e);
})(window.jQuery),
    (function () {
        "use strict";
        window.jQuery.AnalyticsDashboard.init();
    })();

</script>

 <script>

       $(document).ready(function() {
        // Function to toggle date range visibility
        function toggleDateRange() {
            if ($('#filter').val() === 'sortByDate') {
                $('#dateRange').show();
            } else {
                $('#dateRange').hide();
            }
        }

        // Initial check on page load
        toggleDateRange();

        // Event listener for filter change
        $('#filter').change(function() {
            toggleDateRange();
        });
    });
 </script>



@endsection

