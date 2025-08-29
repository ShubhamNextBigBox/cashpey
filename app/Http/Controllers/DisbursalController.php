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

class DisbursalController extends Controller
{
   public function disbursalSheetSendLeads(Request $request){
        $query = DB::table('lms_leads')
                        ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                        ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                        ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                        ->leftJoin('users', 'lms_loan.addedBy', '=', 'users.userID') // Left Join with users for cmID
                        ->where(['lms_loan.status'=>'Pending For Disburse'])
                        ->select('lms_leads.leadID','lms_leads.contactID','lms_leads.status', 'lms_contact.name','lms_contact.mobile','lms_contact.email','lms_contact.pancard','lms_approval.branch','lms_approval.roi','lms_approval.loanAmtApproved','lms_approval.adminFee','lms_approval.monthlyIncome','lms_approval.cibil','lms_loan.accountNo','lms_loan.disbursalAmount','lms_loan.ifscCode','lms_loan.loanNo','lms_loan.addedBy','lms_loan.addedOn')
                        ->orderBy('lms_loan.id','DESC');

         if ($request->filter == 'sortBySearch' && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('lms_contact.name', 'like', "%{$search}%")
                      ->orWhere('lms_contact.email', 'like', "%{$search}%")
                      ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                      ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                      ->orWhere('users.displayName', 'like', "%{$search}%") // Search in rm_users displayName
                      ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
            });
        }

        // Apply date range filter based on sortByDate filter and searchRange
        if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
            $dates = explode(' - ', $request->searchRange);
            $fromDate = date('Y-m-d', strtotime($dates[0]));
            $toDate = date('Y-m-d', strtotime($dates[1]));
            $query->whereBetween('lms_loan.commingLeadsDate', [$fromDate, $toDate]);
        } elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $query->whereDate('lms_leads.commingLeadsDate', $today);
        } elseif ($request->filter == 'sortByWeek') {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $query->whereBetween('lms_leads.commingLeadsDate', [$sevenDaysAgo, $today]);
        } elseif ($request->filter == 'sortByThisMonth') {
            $query->whereMonth('lms_leads.commingLeadsDate', '=', date('m'))
                  ->whereYear('lms_leads.commingLeadsDate', '=', date('Y'));
        } elseif ($request->filter == 'sortByLastMonth') {
            $lastMonth = date('m') - 1;
            $lastMonthYear = date('Y');
            if ($lastMonth == 0) {
                $lastMonth = 12;
                $lastMonthYear = date('Y') - 1;
            }
            $query->whereMonth('lms_leads.commingLeadsDate', '=', $lastMonth)
                  ->whereYear('lms_leads.commingLeadsDate', '=', $lastMonthYear);
        }elseif ($request->filter == 'exportAll') {
                // Create a new Spreadsheet object
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                
                // Set the headers for the spreadsheet
                $sheet->setCellValue('A1', 'Lead ID');
                $sheet->setCellValue('B1', 'Sanction By');
                $sheet->setCellValue('C1', 'Branch');
                $sheet->setCellValue('D1', 'Name');
                $sheet->setCellValue('E1', 'Email');
                $sheet->setCellValue('F1', 'Mobile');
                $sheet->setCellValue('G1', 'Pancard');
                $sheet->setCellValue('H1', 'Account No.');
                $sheet->setCellValue('I1', 'Loan Amount');
                $sheet->setCellValue('J1', 'Disbursed Amount');
                $sheet->setCellValue('K1', 'IFSC Code');
                $sheet->setCellValue('L1', 'Loan No.');
                $sheet->setCellValue('M1', 'ROI');
                $sheet->setCellValue('N1', 'Admin Fee');
                $sheet->setCellValue('O1', 'Monthly Income');
                $sheet->setCellValue('P1', 'Cibil');
                $sheet->setCellValue('Q1', 'Status');
                $sheet->setCellValue('R1', 'Date');
                
                $row = 2; // Start from row 2 to leave space for headers
                
                // Chunk the query in batches to avoid memory overload
                $query->chunk(1000, function ($records) use ($sheet, &$row) {
                    foreach ($records as $record) {
                        // Set values for each column in the current row
                        $sheet->setCellValue('A' . $row, $record->leadID);  // Lead ID
                        $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->addedBy, 'displayName'));  // Sanction By
                        $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));  // Branch
                        $sheet->setCellValue('D' . $row, ucwords($record->name));  // Name (capitalize first letter)
                        $sheet->setCellValue('E' . $row, $record->email);  // Email
                        $sheet->setCellValue('F' . $row, $record->mobile);  // Mobile
                        $sheet->setCellValue('G' . $row, $record->pancard);  // Pancard
                        $sheet->setCellValue('H' . $row, $record->accountNo);  // Account No.
                        $sheet->setCellValue('I' . $row, nf($record->loanAmtApproved));  // Loan Amount (formatted)
                        $sheet->setCellValue('J' . $row, nf($record->disbursalAmount));  // Disbursed Amount (formatted)
                        $sheet->setCellValue('K' . $row, $record->ifscCode);  // IFSC Code
                        $sheet->setCellValue('L' . $row, $record->loanNo);  // Loan No.
                        $sheet->setCellValue('M' . $row, $record->roi . ' %');  // ROI (with '%')
                        $sheet->setCellValue('N' . $row, nf($record->adminFee));  // Admin Fee (formatted)
                        $sheet->setCellValue('O' . $row, nf($record->monthlyIncome));  // Monthly Income (formatted)
                        $sheet->setCellValue('P' . $row, $record->cibil);  // Cibil
                        $sheet->setCellValue('Q' . $row, $record->status);  // Status
                        
                        $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                        $excelDate = Date::PHPToExcel($formattedDate);
                        $sheet->setCellValue('R' . $row, $excelDate);  
                        $sheet->getStyle('R' . $row)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                        // Move to the next row for the next record
                        $row++;
                    }
                });
                
                // Create writer and file download logic
                $writer = new Xlsx($spreadsheet);
                $fileName = cmp()->companyName.'_'.'Disbursal_Sheet_Send_Exported_Leads_Data.xlsx';
                
                // Log export action
                $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                actLogs('leads', 'Disbursal Sheet Send Leads Exported (All Export)', $logData);
                
                // Output the file as a download
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
                    
                    // Set the headers for the spreadsheet
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Sanction By');
                    $sheet->setCellValue('C1', 'Branch');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Account No.');
                    $sheet->setCellValue('I1', 'Loan Amount');
                    $sheet->setCellValue('J1', 'Disbursed Amount');
                    $sheet->setCellValue('K1', 'IFSC Code');
                    $sheet->setCellValue('L1', 'Loan No.');
                    $sheet->setCellValue('M1', 'ROI');
                    $sheet->setCellValue('N1', 'Admin Fee');
                    $sheet->setCellValue('O1', 'Monthly Income');
                    $sheet->setCellValue('P1', 'Cibil');
                    $sheet->setCellValue('Q1', 'Status');
                    $sheet->setCellValue('R1', 'Date');
                
                    
                    $row = 2; // Start from row 2 to leave space for headers
                    
                    // Chunk the query to avoid memory overload
                    $query->chunk(1000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            // Ensure you use the correct variable name ($record) and not $arr
                            $sheet->setCellValue('A' . $row, $record->leadID);  // Lead ID
                            $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->addedBy, 'displayName'));  // Sanction By
                            $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));  // Branch
                            $sheet->setCellValue('D' . $row, ucwords($record->name));  // Name (capitalize first letter)
                            $sheet->setCellValue('E' . $row, $record->email);  // Email
                            $sheet->setCellValue('F' . $row, $record->mobile);  // Mobile
                            $sheet->setCellValue('G' . $row, $record->pancard);  // Pancard
                            $sheet->setCellValue('H' . $row, $record->accountNo);  // Account No.
                            $sheet->setCellValue('I' . $row, nf($record->loanAmtApproved));  // Loan Amount (formatted)
                            $sheet->setCellValue('J' . $row, nf($record->disbursalAmount));  // Disbursed Amount (formatted)
                            $sheet->setCellValue('K' . $row, $record->ifscCode);  // IFSC Code
                            $sheet->setCellValue('L' . $row, $record->loanNo);  // Loan No.
                            $sheet->setCellValue('M' . $row, $record->roi . ' %');  // ROI (with '%')
                            $sheet->setCellValue('N' . $row, nf($record->adminFee));  // Admin Fee (formatted)
                            $sheet->setCellValue('O' . $row, nf($record->monthlyIncome));  // Monthly Income (formatted)
                            $sheet->setCellValue('P' . $row, $record->cibil);  // Cibil
                            $sheet->setCellValue('Q' . $row, $record->status);  // Status
                            
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('R' . $row, $excelDate);  
                            $sheet->getStyle('R' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            // Move to the next row for the next record
                            $row++;
                        }
                    });
                    
                    // Create writer and file download logic
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Disbursal_Sheet_Send_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Disbursal Sheet Send Leads Exported (Date Range Export)', $logData);
                    
                    // Output the file as a download
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

        if(role()=='Credit Manager'){
            $userID = getUserID(); //helper to fetch current user logged in ID
            $query->where(function($query) use ($userID) {
                $query->where('lms_leads.cmID', $userID);
            });
        }
                
        // Paginate the results
        $leads = $query->paginate(10);
        // Prepare other data needed for the view
        $queryParameters = $request->query();
        $filter = $request->filter;
        $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
        $data = compact('leads','page_info','filter','queryParameters');
        return view('disbursal.disbursalSheetSendLeads')->with($data); 
    }        


    public function disbursedLeads(Request $request){
        $query = DB::table('lms_leads')
                        ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                        ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                        ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                        ->leftJoin('users', 'lms_loan.addedBy', '=', 'users.userID') // Left Join with users for cmID
                        ->where('lms_leads.status', 'Disbursed')
                        ->where('lms_loan.status', 'Disbursed')
                        ->select('lms_leads.leadID','lms_leads.contactID','lms_leads.leadID','lms_leads.status', 'lms_contact.name','lms_contact.mobile','lms_contact.pancard','lms_contact.email','lms_approval.branch','lms_approval.roi','lms_approval.loanAmtApproved','lms_approval.adminFee','lms_approval.monthlyIncome','lms_approval.cibil','lms_loan.accountNo','lms_loan.disbursalAmount','lms_loan.disbursalDate','lms_loan.disburseTime','lms_loan.ifscCode','lms_loan.loanNo','lms_loan.addedBy','lms_loan.addedOn')
                        ->orderBy('lms_loan.id','DESC');

         if ($request->filter == 'sortBySearch' && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('lms_contact.name', 'like', "%{$search}%")
                      ->orWhere('lms_contact.email', 'like', "%{$search}%")
                      ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                      ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                      ->orWhere('users.displayName', 'like', "%{$search}%") // Search in rm_users displayName
                      ->orWhere('lms_leads.leadID', 'like', "%{$search}%")
                      ->orWhere('lms_loan.loanNo', 'like', "%{$search}%");
            });
        }

        // Apply date range filter based on sortByDate filter and searchRange
        if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
            $dates = explode(' - ', $request->searchRange);
            $fromDate = date('Y-m-d', strtotime($dates[0]));
            $toDate = date('Y-m-d', strtotime($dates[1]));
            $query->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
        } elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $query->whereDate('lms_loan.disbursalDate', $today);
        } elseif ($request->filter == 'sortByWeek') {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $query->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);
        } elseif ($request->filter == 'sortByThisMonth') {
            $query->whereMonth('lms_loan.disbursalDate', '=', date('m'))
                  ->whereYear('lms_loan.disbursalDate', '=', date('Y'));
        } elseif ($request->filter == 'sortByLastMonth') {
            $lastMonth = date('m') - 1;
            $lastMonthYear = date('Y');
            if ($lastMonth == 0) {
                $lastMonth = 12;
                $lastMonthYear = date('Y') - 1;
            }
            $query->whereMonth('lms_loan.disbursalDate', '=', $lastMonth)
                  ->whereYear('lms_loan.disbursalDate', '=', $lastMonthYear);
        }elseif ($request->filter == 'exportAll') {
                
                // Create a new Spreadsheet object
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                
                // Set the headers for the spreadsheet
                $sheet->setCellValue('A1', 'LeadID');
                $sheet->setCellValue('B1', 'Disbursed By');
                $sheet->setCellValue('C1', 'Branch');
                $sheet->setCellValue('D1', 'Name');
                $sheet->setCellValue('E1', 'Email');
                $sheet->setCellValue('F1', 'Mobile');
                $sheet->setCellValue('G1', 'Pancard');
                $sheet->setCellValue('H1', 'Account No.');
                $sheet->setCellValue('I1', 'Loan Amount');
                $sheet->setCellValue('J1', 'Disbursed Amount');
                $sheet->setCellValue('K1', 'IFSC Code');
                $sheet->setCellValue('L1', 'Loan No.');
                $sheet->setCellValue('M1', 'ROI');
                $sheet->setCellValue('N1', 'Admin Fee');
                $sheet->setCellValue('O1', 'Monthly Income');
                $sheet->setCellValue('P1', 'Cibil');
                $sheet->setCellValue('Q1', 'Status');
                $sheet->setCellValue('R1', 'Date');
                
                // Start from row 2 to leave space for headers
                $row = 2;
                
                // Chunk the query to avoid memory overload
                $query->chunk(5000, function ($records) use ($sheet, &$row) {
                    foreach ($records as $record) {
                        // Use the correct variable $record, not $arr
                        $sheet->setCellValue('A' . $row, $record->leadID);  // Lead ID
                        $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->addedBy, 'displayName'));  // Disbursed By (User Name)
                        $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));  // Branch (City Name)
                        $sheet->setCellValue('D' . $row, ucwords($record->name));  // Name (capitalize first letter)
                        $sheet->setCellValue('E' . $row, $record->email);  // Email
                        $sheet->setCellValue('F' . $row, $record->mobile);  // Mobile
                        $sheet->setCellValue('G' . $row, $record->pancard);  // Pancard
                        $sheet->setCellValueExplicit('H' . $row, str_pad($record->accountNo,10, ' ', STR_PAD_LEFT), DataType::TYPE_STRING);
                        $sheet->setCellValue('I' . $row, nf($record->loanAmtApproved));  // Loan Amount (formatted)
                        $sheet->setCellValue('J' . $row, nf($record->disbursalAmount));  // Disbursed Amount (formatted)
                        $sheet->setCellValue('K' . $row, $record->ifscCode);  // IFSC Code
                        $sheet->setCellValue('L' . $row, $record->loanNo);  // Loan No.
                        $sheet->setCellValue('M' . $row, $record->roi . ' %');  // ROI (with '%')
                        $sheet->setCellValue('N' . $row, nf($record->adminFee));  // Admin Fee (formatted)
                        $sheet->setCellValue('O' . $row, nf($record->monthlyIncome));  // Monthly Income (formatted)
                        $sheet->setCellValue('P' . $row, $record->cibil);  // Cibil
                        $sheet->setCellValue('Q' . $row, $record->status);  // Status
                        
                        $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                        $excelDate = Date::PHPToExcel($formattedDate);
                        $sheet->setCellValue('R' . $row, $excelDate);  
                        $sheet->getStyle('R' . $row)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                        
                        // Move to the next row for the next record
                        $row++;
                    }
                });
                
                // Create writer and file download logic
                $writer = new Xlsx($spreadsheet);
                $fileName = cmp()->companyName.'_'.'Disbursed_Leads_Exported_Leads_Data.xlsx';
                
                // Log the export action
                $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                actLogs('leads', 'Disbursed Leads Exported (All Export)', $logData);
                
                // Output the file as a download
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
                    
                    // Set the headers for the spreadsheet
                    $sheet->setCellValue('A1', 'LeadID');
                    $sheet->setCellValue('B1', 'Disbursed By');
                    $sheet->setCellValue('C1', 'Branch');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Account No.');
                    $sheet->setCellValue('I1', 'Loan Amount');
                    $sheet->setCellValue('J1', 'Disbursed Amount');
                    $sheet->setCellValue('K1', 'IFSC Code');
                    $sheet->setCellValue('L1', 'Loan No.');
                    $sheet->setCellValue('M1', 'ROI');
                    $sheet->setCellValue('N1', 'Admin Fee');
                    $sheet->setCellValue('O1', 'Monthly Income');
                    $sheet->setCellValue('P1', 'Cibil');
                    $sheet->setCellValue('Q1', 'Status');
                    $sheet->setCellValue('R1', 'Date');
                    
                    // Start from row 2 to leave space for headers
                    $row = 2;
                    
                    // Chunk the query to avoid memory overload
                     $query->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            // Use the correct variable $record, not $arr
                            $sheet->setCellValue('A' . $row, $record->leadID);  // Lead ID
                            $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->addedBy, 'displayName'));  // Disbursed By (User Name)
                            $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));  // Branch (City Name)
                            $sheet->setCellValue('D' . $row, ucwords($record->name));  // Name (capitalize first letter)
                            $sheet->setCellValue('E' . $row, $record->email);  // Email
                            $sheet->setCellValue('F' . $row, $record->mobile);  // Mobile
                            $sheet->setCellValue('G' . $row, $record->pancard);  // Pancard
                            $sheet->setCellValueExplicit('H' . $row, str_pad($record->accountNo,10, ' ', STR_PAD_LEFT), DataType::TYPE_STRING);
                            $sheet->setCellValue('I' . $row, nf($record->loanAmtApproved));  // Loan Amount (formatted)
                            $sheet->setCellValue('J' . $row, nf($record->disbursalAmount));  // Disbursed Amount (formatted)
                            $sheet->setCellValue('K' . $row, $record->ifscCode);  // IFSC Code
                            $sheet->setCellValue('L' . $row, $record->loanNo);  // Loan No.
                            $sheet->setCellValue('M' . $row, $record->roi . ' %');  // ROI (with '%')
                            $sheet->setCellValue('N' . $row, nf($record->adminFee));  // Admin Fee (formatted)
                            $sheet->setCellValue('O' . $row, nf($record->monthlyIncome));  // Monthly Income (formatted)
                            $sheet->setCellValue('P' . $row, $record->cibil);  // Cibil
                            $sheet->setCellValue('Q' . $row, $record->status);  // Status
                            
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('R' . $row, $excelDate);  
                            $sheet->getStyle('R' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Move to the next row for the next record
                            $row++;
                        }
                    });
                    
                    // Create writer and file download logic
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Disbursed_Leads_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Disbursed Leads Exported (Date Range Export)', $logData);
                    
                    // Output the file as a download
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
    
          if(role()=='Credit Manager'){
            $userID = getUserID(); //helper to fetch current user logged in ID
            $query->where(function($query) use ($userID) {
                $query->where('lms_leads.cmID', $userID);
            });
        }
                       
        // Paginate the results
        $leads = $query->paginate(10);
        // Prepare other data needed for the view
        $queryParameters = $request->query();
        $filter = $request->filter;
        $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
        $data = compact('leads','page_info','filter','queryParameters');
        return view('disbursal.disbursedLeads')->with($data); 
    }        
}
