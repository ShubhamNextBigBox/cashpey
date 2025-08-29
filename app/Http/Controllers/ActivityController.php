<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ActivityController extends Controller
{
    public function activityLogs(Request $request) {
        // Initialize query builder for activity logs
        $activityLogs = DB::table('lms_activity_logs')
            ->orderBy('id', 'desc');  // Sort by ID in descending order
    
        // Filter by date range
        if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
            $dates = explode(' - ', $request->searchRange);
            $fromDate = date('Y-m-d', strtotime($dates[0]));
            $toDate = date('Y-m-d', strtotime($dates[1]));
            $activityLogs->whereBetween('lms_activity_logs.addedOn', [$fromDate, $toDate]);
        } 
        // Filter by today
        elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $activityLogs->whereDate('lms_activity_logs.addedOn', $today);
        }
        // Filter by the last 7 days
        elseif ($request->filter == 'sortByWeek') {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $activityLogs->whereBetween('lms_activity_logs.addedOn', [$sevenDaysAgo, $today]);
        } 
        // Filter by this month
        elseif ($request->filter == 'sortByThisMonth') {
            $activityLogs->whereMonth('lms_activity_logs.addedOn', '=', date('m'))
                ->whereYear('lms_activity_logs.addedOn', '=', date('Y'));
        } 
        // Filter by last month
        elseif ($request->filter == 'sortByLastMonth') {
            $lastMonth = date('m') - 1;
            $lastMonthYear = date('Y');
            if ($lastMonth == 0) {
                $lastMonth = 12;
                $lastMonthYear = date('Y') - 1;
            }
            $activityLogs->whereMonth('lms_activity_logs.addedOn', '=', $lastMonth)
                ->whereYear('lms_activity_logs.addedOn', '=', $lastMonthYear);
        }
    
        // Apply pagination
        $activityLogs = $activityLogs->paginate(10);
    
        // Get query parameters for the current request
        $queryParameters = $request->query();
        $filter = $request->filter;
    
        // Prepare page information for the view
        $page_info = pageInfo('Activity Logs', $request->segment(1));
    
        // Return the view with the data
        return view('activity.activityLogs', compact('activityLogs', 'page_info', 'queryParameters', 'filter'));
    }


    public function activityLeadsTransferLogs(Request $request){
        // Fetch activity logs with pagination
        $activityLeadsTransferLogs = DB::table('lms_lead_transfer_activity_log')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Prepare page information
        $page_info = pageInfo('Leads Transfer Activity Logs', $request->segment(1));

        // Return view with data
        return view('activity.activityLeadsTransferLogs', compact('activityLeadsTransferLogs', 'page_info')); 
    }
}
