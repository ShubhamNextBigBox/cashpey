<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    //
    public function index(Request $request) {
        
          $reportType = $request->reportType ?? 'Business Overview';
            
           if(isSuperAdmin() || role()=='CRM Support' || isAdmin()){
              $reportType = $request->reportType ?? 'Business Overview'; 
           }elseif(isSuperAdmin()|| role()=='Sr. Recovery Manager' || role()=='Recovery Manager' || role()=='Recovery Executive'){
              $reportType = $request->reportType ?? 'Recovery Volume'; 
           }elseif(role()=='ViewOnly'){
               return redirect('reporting/list');
           }else{
                $reportType = $request->reportType ?? 'Sanction 360'; 
           }  
           
           
          if($request->filter=='sortByDate'){
            $filterShow = 'Custom Range';
          }elseif($request->filter=='sortByToday'){
            $filterShow = 'Today';
          }elseif($request->filter=='sortByWeek'){
            $filterShow = 'Week';
          }elseif($request->filter=='sortByThisMonth'){
            $filterShow = 'This Month';
          }elseif($request->filter=='sortByLastMonth'){
            $filterShow = 'Last Month';
          }else{
            $filterShow = 'sortByThisMonth';
          }
 
          if($reportType=='Business Overview'){ 
            
            $currentYear = date('Y');
            $nextYear = $currentYear + 1;
            // Initialize query for lead counts
            $queryFreshLeadsCount = DB::table('lms_leads')->where('status', 'Fresh');
            $queryRejectedLeadsCount = DB::table('lms_leads')->leftjoin('lms_timeline', 'lms_leads.leadID', '=', 'lms_timeline.leadID')->where('lms_leads.status', 'Rejected');  // Count the number of rows that match the conditions
            $queryDisbursedLeadsCount = DB::table('lms_loan')->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')->where('lms_loan.status', 'Disbursed'); 
            $querySanctionLeadsCount = DB::table('lms_approval')->leftJoin('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')->where('lms_approval.status', 'Approved');


            $statusesOfInterest = [
                'Callback','Document Received','Interested', 'Not Interested', 'Wrong Number', 
                'Incomplete Documents', 'DNC', 'Rejected', 'Not Eligible', 
                'Duplicate', 'Other', 'No Answer', 'Less Salary','Self Employed', 'Loan Running', 'Switch Off'
            ];
            
            $statusesOfInterest2 = [
                'Closed', 'Part Payment', 'Settlement', 'Payday Preclose', 
                'Bad Debts', 'Write Off', 'Settled To Closed'];

         // Get the active statuses from the lms_leads_status table
            $queryLeadsStatuses = DB::table('lms_leads_status')->select('name')->where('status', 1)->get();
            
            // Extract the names into an array
            $statusesArray = $queryLeadsStatuses->pluck('name')->toArray();
            
            // Fetch the counts from the lms_timeline table with your statuses of interest and a date range (e.g., the last 7 days)
            $queryLeadStatusCounts = DB::table('lms_timeline')
                ->join('lms_leads', 'lms_timeline.leadID', '=', 'lms_leads.leadID') // Join with lms_leads table
                ->selectRaw('lms_timeline.status, MAX(lms_timeline.leadID) as latest_leadID, COUNT(*) as total') // Use MAX() to get the most recent leadID
                ->whereIn('lms_timeline.status', $statusesOfInterest) // Filter by statuses of interest
                ->groupBy('lms_timeline.status') // Group by status only
                ->orderByDesc('latest_leadID'); // Order by the most recent leadID
             
            $queryLeadCollectionsStatusCounts = DB::table('lms_collection')
                ->join('lms_leads', 'lms_collection.leadID', '=', 'lms_leads.leadID') // Join with lms_leads table
                ->selectRaw('lms_collection.status, MAX(lms_collection.leadID) as latest_leadID, COUNT(*) as total') // Use MAX() to get the most recent leadID
                ->whereIn('lms_collection.status', $statusesOfInterest2) // Filter by statuses of interest
                ->groupBy('lms_collection.status') // Group by status only
                ->orderByDesc('latest_leadID'); // Order by the most recent leadID    
  
            $queryFreshLeadStatusCounts = DB::table('lms_leads')
                ->selectRaw('status, COUNT(*) as total')
                ->where('status', 'Fresh')  // Filter by Fresh status
                ->groupBy('status');  
            
            $querydisbursalSheetSendLeadsCount = DB::table('lms_loan')
                ->selectRaw('status, COUNT(*) as total')
                ->where('status', 'Disbursal Sheet Send')  // Filter by Fresh status
                ->groupBy('status');      
           
         
             // Initialize queries for amounts with joins on leadID
              $queryDisbursalAmount = DB::table('lms_loan')
                  
                  ->join('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID');
              $queryCollectionAmount = DB::table('lms_collection')
                  
                  ->join('lms_leads', 'lms_collection.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_loan', 'lms_collection.leadID', '=', 'lms_loan.leadID')
                  ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID');
              $queryAdminFeeAmount = DB::table('lms_approval')
                  
                  ->join('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID');
                  
              $queryAdminFeeAmountForYear = DB::table('lms_approval')
                  
                  ->join('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                  ->whereBetween('lms_loan.disbursalDate', ["$currentYear-01-01", "$currentYear-12-31"]);    
                  
              $queryInterestAmount = DB::table('lms_collection')
                  
                  ->join('lms_leads', 'lms_collection.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                  ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID');
              
              $queryInterestAmountForYear = DB::table('lms_collection')
                  
                  ->join('lms_leads', 'lms_collection.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                  ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                  ->whereBetween('lms_loan.disbursalDate', ["$currentYear-01-01", "$currentYear-12-31"]);      

             $queryPerformanceRM = DB::table('lms_timeline')
                ->select(
                    'users.displayName',
                    'lms_users_details.profile',
                    'lms_users_details.email',
                    DB::raw('COUNT(DISTINCT lms_timeline.leadID) as lead_count')
                )
                ->join('lms_leads', 'lms_timeline.leadID', '=', 'lms_leads.leadID')
                ->join('users', 'lms_leads.rmID', '=', 'users.userID')
                ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                ->where([
                    
                    'lms_timeline.status' => 'Document Received',
                    'users.status' => 1
                ])
                ->groupBy('users.userID', 'users.displayName', 'lms_users_details.profile', 'lms_users_details.email') // Group by userID to ensure uniqueness
                ->orderBy('lead_count', 'desc');



            $queryPerformanceCM = DB::table('lms_loan')
                ->select(
                    'users.userID', // Include userID to ensure uniqueness
                    'users.displayName',
                    'lms_users_details.profile',
                    'lms_users_details.email',
                    DB::raw('COUNT(DISTINCT lms_loan.leadID) as lead_count')
                )
                ->join('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                ->join('users', 'lms_loan.addedBy', '=', 'users.userID')
                ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                ->where([
                    
                    'lms_loan.status' => 'Disbursed',
                    'users.status' => 1
                ])
                ->groupBy('users.userID', 'users.displayName', 'lms_users_details.profile', 'lms_users_details.email') // Group by userID for uniqueness
                ->orderBy('lead_count', 'desc');


              $queryPerformanceCB = DB::table('lms_collection')
              ->select('lms_approval.branch', DB::raw('SUM(lms_collection.collectedAmount) as amount_count'))
              ->join('lms_leads', 'lms_collection.leadID', '=', 'lms_leads.leadID')
              ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
              ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
              
              ->whereIn('lms_collection.status',['Closed','Part Payment','Settlement','Payday Preclose','Bad Debts','Write Off','Settled To Closed'])
              ->groupBy('lms_approval.branch')
              ->orderBy('amount_count','desc');
              
              
            // Apply filters based on request
            if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                $dates = explode(' - ', $request->searchRange);
                $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
                $toDate = date('Y-m-d', strtotime(trim($dates[1])));
                
                $queryFreshLeadsCount->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
                $queryRejectedLeadsCount->whereBetween('lms_timeline.date', [$fromDate, $toDate]);
                $queryDisbursedLeadsCount->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
                $querySanctionLeadsCount->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
                $queryDisbursalAmount->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
                $querydisbursalSheetSendLeadsCount->whereBetween('lms_loan.addedOn', [$fromDate, $toDate]);
                $queryLeadCollectionsStatusCounts->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]);
                $queryCollectionAmount->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]);
                $queryLeadStatusCounts->whereBetween('lms_timeline.date', [$fromDate, $toDate]);
                $queryFreshLeadStatusCounts->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
                $queryAdminFeeAmount->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
                $queryInterestAmount->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]);
                $queryPerformanceRM->whereBetween('lms_timeline.date', [$fromDate, $toDate]);
                $queryPerformanceCM->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
                $queryPerformanceCB->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]); 
              
            } elseif ($request->filter == 'sortByToday') {
                $today = date('Y-m-d');
                 
                $queryFreshLeadsCount->whereDate('lms_leads.commingLeadsDate', $today);
                $queryRejectedLeadsCount->whereDate('lms_timeline.date',  $today);
                $queryDisbursedLeadsCount->whereDate('lms_loan.disbursalDate',  $today);
                $querySanctionLeadsCount->whereDate('lms_approval.createdDate', $today);
                $queryDisbursalAmount->whereDate('lms_loan.disbursalDate', $today);
                $querydisbursalSheetSendLeadsCount->whereDate('lms_loan.addedOn', $today);
                $queryCollectionAmount->whereDate('lms_collection.collectedDate', $today);
                $queryLeadStatusCounts->whereDate('lms_timeline.date', $today);
                $queryFreshLeadStatusCounts->whereDate('lms_leads.commingLeadsDate', $today);
                $queryLeadCollectionsStatusCounts->whereDate('lms_collection.collectedDate', $today);
                $queryAdminFeeAmount->whereDate('lms_approval.createdDate', $today);
                $queryInterestAmount->whereDate('lms_collection.collectedDate', $today);
                $queryPerformanceRM->whereDate('lms_timeline.date', $today);
                $queryPerformanceCM->whereDate('lms_loan.disbursalDate', $today);
                $queryPerformanceCB->whereDate('lms_collection.collectedDate', $today);

            } elseif ($request->filter == 'sortByWeek') {
                $today = date('Y-m-d');
                $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                
                $queryFreshLeadsCount->whereDate('lms_leads.commingLeadsDate', [$sevenDaysAgo, $today]);
                $queryRejectedLeadsCount->whereDate('lms_timeline.date', [$sevenDaysAgo, $today]);
                $queryDisbursedLeadsCount->whereDate('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
                $querySanctionLeadsCount->whereDate('lms_approval.createdDate', [$sevenDaysAgo, $today]);
                $queryDisbursalAmount->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
                $querydisbursalSheetSendLeadsCount->whereBetween('lms_loan.addedOn', [$sevenDaysAgo, $today]);
                $queryCollectionAmount->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);
                $queryLeadStatusCounts->whereBetween('lms_timeline.date', [$sevenDaysAgo, $today]);
                $queryFreshLeadStatusCounts->whereBetween('lms_leads.commingLeadsDate', [$sevenDaysAgo, $today]);
                $queryLeadCollectionsStatusCounts->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);
                $queryAdminFeeAmount->whereBetween('lms_approval.createdDate', [$sevenDaysAgo, $today]);
                $queryInterestAmount->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);
                $queryPerformanceRM->whereBetween('lms_timeline.date', [$sevenDaysAgo, $today]);
                $queryPerformanceCM->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
                $queryPerformanceCB->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);

            } elseif ($request->filter == 'sortByThisMonth') {
              
                $queryFreshLeadsCount->whereMonth('lms_leads.commingLeadsDate', date('m'))
                    ->whereYear('lms_leads.commingLeadsDate', date('Y'));
                $queryRejectedLeadsCount->whereMonth('lms_timeline.date', date('m'))
                    ->whereYear('lms_timeline.date', date('Y'));
                $queryDisbursedLeadsCount->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
                $querySanctionLeadsCount->whereMonth('lms_approval.createdDate', date('m'))
                    ->whereYear('lms_approval.createdDate', date('Y'));
                $queryDisbursalAmount->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
                $querydisbursalSheetSendLeadsCount->whereMonth('lms_loan.addedOn', date('m'))
                    ->whereYear('lms_loan.addedOn', date('Y'));
                $queryCollectionAmount->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));
                $queryLeadStatusCounts->whereMonth('lms_timeline.date', date('m'))
                    ->whereYear('lms_timeline.date', date('Y'));
                $queryFreshLeadStatusCounts->whereMonth('lms_leads.commingLeadsDate', date('m'))
                    ->whereYear('lms_leads.commingLeadsDate', date('Y'));         
                $queryLeadCollectionsStatusCounts->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y')); 
                $queryAdminFeeAmount->whereMonth('lms_approval.createdDate', date('m'))
                    ->whereYear('createdDate', date('Y'));   
                $queryPerformanceRM->whereMonth('lms_timeline.date', date('m'))
                    ->whereYear('lms_timeline.date', date('Y'));   
                $queryPerformanceCM->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));           
                $queryPerformanceCB->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));   
                $queryInterestAmount->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));                    

            } elseif ($request->filter == 'sortByLastMonth') {
                $lastMonth = date('m') - 1;
                $lastMonthYear = date('Y');
                if ($lastMonth == 0) {
                    $lastMonth = 12;
                    $lastMonthYear = date('Y') - 1;
                }
                
                $queryFreshLeadsCount->whereMonth('lms_leads.commingLeadsDate', $lastMonth)
                    ->whereYear('lms_leads.commingLeadsDate', $lastMonthYear);
                $queryRejectedLeadsCount->whereMonth('lms_timeline.date', $lastMonth)
                    ->whereYear('lms_timeline.date', $lastMonthYear);
                $queryDisbursedLeadsCount->whereMonth('lms_loan.disbursalDate', $lastMonth)
                    ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
                $querySanctionLeadsCount->whereMonth('lms_approval.createdDate', $lastMonth)
                    ->whereYear('lms_approval.createdDate', $lastMonthYear); 
                $queryDisbursalAmount->whereMonth('lms_loan.disbursalDate', $lastMonth)
                    ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
                $querydisbursalSheetSendLeadsCount->whereMonth('lms_loan.addedOn', $lastMonth)
                    ->whereYear('lms_loan.addedOn', $lastMonthYear);
                $queryCollectionAmount->whereMonth('lms_collection.collectedDate', $lastMonth)
                    ->whereYear('lms_collection.collectedDate', $lastMonthYear);
                $queryLeadStatusCounts->whereMonth('lms_timeline.date', $lastMonth)
                    ->whereYear('lms_timeline.date', $lastMonthYear);
                $queryFreshLeadStatusCounts->whereMonth('lms_leads.commingLeadsDate', $lastMonth)
                    ->whereYear('lms_leads.commingLeadsDate', $lastMonthYear);
                $queryLeadCollectionsStatusCounts->whereMonth('lms_collection.collectedDate', $lastMonth)
                    ->whereYear('lms_collection.collectedDate', $lastMonthYear);
                $queryAdminFeeAmount->whereMonth('lms_approval.createdDate', $lastMonth)
                    ->whereYear('lms_approval.createdDate', $lastMonthYear);
                $queryInterestAmount->whereMonth('lms_collection.collectedDate', $lastMonth)
                    ->whereYear('lms_collection.collectedDate', $lastMonthYear);    
                $queryPerformanceRM->whereMonth('lms_timeline.date', $lastMonth)
                    ->whereYear('lms_timeline.date', $lastMonthYear);
                $queryPerformanceCM->whereMonth('lms_loan.disbursalDate', $lastMonth)
                    ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
                $queryPerformanceCB->whereMonth('lms_collection.collectedDate', $lastMonth)
                    ->whereYear('lms_collection.collectedDate', $lastMonthYear);        
            } else {
                $queryFreshLeadsCount->whereMonth('lms_leads.commingLeadsDate', date('m'))
                    ->whereYear('lms_leads.commingLeadsDate', date('Y'));
                $queryRejectedLeadsCount->whereMonth('lms_timeline.date', date('m'))
                    ->whereYear('lms_timeline.date', date('Y'));
                $queryDisbursedLeadsCount->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
                $querySanctionLeadsCount->whereMonth('lms_approval.createdDate', date('m'))
                    ->whereYear('lms_approval.createdDate', date('Y'));
                $queryDisbursalAmount->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
                $querydisbursalSheetSendLeadsCount->whereMonth('lms_loan.addedOn', date('m'))
                    ->whereYear('lms_loan.addedOn', date('Y'));
                $queryLeadCollectionsStatusCounts->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));    
                $queryCollectionAmount->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));
                $queryLeadStatusCounts->whereMonth('lms_timeline.date', date('m'))
                    ->whereYear('lms_timeline.date', date('Y'));
                $queryFreshLeadStatusCounts->whereMonth('lms_leads.commingLeadsDate', date('m'))
                    ->whereYear('lms_leads.commingLeadsDate', date('Y'));
                $queryAdminFeeAmount->whereMonth('lms_approval.createdDate', date('m'))
                    ->whereYear('createdDate', date('Y'));   
                $queryPerformanceRM->whereMonth('lms_timeline.date', date('m'))
                    ->whereYear('lms_timeline.date', date('Y'));   
                $queryPerformanceCM->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));           
                $queryPerformanceCB->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));   
                $queryInterestAmount->whereMonth('lms_collection.collectedDate', date('m'))
                    ->whereYear('lms_collection.collectedDate', date('Y'));                    
            }

              // Execute queries
             
              $freshLeadsCount     = $queryFreshLeadsCount->count();
              $rejectedLeadsCount  = $queryRejectedLeadsCount->count();
              $disbursedLeadsCount = $queryDisbursedLeadsCount->count();
              $sanctionLeadsCount  = $querySanctionLeadsCount->count();
              
              
             
              $queryInterestAmountShow = $queryInterestAmountForYear->sum('lms_approval.totalInterestAmount');
              $queryAdminFeeAmountShow = $queryAdminFeeAmountForYear->sum('lms_approval.adminFee');
  
              $sumDisbursalAmount = $queryDisbursalAmount->sum('lms_loan.disbursalAmount');
              $sumCollectionAmount = $queryCollectionAmount->sum('lms_collection.collectedAmount');
              $totalClosedCases = $queryCollectionAmount->where('lms_collection.status','Closed')->count();
              $sumAdminFeeAmount = $queryAdminFeeAmount->sum('lms_approval.adminFee');
              $sumInterestAmount = $queryInterestAmount->sum('lms_approval.totalInterestAmount');
              $sumGstAmount = $queryAdminFeeAmount->sum('lms_approval.adminGstAmount');
              $sumLoanApprovedAmount = $queryAdminFeeAmount->sum('lms_approval.loanAmtApproved');
              $totalDisbursedCases = $queryDisbursalAmount->where('lms_loan.status','Disbursed')->count();
              $totalPerformanceCountRM = $queryPerformanceRM->limit(6)->get();
              $totalPerformanceCountCM = $queryPerformanceCM->limit(6)->get();
              $totalPerformanceCountCB = $queryPerformanceCB->limit(5)->get();
       
        
              $totalPerformanceCountCB = $totalPerformanceCountCB->map(function($item) {
                  // Replace 'yourHelperFunction' with the actual function name
                  $item->branch = getUserNameById('lms_cities', 'cityID',$item->branch, 'cityName');
                  return $item;
              });
     
              // Get the lead status counts
              $leadStatusCounts = $queryLeadStatusCounts->get()->keyBy('status')->toArray();
              $leadCollectionsStatusCounts = $queryLeadCollectionsStatusCounts->get()->keyBy('status')->toArray();
              $leadFreshStatusCounts = $queryFreshLeadStatusCounts->get()->keyBy('status')->toArray();
              $leadsDisbursalSheetSendCounts = $querydisbursalSheetSendLeadsCount->count();
           
              
                 $allLeadsCountSum = [];
                
                foreach ($leadFreshStatusCounts as $status) {
                   
                    $timelineCount = isset($status->total) ? $status->total : 0;
                    // // Add the result to the final array
                    $allLeadsCountSum[] = [
                        'status' => $status->status,
                        'total' => $timelineCount, // This is the count from the lms_timeline table
                    ];
                }
                // First, merge data from the first query (lms_timeline)
                foreach ($statusesOfInterest as $status) {
                    // Get the count from the first query (lms_timeline)
                    $timelineCount = isset($leadStatusCounts[$status]) ? $leadStatusCounts[$status]->total : 0;
                
                    // Add the result to the final array
                    $allLeadsCountSum[] = [
                        'status' => $status,
                        'total' => $timelineCount, // This is the count from the lms_timeline table
                    ];
                }
              
                foreach ($statusesOfInterest2 as $status) {
                    // Get the count from the first query (lms_timeline)
                    $timelineCount = isset($leadCollectionsStatusCounts[$status]) ? $leadCollectionsStatusCounts[$status]->total : 0;
                
                    // Add the result to the final array
                    $allLeadsCountSum[] = [
                        'status' => $status,
                        'total' => $timelineCount, // This is the count from the lms_timeline table
                    ];
                }
                
                
        
                $allLeadsCountSum[] = [
                        'status' => 'Approved',
                        'total' => $sanctionLeadsCount, // This is the count from the lms_timeline table
                      ];
                $allLeadsCountSum[] = [
                        'status' => 'Disbursal Sheet Send',
                        'total' => $leadsDisbursalSheetSendCounts, // This is the count from the lms_timeline table
                      ];
                $allLeadsCountSum[] = [
                        'status' => 'Disbursed',
                        'total' => $disbursedLeadsCount, // This is the count from the lms_timeline table
                      ];
                
                      
           
             

              // Fetch data from the database for disbursal
             
              $monthlyDisbursed = DB::table('lms_loan')
                  ->join('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                  ->select(DB::raw('DATE_FORMAT(lms_loan.disbursalDate, "%Y-%m") as month, SUM(disbursalAmount) as total_disbursed'))
                  ->whereBetween('lms_loan.disbursalDate', ["$currentYear-01-01", "$currentYear-12-31"])
                  ->groupBy('month') // Group by the formatted month
                  ->orderBy('month')
                  ->get(); 


             // Fetch data from the database for admin fee
              $monthlyAdminFee = DB::table('lms_loan')
                  ->join('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                  ->select(DB::raw('DATE_FORMAT(lms_approval.createdDate, "%Y-%m") as month, SUM(lms_approval.adminFee) as total_adminFee'))
                  ->whereBetween('lms_approval.createdDate', ["$currentYear-01-01", "$currentYear-12-31"])
                  ->groupBy('month') // Group by the formatted month
                  ->orderBy('month')
                  ->get();

 
             // Fetch data from the database for interest
              $monthlyInterest = DB::table('lms_loan')
                  ->join('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                  ->join('lms_collection', 'lms_leads.leadID', '=', 'lms_collection.leadID')
                  ->select(DB::raw('DATE_FORMAT(lms_approval.createdDate, "%Y-%m") as month, SUM(lms_approval.totalInterestAmount) as total_interest'))
                  ->whereBetween('lms_loan.disbursalDate', ["$currentYear-01-01", "$currentYear-12-31"])
                  ->groupBy('month') // Group by the formatted month
                  ->orderBy('month')
                  ->get();     
            
              // Fetch data for approvals with join
              $monthlyApproved = DB::table('lms_approval')
                  ->join('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
                  ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                  ->select(DB::raw('DATE_FORMAT(lms_approval.createdDate, "%Y-%m") as month, SUM(loanAmtApproved) as total_approved'))
                  ->where('lms_approval.status','Approved')
                  ->where('lms_loan.status','Disbursed')
                  ->whereBetween('lms_approval.createdDate', ["$currentYear-01-01", "$currentYear-12-31"])
                  ->groupBy('month') // Group by the formatted month
                  ->orderBy('month')
                  ->get();

              // Fetch data for collections with join
              $monthlyCollection = DB::table('lms_collection')
                  ->join('lms_leads', 'lms_collection.leadID', '=', 'lms_leads.leadID')
                  ->select(DB::raw('DATE_FORMAT(lms_collection.collectedDate, "%Y-%m") as month, SUM(collectedAmount) as total_collection'))
                  ->whereBetween('lms_collection.collectedDate', ["$currentYear-01-01", "$currentYear-12-31"])
                  ->groupBy('month') // Group by the formatted month
                  ->orderBy('month')
                  ->get();    

              // Combine results
              $months = [
                  "$currentYear-01" => 'Jan',
                  "$currentYear-02" => 'Feb',
                  "$currentYear-03" => 'Mar',
                  "$currentYear-04" => 'Apr',
                  "$currentYear-05" => 'May',
                  "$currentYear-06" => 'Jun',
                  "$currentYear-07" => 'Jul',
                  "$currentYear-08" => 'Aug',
                  "$currentYear-09" => 'Sep',
                  "$currentYear-10" => 'Oct',
                  "$currentYear-11" => 'Nov',
                  "$currentYear-12" => 'Dec',
                 
              ];

              $result = [];
              foreach ($months as $key => $monthName) {

                  $totalDisbursed = $monthlyDisbursed->where('month', $key)->first()->total_disbursed ?? 0;
                //  $totalDisbursedSum = $monthlyDisbursed->first()->total_disbursed ?? 0;
                  $totalApproved = $monthlyApproved->where('month', $key)->first()->total_approved ?? 0;
                  $totalAdminFee = $monthlyAdminFee->where('month', $key)->first()->total_adminFee ?? 0;
               //   $totalAdminFeeSum = $monthlyAdminFee->first()->total_adminFee ?? 0;
                  $totalInterest = $monthlyInterest->where('month', $key)->first()->total_interest ?? 0;
               //   $totalInterestSum = $monthlyInterest->first()->total_interest ?? 0;
                  $totalCollection = $monthlyCollection->where('month', $key)->first()->total_collection ?? 0;
                  $totalCollectionSum = $monthlyCollection->first()->total_collection ?? 0;


                  $result[] = [
                      'month' => $monthName,
                    //   'total_disbursed' => $totalDisbursed,
                    //   'total_disbursed_sum' => $totalDisbursedSum,
                      'total_adminFee' => $totalAdminFee,
                     // 'total_adminFee_sum' => $totalAdminFeeSum,
                      'total_interest' => $totalInterest,
                    //  'total_interest_sum' => $totalInterestSum,
                      'total_approved' => $totalApproved,
                      'total_collection' => $totalCollection,
                   //   'total_collection_sum' => $totalCollectionSum,
                  ];
              }
 

              $disbursalPerMonth = $result;  
           
              $filter = $request->filter;
              $page_info = pageInfo('Dashboard', $request->segment(1));
              $data = compact('freshLeadsCount', 'rejectedLeadsCount', 'disbursedLeadsCount','queryInterestAmountShow','queryAdminFeeAmountShow','sanctionLeadsCount','page_info', 'disbursalPerMonth', 'filter','reportType','filterShow','sumDisbursalAmount', 'sumCollectionAmount', 'allLeadsCountSum','sumAdminFeeAmount','sumGstAmount','sumLoanApprovedAmount','totalDisbursedCases','totalClosedCases','sumInterestAmount','currentYear','nextYear','totalPerformanceCountRM','totalPerformanceCountCM','totalPerformanceCountCB');
              
            
              return view('dashboard')->with($data); 
      }elseif($reportType=='Business Analytics'){ 
        echo "TBD";
     }elseif($reportType=='Sanction 360'){ 
            
       $querySanctionData = DB::table('lms_sanction_target')
            ->leftJoin('lms_approval', 'lms_sanction_target.userID', '=', 'lms_approval.creditedBy')
            ->leftJoin('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
            ->leftJoin('lms_loan', 'lms_approval.leadID', '=', 'lms_loan.leadID')
            ->leftJoin('lms_users_details', 'lms_sanction_target.userID', '=', 'lms_users_details.userID')
            ->where('lms_loan.status', 'Disbursed')  // Ensure loan is disbursed
            ->select(
                'lms_users_details.profile',
                'lms_sanction_target.userID',
                'lms_sanction_target.target',
                DB::raw('COALESCE(SUM(lms_approval.loanAmtApproved), 0) as totalLoanAmtApproved')  // Handling null sum case
            )
            ->groupBy('lms_sanction_target.userID', 'lms_users_details.profile', 'lms_sanction_target.target')
            ->orderByDesc(DB::raw('COALESCE(SUM(lms_approval.loanAmtApproved), 0)'));
            
            
       $queryBranchData = DB::table('lms_branch_target')
            ->join('lms_approval', 'lms_approval.branch', '=', 'lms_branch_target.branchId')
            ->join('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
            ->join('lms_loan', 'lms_approval.leadID', '=', 'lms_loan.leadID')
            ->where('lms_loan.status', 'Disbursed')  // Ensure loan is disbursed
            ->select(
                'lms_branch_target.branchId',
                'lms_branch_target.target',
                DB::raw('SUM(lms_approval.loanAmtApproved) as totalLoanAmtApproved')  // Sum of approved loans
            )
            ->groupBy('lms_branch_target.branchId', 'lms_branch_target.target');
            
        $querySanctionFreshRepeatData = DB::table('lms_contact')
                    ->leftJoin('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->leftJoin('lms_loan', 'lms_approval.leadID', '=', 'lms_loan.leadID')
                    ->leftJoin('users', 'lms_approval.creditedBy', '=', 'users.userID')
                    ->leftJoin('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                    ->leftJoin(DB::raw('(SELECT leadID, 
                                            contactID,
                                            ROW_NUMBER() OVER (PARTITION BY contactID ORDER BY lms_loan.disbursalDate) as rn,
                                            EXTRACT(MONTH FROM lms_loan.disbursalDate) as loanMonth,
                                            EXTRACT(YEAR FROM lms_loan.disbursalDate) as loanYear
                                         FROM lms_loan
                                         WHERE lms_loan.status = "Disbursed"
                                      ) as rankedLeads'), 'lms_leads.leadID', '=', 'rankedLeads.leadID')
                    ->select(
                        'lms_users_details.userID',
                        'lms_users_details.profile',
                        DB::raw('SUM(CASE 
                                        WHEN rankedLeads.rn = 1 THEN 1 
                                        ELSE 0 
                                    END) as freshCases'),  // Count fresh cases where rn = 1
                        DB::raw('SUM(CASE 
                                        WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved
                                        ELSE 0 
                                    END) as freshLoanAmount'),  // Sum fresh loan amounts
                        DB::raw('SUM(CASE 
                                        WHEN rankedLeads.rn > 1 THEN 1 
                                        ELSE 0 
                                    END) as repeatCases'),  // Count repeat cases where rn > 1
                        DB::raw('SUM(CASE 
                                        WHEN rankedLeads.rn > 1 THEN lms_approval.loanAmtApproved
                                        ELSE 0 
                                    END) as repeatLoanAmount')  // Sum repeat loan amounts
                    )
                    ->groupBy('lms_users_details.userID', 'lms_users_details.profile')
                    ->orderByDesc(DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END)'));  // Order by fresh loan amount (descending)
                
            if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    // Filter by a custom date range (start and end date)
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
                    $toDate = date('Y-m-d', strtotime(trim($dates[1])));
                    
                    // Apply to all queries: $querySanctionData, $queryBranchData, $querySanctionFreshRepeatData
                    $querySanctionData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])
                        ->whereBetween('lms_sanction_target.addedOn', [$fromDate, $toDate]);
                    
                    $queryBranchData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])
                        ->whereBetween('lms_branch_target.addedOn', [$fromDate, $toDate]);
                    
                    $querySanctionFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
                    
                } elseif ($request->filter == 'sortByToday') {
                    // Filter by today
                    $today = date('Y-m-d');
                    
                    // Apply to all queries: $querySanctionData, $queryBranchData, $querySanctionFreshRepeatData
                    $querySanctionData->whereDate('lms_loan.disbursalDate', $today)
                        ->whereDate('lms_sanction_target.addedOn', $today);
                    
                    $queryBranchData->whereDate('lms_loan.disbursalDate', $today)
                        ->whereDate('lms_branch_target.addedOn', $today);
                    
                    $querySanctionFreshRepeatData->whereDate('lms_loan.disbursalDate', $today);
                    
                } elseif ($request->filter == 'sortByWeek') {
                    // Filter by last 7 days (one week)
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    
                    // Apply to all queries: $querySanctionData, $queryBranchData, $querySanctionFreshRepeatData
                    $querySanctionData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today])
                        ->whereBetween('lms_sanction_target.addedOn', [$sevenDaysAgo, $today]);
                    
                    $queryBranchData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today])
                        ->whereBetween('lms_branch_target.addedOn', [$sevenDaysAgo, $today]);
                    
                    $querySanctionFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
                    
                } elseif ($request->filter == 'sortByThisMonth') {
                    // Filter by the current month and year
                    $querySanctionData->whereMonth('lms_loan.disbursalDate', date('m'))
                        ->whereYear('lms_loan.disbursalDate', date('Y'))
                        ->whereMonth('lms_sanction_target.addedOn', date('m'))
                        ->whereYear('lms_sanction_target.addedOn', date('Y'));
                
                    $queryBranchData->whereMonth('lms_loan.disbursalDate', date('m'))
                        ->whereYear('lms_loan.disbursalDate', date('Y'))
                        ->whereMonth('lms_branch_target.addedOn', date('m'))
                        ->whereYear('lms_branch_target.addedOn', date('Y'));
                    
                    $querySanctionFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                        ->whereYear('lms_loan.disbursalDate', date('Y'));
                    
                } elseif ($request->filter == 'sortByLastMonth') {
                    // Filter by the last month
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                
                    $querySanctionData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                        ->whereYear('lms_loan.disbursalDate', $lastMonthYear)
                        ->whereMonth('lms_sanction_target.addedOn', $lastMonth)
                        ->whereYear('lms_sanction_target.addedOn', $lastMonthYear);
                
                    $queryBranchData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                        ->whereYear('lms_loan.disbursalDate', $lastMonthYear)
                        ->whereMonth('lms_branch_target.addedOn', $lastMonth)
                        ->whereYear('lms_branch_target.addedOn', $lastMonthYear);
                
                    $querySanctionFreshRepeatData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                        ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
                    
                } else {
                    // Default filter for current month and year
                    $querySanctionData->whereMonth('lms_loan.disbursalDate', date('m'))
                        ->whereYear('lms_loan.disbursalDate', date('Y'))
                        ->whereMonth('lms_sanction_target.addedOn', date('m'))
                        ->whereYear('lms_sanction_target.addedOn', date('Y'));
                
                    $queryBranchData->whereMonth('lms_loan.disbursalDate', date('m'))
                        ->whereYear('lms_loan.disbursalDate', date('Y'))
                        ->whereMonth('lms_branch_target.addedOn', date('m'))
                        ->whereYear('lms_branch_target.addedOn', date('Y'));
                
                    $querySanctionFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                        ->whereYear('lms_loan.disbursalDate', date('Y'));
                }
                
                
            $sanctionData = $querySanctionData->get();
            $branchData = $queryBranchData->get();
            $sanctionFreshRepeatData = $querySanctionFreshRepeatData->get();
 
       
            // Passing data to the view
            $filter = $request->filter;
            $page_info = pageInfo('Dashboard Sanction 360', $request->segment(1));
            $data = compact('sanctionData','branchData','sanctionFreshRepeatData','page_info', 'filter', 'reportType', 'filterShow');
            return view('dashboardSanction360')->with($data);
            
            }
            elseif($reportType=='Recovery Value'){ 

                $queryRecoverySanctionValuesData = DB::table('lms_sanction_target')
                        ->leftJoin('lms_approval', 'lms_sanction_target.userID', '=', 'lms_approval.CreditedBy')
                        ->leftJoin('lms_leads', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                        ->leftJoin('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                        ->leftJoin('lms_collection', function($join) {
                            $join->on('lms_collection.leadID', '=', 'lms_loan.leadID')
                                 ->whereIn('lms_collection.status', [
                                    'Closed', 'Settled to Closed', 'Part Payment', 'Settlement', 'Payday Preclose', 'Bad Debts', 'Write Off'
                                ]);
                        })
                         ->select(
                                'users.displayName as CreditBy','lms_users_details.profile',
                                DB::raw('IFNULL(COUNT(lms_loan.loanNo), 0) AS DueCases'),
                                DB::raw('FLOOR(IFNULL(SUM(lms_approval.loanAmtApproved + (lms_approval.loanAmtApproved * lms_approval.roi * lms_approval.tenure / 100)), 0)) AS RepayAmount'),
                                DB::raw('IFNULL(COUNT(lms_collection.contactID), 0) AS PaidCases'),
                                DB::raw('FLOOR(IFNULL(SUM(lms_collection.collectedAmount), 0)) AS PaidAmount'),
                                DB::raw('FLOOR(IFNULL(IFNULL(SUM(lms_approval.loanAmtApproved + (lms_approval.loanAmtApproved * lms_approval.roi * lms_approval.tenure / 100)), 0) - IFNULL(SUM(lms_collection.collectedAmount), 0), 0)) AS Deficit'),
                                DB::raw('FLOOR(IFNULL(SUM(lms_collection.collectedAmount), 0) * 100 / IFNULL(SUM(lms_approval.loanAmtApproved + (lms_approval.loanAmtApproved * lms_approval.roi * lms_approval.tenure / 100)), 0)) AS PerCollect')
                            )
                            ->groupBy('users.displayName', 'lms_users_details.profile') // Ensure profile picture is part of group by
                           
                        ->leftJoin('users', 'lms_sanction_target.userID', '=', 'users.userID')
                        ->leftJoin('lms_users_details', 'users.userID', '=', 'lms_users_details.userID');
                    
                $queryRecoverySanctionBranchwiseValuesData = DB::table('lms_branch_target')
                        ->join('lms_approval', 'lms_approval.branch', '=', 'lms_branch_target.branchId')
                        ->leftJoin('lms_leads', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                        ->leftJoin('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                        ->leftJoin('lms_collection', function($join) {
                            $join->on('lms_collection.leadID', '=', 'lms_loan.leadID')
                                 ->whereIn('lms_collection.status', [
                                    'Closed', 'Settled to Closed', 'Part Payment', 'Settlement', 'Payday Preclose', 'Bad Debts', 'Write Off'
                                ]);
                        })
                         ->select(
                                'lms_branch_target.branchId','lms_branch_target.target',
                                DB::raw('IFNULL(COUNT(lms_loan.loanNo), 0) AS DueCases'),
                                DB::raw('FLOOR(IFNULL(SUM(lms_approval.loanAmtApproved + (lms_approval.loanAmtApproved * lms_approval.roi * lms_approval.tenure / 100)), 0)) AS RepayAmount'),
                                DB::raw('IFNULL(COUNT(lms_collection.contactID), 0) AS PaidCases'),
                                DB::raw('FLOOR(IFNULL(SUM(lms_collection.collectedAmount), 0)) AS PaidAmount'),
                                DB::raw('FLOOR(IFNULL(IFNULL(SUM(lms_approval.loanAmtApproved + (lms_approval.loanAmtApproved * lms_approval.roi * lms_approval.tenure / 100)), 0) - IFNULL(SUM(lms_collection.collectedAmount), 0), 0)) AS Deficit'),
                                DB::raw('FLOOR(IFNULL(SUM(lms_collection.collectedAmount), 0) * 100 / IFNULL(SUM(lms_approval.loanAmtApproved + (lms_approval.loanAmtApproved * lms_approval.roi * lms_approval.tenure / 100)), 0)) AS PerCollect')
                            )
                        ->groupBy('lms_branch_target.branchId', 'lms_branch_target.target');
                        
                    
                    if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                        // Filter by a custom date range (start and end date)
                        $dates = explode(' - ', $request->searchRange);
                        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
                        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
                        
                        $queryRecoverySanctionValuesData->whereBetween('lms_loan.repayDate', [$fromDate, $toDate])
                            ->whereBetween('lms_sanction_target.addedOn', [$fromDate, $toDate]);
                            
                         $queryRecoverySanctionBranchwiseValuesData->whereBetween('lms_loan.repayDate', [$fromDate, $toDate])
                            ->whereBetween('lms_branch_target.addedOn', [$fromDate, $toDate]);    
                        
                    } elseif ($request->filter == 'sortByToday') {
                        // Filter by today
                        $today = date('Y-m-d');
                        $queryRecoverySanctionValuesData->whereDate('lms_loan.repayDate', '=', $today);
                        //    ->whereDate('lms_sanction_target.addedOn', '=', $today);
                        
                        $queryRecoverySanctionBranchwiseValuesData->whereDate('lms_loan.repayDate', '=', $today);
                          //  ->whereDate('lms_branch_target.addedOn', '=', $today);
                        
                    } elseif ($request->filter == 'sortByWeek') {
                        // Filter by last 7 days (one week)
                        $today = date('Y-m-d');
                        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                        $queryRecoverySanctionValuesData->whereBetween('lms_loan.repayDate', [$sevenDaysAgo, $today])
                            ->whereBetween('lms_sanction_target.addedOn', [$sevenDaysAgo, $today]);
                            
                         $queryRecoverySanctionBranchwiseValuesData->whereBetween('lms_loan.repayDate', [$sevenDaysAgo, $today])
                            ->whereBetween('lms_branch_target.addedOn', [$sevenDaysAgo, $today]);
                        
                    } elseif ($request->filter == 'sortByThisMonth') {
                        // Filter by the current month and year
                        $queryRecoverySanctionValuesData->whereMonth('lms_loan.repayDate', date('m'))
                            ->whereYear('lms_loan.repayDate', date('Y'))
                            ->whereMonth('lms_sanction_target.addedOn', date('m'))
                            ->whereYear('lms_sanction_target.addedOn', date('Y'));
                            
                        $queryRecoverySanctionBranchwiseValuesData->whereMonth('lms_loan.repayDate', date('m'))
                            ->whereYear('lms_loan.repayDate', date('Y'))
                            ->whereMonth('lms_branch_target.addedOn', date('m'))
                            ->whereYear('lms_branch_target.addedOn', date('Y'));    
                        
                    } elseif ($request->filter == 'sortByLastMonth') {
                        // Filter by the last month
                        $lastMonth = date('m') - 1;
                        $lastMonthYear = date('Y');
                        if ($lastMonth == 0) {
                            $lastMonth = 12;
                            $lastMonthYear = date('Y') - 1;
                        }
                        
                        $queryRecoverySanctionValuesData->whereMonth('lms_loan.repayDate', $lastMonth)
                            ->whereYear('lms_loan.repayDate', $lastMonthYear)
                            ->whereMonth('lms_sanction_target.addedOn', $lastMonth)
                            ->whereYear('lms_sanction_target.addedOn', $lastMonthYear);
                            
                        $queryRecoverySanctionBranchwiseValuesData->whereMonth('lms_loan.repayDate', $lastMonth)
                            ->whereYear('lms_loan.repayDate', $lastMonthYear)
                            ->whereMonth('lms_branch_target.addedOn', $lastMonth)
                            ->whereYear('lms_branch_target.addedOn', $lastMonthYear);    
                    }
                    
                   
            $recoverySanctionValuesData = $queryRecoverySanctionValuesData->get();
            $recoverySanctionBranchwiseValuesData = $queryRecoverySanctionBranchwiseValuesData->get();
            
       
            // Passing data to the view
            $filter = $request->filter;
            $page_info = pageInfo('Dashboard Recovery Values', $request->segment(1));
            $data = compact('recoverySanctionValuesData','recoverySanctionBranchwiseValuesData','page_info', 'filter', 'reportType', 'filterShow');
            return view('dashboardRecoveryValue')->with($data);
            
            }    
            
            elseif($reportType=='Recovery Volume'){ 
               $queryRecoverySanctionVolumeData = DB::table('lms_sanction_target')
                    ->select(
                        'lms_approval.creditedBy', 
                        'lms_users_details.profile', 
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Disbursed", "Part Payment", "Settlement", "Closed", "Settled to Closed", "Payday Preclose", "Bad Debts", "Write Off") THEN 1 ELSE 0 END), 0) AS DueCases'),
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Disbursed", "Part Payment") THEN 1 ELSE 0 END), 0) AS PendingCases'),
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Payday Preclose", "Settlement", "Closed", "Settled to Closed", "Write Off") THEN 1 ELSE 0 END), 0) AS ClosedCases'),
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Payday Preclose", "Settlement", "Closed", "Settled to Closed", "Write Off") THEN 1 ELSE 0 END) / NULLIF(SUM(CASE WHEN lms_leads.status IN ("Disbursed", "Part Payment", "Settlement", "Closed", "Settled to Closed", "Payday Preclose", "Bad Debts", "Write Off") THEN 1 ELSE 0 END), 0) * 100, 0) AS Ratio')
                    )
                    ->leftJoin('users', 'lms_sanction_target.userID', '=', 'users.userID')
                    ->leftJoin('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                    ->leftJoin('lms_approval', 'lms_sanction_target.userID', '=', 'lms_approval.creditedBy')
                    ->leftJoin('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                    ->leftJoin('lms_collection', 'lms_loan.loanNo', '=', 'lms_collection.loanNo')
                    ->groupBy('lms_approval.creditedBy','lms_users_details.profile');
                
                $queryRecoveryBranchVolumeData = DB::table('lms_sanction_target')
                    ->select(
                        'lms_approval.branch',  // Replace creditedBy with branch
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Disbursed", "Part Payment", "Settlement", "Closed", "Settled to Closed", "Payday Preclose", "Bad Debts", "Write Off") THEN 1 ELSE 0 END), 0) AS DueCases'),
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Disbursed", "Part Payment") THEN 1 ELSE 0 END), 0) AS PendingCases'),
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Payday Preclose", "Settlement", "Closed", "Settled to Closed", "Write Off") THEN 1 ELSE 0 END), 0) AS ClosedCases'),
                        DB::raw('IFNULL(SUM(CASE WHEN lms_leads.status IN ("Payday Preclose", "Settlement", "Closed", "Settled to Closed", "Write Off") THEN 1 ELSE 0 END) / NULLIF(SUM(CASE WHEN lms_leads.status IN ("Disbursed", "Part Payment", "Settlement", "Closed", "Settled to Closed", "Payday Preclose", "Bad Debts", "Write Off") THEN 1 ELSE 0 END), 0) * 100, 0) AS Ratio')
                    )
                    ->leftJoin('users', 'lms_sanction_target.userID', '=', 'users.userID')
                    ->leftJoin('lms_approval', 'lms_sanction_target.userID', '=', 'lms_approval.creditedBy')
                    ->leftJoin('lms_leads', 'lms_approval.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                    ->leftJoin('lms_collection', 'lms_loan.loanNo', '=', 'lms_collection.loanNo')
                    ->groupBy('lms_approval.branch'); // Group by branch instead of creditedBy
                  
                // Apply filter conditions using `if` statements (same as you had earlier)
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
                    $toDate = date('Y-m-d', strtotime(trim($dates[1])));
                    
                    $queryRecoverySanctionVolumeData->whereBetween('lms_loan.repayDate', [$fromDate, $toDate])
                        ->whereBetween('lms_sanction_target.addedOn', [$fromDate, $toDate]);
                        
                    $queryRecoveryBranchVolumeData->whereBetween('lms_loan.repayDate', [$fromDate, $toDate])
                        ->whereBetween('lms_sanction_target.addedOn', [$fromDate, $toDate]);    
                        
                        
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $queryRecoverySanctionVolumeData->whereDate('lms_loan.repayDate', '=', $today);
                       // ->whereDate('lms_sanction_target.addedOn', '=', $today);
                    $queryRecoveryBranchVolumeData->whereDate('lms_loan.repayDate', '=', $today);
                      //  ->whereDate('lms_sanction_target.addedOn', '=', $today);    
                        
                } elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $queryRecoverySanctionVolumeData->whereBetween('lms_loan.repayDate', [$sevenDaysAgo, $today])
                        ->whereBetween('lms_sanction_target.addedOn', [$sevenDaysAgo, $today]);
                    $queryRecoveryBranchVolumeData->whereBetween('lms_loan.repayDate', [$sevenDaysAgo, $today])
                        ->whereBetween('lms_sanction_target.addedOn', [$sevenDaysAgo, $today]);     
                        
                } elseif ($request->filter == 'sortByThisMonth') {
                    $queryRecoverySanctionVolumeData->whereMonth('lms_loan.repayDate', date('m'))
                        ->whereYear('lms_loan.repayDate', date('Y'))
                        ->whereMonth('lms_sanction_target.addedOn', date('m'))
                        ->whereYear('lms_sanction_target.addedOn', date('Y'));
                        
                    $queryRecoveryBranchVolumeData->whereMonth('lms_loan.repayDate', date('m'))
                        ->whereYear('lms_loan.repayDate', date('Y'))
                        ->whereMonth('lms_sanction_target.addedOn', date('m'))
                        ->whereYear('lms_sanction_target.addedOn', date('Y')); 
                        
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    
                    $queryRecoverySanctionVolumeData->whereMonth('lms_loan.repayDate', $lastMonth)
                        ->whereYear('lms_loan.repayDate', $lastMonthYear)
                        ->whereMonth('lms_sanction_target.addedOn', $lastMonth)
                        ->whereYear('lms_sanction_target.addedOn', $lastMonthYear);
                    $queryRecoveryBranchVolumeData->whereMonth('lms_loan.repayDate', $lastMonth)
                        ->whereYear('lms_loan.repayDate', $lastMonthYear)
                        ->whereMonth('lms_sanction_target.addedOn', $lastMonth)
                        ->whereYear('lms_sanction_target.addedOn', $lastMonthYear);    
                }
                
                $recoverySanctionVolumeData = $queryRecoverySanctionVolumeData->get();
                $recoveryBranchVolumeData = $queryRecoveryBranchVolumeData->get();
            
                    // // Passing data to the view
                    $filter = $request->filter;
                    $page_info = pageInfo('Dashboard Recovery Volumes', $request->segment(1));
                    $data = compact('recoverySanctionVolumeData','recoveryBranchVolumeData','page_info', 'filter', 'reportType', 'filterShow');
                    return view('dashboardRecoveryVolume')->with($data);
            
            }     
}
 



    
  public function notificationsCount(Request $request){
       
      if(isSuperAdmin() || role()=='CRM Support' || role()=='Technical Support'){
        $status  = ['Open','Pending','Hold'];    
      }else{
        $status  = ['Closed'];
      } 
      
      $count = DB::table('lms_tickets')
                 ->whereIn('status', $status) //
                 ->count();
      return response()->json(['count' => $count]);
  }
}
