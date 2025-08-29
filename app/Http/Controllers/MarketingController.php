<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class MarketingController extends Controller
{
      
    public function cmFreshRepeated(Request $request) {
     
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
            $filterShow = 'Current Month';
          }
          
    // Build the base query for data
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
            DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN 1 ELSE 0 END) as freshCases'),
            DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END) as freshLoanAmount'),
            DB::raw('SUM(CASE WHEN rankedLeads.rn > 1 THEN 1 ELSE 0 END) as repeatCases'),
            DB::raw('SUM(CASE WHEN rankedLeads.rn > 1 THEN lms_approval.loanAmtApproved ELSE 0 END) as repeatLoanAmount')
        )
        ->groupBy('lms_users_details.userID', 'lms_users_details.profile')
        ->orderByDesc(DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END)'));

    // Apply filters based on the request
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $querySanctionFreshRepeatData->where(function ($query) use ($search) {
            $query->where('lms_contact.name', 'like', "%{$search}%")
                  ->orWhere('users.displayName', 'like', "%{$search}%");
        });
    } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $querySanctionFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        $today = date('Y-m-d');
        $querySanctionFreshRepeatData->whereDate('lms_loan.disbursalDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $querySanctionFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        $querySanctionFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                                     ->whereYear('lms_loan.disbursalDate', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $querySanctionFreshRepeatData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                                     ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
    }
     elseif ($request->filter == 'exportAll') {

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Sanction Officer');
        $sheet->setCellValue('B1', 'Fresh Cases');
        $sheet->setCellValue('C1', 'Fresh Loan Amount');
        $sheet->setCellValue('D1', 'Repeat Cases');
        $sheet->setCellValue('E1', 'Repeat Loan Amount');
        $sheet->setCellValue('F1', 'Grand Total Cases');
        $sheet->setCellValue('G1', 'Grand Total Loan Amount');

        // Initialize total counters
        $totalFreshCases = 0;
        $totalFreshLoanAmount = 0;
        $totalRepeatCases = 0;
        $totalRepeatLoanAmount = 0;

        // Initialize row for data entry (starts at row 2)
        $row = 2;

        // Fetch data in chunks
        $querySanctionFreshRepeatData->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
            foreach ($records as $arr) {
                $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;

                // Accumulate totals
                $totalFreshCases += $arr->freshCases;
                $totalFreshLoanAmount += $arr->freshLoanAmount;
                $totalRepeatCases += $arr->repeatCases;
                $totalRepeatLoanAmount += $arr->repeatLoanAmount;

                // Set row data
                $sheet->setCellValue('A' . $row, getUserNameById('users', 'userID', $arr->userID, 'displayName'));
                $sheet->setCellValue('B' . $row, $arr->freshCases);
                $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                $sheet->setCellValue('D' . $row, $arr->repeatCases);
                $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                $sheet->setCellValue('F' . $row, $grandTotalCases);
                $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));

                // Increment the row for the next entry
                $row++;
            }

            // After chunk processing, set grand totals at the end
            $sheet->setCellValue('A' . $row, 'Grand Total');
            $sheet->setCellValue('B' . $row, $totalFreshCases);
            $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
            $sheet->setCellValue('D' . $row, $totalRepeatCases);
            $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
            $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
            $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));

            // Apply number formats for financial columns
            $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        });

        // Create the file
        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName . '_CM_FreshRepeated_Exported_Leads_Data.xlsx';

        // Log the export action
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('Marketing Analysis', 'CM Fresh Repeated Data Leads (All Export)', $logData);

        // Return response for download
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }elseif ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
    
    $dates = explode(' - ', $request->exportRange);
    $fromDate = date('Y-m-d', strtotime($dates[0]));
    $toDate = date('Y-m-d', strtotime($dates[1]));
    
    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set new headers based on your updated requirements
    $sheet->setCellValue('A1', 'Sanction Officer');
    $sheet->setCellValue('B1', 'Fresh Cases');
    $sheet->setCellValue('C1', 'Fresh Loan Amount');
    $sheet->setCellValue('D1', 'Repeat Cases');
    $sheet->setCellValue('E1', 'Repeat Loan Amount');
    $sheet->setCellValue('F1', 'Grand Total Cases');
    $sheet->setCellValue('G1', 'Grand Total Loan Amount');

    // Initialize total counters
    $totalFreshCases = 0;
    $totalFreshLoanAmount = 0;
    $totalRepeatCases = 0;
    $totalRepeatLoanAmount = 0;
    
    // Start populating data from row 2
    $row = 2;
    
    // Fetch data in chunks
    $querySanctionFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
        foreach ($records as $arr) {
            $grandTotalCases = $arr->freshCases + $arr->repeatCases;
            $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;

            // Accumulate totals
            $totalFreshCases += $arr->freshCases;
            $totalFreshLoanAmount += $arr->freshLoanAmount;
            $totalRepeatCases += $arr->repeatCases;
            $totalRepeatLoanAmount += $arr->repeatLoanAmount;

            // Set row data
            $sheet->setCellValue('A' . $row, getUserNameById('users', 'userID', $arr->userID, 'displayName'));
            $sheet->setCellValue('B' . $row, $arr->freshCases);
            $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
            $sheet->setCellValue('D' . $row, $arr->repeatCases);
            $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
            $sheet->setCellValue('F' . $row, $grandTotalCases);
            $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));

            // Increment the row for the next entry
            $row++;
        }

        // After chunk processing, set grand totals at the end
        $sheet->setCellValue('A' . $row, 'Grand Total');
        $sheet->setCellValue('B' . $row, $totalFreshCases);
        $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
        $sheet->setCellValue('D' . $row, $totalRepeatCases);
        $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
        $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
        $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));

        // Apply number formats for financial columns
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    });
    
    // Create the file
    $writer = new Xlsx($spreadsheet);
    $fileName = cmp()->companyName . '_CM_FreshRepeated_Exported_Leads_Data.xlsx';

    // Log the export action
    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
    actLogs('Marketing Analysis', 'CM Fresh Repeated Data Leads (Export By Date)', $logData);

    // Return response for download
    return response()->stream(
        function () use ($writer) {
            $writer->save('php://output');
        },
        200,
        [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]
    );
}

    else {
        $querySanctionFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                                     ->whereYear('lms_loan.disbursalDate', date('Y'));
    }

    // Retrieve the data for the view
    $sanctionFreshRepeatData = $querySanctionFreshRepeatData->get();

    // Passing data to the view
    $filter = $request->filter;
    $queryParameters = $request->query();
    $page_info = pageInfo('CM Fresh vs Repeated', $request->segment(1));
    $data = compact('sanctionFreshRepeatData', 'page_info','filter','filterShow');
    
    return view('marketing.cmFreshRepeated')->with($data);
}

 
     public function branchFreshRepeated(Request $request)
        {
           
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
            $filterShow = 'Current Month';
          }
         
            $queryBranchFreshRepeatData = DB::table('lms_contact')
                ->leftJoin('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
                ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                ->leftJoin('lms_loan', 'lms_approval.leadID', '=', 'lms_loan.leadID')
                ->leftJoin('lms_cities', 'lms_approval.branch', '=', 'lms_cities.cityID')  // Joining lms_cities to get branch name
                ->leftJoin(DB::raw('(SELECT leadID, 
                                            contactID,
                                            ROW_NUMBER() OVER (PARTITION BY contactID ORDER BY lms_loan.disbursalDate) as rn,
                                            EXTRACT(MONTH FROM lms_loan.disbursalDate) as loanMonth,
                                            EXTRACT(YEAR FROM lms_loan.disbursalDate) as loanYear
                                         FROM lms_loan
                                         WHERE lms_loan.status = "Disbursed"
                                      ) as rankedLeads'), 'lms_leads.leadID', '=', 'rankedLeads.leadID')
                ->select(
                    'lms_cities.cityName',  // Selecting the branch name from lms_cities
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
                ->where('lms_loan.status', 'Disbursed')
                ->groupBy('lms_cities.cityName')  // Group by branchName from lms_cities
                ->orderByDesc(DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END)'));  // Order by fresh loan amount (descending)
        
            // Apply filters based on the request
                if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $queryBranchFreshRepeatData->where(function ($queryBranchFreshRepeatData) use ($search) {
                        $queryBranchFreshRepeatData->where('lms_cities.cityName', 'like', "%{$search}%");
                    });
               } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                // Filter by a custom date range (start and end date)
                $dates = explode(' - ', $request->searchRange);
                $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
                $toDate = date('Y-m-d', strtotime(trim($dates[1])));
                $queryBranchFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
            } elseif ($request->filter == 'sortByToday') {
                // Filter by today
                $today = date('Y-m-d');
                $queryBranchFreshRepeatData->whereDate('lms_loan.disbursalDate', $today);
            } elseif ($request->filter == 'sortByWeek') {
                // Filter by last 7 days (one week)
                $today = date('Y-m-d');
                $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                $queryBranchFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
            } elseif ($request->filter == 'sortByThisMonth') {
                // Filter by this month
                $queryBranchFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
            } elseif ($request->filter == 'sortByLastMonth') {
                            // Filter by the last month
                            $lastMonth = date('m') - 1;
                            $lastMonthYear = date('Y');
                            if ($lastMonth == 0) {
                                $lastMonth = 12;
                                $lastMonthYear = date('Y') - 1;
                            }
                            $queryBranchFreshRepeatData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                                ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
                        }
         elseif ($request->filter == 'exportAll') {
            
                    // Create a new Spreadsheet object
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
            
                    // Set headers
                    $sheet->setCellValue('A1', 'Branches');
                    $sheet->setCellValue('B1', 'Fresh Cases');
                    $sheet->setCellValue('C1', 'Fresh Loan Amount');
                    $sheet->setCellValue('D1', 'Repeat Cases');
                    $sheet->setCellValue('E1', 'Repeat Loan Amount');
                    $sheet->setCellValue('F1', 'Grand Total Cases');
                    $sheet->setCellValue('G1', 'Grand Total Loan Amount');
            
                    // Initialize total counters
                    $totalFreshCases = 0;
                    $totalFreshLoanAmount = 0;
                    $totalRepeatCases = 0;
                    $totalRepeatLoanAmount = 0;
            
                    // Initialize row for data entry (starts at row 2)
                    $row = 2;
            
                    // Fetch data in chunks
                    $queryBranchFreshRepeatData->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
                        foreach ($records as $arr) {
                            $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                            $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;
            
                            // Accumulate totals
                            $totalFreshCases += $arr->freshCases;
                            $totalFreshLoanAmount += $arr->freshLoanAmount;
                            $totalRepeatCases += $arr->repeatCases;
                            $totalRepeatLoanAmount += $arr->repeatLoanAmount;
            
                            // Set row data
                            $sheet->setCellValue('A' . $row, $arr->cityName);
                            $sheet->setCellValue('B' . $row, $arr->freshCases);
                            $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                            $sheet->setCellValue('D' . $row, $arr->repeatCases);
                            $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                            $sheet->setCellValue('F' . $row, $grandTotalCases);
                            $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));
            
                            // Increment the row for the next entry
                            $row++;
                        }
            
                        // After chunk processing, set grand totals at the end
                        $sheet->setCellValue('A' . $row, 'Grand Total');
                        $sheet->setCellValue('B' . $row, $totalFreshCases);
                        $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
                        $sheet->setCellValue('D' . $row, $totalRepeatCases);
                        $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
                        $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
                        $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));
            
                        // Apply number formats for financial columns
                        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    });
            
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName . '_Branch_FreshRepeated_Exported_Leads_Data.xlsx';
            
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('Marketing Analysis', 'Branch Fresh Repeated Leads (All Export)', $logData);
            
                    // Return response for download
                    return response()->stream(
                        function () use ($writer) {
                            $writer->save('php://output');
                        },
                        200,
                        [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                        ]
                    );
                }elseif ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
                
                $dates = explode(' - ', $request->exportRange);
                $fromDate = date('Y-m-d', strtotime($dates[0]));
                $toDate = date('Y-m-d', strtotime($dates[1]));
                
                // Create a new Spreadsheet object
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                
                // Set new headers based on your updated requirements
                $sheet->setCellValue('A1', 'Branches');
                $sheet->setCellValue('B1', 'Fresh Cases');
                $sheet->setCellValue('C1', 'Fresh Loan Amount');
                $sheet->setCellValue('D1', 'Repeat Cases');
                $sheet->setCellValue('E1', 'Repeat Loan Amount');
                $sheet->setCellValue('F1', 'Grand Total Cases');
                $sheet->setCellValue('G1', 'Grand Total Loan Amount');
            
                // Initialize total counters
                $totalFreshCases = 0;
                $totalFreshLoanAmount = 0;
                $totalRepeatCases = 0;
                $totalRepeatLoanAmount = 0;
                
                // Start populating data from row 2
                $row = 2;
                
                // Fetch data in chunks
                $queryBranchFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
                    foreach ($records as $arr) {
                        $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                        $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;
            
                        // Accumulate totals
                        $totalFreshCases += $arr->freshCases;
                        $totalFreshLoanAmount += $arr->freshLoanAmount;
                        $totalRepeatCases += $arr->repeatCases;
                        $totalRepeatLoanAmount += $arr->repeatLoanAmount;
            
                        // Set row data
                        $sheet->setCellValue('A' . $row, $arr->cityName);
                        $sheet->setCellValue('B' . $row, $arr->freshCases);
                        $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                        $sheet->setCellValue('D' . $row, $arr->repeatCases);
                        $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                        $sheet->setCellValue('F' . $row, $grandTotalCases);
                        $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));
            
                        // Increment the row for the next entry
                        $row++;
                    }
            
                    // After chunk processing, set grand totals at the end
                    $sheet->setCellValue('A' . $row, 'Grand Total');
                    $sheet->setCellValue('B' . $row, $totalFreshCases);
                    $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
                    $sheet->setCellValue('D' . $row, $totalRepeatCases);
                    $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
                    $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
                    $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));
            
                    // Apply number formats for financial columns
                    $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                });
                
                // Create the file
                $writer = new Xlsx($spreadsheet);
                $fileName = cmp()->companyName . '_Cash_Pending_Exported_Leads_Data.xlsx';
            
                // Log the export action
                $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                  actLogs('Marketing Analysis', 'Branch Fresh Repeated Leads (Export By Date)', $logData);
            
            
                // Return response for download
                return response()->stream(
                    function () use ($writer) {
                        $writer->save('php://output');
                    },
                    200,
                    [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    ]
                );
            }
                        
            else {
                // Default filter (current month)
                $queryBranchFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
            }
        
            // Execute the query to get the results
            $branchFreshRepeatData = $queryBranchFreshRepeatData->get();
        
            // Passing data to the view
            $filter = $request->filter;
            $queryParameters = $request->query();
            $page_info = pageInfo('CM Fresh vs Repeated', $request->segment(1));
            $data = compact('branchFreshRepeatData', 'page_info','filter','filterShow');
        
            return view('marketing.branchFreshRepeated')->with($data);
        }


    public function pincodeFreshRepeated(Request $request)
        {
         
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
            $filterShow = 'Current Month';
          }
          
            // Building the query for pincode grouping
            $queryPincodeFreshRepeatData = DB::table('lms_contact')
                ->leftJoin('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
                ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                ->leftJoin('lms_loan', 'lms_approval.leadID', '=', 'lms_loan.leadID')
                ->leftJoin('lms_address', 'lms_address.leadID', '=', 'lms_leads.leadID') // Corrected join
                ->leftJoin(DB::raw('(SELECT leadID, 
                                            contactID,
                                            ROW_NUMBER() OVER (PARTITION BY contactID ORDER BY lms_loan.disbursalDate) as rn,
                                            EXTRACT(MONTH FROM lms_loan.disbursalDate) as loanMonth,
                                            EXTRACT(YEAR FROM lms_loan.disbursalDate) as loanYear
                                         FROM lms_loan
                                         WHERE lms_loan.status = "Disbursed"
                                      ) as rankedLeads'), 'lms_leads.leadID', '=', 'rankedLeads.leadID')
                ->select(
                    'lms_address.pincode',  // Selecting pincode from lms_leads table
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
                ->where('lms_loan.status', 'Disbursed')
                // Apply filter before execution
                ->groupBy('lms_address.pincode')  // Group by pincode from lms_leads table
                ->orderByDesc(DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END)'));  // Order by fresh loan amount (descending)
        
            // Apply filters based on the request
               if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $queryPincodeFreshRepeatData->where(function ($queryPincodeFreshRepeatData) use ($search) {
                        $queryPincodeFreshRepeatData->where('lms_address.pincode', 'like', "%{$search}%");
                    });
            } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                // Filter by a custom date range (start and end date)
                $dates = explode(' - ', $request->searchRange);
                $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
                $toDate = date('Y-m-d', strtotime(trim($dates[1])));
                $queryPincodeFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
            } elseif ($request->filter == 'sortByToday') {
                // Filter by today
                $today = date('Y-m-d');
                $queryPincodeFreshRepeatData->whereDate('lms_loan.disbursalDate', $today);
            } elseif ($request->filter == 'sortByWeek') {
                // Filter by last 7 days (one week)
                $today = date('Y-m-d');
                $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                $queryPincodeFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
            } elseif ($request->filter == 'sortByThisMonth') {
                // Filter by this month
                $queryPincodeFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
            } elseif ($request->filter == 'sortByLastMonth') {
                // Filter by the last month
                $lastMonth = date('m') - 1;
                $lastMonthYear = date('Y');
                if ($lastMonth == 0) {
                    $lastMonth = 12;
                    $lastMonthYear = date('Y') - 1;
                }
                $queryPincodeFreshRepeatData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                    ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
            } elseif ($request->filter == 'exportAll') {
            
                    // Create a new Spreadsheet object
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
            
                    // Set headers
                    $sheet->setCellValue('A1', 'Pincode');
                    $sheet->setCellValue('B1', 'Fresh Cases');
                    $sheet->setCellValue('C1', 'Fresh Loan Amount');
                    $sheet->setCellValue('D1', 'Repeat Cases');
                    $sheet->setCellValue('E1', 'Repeat Loan Amount');
                    $sheet->setCellValue('F1', 'Grand Total Cases');
                    $sheet->setCellValue('G1', 'Grand Total Loan Amount');
            
                    // Initialize total counters
                    $totalFreshCases = 0;
                    $totalFreshLoanAmount = 0;
                    $totalRepeatCases = 0;
                    $totalRepeatLoanAmount = 0;
            
                    // Initialize row for data entry (starts at row 2)
                    $row = 2;
            
                    // Fetch data in chunks
                    $queryPincodeFreshRepeatData->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
                        foreach ($records as $arr) {
                            $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                            $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;
            
                            // Accumulate totals
                            $totalFreshCases += $arr->freshCases;
                            $totalFreshLoanAmount += $arr->freshLoanAmount;
                            $totalRepeatCases += $arr->repeatCases;
                            $totalRepeatLoanAmount += $arr->repeatLoanAmount;
            
                            // Set row data
                            $sheet->setCellValue('A' . $row, $arr->pincode);
                            $sheet->setCellValue('B' . $row, $arr->freshCases);
                            $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                            $sheet->setCellValue('D' . $row, $arr->repeatCases);
                            $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                            $sheet->setCellValue('F' . $row, $grandTotalCases);
                            $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));
            
                            // Increment the row for the next entry
                            $row++;
                        }
            
                        // After chunk processing, set grand totals at the end
                        $sheet->setCellValue('A' . $row, 'Grand Total');
                        $sheet->setCellValue('B' . $row, $totalFreshCases);
                        $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
                        $sheet->setCellValue('D' . $row, $totalRepeatCases);
                        $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
                        $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
                        $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));
            
                        // Apply number formats for financial columns
                        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                        $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    });
            
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName . '_Pincode_FreshRepeated_Exported_Leads_Data.xlsx';
            
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Pincode Fresh Repeated Exported Leads (All Export)', $logData);
            
                    // Return response for download
                    return response()->stream(
                        function () use ($writer) {
                            $writer->save('php://output');
                        },
                        200,
                        [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                        ]
                    );
                }elseif ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
                
                $dates = explode(' - ', $request->exportRange);
                $fromDate = date('Y-m-d', strtotime($dates[0]));
                $toDate = date('Y-m-d', strtotime($dates[1]));
                
                // Create a new Spreadsheet object
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                
                // Set new headers based on your updated requirements
                $sheet->setCellValue('A1', 'Pincode');
                $sheet->setCellValue('B1', 'Fresh Cases');
                $sheet->setCellValue('C1', 'Fresh Loan Amount');
                $sheet->setCellValue('D1', 'Repeat Cases');
                $sheet->setCellValue('E1', 'Repeat Loan Amount');
                $sheet->setCellValue('F1', 'Grand Total Cases');
                $sheet->setCellValue('G1', 'Grand Total Loan Amount');
            
                // Initialize total counters
                $totalFreshCases = 0;
                $totalFreshLoanAmount = 0;
                $totalRepeatCases = 0;
                $totalRepeatLoanAmount = 0;
                
                // Start populating data from row 2
                $row = 2;
                
                // Fetch data in chunks
                $queryPincodeFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
                    foreach ($records as $arr) {
                        $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                        $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;
            
                        // Accumulate totals
                        $totalFreshCases += $arr->freshCases;
                        $totalFreshLoanAmount += $arr->freshLoanAmount;
                        $totalRepeatCases += $arr->repeatCases;
                        $totalRepeatLoanAmount += $arr->repeatLoanAmount;
            
                        // Set row data
                        $sheet->setCellValue('A' . $row, $arr->pincode);
                        $sheet->setCellValue('B' . $row, $arr->freshCases);
                        $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                        $sheet->setCellValue('D' . $row, $arr->repeatCases);
                        $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                        $sheet->setCellValue('F' . $row, $grandTotalCases);
                        $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));
            
                        // Increment the row for the next entry
                        $row++;
                    }
            
                    // After chunk processing, set grand totals at the end
                    $sheet->setCellValue('A' . $row, 'Grand Total');
                    $sheet->setCellValue('B' . $row, $totalFreshCases);
                    $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
                    $sheet->setCellValue('D' . $row, $totalRepeatCases);
                    $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
                    $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
                    $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));
            
                    // Apply number formats for financial columns
                    $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                });
                
                // Create the file
                $writer = new Xlsx($spreadsheet);
                $fileName = cmp()->companyName . '_Pincode_FreshRepeated_Leads_Data.xlsx';
            
                // Log the export action
                $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                actLogs('leads', 'Pincode Fresh Repeated Exported Leads (Export By Date)', $logData);
            
                // Return response for download
                return response()->stream(
                    function () use ($writer) {
                        $writer->save('php://output');
                    },
                    200,
                    [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    ]
                );
            } else {
                // Default filter (current month)
                $queryPincodeFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                    ->whereYear('lms_loan.disbursalDate', date('Y'));
            }
        
            // Execute the query to get the results
            $pincodeFreshRepeatData = $queryPincodeFreshRepeatData->paginate(10);
        
            // Passing data to the view
            $filter = $request->filter;
            $queryParameters = $request->query();
            $page_info = pageInfo('CM Fresh vs Repeated', $request->segment(1));
            $data = compact('pincodeFreshRepeatData', 'page_info','filter','queryParameters','filterShow');
            
          
            return view('marketing.pincodeFreshRepeated')->with($data);
        }

     
