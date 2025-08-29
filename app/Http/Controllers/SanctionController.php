<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class SanctionController extends Controller
{

        public function approvedLeads(Request $request){
             
                $query = DB::table('lms_leads')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('users', 'lms_approval.addedBy', '=', 'users.userID') // Left Join with users for cmID
                    ->where(['lms_leads.status' => 'Approved', 'lms_approval.status' => 'Approved'])
                    ->distinct('lms_leads.leadID')  // Ensure distinct leadID
                    ->select('lms_approval.*', 'lms_leads.*', 'lms_contact.*','lms_approval.addedOn as approval_addedOn','lms_approval.tenure as approval_tenure') // Select all necessary columns
                    ->orderBy('lms_approval.id', 'DESC');

                  if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('users.displayName', 'like', "%{$search}%")  // Search in sanction by displayName
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }
                
                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_approval.createdDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_approval.createdDate', [$sevenDaysAgo, $today]);
                } elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_approval.createdDate', '=', date('m'))
                          ->whereYear('lms_approval.createdDate', '=', date('Y'));
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_approval.createdDate', '=', $lastMonth)
                          ->whereYear('lms_approval.createdDate', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
   
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Sanction By');
                    $sheet->setCellValue('C1', 'Branch');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Loan Amount');
                    $sheet->setCellValue('I1', 'Tenure');
                    $sheet->setCellValue('J1', 'ROI');
                    $sheet->setCellValue('K1', 'Admin Fee');
                    $sheet->setCellValue('L1', 'Monthly Income');
                    $sheet->setCellValue('M1', 'Cibil');
                    $sheet->setCellValue('N1', 'Status');
                    $sheet->setCellValue('O1', 'Date');
                    
                    $row = 2;
                    
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            $sheet->setCellValue('A' . $row, $record->leadID);
                            $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                            $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                            $sheet->setCellValue('D' . $row, ucwords($record->name));
                            $sheet->setCellValue('E' . $row, $record->email);
                            $sheet->setCellValue('F' . $row, $record->mobile);
                            $sheet->setCellValue('G' . $row, $record->pancard);
                            $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                            $sheet->setCellValue('I' . $row, $record->approval_tenure . ' Days');
                            $sheet->setCellValue('J' . $row, $record->roi . ' %');
                          
                            $sheet->setCellValue('K' . $row, $record->adminFee);
                            $sheet->setCellValue('L' . $row, nf($record->monthlyIncome));
                            $sheet->setCellValue('M' . $row, $record->cibil);
                            $sheet->setCellValue('N' . $row, $record->status);
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('O' . $row, $excelDate);  
                            $sheet->getStyle('O' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Sanction Approved Leads Data.xlsx';
                    $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Sanction Approved Leads Exported (All Export)', $logData);
                    
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

                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Sanction By');
                        $sheet->setCellValue('C1', 'Branch');
                        $sheet->setCellValue('D1', 'Name');
                        $sheet->setCellValue('E1', 'Email');
                        $sheet->setCellValue('F1', 'Mobile');
                        $sheet->setCellValue('G1', 'Pancard');
                        $sheet->setCellValue('H1', 'Loan Amount');
                        $sheet->setCellValue('I1', 'Tenure');
                        $sheet->setCellValue('J1', 'ROI');
                        $sheet->setCellValue('K1', 'Admin Fee');
                        $sheet->setCellValue('L1', 'Monthly Income');
                        $sheet->setCellValue('M1', 'Cibil');
                        $sheet->setCellValue('N1', 'Status');
                        $sheet->setCellValue('O1', 'Date');
                    

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('D' . $row, ucwords($record->name));
                                $sheet->setCellValue('E' . $row, $record->email);
                                $sheet->setCellValue('F' . $row, $record->mobile);
                                $sheet->setCellValue('G' . $row, $record->pancard);
                                $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('I' . $row, $record->approval_tenure . ' Days');
                                $sheet->setCellValue('J' . $row, $record->roi . ' %');
                              
                                $sheet->setCellValue('K' . $row, $record->adminFee);
                                $sheet->setCellValue('L' . $row, nf($record->monthlyIncome));
                                $sheet->setCellValue('M' . $row, $record->cibil);
                                $sheet->setCellValue('N' . $row, $record->status);
                                $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                                $excelDate = Date::PHPToExcel($formattedDate);
                                $sheet->setCellValue('O' . $row, $excelDate);  
                                $sheet->getStyle('O' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                         $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'Sanction Approved Leads Data.xlsx';
                        $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                        actLogs('leads', 'Sanction Approved Leads Exported (Date Range Export)', $logData);
                        
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
                $page_info = pageInfo('Sanction Approved Leads',$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                
                return view('sanction.approvedLeads')->with($data); 
        }        

        public function customerApprovedLeads(Request $request){
             
                $query = DB::table('lms_leads')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('users', 'lms_approval.addedBy', '=', 'users.userID') // Left Join with users for cmID
                    ->where(['lms_leads.status' => 'Customer Approved', 'lms_approval.status' => 'Customer Approved'])
                    ->distinct('lms_leads.leadID')  // Ensure distinct leadID
                    ->select('lms_approval.*', 'lms_leads.*', 'lms_contact.*','lms_approval.addedOn as approval_addedOn','lms_approval.tenure as approval_tenure') // Select all necessary columns
                    ->orderBy('lms_approval.id', 'DESC');

                  if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('users.displayName', 'like', "%{$search}%")  // Search in sanction by displayName
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }
                
                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_approval.createdDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_approval.createdDate', [$sevenDaysAgo, $today]);
                } elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_approval.createdDate', '=', date('m'))
                          ->whereYear('lms_approval.createdDate', '=', date('Y'));
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_approval.createdDate', '=', $lastMonth)
                          ->whereYear('lms_approval.createdDate', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
   
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Sanction By');
                    $sheet->setCellValue('C1', 'Branch');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Loan Amount');
                    $sheet->setCellValue('I1', 'Tenure');
                    $sheet->setCellValue('J1', 'ROI');
                    $sheet->setCellValue('K1', 'Admin Fee');
                    $sheet->setCellValue('L1', 'Monthly Income');
                    $sheet->setCellValue('M1', 'Cibil');
                    $sheet->setCellValue('N1', 'Status');
                    $sheet->setCellValue('O1', 'Date');
                    
                    $row = 2;
                    
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            $sheet->setCellValue('A' . $row, $record->leadID);
                            $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                            $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                            $sheet->setCellValue('D' . $row, ucwords($record->name));
                            $sheet->setCellValue('E' . $row, $record->email);
                            $sheet->setCellValue('F' . $row, $record->mobile);
                            $sheet->setCellValue('G' . $row, $record->pancard);
                            $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                            $sheet->setCellValue('I' . $row, $record->approval_tenure . ' Days');
                            $sheet->setCellValue('J' . $row, $record->roi . ' %');
                          
                            $sheet->setCellValue('K' . $row, $record->adminFee);
                            $sheet->setCellValue('L' . $row, nf($record->monthlyIncome));
                            $sheet->setCellValue('M' . $row, $record->cibil);
                            $sheet->setCellValue('N' . $row, $record->status);
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('O' . $row, $excelDate);  
                            $sheet->getStyle('O' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Sanction Approved Leads Data.xlsx';
                    $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Sanction Approved Leads Exported (All Export)', $logData);
                    
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

                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Sanction By');
                        $sheet->setCellValue('C1', 'Branch');
                        $sheet->setCellValue('D1', 'Name');
                        $sheet->setCellValue('E1', 'Email');
                        $sheet->setCellValue('F1', 'Mobile');
                        $sheet->setCellValue('G1', 'Pancard');
                        $sheet->setCellValue('H1', 'Loan Amount');
                        $sheet->setCellValue('I1', 'Tenure');
                        $sheet->setCellValue('J1', 'ROI');
                        $sheet->setCellValue('K1', 'Admin Fee');
                        $sheet->setCellValue('L1', 'Monthly Income');
                        $sheet->setCellValue('M1', 'Cibil');
                        $sheet->setCellValue('N1', 'Status');
                        $sheet->setCellValue('O1', 'Date');
                    

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('D' . $row, ucwords($record->name));
                                $sheet->setCellValue('E' . $row, $record->email);
                                $sheet->setCellValue('F' . $row, $record->mobile);
                                $sheet->setCellValue('G' . $row, $record->pancard);
                                $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('I' . $row, $record->approval_tenure . ' Days');
                                $sheet->setCellValue('J' . $row, $record->roi . ' %');
                              
                                $sheet->setCellValue('K' . $row, $record->adminFee);
                                $sheet->setCellValue('L' . $row, nf($record->monthlyIncome));
                                $sheet->setCellValue('M' . $row, $record->cibil);
                                $sheet->setCellValue('N' . $row, $record->status);
                                $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                                $excelDate = Date::PHPToExcel($formattedDate);
                                $sheet->setCellValue('O' . $row, $excelDate);  
                                $sheet->getStyle('O' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                         $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'Sanction Approved Leads Data.xlsx';
                        $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                        actLogs('leads', 'Sanction Approved Leads Exported (Date Range Export)', $logData);
                        
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

                if(role()=='Recovery Executive' || role()=='Recovery Manager' || role()=='Sr. Recovery Manager'){
                    $userID = getUserID(); //helper to fetch current user logged in ID
                    $query->where(function($query) use ($userID) {
                        $query->where('lms_leads.pdStart', '1');
                        $query->where('lms_approval.pdVerifiedBy', $userID);
                    });
                } 
                
                // Paginate the results
                $leads = $query->paginate(10);

                // Prepare other data needed for the view
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo('Customer Approved Leads',$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                
                return view('sanction.customerApprovedLeads')->with($data); 
        }        
        public function enachList(Request $request){
                 
                $query = DB::table('lms_enach_register')
                         ->orderBy('eNachID', 'DESC');

                  if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_enach_register.cusName', 'like', "%{$search}%")
                              ->orWhere('lms_enach_register.cusEmail', 'like', "%{$search}%")
                              ->orWhere('lms_enach_register.cusPan', 'like', "%{$search}%")
                              ->orWhere('lms_enach_register.accountNo', 'like', "%{$search}%")
                              ->orWhere('lms_enach_register.leadID', 'like', "%{$search}%");
                    });
                }
                
                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_enach_register.scheduleDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_enach_register.scheduleDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_enach_register.scheduleDate', [$sevenDaysAgo, $today]);
                } elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_enach_register.scheduleDate', '=', date('m'))
                          ->whereYear('lms_enach_register.scheduleDate', '=', date('Y'));
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_enach_register.scheduleDate', '=', $lastMonth)
                          ->whereYear('lms_enach_register.scheduleDate', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
   
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Status');
                    $sheet->setCellValue('C1', 'Unique Reference Number');
                    $sheet->setCellValue('D1', 'Mandate Registration ID');
                    $sheet->setCellValue('E1', 'Customer Name');
                    $sheet->setCellValue('F1', 'Email');
                    $sheet->setCellValue('G1', 'Pan No');
                    $sheet->setCellValue('H1', 'Account No');
                    $sheet->setCellValue('I1', 'IFSC Code');
                    $sheet->setCellValue('J1', 'Repayment Amount');
                    $sheet->setCellValue('K1', 'Sanction Date');
                    $sheet->setCellValue('L1', 'Expiry Date');
                    $sheet->setCellValue('M1', 'Transaction Time'); // Added this for the last column

                    $row = 2;
                    
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            
                            $sheet->setCellValue('A' . $row, $record->leadID);  // Lead ID
                            $sheet->setCellValue('B' . $row, $record->status);  // Status
                            $sheet->setCellValue('C' . $row, $record->consumerID);  // Unique Reference Number (Consumer ID)
                            $sheet->setCellValue('D' . $row, $record->mandate_reg_no);  // Mandate Registration ID
                            $sheet->setCellValue('E' . $row, ucwords($record->cusName));  // Customer Name
                            $sheet->setCellValue('F' . $row, $record->cusEmail);  // Email
                            $sheet->setCellValue('G' . $row, $record->cusPan);  // Pan No
                            $sheet->setCellValueExplicit('H' . $row, str_pad($record->accountNo,8, '', STR_PAD_LEFT), DataType::TYPE_STRING);
                            $sheet->setCellValue('I' . $row, $record->ifscCode);  // IFSC Code
                            $sheet->setCellValue('J' . $row, $record->enachAmount);  // Repayment Amount
                            
                            // Formatting the Sanction Date
                            $formattedDate = date('Y-m-d', strtotime($record->scheduleDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('K' . $row, $excelDate);  
                            $sheet->getStyle('K' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Formatting Expiry Date as DD/MM/YYYY
                            $formattedExpiryDate = date('Y-m-d', strtotime($record->expiryDate));  
                            $excelExpiryDate = Date::PHPToExcel($formattedExpiryDate);
                            $sheet->setCellValue('L' . $row, $excelExpiryDate);  // Expiry Date formatted
                            $sheet->getStyle('L' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);  // Applying the DD/MM/YYYY format
                            
                            $sheet->setCellValue('M' . $row, $record->tpsl_txn_time);  // Transaction Time
                            $row++;

                        }
                    });
                    
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Enach Registration Data.xlsx';
                    $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Enach Registration Data Exported (All Export)', $logData);
                    
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

                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('d-m-Y', strtotime($dates[0]));
                    $toDate = date('d-m-Y', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Status');
                        $sheet->setCellValue('C1', 'Unique Reference Number');
                        $sheet->setCellValue('D1', 'Mandate Registration ID');
                        $sheet->setCellValue('E1', 'Customer Name');
                        $sheet->setCellValue('F1', 'Email');
                        $sheet->setCellValue('G1', 'Pan No');
                        $sheet->setCellValue('H1', 'Account No');
                        $sheet->setCellValue('I1', 'IFSC Code');
                        $sheet->setCellValue('J1', 'Repayment Amount');
                        $sheet->setCellValue('K1', 'Sanction Date');
                        $sheet->setCellValue('L1', 'Expiry Date');
                        $sheet->setCellValue('M1', 'Transaction Time'); // Added this for the last column
                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_enach_register.scheduleDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                 $sheet->setCellValue('A' . $row, $record->leadID);  // Lead ID
                            $sheet->setCellValue('B' . $row, $record->status);  // Status
                            $sheet->setCellValue('C' . $row, $record->consumerID);  // Unique Reference Number (Consumer ID)
                            $sheet->setCellValue('D' . $row, $record->mandate_reg_no);  // Mandate Registration ID
                            $sheet->setCellValue('E' . $row, ucwords($record->cusName));  // Customer Name
                            $sheet->setCellValue('F' . $row, $record->cusEmail);  // Email
                            $sheet->setCellValue('G' . $row, $record->cusPan);  // Pan No
                            $sheet->setCellValueExplicit('H' . $row, str_pad($record->accountNo,8, '', STR_PAD_LEFT), DataType::TYPE_STRING);
                            $sheet->setCellValue('I' . $row, $record->ifscCode);  // IFSC Code
                            $sheet->setCellValue('J' . $row, $record->enachAmount);  // Repayment Amount
                            
                            // Formatting the Sanction Date
                            $formattedDate = date('Y-m-d', strtotime($record->scheduleDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('K' . $row, $excelDate);  
                            $sheet->getStyle('K' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Formatting Expiry Date as DD/MM/YYYY
                            $formattedExpiryDate = date('Y-m-d', strtotime($record->expiryDate));  
                            $excelExpiryDate = Date::PHPToExcel($formattedExpiryDate);
                            $sheet->setCellValue('L' . $row, $excelExpiryDate);  // Expiry Date formatted
                            $sheet->getStyle('L' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);  // Applying the DD/MM/YYYY format
                            
                            $sheet->setCellValue('M' . $row, $record->tpsl_txn_time);  // Transaction Time
                            $row++;
                            }
                        });
                        // Write the spreadsheet to a file
                         $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'Enach Registration Data.xlsx';
                        $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                        actLogs('leads', 'Enach Registration Data Exported (Date Range Export)', $logData);
                        
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
 

                // Paginate the results
                $leads = $query->paginate(10);
                 
               
                // Prepare other data needed for the view
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo('E-Nach Registration List',$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                
                return view('sanction.enachList')->with($data); 
        }
        
        public function sanctionRejectedLeads(Request $request){
                
               $query = DB::table('lms_leads')
                        ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                        ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                        ->where(['lms_leads.status' => 'Rejected'])
                        ->where(function ($query) {
                            $query->where('lms_approval.status', 'Rejected');
                                  // ->orWhereNull('lms_approval.status'); // Allow NULL if no matching approval
                        })
                        ->distinct('lms_leads.leadID')  // Ensure distinct leadID
                        ->select('lms_approval.*', 'lms_leads.*', 'lms_contact.*') // Select all necessary columns
                        ->orderBy('lms_approval.id', 'DESC');
    
                 if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }

                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_approval.createdDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_approval.createdDate', [$sevenDaysAgo, $today]);
                } elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_approval.createdDate', '=', date('m'))
                          ->whereYear('lms_approval.createdDate', '=', date('Y'));
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_approval.createdDate', '=', $lastMonth)
                          ->whereYear('lms_approval.createdDate', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
   
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Branch');
                        $sheet->setCellValue('C1', 'Sanction By');
                        $sheet->setCellValue('D1', 'Name');
                        $sheet->setCellValue('E1', 'Email');
                        $sheet->setCellValue('F1', 'Mobile');
                        $sheet->setCellValue('G1', 'Employed');
                        $sheet->setCellValue('H1', 'Loan Amount');
                        $sheet->setCellValue('I1', 'Tenure');
                        $sheet->setCellValue('J1', 'ROI');
                        $sheet->setCellValue('K1', 'Admin Fee');
                        $sheet->setCellValue('L1', 'Monthly Income');
                        $sheet->setCellValue('M1', 'Cibil');
                        $sheet->setCellValue('N1', 'Date');



                        $row = 2; // Start row for data

                      $query->chunk(500000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                                $sheet->setCellValue('D' . $row, $record->name);
                                $sheet->setCellValue('E' . $row, $record->email);
                                $sheet->setCellValue('F' . $row, $record->mobile);
                                $sheet->setCellValue('G' . $row, $record->employed);
                                $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('I' . $row, $record->tenure);
                                $sheet->setCellValue('J' . $row, $record->roi);
                                $sheet->setCellValue('K' . $row, $record->adminFee);
                                $sheet->setCellValue('L' . $row, $record->monthlyIncome);
                                $sheet->setCellValue('M' . $row, $record->cibil);
                                $sheet->setCellValue('N' . $row, $record->addedOn);
                                $row++;
                            }
                        });
                      
                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'exported_data.xlsx';
                         actLogs('leads','status wise leads exported all',$request->all());
                        // Return the file as a download response
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
                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Branch');
                        $sheet->setCellValue('C1', 'Sanction By');
                        $sheet->setCellValue('D1', 'Name');
                        $sheet->setCellValue('E1', 'Email');
                        $sheet->setCellValue('F1', 'Mobile');
                        $sheet->setCellValue('G1', 'Employed');
                        $sheet->setCellValue('H1', 'Loan Amount');
                        $sheet->setCellValue('I1', 'Tenure');
                        $sheet->setCellValue('J1', 'ROI');
                        $sheet->setCellValue('K1', 'Admin Fee');
                        $sheet->setCellValue('L1', 'Monthly Income');
                        $sheet->setCellValue('M1', 'Cibil');
                        $sheet->setCellValue('N1', 'Date');

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate])->chunk(5000000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                                $sheet->setCellValue('D' . $row, $record->name);
                                $sheet->setCellValue('E' . $row, $record->email);
                                $sheet->setCellValue('F' . $row, $record->mobile);
                                $sheet->setCellValue('G' . $row, $record->employed);
                                $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('I' . $row, $record->tenure);
                                $sheet->setCellValue('J' . $row, $record->roi);
                                $sheet->setCellValue('K' . $row, $record->adminFee);
                                $sheet->setCellValue('L' . $row, $record->monthlyIncome);
                                $sheet->setCellValue('M' . $row, $record->cibil);
                                $sheet->setCellValue('N' . $row, $record->addedOn);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = 'exported_data.xlsx';
                        actLogs('leads','status wise leads exported date wise',$request->all());
                        // Return the file as a download response
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
                $page_info = pageInfo('Sanction Rejected Leads',$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                
                return view('sanction.sanctionRejectedLeads')->with($data); 
        }            
      

      public function directRejectedLeads(Request $request){
                
               $query = DB::table('lms_leads')
                        ->leftJoin('lms_loan_rejection', 'lms_leads.leadID', '=', 'lms_loan_rejection.leadID') // Change to leftJoin
                        ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                        ->where(['lms_leads.status' => 'Rejected','lms_loan_rejection.status' => 'Rejected'])
                         
                        ->distinct('lms_leads.leadID')  // Ensure distinct leadID
                        ->select('lms_loan_rejection.*', 'lms_leads.loanRequired','lms_leads.status', 'lms_contact.name','lms_contact.mobile') // Select all necessary columns
                        ->orderBy('lms_loan_rejection.id', 'DESC');
    
                 if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }

                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_loan_rejection.createdDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_loan_rejection.createdDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_loan_rejection.createdDate', [$sevenDaysAgo, $today]);
                } elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_loan_rejection.createdDate', '=', date('m'))
                          ->whereYear('lms_loan_rejection.createdDate', '=', date('Y'));
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_loan_rejection.createdDate', '=', $lastMonth)
                          ->whereYear('lms_loan_rejection.createdDate', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
   
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Branch');
                        $sheet->setCellValue('C1', 'Rejected By');
                        $sheet->setCellValue('D1', 'Rejected Reason');
                        $sheet->setCellValue('E1', 'Remarks');
                        $sheet->setCellValue('F1', 'Name');
                        $sheet->setCellValue('G1', 'Official Email');
                        $sheet->setCellValue('H1', 'Mobile');
                        $sheet->setCellValue('I1', 'Loan Amount');
                        $sheet->setCellValue('J1', 'Cibil');
                        $sheet->setCellValue('K1', 'Date');



                        $row = 2; // Start row for data

                      $query->chunk(500000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('users', 'userID', $record->addedBy, 'displayName'));
                                $reason = $record->rejectionReason;
                                if (is_string($reason) && str_starts_with($reason, '[')) {
                                    $decoded = json_decode($reason, true);
                                    if (is_array($decoded)) {
                                        $reason = implode(', ', $decoded);
                                    }
                                }
                                $sheet->setCellValue('D' . $row, $reason);
                                $sheet->setCellValue('E' . $row, $record->remarks);
                                $sheet->setCellValue('F' . $row, ucwords($record->name));
                                $sheet->setCellValue('G' . $row, $record->officialEmail);
                                $sheet->setCellValue('H' . $row, $record->mobile);
                                $sheet->setCellValue('I' . $row, $record->loanRequired);
                                $sheet->setCellValue('J' . $row, $record->cibil);
                                $sheet->setCellValue('K' . $row, df($record->addedOn));
                                $row++;
                            }
                        });
                      
                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'exported_data.xlsx';
                         actLogs('leads','status wise leads exported all',$request->all());
                        // Return the file as a download response
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
                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Branch');
                        $sheet->setCellValue('C1', 'Rejected By');
                        $sheet->setCellValue('D1', 'Rejected Reason');
                        $sheet->setCellValue('E1', 'Remarks');
                        $sheet->setCellValue('F1', 'Name');
                        $sheet->setCellValue('G1', 'Official Email');
                        $sheet->setCellValue('H1', 'Mobile');
                        $sheet->setCellValue('I1', 'Loan Amount');
                        $sheet->setCellValue('J1', 'Cibil');
                        $sheet->setCellValue('K1', 'Date');

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_loan_rejection.createdDate', [$fromDate, $toDate])->chunk(5000000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('users', 'userID', $record->addedBy, 'displayName'));
                                $reason = $record->rejectionReason;
                                if (is_string($reason) && str_starts_with($reason, '[')) {
                                    $decoded = json_decode($reason, true);
                                    if (is_array($decoded)) {
                                        $reason = implode(', ', $decoded);
                                    }
                                }
                                $sheet->setCellValue('D' . $row, $reason);
                                $sheet->setCellValue('E' . $row, $record->remarks);
                                $sheet->setCellValue('F' . $row, ucwords($record->name));
                                $sheet->setCellValue('G' . $row, $record->officialEmail);
                                $sheet->setCellValue('H' . $row, $record->mobile);
                                $sheet->setCellValue('I' . $row, $record->loanRequired);
                                $sheet->setCellValue('J' . $row, $record->cibil);
                                $sheet->setCellValue('K' . $row, df($record->addedOn));
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = 'exported_data.xlsx';
                        actLogs('leads','status wise leads exported date wise',$request->all());
                        // Return the file as a download response
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
                $page_info = pageInfo('Direct Rejected Leads',$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                
                return view('sanction.directRejectedLeads')->with($data); 
        }
      
       public function approvalPendingLeads(Request $request) {
                $query = DB::table('lms_leads')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('users', 'lms_approval.addedBy', '=', 'users.userID') // Left Join with users for cmID
                    ->where(['lms_approval.status' => 'Pending For Approval'])
                    ->distinct('lms_leads.leadID')  // Ensure distinct leadID
                    ->select('lms_approval.*', 'lms_leads.*', 'lms_contact.*','lms_approval.addedOn as approval_addedOn','lms_approval.tenure as approval_tenure') // Select all necessary columns
                    ->orderBy('lms_approval.id', 'DESC');

                  if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('users.displayName', 'like', "%{$search}%")  // Search in sanction by displayName
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }
                
                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_approval.createdDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_approval.createdDate', [$sevenDaysAgo, $today]);
                } elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_approval.createdDate', '=', date('m'))
                          ->whereYear('lms_approval.createdDate', '=', date('Y'));
                } elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_approval.createdDate', '=', $lastMonth)
                          ->whereYear('lms_approval.createdDate', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
   
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Sanction By');
                    $sheet->setCellValue('C1', 'Branch');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Loan Amount');
                    $sheet->setCellValue('I1', 'Tenure');
                    $sheet->setCellValue('J1', 'ROI');
                    $sheet->setCellValue('K1', 'Admin Fee');
                    $sheet->setCellValue('L1', 'Monthly Income');
                    $sheet->setCellValue('M1', 'Cibil');
                    $sheet->setCellValue('N1', 'Status');
                    $sheet->setCellValue('O1', 'Date');
                    
                    $row = 2;
                    
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            $sheet->setCellValue('A' . $row, $record->leadID);
                            $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                            $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                            $sheet->setCellValue('D' . $row, ucwords($record->name));
                            $sheet->setCellValue('E' . $row, $record->email);
                            $sheet->setCellValue('F' . $row, $record->mobile);
                            $sheet->setCellValue('G' . $row, $record->pancard);
                            $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                            $sheet->setCellValue('I' . $row, $record->approval_tenure . ' Days');
                            $sheet->setCellValue('J' . $row, $record->roi . ' %');
                          
                            $sheet->setCellValue('K' . $row, $record->adminFee);
                            $sheet->setCellValue('L' . $row, nf($record->monthlyIncome));
                            $sheet->setCellValue('M' . $row, $record->cibil);
                            $sheet->setCellValue('N' . $row, $record->status);
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('O' . $row, $excelDate);  
                            $sheet->getStyle('O' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Pending For Approval Leads Data.xlsx';
                    $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Pending For Approval Leads Exported (All Export)', $logData);
                    
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

                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Sanction By');
                        $sheet->setCellValue('C1', 'Branch');
                        $sheet->setCellValue('D1', 'Name');
                        $sheet->setCellValue('E1', 'Email');
                        $sheet->setCellValue('F1', 'Mobile');
                        $sheet->setCellValue('G1', 'Pancard');
                        $sheet->setCellValue('H1', 'Loan Amount');
                        $sheet->setCellValue('I1', 'Tenure');
                        $sheet->setCellValue('J1', 'ROI');
                        $sheet->setCellValue('K1', 'Admin Fee');
                        $sheet->setCellValue('L1', 'Monthly Income');
                        $sheet->setCellValue('M1', 'Cibil');
                        $sheet->setCellValue('N1', 'Status');
                        $sheet->setCellValue('O1', 'Date');
                    

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_approval.createdDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, getUserNameById('users', 'userID', $record->creditedBy, 'displayName'));
                                $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName'));
                                $sheet->setCellValue('D' . $row, ucwords($record->name));
                                $sheet->setCellValue('E' . $row, $record->email);
                                $sheet->setCellValue('F' . $row, $record->mobile);
                                $sheet->setCellValue('G' . $row, $record->pancard);
                                $sheet->setCellValue('H' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('I' . $row, $record->approval_tenure . ' Days');
                                $sheet->setCellValue('J' . $row, $record->roi . ' %');
                              
                                $sheet->setCellValue('K' . $row, $record->adminFee);
                                $sheet->setCellValue('L' . $row, nf($record->monthlyIncome));
                                $sheet->setCellValue('M' . $row, $record->cibil);
                                $sheet->setCellValue('N' . $row, $record->status);
                                $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                                $excelDate = Date::PHPToExcel($formattedDate);
                                $sheet->setCellValue('O' . $row, $excelDate);  
                                $sheet->getStyle('O' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                         $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'Pending For Approval Leads Data.xlsx';
                        $logData = array_merge($request->all(), ['Export_By' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                        actLogs('leads', 'Pending For Approval Leads Exported (Date Range Export)', $logData);
                        
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
                $page_info = pageInfo('Pending For Approval Leads',$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                return view('sanction.pendingForApprovalLead')->with($data);
            
    }    


       public function pendingApprovalEdit(Request $request){
            
          
            // Fetch lead data from the database
           $pendingApprovalData = DB::table('lms_approval')->select('leadID','creditStatus','approvalRemarks')->where('leadID',$request->leadID)->first();
          
            // Check if lead data exists
            if (!$pendingApprovalData) {
                return response()->json(['error' => 'Lead not found'], 404);
            }

            // Return lead data as JSON response
            return response()->json($pendingApprovalData);
       }

        public function pendingApprovalUpdate(Request $request){
             $validator =  Validator::make($request->all(),[
                'leadID' =>'required',
                'creditStatus' =>'required',
                'approvalRemarks' =>'required',
            ]);
 
             $data = [
                'creditStatus' =>$request->creditStatus,
                'approvalRemarks' =>$request->approvalRemarks,
                'matrixApprovalBy' =>Session::get('userID'),
             ];

             if($validator){
                      DB::table('lms_approval')->where('leadID',$request->leadID)->update($data);
                      if ($request->creditStatus=='Rejected') {
                             $dataLead['status'] = 'Rejected';
                             actLogs('Sanction','pending approval update',$data);
                             DB::table('lms_leads')->where('leadID',$request->leadID)->update($dataLead);
                         }
                     return response()->json(['response'=>'success','message'=>'Approval Pending updated successfully.']);
                }else{
                     return response()->json(['response'=>'error','message'=>'Approval Pending failed to update']);
                }
           
        }


     
}