public function employmentWise(Request $request)
{
     
         
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
            $filterShow = 'Current Month';
          }  
    // Building the query for employment-wise data
    $queryEmploymentWiseData = DB::table('lms_contact')
        ->leftJoin('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
        ->leftJoin('lms_approval', 'lms_leads.contactID', '=', 'lms_approval.contactID')
        ->leftJoin('lms_loan', 'lms_approval.contactID', '=', 'lms_loan.contactID')
        ->where('lms_approval.employed', '!=', '')
        ->where('lms_loan.status', 'Disbursed')
        ->groupBy('lms_approval.employed')
        ->select(
            'lms_approval.employed',
            DB::raw('COUNT(lms_approval.contactID) as total_count')
        );

    // Apply filters based on the request
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $queryEmploymentWiseData->where(function ($queryEmploymentWiseData) use ($search) {
            $queryEmploymentWiseData->where('lms_approval.employed', 'like', "%{$search}%");
        });
    } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryEmploymentWiseData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        $today = date('Y-m-d');
        $queryEmploymentWiseData->whereDate('lms_loan.disbursalDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $queryEmploymentWiseData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        $queryEmploymentWiseData->whereMonth('lms_loan.disbursalDate', date('m'))
            ->whereYear('lms_loan.disbursalDate', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $queryEmploymentWiseData->whereMonth('lms_loan.disbursalDate', $lastMonth)
            ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
    } else {
        $queryEmploymentWiseData->whereMonth('lms_loan.disbursalDate', date('m'))
            ->whereYear('lms_loan.disbursalDate', date('Y'));
    }

    // Get the data (this will return a collection)
    $employmentWiseData = $queryEmploymentWiseData->get();

    // Get total count of all records (sum of total_count for all employment categories)
    $totalCount = $employmentWiseData->sum('total_count');

    // Calculate percentage for each employment category
    $employmentWiseData->transform(function ($data) use ($totalCount) {
        if ($totalCount > 0) {
            $data->percentage = round(($data->total_count / $totalCount) * 100, 2);
        } else {
            $data->percentage = 0;
        }
        return $data;
    });

    // Export logic: exportAll
    if ($request->filter == 'exportAll') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Status');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($employmentWiseData as $data) {
            $sheet->setCellValue('A' . $row, $data->employed);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Employment_Wise_Leads_Export_All.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Employment_Wise_Exported Leads', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Export logic: exportByDate (same as exportAll but with date filtering)
    if ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
        $dates = explode(' - ', $request->exportRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryEmploymentWiseData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);

        // Get the filtered data for export
        $employmentWiseDataForExport = $queryEmploymentWiseData->get();
        $totalCountForExport = $employmentWiseDataForExport->sum('total_count');

        // Calculate percentage for the filtered export data
        $employmentWiseDataForExport->transform(function ($data) use ($totalCountForExport) {
            if ($totalCountForExport > 0) {
                $data->percentage = round(($data->total_count / $totalCountForExport) * 100, 2);
            } else {
                $data->percentage = 0;
            }
            return $data;
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Status');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($employmentWiseDataForExport as $data) {
            $sheet->setCellValue('A' . $row, $data->employed);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Employment_Wise_Leads_Export_By_Date.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Employment_Wise_Exported Leads By Date', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // If no export, return the view with the data
    $filter = $request->filter;
    $queryParameters = $request->query();
    $page_info = pageInfo('CM Fresh vs Repeated', $request->segment(1));
    $data = compact('employmentWiseData', 'page_info', 'filter', 'queryParameters', 'totalCount','filterShow');

    return view('marketing.employmentWiseData')->with($data);
}


 public function salaryWise(Request $request)
{
     
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
            $filterShow = 'Current Month';
          }
    // Building the query for monthly income-wise data
    $queryIncomeWiseData = DB::table('lms_approval')
        ->leftJoin('lms_contact', 'lms_approval.contactID', '=', 'lms_contact.contactID')
        ->select(
            DB::raw("CASE 
                        WHEN monthlyIncome BETWEEN 0 AND 30000 THEN '0 to 30k'
                        WHEN monthlyIncome BETWEEN 30001 AND 40000 THEN '>30k to 40k'
                        WHEN monthlyIncome BETWEEN 40001 AND 50000 THEN '>40k to 50k'
                        WHEN monthlyIncome BETWEEN 50001 AND 60000 THEN '>50k to 60k'
                        WHEN monthlyIncome BETWEEN 60001 AND 70000 THEN '>60k to 70k'
                        WHEN monthlyIncome BETWEEN 70001 AND 80000 THEN '>70k to 80k'
                        WHEN monthlyIncome BETWEEN 80001 AND 90000 THEN '>80k to 90k'
                        WHEN monthlyIncome BETWEEN 90001 AND 100000 THEN '>90k to 1lakh'
                        WHEN monthlyIncome > 100000 THEN '>1lakh'
                        ELSE 'Not Defined' 
                    END as income_range"),
            DB::raw('COUNT(lms_approval.contactID) as total_count')
        )
        ->groupBy('income_range')
        ->orderBy('income_range'); // Optional, to ensure consistent ordering

    // Apply filters based on the request
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $queryIncomeWiseData->where(function ($queryIncomeWiseData) use ($search) {
            $queryIncomeWiseData->where('lms_approval.monthlyIncome', 'like', "%{$search}%");
        });
    } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        // Filter by a custom date range (start and end date)
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryIncomeWiseData->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        // Filter by today
        $today = date('Y-m-d');
        $queryIncomeWiseData->whereDate('lms_approval.createdDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        // Filter by last 7 days (one week)
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $queryIncomeWiseData->whereBetween('lms_approval.createdDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        // Filter by this month
        $queryIncomeWiseData->whereMonth('lms_approval.createdDate', date('m'))
            ->whereYear('lms_approval.createdDate', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        // Filter by the last month
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $queryIncomeWiseData->whereMonth('lms_approval.createdDate', $lastMonth)
            ->whereYear('lms_approval.createdDate', $lastMonthYear);
    } else {
        // Default filter (current month)
        $queryIncomeWiseData->whereMonth('lms_approval.createdDate', date('m'))
            ->whereYear('lms_approval.createdDate', date('Y'));
    }

    // Get the data (this will return a collection)
    $incomeWiseData = $queryIncomeWiseData->get();

    // Get total count of all records (sum of total_count for all income categories)
    $totalCount = $incomeWiseData->sum('total_count');

    // Calculate percentage for each income category
    $incomeWiseData->transform(function ($data) use ($totalCount) {
        if ($totalCount > 0) {
            // Calculate the percentage for each category
            $data->percentage = round(($data->total_count / $totalCount) * 100, 2);
        } else {
            // If the total count is zero, the percentage is set to 0
            $data->percentage = 0;
        }
        return $data;
    });

    // Export logic: exportAll
    if ($request->filter == 'exportAll') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Income Range');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($incomeWiseData as $data) {
            $sheet->setCellValue('A' . $row, $data->income_range);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Income_Wise_Leads_Export_All.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Income_Wise_Exported Leads', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Export logic: exportByDate (same as exportAll but with date filtering)
    if ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
        $dates = explode(' - ', $request->exportRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryIncomeWiseData->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);

        // Get the filtered data for export
        $incomeWiseDataForExport = $queryIncomeWiseData->get();
        $totalCountForExport = $incomeWiseDataForExport->sum('total_count');

        // Calculate percentage for the filtered export data
        $incomeWiseDataForExport->transform(function ($data) use ($totalCountForExport) {
            if ($totalCountForExport > 0) {
                $data->percentage = round(($data->total_count / $totalCountForExport) * 100, 2);
            } else {
                $data->percentage = 0;
            }
            return $data;
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Income Range');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($incomeWiseDataForExport as $data) {
            $sheet->setCellValue('A' . $row, $data->income_range);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Income_Wise_Leads_Export_By_Date.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Income_Wise_Exported Leads By Date', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Passing data to the view
    $filter = $request->filter;
    $queryParameters = $request->query();
    $page_info = pageInfo('Monthly Income Wise Leads', $request->segment(1));
    $data = compact('incomeWiseData', 'page_info', 'filter', 'queryParameters', 'totalCount','filterShow');

    return view('marketing.monthlyIncomeWiseData')->with($data);
}


public function leadStatusWise(Request $request) {
    
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
            $filterShow = 'Current Month';
          }
          
    // Building the query for lead status-wise data
    $queryLeadStatusWiseData = DB::table('lms_leads')
        ->groupBy('lms_leads.status')
        ->select(
            'lms_leads.status',
            DB::raw('COUNT(lms_leads.leadID) as total_count')
        );

    // Apply filters based on the request
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $queryLeadStatusWiseData->where(function ($queryLeadStatusWiseData) use ($search) {
            $queryLeadStatusWiseData->where('lms_leads.status', 'like', "%{$search}%");
        });
    } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        // Filter by a custom date range (start and end date)
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryLeadStatusWiseData->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        // Filter by today
        $today = date('Y-m-d');
        $queryLeadStatusWiseData->whereDate('lms_leads.commingLeadsDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        // Filter by last 7 days (one week)
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $queryLeadStatusWiseData->whereBetween('lms_leads.commingLeadsDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        // Filter by this month
        $queryLeadStatusWiseData->whereMonth('lms_leads.commingLeadsDate', date('m'))
            ->whereYear('lms_leads.commingLeadsDate', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        // Filter by the last month
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $queryLeadStatusWiseData->whereMonth('lms_leads.commingLeadsDate', $lastMonth)
            ->whereYear('lms_leads.commingLeadsDate', $lastMonthYear);
    } else {
        // Default filter (current month)
        $queryLeadStatusWiseData->whereMonth('lms_leads.commingLeadsDate', date('m'))
            ->whereYear('lms_leads.commingLeadsDate', date('Y'));
    }

    // Get the data (this will return a collection)
    $leadStatusWiseData = $queryLeadStatusWiseData->get();

    // Get total count of all records (sum of total_count for all lead status categories)
    $totalCount = $leadStatusWiseData->sum('total_count');

    // Calculate percentage for each lead status category
    $leadStatusWiseData->transform(function ($data) use ($totalCount) {
        if ($totalCount > 0) {
            // Calculate the percentage for each category
            $data->percentage = round(($data->total_count / $totalCount) * 100, 2);
        } else {
            // If the total count is zero, the percentage is set to 0
            $data->percentage = 0;
        }
        return $data;
    });

    // Export logic: exportAll
    if ($request->filter == 'exportAll') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Lead Status');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($leadStatusWiseData as $data) {
            $sheet->setCellValue('A' . $row, $data->status);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Lead_Status_Wise_Leads_Export_All.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Lead_Status_Wise_Exported Leads', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Export logic: exportByDate (same as exportAll but with date filtering)
    if ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
        $dates = explode(' - ', $request->exportRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryLeadStatusWiseData->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);

        // Get the filtered data for export
        $leadStatusWiseDataForExport = $queryLeadStatusWiseData->get();
        $totalCountForExport = $leadStatusWiseDataForExport->sum('total_count');

        // Calculate percentage for the filtered export data
        $leadStatusWiseDataForExport->transform(function ($data) use ($totalCountForExport) {
            if ($totalCountForExport > 0) {
                $data->percentage = round(($data->total_count / $totalCountForExport) * 100, 2);
            } else {
                $data->percentage = 0;
            }
            return $data;
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Lead Status');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($leadStatusWiseDataForExport as $data) {
            $sheet->setCellValue('A' . $row, $data->status);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Lead_Status_Wise_Leads_Export_By_Date.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Lead_Status_Wise_Exported Leads By Date', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Passing data to the view
    $filter = $request->filter;
    $queryParameters = $request->query();
    $page_info = pageInfo('Lead Status Wise Leads', $request->segment(1));
    $data = compact('leadStatusWiseData', 'page_info', 'filter', 'queryParameters', 'totalCount','filterShow');

    return view('marketing.leadStatusWiseData')->with($data);
}

   public function utmSourceWise(Request $request) {
   
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
            $filterShow = 'Current Month';
          }
          
          
    // Building the query for lead source-wise data
    $queryUtmSourceWiseData = DB::table('lms_leads')
        ->groupBy('lms_leads.utmSource')
        ->select(
            'lms_leads.utmSource',
            DB::raw('COUNT(lms_leads.leadID) as total_count')
        );

    // Apply filters based on the request
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $queryUtmSourceWiseData->where(function ($queryUtmSourceWiseData) use ($search) {
            $queryUtmSourceWiseData->where('lms_leads.utmSource', 'like', "%{$search}%");
        });
    } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        // Filter by a custom date range (start and end date)
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryUtmSourceWiseData->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        // Filter by today
        $today = date('Y-m-d');
        $queryUtmSourceWiseData->whereDate('lms_leads.commingLeadsDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        // Filter by last 7 days (one week)
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $queryUtmSourceWiseData->whereBetween('lms_leads.commingLeadsDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        // Filter by this month
        $queryUtmSourceWiseData->whereMonth('lms_leads.commingLeadsDate', date('m'))
            ->whereYear('lms_leads.commingLeadsDate', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        // Filter by the last month
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $queryUtmSourceWiseData->whereMonth('lms_leads.commingLeadsDate', $lastMonth)
            ->whereYear('lms_leads.commingLeadsDate', $lastMonthYear);
    } else {
        // Default filter (current month)
        $queryUtmSourceWiseData->whereMonth('lms_leads.commingLeadsDate', date('m'))
            ->whereYear('lms_leads.commingLeadsDate', date('Y'));
    }

    // Get the data (this will return a collection)
    $utmSourceWiseData = $queryUtmSourceWiseData->get();

    // Get total count of all records (sum of total_count for all lead source categories)
    $totalCount = $utmSourceWiseData->sum('total_count');

    // Calculate percentage for each lead source category
    $utmSourceWiseData->transform(function ($data) use ($totalCount) {
        if ($totalCount > 0) {
            // Calculate the percentage for each category
            $data->percentage = round(($data->total_count / $totalCount) * 100, 2);
        } else {
            // If the total count is zero, the percentage is set to 0
            $data->percentage = 0;
        }
        return $data;
    });

    // Export logic: exportAll
    if ($request->filter == 'exportAll') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'UTM Source');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($utmSourceWiseData as $data) {
            $sheet->setCellValue('A' . $row, $data->utmSource);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Lead_Source_Wise_Leads_Export_All.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Lead_Source_Wise_Exported Leads', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Export logic: exportByDate (same as exportAll but with date filtering)
    if ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
        $dates = explode(' - ', $request->exportRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryUtmSourceWiseData->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);

        // Get the filtered data for export
        $utmSourceWiseDataForExport = $queryUtmSourceWiseData->get();
        $totalCountForExport = $utmSourceWiseDataForExport->sum('total_count');

        // Calculate percentage for the filtered export data
        $utmSourceWiseDataForExport->transform(function ($data) use ($totalCountForExport) {
            if ($totalCountForExport > 0) {
                $data->percentage = round(($data->total_count / $totalCountForExport) * 100, 2);
            } else {
                $data->percentage = 0;
            }
            return $data;
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for the export file
        $sheet->setCellValue('A1', 'Lead Source');
        $sheet->setCellValue('B1', 'Total Leads');
        $sheet->setCellValue('C1', 'Percentage');

        // Populate the data in the sheet
        $row = 2;
        foreach ($utmSourceWiseDataForExport as $data) {
            $sheet->setCellValue('A' . $row, $data->utmSource);
            $sheet->setCellValue('B' . $row, $data->total_count);
            $sheet->setCellValue('C' . $row, $data->percentage / 100);  // Divide by 100 for correct Excel percentage format
            $row++;
        }

        // Apply percentage format to column C
        $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName .'Lead_Source_Wise_Leads_Export_By_Date.xlsx';

        // Log export action (optional)
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads', 'Lead_Source_Wise_Exported Leads By Date', $logData);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    // Passing data to the view
    $filter = $request->filter;
    $queryParameters = $request->query();
    $page_info = pageInfo('CM Fresh vs Repeated', $request->segment(1));
    $data = compact('utmSourceWiseData', 'page_info', 'filter', 'queryParameters', 'totalCount','filterShow');

    return view('marketing.utmSourceWiseData')->with($data);
}

            

public function utmFreshRepeatedWise(Request $request) {
    $loanType = $request->loanType ?? 'Payday';
    
    if($request->filter == 'sortByDate') {
        $filterShow = 'Custom Range';
    } elseif($request->filter == 'sortByToday') {
        $filterShow = 'Today';
    } elseif($request->filter == 'sortByWeek') {
        $filterShow = 'Week';
    } elseif($request->filter == 'sortByThisMonth') {
        $filterShow = 'This Month';
    } elseif($request->filter == 'sortByLastMonth') {
        $filterShow = 'Last Month';
    } else {
        $filterShow = 'Current Month';
    }

    // Build the base query for data
    $queryUtmFreshRepeatData = DB::table('lms_contact')
        ->leftJoin('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
        ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
        ->leftJoin('lms_loan', 'lms_approval.leadID', '=', 'lms_loan.leadID')
        ->leftJoin(DB::raw('(SELECT leadID, 
                                contactID,
                                ROW_NUMBER() OVER (PARTITION BY contactID ORDER BY lms_loan.disbursalDate) as rn,
                                EXTRACT(MONTH FROM lms_loan.disbursalDate) as loanMonth,
                                EXTRACT(YEAR FROM lms_loan.disbursalDate) as loanYear
                             FROM lms_loan
                             WHERE lms_loan.status = "Disbursed"
                          ) as rankedLeads'), 'lms_leads.leadID', '=', 'rankedLeads.leadID')
        ->select(
            'lms_leads.utmSource',
            DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN 1 ELSE 0 END) as freshCases'),
            DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END) as freshLoanAmount'),
            DB::raw('SUM(CASE WHEN rankedLeads.rn > 1 THEN 1 ELSE 0 END) as repeatCases'),
            DB::raw('SUM(CASE WHEN rankedLeads.rn > 1 THEN lms_approval.loanAmtApproved ELSE 0 END) as repeatLoanAmount')
        )
        ->groupBy('lms_leads.utmSource')
        ->orderByDesc(DB::raw('SUM(CASE WHEN rankedLeads.rn = 1 THEN lms_approval.loanAmtApproved ELSE 0 END)'));

    // Apply filters based on the request
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $queryUtmFreshRepeatData->where(function ($query) use ($search) {
            $query->where('lms_leads.utmSource', 'like', "%{$search}%");
        });
    } elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime(trim($dates[0])));
        $toDate = date('Y-m-d', strtotime(trim($dates[1])));
        $queryUtmFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        $today = date('Y-m-d');
        $queryUtmFreshRepeatData->whereDate('lms_loan.disbursalDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $queryUtmFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        $queryUtmFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                                 ->whereYear('lms_loan.disbursalDate', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $queryUtmFreshRepeatData->whereMonth('lms_loan.disbursalDate', $lastMonth)
                                 ->whereYear('lms_loan.disbursalDate', $lastMonthYear);
    } elseif ($request->filter == 'exportAll') {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Sanction Officer');
        $sheet->setCellValue('B1', 'Fresh Cases');
        $sheet->setCellValue('C1', 'Fresh Loan Amount');
        $sheet->setCellValue('D1', 'Repeat Cases');
        $sheet->setCellValue('E1', 'Repeat Loan Amount');
        $sheet->setCellValue('F1', 'Grand Total Cases');
        $sheet->setCellValue('G1', 'Grand Total Loan Amount');

        // Initialize total counters
        $totalFreshCases = 0;
        $totalFreshLoanAmount = 0;
        $totalRepeatCases = 0;
        $totalRepeatLoanAmount = 0;

        // Initialize row for data entry (starts at row 2)
        $row = 2;

        // Fetch data in chunks
        $queryUtmFreshRepeatData->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
            foreach ($records as $arr) {
                $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;

                // Accumulate totals
                $totalFreshCases += $arr->freshCases;
                $totalFreshLoanAmount += $arr->freshLoanAmount;
                $totalRepeatCases += $arr->repeatCases;
                $totalRepeatLoanAmount += $arr->repeatLoanAmount;

                // Set row data
                $sheet->setCellValue('A' . $row, getUserNameById('users', 'userID', $arr->userID, 'displayName'));
                $sheet->setCellValue('B' . $row, $arr->freshCases);
                $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                $sheet->setCellValue('D' . $row, $arr->repeatCases);
                $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                $sheet->setCellValue('F' . $row, $grandTotalCases);
                $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));

                // Increment the row for the next entry
                $row++;
            }

            // After chunk processing, set grand totals at the end
            $sheet->setCellValue('A' . $row, 'Grand Total');
            $sheet->setCellValue('B' . $row, $totalFreshCases);
            $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
            $sheet->setCellValue('D' . $row, $totalRepeatCases);
            $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
            $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
            $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));

            // Apply number formats for financial columns
            $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        });

        // Create the file
        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName . '_CM_FreshRepeated_Exported_Leads_Data.xlsx';

        // Log the export action
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('Marketing Analysis', 'CM Fresh Repeated Data Leads (All Export)', $logData);

        // Return response for download
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    } elseif ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
        $dates = explode(' - ', $request->exportRange);
        $fromDate = date('Y-m-d', strtotime($dates[0]));
        $toDate = date('Y-m-d', strtotime($dates[1]));
        
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set new headers based on your updated requirements
        $sheet->setCellValue('A1', 'Sanction Officer');
        $sheet->setCellValue('B1', 'Fresh Cases');
        $sheet->setCellValue('C1', 'Fresh Loan Amount');
        $sheet->setCellValue('D1', 'Repeat Cases');
        $sheet->setCellValue('E1', 'Repeat Loan Amount');
        $sheet->setCellValue('F1', 'Grand Total Cases');
        $sheet->setCellValue('G1', 'Grand Total Loan Amount');

        // Initialize total counters
        $totalFreshCases = 0;
        $totalFreshLoanAmount = 0;
        $totalRepeatCases = 0;
        $totalRepeatLoanAmount = 0;
        
        // Start populating data from row 2
        $row = 2;
        
        // Fetch data in chunks
        $queryUtmFreshRepeatData->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row, &$totalFreshCases, &$totalFreshLoanAmount, &$totalRepeatCases, &$totalRepeatLoanAmount) {
            foreach ($records as $arr) {
                $grandTotalCases = $arr->freshCases + $arr->repeatCases;
                $grandTotalLoanAmount = $arr->freshLoanAmount + $arr->repeatLoanAmount;

                // Accumulate totals
                $totalFreshCases += $arr->freshCases;
                $totalFreshLoanAmount += $arr->freshLoanAmount;
                $totalRepeatCases += $arr->repeatCases;
                $totalRepeatLoanAmount += $arr->repeatLoanAmount;

                // Set row data
                $sheet->setCellValue('A' . $row, getUserNameById('users', 'userID', $arr->userID, 'displayName'));
                $sheet->setCellValue('B' . $row, $arr->freshCases);
                $sheet->setCellValue('C' . $row, nf($arr->freshLoanAmount));
                $sheet->setCellValue('D' . $row, $arr->repeatCases);
                $sheet->setCellValue('E' . $row, nf($arr->repeatLoanAmount));
                $sheet->setCellValue('F' . $row, $grandTotalCases);
                $sheet->setCellValue('G' . $row, nf($grandTotalLoanAmount));

                // Increment the row for the next entry
                $row++;
            }

            // After chunk processing, set grand totals at the end
            $sheet->setCellValue('A' . $row, 'Grand Total');
            $sheet->setCellValue('B' . $row, $totalFreshCases);
            $sheet->setCellValue('C' . $row, nf($totalFreshLoanAmount));
            $sheet->setCellValue('D' . $row, $totalRepeatCases);
            $sheet->setCellValue('E' . $row, nf($totalRepeatLoanAmount));
            $sheet->setCellValue('F' . $row, $totalFreshCases + $totalRepeatCases);
            $sheet->setCellValue('G' . $row, nf($totalFreshLoanAmount + $totalRepeatLoanAmount));

            // Apply number formats for financial columns
            $sheet->getStyle('C2:C' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('E2:E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('G2:G' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        });
        
        // Create the file
        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName . '_CM_FreshRepeated_Exported_Leads_Data.xlsx';

        // Log the export action
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('Marketing Analysis', 'CM Fresh Repeated Data Leads (Export By Date)', $logData);

        // Return response for download
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    else {
        $queryUtmFreshRepeatData->whereMonth('lms_loan.disbursalDate', date('m'))
                                 ->whereYear('lms_loan.disbursalDate', date('Y'));
    }

    // Retrieve the data for the view
    $utmFreshRepeatData = $queryUtmFreshRepeatData->get();
 
    // Passing data to the view
    $filter = $request->filter;
    $searchRange = $request->searchRange;
    $queryParameters = $request->query();
    $page_info = pageInfo('UTM Fresh vs Repeated', $request->segment(1));
    $data = compact('utmFreshRepeatData', 'page_info', 'loanType', 'searchRange', 'filter','filterShow');
    
    return view('marketing.utmFreshRepeated')->with($data);
}
  
    

}
