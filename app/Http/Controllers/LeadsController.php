<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

 

class LeadsController extends Controller
{
   public function statusWiseLeads(Request $request, $leadType = null) {
    $activeTab = $leadType ?? 'fresh';
    $status = slugToText($activeTab);  // Helper  
    $exists = DB::table('lms_leads_status')->where('name', $status)->exists();

         if ($exists) {
                 $query = DB::table('lms_leads')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_account_details','lms_leads.leadID', '=', 'lms_account_details.leadID')
                        ->leftJoin('lms_address', function ($join) {
                            $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                                 ->where('lms_address.addressType', '=', 'current');
                    })
                    ->leftJoin('users as cm_users', 'lms_leads.cmID', '=', 'cm_users.userID')
                    ->leftJoin('users as rm_users', 'lms_leads.rmID', '=', 'rm_users.userID')
                    ->select(
                        'lms_leads.leadID',
                        'lms_leads.status',
                        'lms_contact.pancard',
                        'lms_leads.commingLeadsDate',
                        'lms_leads.rmID',
                        'lms_leads.cmID',
                        'lms_leads.customerType',
                        'lms_contact.name',
                        'lms_contact.email',
                        'lms_contact.mobile',
                        'lms_leads.utmSource',
                        'lms_leads.domainURL',
                        'lms_leads.addedOn',
                        'lms_leads.id',
                        'lms_account_details.monthlyIncome',
                        'lms_address.city',
                        DB::raw('(SELECT addedOn FROM lms_timeline WHERE leadID = lms_leads.leadID ORDER BY addedOn DESC LIMIT 1) as timelineDate')
                    )
                    ->groupBy(
                        'lms_leads.leadID',
                        'lms_leads.status',
                        'lms_contact.pancard',
                        'lms_leads.commingLeadsDate',
                        'lms_leads.rmID',
                        'lms_leads.cmID',
                        'lms_leads.customerType',
                        'lms_contact.name',
                        'lms_contact.email',
                        'lms_contact.mobile',
                        'lms_leads.utmSource',
                        'lms_leads.domainURL',
                        'lms_leads.addedOn',
                        'lms_leads.id',
                        'lms_account_details.monthlyIncome',
                        'lms_address.city'
                    )
                    ->orderBy('lms_leads.id', 'desc');
  
         
            // Apply status filter based on $activeTab
            if ($activeTab == 'fresh') {
                $query->where('lms_leads.status', $status)
                  ->whereNotIn('lms_leads.leadID', function($query) {
                      $query->select('lms_leads.leadID')
                            ->from('lms_leads')
                            ->join('lms_loan', 'lms_leads.contactID', '=', 'lms_loan.contactID')
                            ->where('lms_loan.status', 'Disbursed');
                  });
            }
            elseif ($activeTab == 'reloan-fresh') {
                $query->join('lms_loan', function($join) {
                    $join->on('lms_contact.contactID', '=', 'lms_loan.contactID')
                         ->where('lms_loan.status', '=', 'Disbursed');
                });
                $query->where('lms_leads.status', 'fresh');
            } 
            elseif ($activeTab == 'callback') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'no-answer') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'interested') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'not-interested') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'not-eligible') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'incomplete-documents') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'less-salary') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'document-received') {
                $query->where('lms_leads.status', $status);
            } elseif ($activeTab == 'rejected') {
                $query->where('lms_leads.status', $status);
            }
            // // Apply search filter if sortBySearch filter is selected and search term is provided
            if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($query) use ($search) {
                    $query->where('lms_contact.name', 'like', "%{$search}%")
                          ->orWhere('lms_contact.email', 'like', "%{$search}%")
                          ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                          ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                          ->orWhere('cm_users.displayName', 'like', "%{$search}%")  // Search in cm_users displayName
                          ->orWhere('rm_users.displayName', 'like', "%{$search}%") // Search in rm_users displayName
                          ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                });
            }
    
            // Apply date range filter based on sortByDate filter and searchRange
            if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                $dates = explode(' - ', $request->searchRange);
                $fromDate = date('Y-m-d', strtotime($dates[0]));
                $toDate = date('Y-m-d', strtotime($dates[1]));
                $query->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
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
            } elseif ($request->filter == 'exportAll') {
                
                 // Create a new Spreadsheet object
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Assigned RM');
                    $sheet->setCellValue('C1', 'Assigned CM');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Monthly Income');
                    $sheet->setCellValue('I1', 'City');
                    $sheet->setCellValue('J1', 'Employment Type');
                    $sheet->setCellValue('K1', 'UTM Source');
                    $sheet->setCellValue('L1', 'Domain URL');
                    $sheet->setCellValue('M1', 'Date');
                    
                    $row = 2;
                    
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                            $sheet->setCellValue('A' . $row, $record->leadID);
                            $sheet->setCellValue('B' . $row, !empty($record->rmID) ? getUserNameById('users', 'userID', $record->rmID, 'displayName') : 'N/A');
                            $sheet->setCellValue('C' . $row, !empty($record->cmID) ? getUserNameById('users', 'userID', $record->cmID, 'displayName') : 'N/A');
                            $sheet->setCellValue('D' . $row, ucwords($record->name));
                            $sheet->setCellValue('E' . $row, $record->email);
                            $sheet->setCellValue('F' . $row, $record->mobile);
                            $sheet->setCellValue('G' . $row, $record->pancard);
                            $sheet->setCellValue('H' . $row, nf($record->monthlyIncome));
                            $sheet->setCellValue('I' . $row, getUserNameById('lms_cities', 'cityID', $record->city, 'cityName'));
                            $sheet->setCellValue('J' . $row, $record->customerType);
                            $sheet->setCellValue('K' . $row, $record->utmSource);
                            $sheet->setCellValue('L' . $row, $record->domainURL ?? 'N/A');
                            
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Status_Wise_Exported_Fresh_Leads_Data.xlsx';
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Status Wise Exported Fresh Leads (All Export)',$logData);
                    
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
                    
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Assigned RM');
                    $sheet->setCellValue('C1', 'Assigned CM');
                    $sheet->setCellValue('D1', 'Name');
                    $sheet->setCellValue('E1', 'Email');
                    $sheet->setCellValue('F1', 'Mobile');
                    $sheet->setCellValue('G1', 'Pancard');
                    $sheet->setCellValue('H1', 'Monthly Income');
                    $sheet->setCellValue('I1', 'City');
                    $sheet->setCellValue('J1', 'Employment Type');
                    $sheet->setCellValue('K1', 'UTM Source');
                    $sheet->setCellValue('L1', 'Domain URL');
                    $sheet->setCellValue('M1', 'Date');
                    
                    $row = 2;
                    
                  $query->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $record) {
                          $sheet->setCellValue('A' . $row, ltrim($record->leadID));
                            $sheet->setCellValue('B' . $row, !empty($record->rmID) ? getUserNameById('users', 'userID', $record->rmID, 'displayName') : 'N/A');
                            $sheet->setCellValue('C' . $row, !empty($record->cmID) ? getUserNameById('users', 'userID', $record->cmID, 'displayName') : 'N/A');
                            $sheet->setCellValue('D' . $row, ucwords($record->name));
                            $sheet->setCellValue('E' . $row, $record->email);
                            $sheet->setCellValue('F' . $row, $record->mobile);
                            $sheet->setCellValue('G' . $row, $record->pancard);
                            $sheet->setCellValue('H' . $row, nf($record->monthlyIncome));
                            $sheet->setCellValue('I' . $row, getUserNameById('lms_cities', 'cityID', $record->city, 'cityName'));
                            $sheet->setCellValue('J' . $row, $record->customerType);
                            $sheet->setCellValue('K' . $row, $record->utmSource);
                            $sheet->setCellValue('L' . $row, $record->domainURL ?? 'N/A');
                            
                            $formattedDate = date('Y-m-d', strtotime($record->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Status_Wise_Exported_Fresh_Leads_Data.xlsx';
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads', 'Status Wise Exported Fresh Leads (Date Range Export)', $logData);
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
    
            if (role()=='Sr. Credit Manager' || role()=='Credit Manager') {
                 $userID = getUserID(); //helper to fetch current user logged in ID
                $query->where(function($query) use ($userID) {
                    $query->where('lms_leads.rmID', $userID)
                          ->orWhere('lms_leads.cmID', $userID);
                }); 
            }
    
            // Paginate the results
            $leads = $query->paginate(10);
          
            $rmUsers = DB::table('users')->select('userID', 'displayName')
                ->whereIn('role', ['Relationship Manager'])
                ->where(['leadAssignment' => 1, 'status' => 1])
                ->orderBy('id', 'desc')
                ->get();
            $cmUsers = DB::table('users')->select('userID', 'displayName')
                ->whereIn('role', ['Credit Manager', 'Sr. Credit Manager'])
                ->where('status', 1)
                ->where('leadAssignment', 1)
                ->orderBy('id', 'desc')
                ->get();
            // Prepare other data needed for the view
            $queryParameters = $request->query();
            $filter = $request->filter;
           
            $states = DB::table('lms_states')->where('status', 1)->orderBy('id', 'desc')->get();
            $leadStatus = DB::table('lms_leads_status')->where('status', 1)->orderBy('id', 'desc')->get();
            $page_info = pageInfo('Leads', $status);
            $data = compact('activeTab','leads', 'rmUsers', 'cmUsers', 'states', 'page_info', 'filter', 'leadStatus', 'queryParameters');
    
            return view('leads.statusWise')->with($data);
        } else {
            abort(404);
        }
    }

    public function allLeads(Request $request) {
         
              $query = DB::table('lms_leads')
                ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                ->leftJoin('lms_account_details','lms_leads.leadID', '=', 'lms_account_details.leadID')
                    ->leftJoin('lms_address', function ($join) {
                        $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                             ->where('lms_address.addressType', '=', 'current');
                })
                ->leftJoin('users as cm_users', 'lms_leads.cmID', '=', 'cm_users.userID')
                ->leftJoin('users as rm_users', 'lms_leads.rmID', '=', 'rm_users.userID')
                ->select(
                        'lms_leads.leadID',
                        'lms_leads.status',
                        'lms_contact.pancard',
                        'lms_leads.commingLeadsDate',
                        'lms_leads.rmID',
                        'lms_leads.cmID',
                        'lms_leads.customerType',
                        'lms_contact.name',
                        'lms_contact.email',
                        'lms_contact.mobile',
                        'lms_leads.utmSource',
                        'lms_leads.domainURL',
                        'lms_leads.addedOn',
                        'lms_leads.id',
                        'lms_account_details.monthlyIncome',
                        'lms_address.city',
                        DB::raw('(SELECT addedOn FROM lms_timeline WHERE leadID = lms_leads.leadID ORDER BY addedOn DESC LIMIT 1) as timelineDate')
                    )
                    ->groupBy(
                        'lms_leads.leadID',
                        'lms_leads.status',
                        'lms_contact.pancard',
                        'lms_leads.commingLeadsDate',
                        'lms_leads.rmID',
                        'lms_leads.cmID',
                        'lms_leads.customerType',
                        'lms_contact.name',
                        'lms_contact.email',
                        'lms_contact.mobile',
                        'lms_leads.utmSource',
                        'lms_leads.domainURL',
                        'lms_leads.addedOn',
                        'lms_leads.id',
                        'lms_account_details.monthlyIncome',
                        'lms_address.city'
                    )
                ->orderBy('lms_leads.id', 'desc');


  
                // Apply search filter if sortBySearch filter is selected and search term is provided
                if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('cm_users.displayName', 'like', "%{$search}%")  // Search in cm_users displayName
                              ->orWhere('rm_users.displayName', 'like', "%{$search}%") // Search in rm_users displayName
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }

                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_leads.commingLeadsDate', $today);
                }elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_leads.commingLeadsDate', [$sevenDaysAgo, $today]);
                }elseif ($request->filter == 'sortByThisMonth') {
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
                }elseif($request->filter == 'exportAll'){
 
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                       // Set column headings (Updated according to the provided headers)
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'Status');
                        $sheet->setCellValue('C1', 'Assigned RM');
                        $sheet->setCellValue('D1', 'Assigned CM');
                        $sheet->setCellValue('E1', 'Name');
                        $sheet->setCellValue('F1', 'Email');
                        $sheet->setCellValue('G1', 'Mobile');
                        $sheet->setCellValue('H1', 'Pancard');
                        $sheet->setCellValue('I1', 'Monthly Income');
                        $sheet->setCellValue('J1', 'City');
                        $sheet->setCellValue('K1', 'Employment Type');
                        $sheet->setCellValue('L1', 'Utm Source');
                        $sheet->setCellValue('M1', 'Date'); // Additional column for 'Date'

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->chunk(5000, function ($records) use ($sheet, &$row) {
                           foreach ($records as $record) {
                              
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, $record->status);
                                $sheet->setCellValue('C' . $row, !empty($record->rmID) ? getUserNameById('users', 'userID', $record->rmID, 'displayName') : 'N/A');
                                $sheet->setCellValue('D' . $row, !empty($record->cmID) ? getUserNameById('users', 'userID', $record->cmID, 'displayName') : 'N/A');
                                $sheet->setCellValue('E' . $row, ucwords($record->name));
                                $sheet->setCellValue('F' . $row, $record->email);
                                $sheet->setCellValue('G' . $row, $record->mobile);
                                $sheet->setCellValue('H' . $row, $record->pancard);
                                $sheet->setCellValue('I' . $row, nf($record->monthlyIncome));
                                $sheet->setCellValue('J' . $row, getUserNameById('lms_cities', 'cityID', $record->city, 'cityName'));
                                $sheet->setCellValue('K' . $row, $record->customerType);    
                                $sheet->setCellValue('L' . $row, $record->utmSource);
                                $sheet->setCellValue('M' . $row, dft($record->addedOn));
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'All_Leads_Data.xlsx';
                        
                        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                        actLogs('leads','All Leads Exported (All Export)',$logData);
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
                        $sheet->setCellValue('B1', 'Status');
                        $sheet->setCellValue('C1', 'Assigned RM');
                        $sheet->setCellValue('D1', 'Assigned CM');
                        $sheet->setCellValue('E1', 'Name');
                        $sheet->setCellValue('F1', 'Email');
                        $sheet->setCellValue('G1', 'Mobile');
                        $sheet->setCellValue('H1', 'Pancard');
                        $sheet->setCellValue('I1', 'Monthly Income');
                        $sheet->setCellValue('J1', 'City');
                        $sheet->setCellValue('K1', 'Employment Type');
                        $sheet->setCellValue('L1', 'Utm Source');
                        $sheet->setCellValue('M1', 'Date'); // Additional column for 'Date'

                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, $record->status);
                                $sheet->setCellValue('C' . $row, !empty($record->rmID) ? getUserNameById('users', 'userID', $record->rmID, 'displayName') : 'N/A');
                                $sheet->setCellValue('D' . $row, !empty($record->cmID) ? getUserNameById('users', 'userID', $record->cmID, 'displayName') : 'N/A');
                                $sheet->setCellValue('E' . $row, ucwords($record->name));
                                $sheet->setCellValue('F' . $row, $record->email);
                                $sheet->setCellValue('G' . $row, $record->mobile);
                                $sheet->setCellValue('H' . $row, $record->pancard);
                                $sheet->setCellValue('I' . $row, nf($record->monthlyIncome));
                                $sheet->setCellValue('J' . $row, getUserNameById('lms_cities', 'cityID', $record->city, 'cityName'));
                                $sheet->setCellValue('K' . $row, $record->customerType);    
                                $sheet->setCellValue('L' . $row, $record->utmSource);
                                $sheet->setCellValue('M' . $row, dft($record->addedOn));
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'All_Leads_Data.xlsx';
                        
                        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                        actLogs('leads','All Leads Exported (Date Range Export)',$logData);
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


                // if(isSuperAdmin()){
                    
                // }else{
                //     $userID = getUserID(); //helper to fetch current user logged in ID
                //     $query->where(function($query) use ($userID) {
                //         $query->where('rmID', $userID)
                //               ->orWhere('cmID', $userID);
                //     });
                // }
                
                // Paginate the results
                $leads = $query->paginate(10);
                // Prepare other data needed for the view
                $queryParameters = $request->query();
                $filter = $request->filter;
                $users = DB::table('users')->whereIn('role', ['Relationship Manager'])->where('status', 1)->orderBy('id', 'desc')->get();
                $states = DB::table('lms_states')->where('status', 1)->orderBy('id', 'desc')->get();
                $leadStatus = DB::table('lms_leads_status')->where('status', 1)->orderBy('id', 'desc')->get();
                $page_info = pageInfo('Leads','All Leads');
                $data = compact('leads','users', 'states', 'page_info', 'filter','queryParameters');
                return view('leads.allLeads')->with($data);
          
    }


    public function enquiryList(Request $request) {
        
           
           $query = DB::table('lms_enquiry')
                    // Join with a subquery to get the latest lead for each contact
                    
                    // Select necessary fields from both tables
                    ->select(
                        'lms_enquiry.id',
                        'lms_enquiry.mobile',
                        'lms_enquiry.stage',
                        'lms_enquiry.addedOn'
                    );
                
                // Handling search filter
                if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_enquiry.id', 'like', "%{$search}%")
                              ->orWhere('lms_enquiry.addedOn', 'like', "%{$search}%")
                              ->orWhere('lms_enquiry.mobile', 'like', "%{$search}%");
                    });
                }
 
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_enquiry.addedOn', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_enquiry.addedOn', $today);
                } elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_enquiry.addedOn', [$sevenDaysAgo, $today]);
                }elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_enquiry.addedOn', '=', date('m'))
                          ->whereYear('lms_enquiry.addedOn', '=', date('Y'));
                }elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_enquiry.addedOn', '=', $lastMonth)
                          ->whereYear('lms_enquiry.addedOn', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Mobile');
                        $sheet->setCellValue('C1', 'Added On');
                        $row = 2; // Start row for data

                        // Query Builder chunking
                        DB::table('lms_enquiry')
                        ->orderBy('id','desc') // Ensure you specify a column to order by
                        ->chunk(500, function ($records) use ($sheet, &$row) {
                            foreach ($records as $key =>$record) {
                                $sheet->setCellValue('A' . $row, ++$key);
                                $sheet->setCellValueExplicit('B' . $row,$record->mobile,DataType::TYPE_STRING);
                                $sheet->setCellValue('C' . $row, $record->addedOn);
                                $row++;
                            }
                        });


                        // // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = 'exported_Enquiry_data.xlsx';

                        actLogs('Red Flag','all data export',$request->all());
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
                        $sheet->setCellValue('A1', '#');
                        $sheet->setCellValue('B1', 'Mobile');
                        $sheet->setCellValue('C1', 'Added On');
                        $row = 2; // Start row for data

                        // Query Builder chunking
                        DB::table('lms_enquiry')
                        ->orderBy('id','desc') // Ensure you specify a column to order by
                        ->whereBetween('lms_enquiry.addedOn', [$fromDate, $toDate])
                        ->chunk(500, function ($records) use ($sheet, &$row) {
                           foreach ($records as $key => $record) {

                               

                                $sheet->setCellValue('A' . $row, ++$key);
                                $sheet->setCellValue('B' . $row, $record->mobile);
                                $sheet->setCellValue('C' . $row, $record->addedOn);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = 'exported_enquiry_data.xlsx';

                         actLogs('Red Flag','date wise data export',$request->all());
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
                

                // Paginate the results
                $leads = $query->orderBy('id','desc')->paginate(10);
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo($request->segment(1), $request->segment(2));
                $data = compact('leads','page_info', 'filter','queryParameters');
                return view('leads.enquiryList')->with($data);
            
    }  
 
    public function fetchCities(Request $request) {
        
      
       $stateID = $request->stateID;
    
       // Get the cities based on the stateID
        $cities = DB::table('lms_cities')
            ->select('cityID', 'stateID', 'cityName')
            ->where('stateID', $stateID)
            ->get();
      //  Return the cities as a JSON response
        return response()->json($cities);
    }


    
      
       
    public function fetchRelationshipManagers(Request $request) { 

        $userID = $request->cmID; // assuming the cmID is being passed in the request
        $rmUser = DB::table('users')
            ->join('lms_leads_assignment', 'users.userID', '=', 'lms_leads_assignment.userID') // Join with lms_leads_assignment on userID
            ->select('users.userID', 'users.displayName', 'lms_leads_assignment.oldAssignedRM', 'lms_leads_assignment.newAssignedRM') // Select required columns
            ->where([
                ['users.leadAssignment', '=', 1],
                ['users.status', '=', 1],
                ['lms_leads_assignment.userID', '=', $userID] // Filtering by cmID from the request
            ])
            ->orderBy('lms_leads_assignment.id', 'desc') // Order by 'id' or another field to get the most recent
            ->first(); // Fetch only the most recent record
 
        return response()->json($rmUser); 
         
    }


       public function addLeadManual(Request $request){
           
           if(!empty($request->leadID)){
                
               if (role() == 'CRM Support' || isSuperAdmin()) {
                    $validator =  Validator::make($request->all(),[
                        'nameOnPancard' =>'required|string',
                        'email' =>'required|email',
                        'mobile' =>'required|digits:10',
                        'pancard' => 'required|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
                        'adharNumber' => 'required|regex:/^\d{12}$/',
                        'loanAmount' =>'required|numeric',
                        'monthlySalary' =>'required|numeric',
                      //  'dob' =>'required|date',
                        'gender' =>'required',
                        // 'employmentType' =>'required|string',
                        'state' =>'required',
                        'city' =>'required',
                        'pincode' =>'required|numeric',
                        'purpose' =>'required|string',
                        'rmID' =>'required',
                        'cmID' =>'required',
                    ],['rmID.required'=>'RM required','cmID.required'=>'CM required']); 
               } elseif (isAdmin()) {
                   $validator =  Validator::make($request->all(),[
                        'email' =>'required|email',
                        'mobile' =>'required|digits:10',
                        'rmID' =>'required',
                        'cmID' =>'required',
                    ],['rmID.required'=>'RM required','cmID.required'=>'CM required']); 
                   
               } elseif (role() == 'Credit Manager' || role() == 'Sr. Credit Manager') {
                   $validator =  Validator::make($request->all(),[
                        'rmID' =>'required',
                        'cmID' =>'required',
                    ],['rmID.required'=>'RM required','cmID.required'=>'CM required']); 
               }
           }else{
              $validator =  Validator::make($request->all(),[
                'nameOnPancard' =>'required|string',
                'email' =>'required|email',
                'mobile' =>'required|digits:10',
                'pancard' => 'required|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
                'adharNumber' => 'required|regex:/^\d{12}$/',
                'loanAmount' =>'required|numeric',
                'monthlySalary' =>'required|numeric',
                'dob' =>'required|date',
                'gender' =>'required',
                // 'employmentType' =>'required|string',
                'state' =>'required',
                'city' =>'required',
                'pincode' =>'required|numeric',
                'purpose' =>'required|string',
                'rmID' =>'required',
                'cmID' =>'required',
            ],['rmID.required'=>'RM required','cmID.required'=>'CM required']); 
           }
           
 
            $leadID = !empty($request->leadID) ? $request->leadID : randomNo('lms_leads','leadID');
            $contactID = !empty($request->contactID) ? $request->contactID : randomNo('lms_contact', 'contactID');

             
           if($validator->passes()){
               
                    
                if(!empty($request->leadID)){
                   if (role() == 'CRM Support' || isSuperAdmin()) {
                        $contactData = [
                            'name' => ucwords($request->nameOnPancard),
                            'email' => $request->email,
                            'pancard' => strtoupper($request->pancard),
                            'aadharNo' => strtoupper($request->adharNumber),
                            'mobile' => $request->mobile,
                            'dob' => date('Y-m-d',strtotime($request->dob)),
                            'gender' => $request->gender,
                            'addedBy' => Session::get('userID')
                         ]; 
                         
                         $leadData = [
                            'leadID' => $leadID,
                            'contactID' => $contactID,
                            'loanRequired' => $request->loanAmount,
                           // 'customerType' => $request->employmentType,
                            'customerType' => 'Salaried',
                            'purpose' => $request->purpose,
                            'commingLeadsDate'=> date('Y-m-d'),
                            'rmID' => $request->rmID,
                            'cmID' => $request->cmID,
                            'countedRMID' => null,
                            'countedCMID' => null,
                            'status' => 'Fresh',
                            'ip' => $request->ip(),
                            'addedOn' => dt(),
                            'addedBy' => Session::get('userID')
                         ];

                         $accountDetails = [
                            'monthlyIncome' => $request->monthlySalary,
                          ];
                        
                         $addressData = [
                            'leadID' => $leadID,
                            'contactID' => $contactID,
                            'state' => $request->state,
                            'city' => $request->city,
                            'pincode' => $request->pincode,
                         ];
                   } elseif (isAdmin()) {
                      $contactData = [
                            'email' => $request->email,
                            'mobile' => $request->mobile,
                            'addedBy' => Session::get('userID')
                         ]; 
                         
                      $leadData = [
                            'leadID' => $leadID,
                            'contactID' => $contactID,
                            'rmID' => $request->rmID,
                            'cmID' => $request->cmID,
                            'countedRMID' => null,
                            'countedCMID' => null,
                            'ip' => $request->ip(),
                            'addedOn' => dt(),
                            'addedBy' => Session::get('userID')
                         ];     
                   }elseif (role() == 'Credit Manager' || role() == 'Sr. Credit Manager') {
                        $contactData = [];
                        $leadData = [
                            'leadID' => $leadID,
                            'contactID' => $contactID,
                            'rmID' => $request->rmID,
                            'cmID' => $request->cmID,
                            'countedRMID' => null,
                            'countedCMID' => null,
                            'ip' => $request->ip(),
                            'addedOn' => dt(),
                            'addedBy' => Session::get('userID')
                         ];     
                   }
                }else{
                   
                   $contactData = [
                    'contactID' => $contactID,
                    'name' => ucwords($request->nameOnPancard),
                    'email' => $request->email,
                    'pancard' => strtoupper($request->pancard),
                    'aadharNo' => strtoupper($request->adharNumber),
                    'mobile' => $request->mobile,
                    'dob' => date('Y-m-d',strtotime($request->dob)),
                    'gender' => $request->gender,
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ]; 
                 
              
                 $leadData = [
                    'leadID' => $leadID,
                    'contactID' => $contactID,
                    'loanRequired' => $request->loanAmount,
                     // 'customerType' => $request->employmentType,
                    'customerType' => 'Salaried',
                    'purpose' => $request->purpose,
                    'commingLeadsDate'=> date('Y-m-d'),
                    'rmID' => $request->rmID,
                    'cmID' => $request->cmID,
                    'countedRMID' => null,
                    'countedCMID' => null,
                    'status' => 'Fresh',
                    'ip' => $request->ip(),
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                 ];

                 $accountDetails = [
                    'monthlyIncome' => $request->monthlySalary,
                  ];
                
                 $addressData = [
                    'leadID' => $leadID,
                    'contactID' => $contactID,
                    'state' => $request->state,
                    'city' => $request->city,
                    'pincode' => $request->pincode,
                 ];
                }
                  
                 if(!empty($request->rmID)){
                    $leadData['AssignBy'] = Session::get('userID');
                 }
 
                 $checkExistContact =  DB::table('lms_contact')->where(['pancard'=>$request->pancard,'aadharNo'=>$request->adharNumber])->orderBy('id','desc')->first();

                 //checking contactID is exist if exist then update contact table
                 if($checkExistContact) {
                   
                    unset($contactData['pancard'],$contactData['contactID'],$contactData['adharNumber'],$contactData['addedOn']);
                    actLogs('leads','manual contacts updated',$contactData);
                    DB::table('lms_contact')->where(['pancard'=>$request->pancard,'aadharNo'=>$request->adharNumber])->update($contactData);
                      
                //checking leadID is exist if exist then update leads table
                    $checkExistLead =  DB::table('lms_leads')->where(['leadID'=>$leadID])->orderBy('id','desc')->first();
                    if($checkExistLead){
                       unset($leadData['leadID'],$leadData['contactID'],$leadData['addedOn']);
                       unset($leadData['countedRMID'],$leadData['countedCMID']);
                       actLogs('leads','manual leads updated',$leadData);
                       DB::table('lms_leads')->where(['leadID'=>$leadID])->update($leadData);
                       DB::table('lms_account_details')->where(['leadID'=>$leadID])->update($accountDetails);
                       DB::table('lms_address')->where(['leadID'=>$leadID,'addressType'=>'Current'])->update($addressData);
                    }else{
                       actLogs('leads','manual leads inserted',$leadData);
                       $leadData['contactID'] = $checkExistContact->contactID;
                       DB::table('lms_leads')->insert($leadData);
                    }
                 }else{
                   actLogs('leads','manual contacts inserted',$leadData);
                   actLogs('leads','manual leads inserted',$leadData);
                   DB::table('lms_contact')->insert($contactData);
                   DB::table('lms_leads')->insert($leadData);
                 }
                 return response()->json(['response'=>'success','message'=>'Lead added successfully']);
            }else{
                 return response()->json(['response'=>'failed','error'=>$validator->errors()]);
            }
     }
   

     public function freshLeadEdit(Request $request) {
            $leadID = $request->leadID;
            
            // Fetch lead data from the database
               $leadData = DB::table('lms_leads')
                ->leftjoin('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                ->leftjoin('lms_account_details', 'lms_leads.leadID', '=', 'lms_account_details.leadID')
                ->leftJoin('lms_address', function ($join) {
                    $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                     ->where('lms_address.addressType', '=', 'current');
                })
                ->select('lms_leads.*', 'lms_contact.*','lms_account_details.monthlyIncome','lms_address.state','lms_address.city','lms_address.pincode')
                ->where('lms_leads.status', 'Fresh')
                ->where('lms_leads.leadID', $leadID)
                ->first();

            // Check if lead data exists
            if (!$leadData) {
                return response()->json(['error' => 'Lead not found'], 404);
            }

            // Return lead data as JSON response
            return response()->json($leadData);
        }

        public function leadsDelete(Request $request) {
            $leadIDs = $request->leadIDs;
            $query = DB::table('lms_leads')->whereIn('leadID',$leadIDs)->delete();
            if ($query) {
                actLogs('leads','leads deleted',$request->all());
                return response()->json(['response'=>'success','message'=>'Lead deleted successfully']);
            }
                return response()->json(['response'=>'error','message'=>'Lead failed to delete']);
        }

        public function leadsTransfer(Request $request) {
                $validator = Validator::make($request->all(), [
                    'rmTransferID' => 'required',
                    'cmTransferID' => 'required',
                    'leadIDs' => 'required',
                ], [
                    'rmTransferID.required' => 'RM required',
                    'cmTransferID.required' => 'CM required'
                ]);

                if ($validator->passes()) {
                    // Decode leadIDs if it's a JSON string
                    $leadIDs = is_string($request->leadIDs) ? json_decode($request->leadIDs, true) : $request->leadIDs;

                    // Fetch the old lead data
                    $fetchOldLeadData = DB::table('lms_leads')->select('leadID','rmID', 'cmID')->whereIn('leadID', $leadIDs)->get();
 
                    // Update the leads
                    $query = DB::table('lms_leads')->whereIn('leadID', $leadIDs)->update([
                        'rmID' => $request->rmTransferID,
                        'cmID' => $request->cmTransferID,
                        'countedRMID' => null,
                        'countedCMID' => null
                    ]);
 
                    if ($query) {
                        actLogs('leads', 'leads Transfer', $request->all());
 
                        foreach ($fetchOldLeadData as $key => $arr) {
                            $data = [
                                'leadID' => $arr->leadID,
                                'assignedRM' => $arr->rmID,
                                'assignedCM' => $arr->cmID,
                                'transferRM' => $request->rmTransferID,
                                'transferCM' => $request->cmTransferID, // Corrected to transferCM
                                'transferBy' => Session::get('userID'),
                                'ip' => $request->ip(),
                                'date' => date('Y-m-d'),
                                'addedOn' => dt(), 
                            ];
                            DB::table('lms_lead_transfer_activity_log')->insert($data);
                        }

                        return response()->json(['response' => 'success', 'message' => 'Leads Transfer successfully']);
                    }
                } else {
                    return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
                }
            }



        


    // Function for verify pancard starts
            
    public function verifyPancard(Request $request)
    {   
       
        $request->validate([
            'pan' => 'required|string'
        ]);

        // Make the request to the external API
        $response = Http::withHeaders([
            'Authorization' => 'qIOpYzj9svs3Jqsa0KWBGS5QEBiNnmhl',
            'Content-Type' => 'application/json'
        ])->post('https://api-preproduction.signzy.app/api/v3/panextensive', [
            'panNumber' => $request->input('pan')
        ]);

        // Return the response from the external API
        return response()->json($response->json(), $response->status());
                    
    }
    // Function for verify pancard ends


    public function importLeads(Request $request){

            $validator = Validator::make($request->all(), [
             'importLeadFile' => 'required|file|mimes:xlsx,xls',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $file = $request->file('importLeadFile');

            // Load the spreadsheet
            $spreadsheet = IOFactory::load($file->getPathName());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $dataRows = array_slice($rows, 1);

               foreach ($dataRows as $column) {
                     $checkExistContact =  DB::table('lms_contact')->where(['pancard'=>$column[0]])->first();

                     $contactID = !empty($checkExistContact->contactID) ? $checkExistContact->contactID : randomNo(100000, 999999);   
                     $contactData = [
                        'contactID' => $contactID,
                        'name' => ucwords($column[1]),
                        'email' => $column[2],
                        'pancard' => strtoupper($column[0]),
                        'aadharNo' => strtoupper($column[3]),
                        'mobile' => $column[4],
                        'dob' => date('Y-m-d',strtotime($column[5])),
                        'gender' => $column[6],
                        'addedOn' => dt(),
                        'addedBy' => Session::get('userID')
                    ];

                    $leadData = [
                        'leadID' => randomNo(100000, 999999),
                        'contactID' => $contactID,
                        'state' => $column[12],
                        'city' => $column[11],
                        'pincode' => $column[13],
                        'loanRequired' => $column[9],
                        'monthlyIncome' => $column[10],
                        'customerType' => $column[14],
                        'purpose' => ucwords($column[8]),
                        'commingLeadsDate'=> date('Y-m-d',strtotime($column[15])),
                        'rmID' => $column[7],
                        'AssignBy' => Session::get('userID'),
                        'status' => 'Fresh',
                        'ip' => $request->ip(),
                        'addedOn' => dt(),
                        'addedBy' => Session::get('userID')
                    ];
                    

                   if($checkExistContact){

                        actLogs('leads','contacts imported then updated',$contactData);
                        DB::table('lms_contact')->where(['pancard'=>$column[0]])->update($contactData);
                   }else{
                        actLogs('leads','contacts imported then inserted',$contactData);
                        DB::table('lms_contact')->insert($contactData);
                   }    

                     $query =  DB::table('lms_leads')->insert($leadData);
                      if ($query) {
                        actLogs('leads','contacts imported then inserted',$leadData);
                        return response()->json(['response'=>'success','message'=>'Lead imported successfully']);
                      }
                        return response()->json(['response'=>'error','message'=>'Lead failed to import']);
           
           }
    }   


    public function rmList(Request $request){
        $data['rm_list'] = DB::table('users')
            ->select(
                'users.userID',
                'users.displayName',
                'lms_users_details.department',
                'lms_users_details.designation',
                'users.leadAssignment',
                'latest_assignments.oldAssignedCM',
                'latest_assignments.newAssignedCM',
                'latest_assignments.id'
            )
            ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
            ->leftJoin(
                DB::raw('(SELECT userID, oldAssignedCM, newAssignedCM, id
                          FROM lms_leads_assignment
                          WHERE id = (SELECT MAX(id) FROM lms_leads_assignment AS l2 WHERE l2.userID = lms_leads_assignment.userID)
                        ) AS latest_assignments'),
                'users.userID', '=', 'latest_assignments.userID'
            )
            ->whereIn('users.role', ['Relationship Manager'])
            ->where('users.status', 1)
            ->orderBy('users.id', 'desc')
            ->get();
           
         
           $data['cm_list'] = DB::table('users')->select('users.userID', 'users.displayName', 'lms_users_details.department', 'lms_users_details.designation', 'users.leadAssignment')->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')->whereIn('users.role', ['Credit Manager', 'Sr. Credit Manager'])->where('users.status', 1)->orderBy('users.id', 'desc')->get();

           $data['page_info'] = pageInfo('Relationship Managers List',$request->segment(1));
           return view('leads.rmList')->with($data); 
    }

    public function cmList(Request $request){
             $data['cm_list'] = DB::table('users')
                ->select(
                    'users.userID',
                    'users.displayName',
                    'lms_users_details.department',
                    'lms_users_details.designation',
                    'users.leadAssignment',
                    'latest_assignments.oldAssignedRM',
                    'latest_assignments.newAssignedRM',
                    'latest_assignments.id'
                )
                ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                ->leftJoin(
                    DB::raw('(SELECT userID, oldAssignedRM, newAssignedRM, id
                              FROM lms_leads_assignment
                              WHERE id = (SELECT MAX(id) FROM lms_leads_assignment AS l2 WHERE l2.userID = lms_leads_assignment.userID)
                            ) AS latest_assignments'),
                    'users.userID', '=', 'latest_assignments.userID'
                )
                ->whereIn('users.role', ['Credit Manager', 'Sr. Credit Manager'])
                ->where('users.status', 1)
                ->get();
 
           $data['rm_list'] = DB::table('users')->select('users.userID', 'users.displayName', 'lms_users_details.department', 'lms_users_details.designation', 'users.leadAssignment')->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')->whereIn('users.role', ['Relationship Manager'])->where('users.status', 1)->orderBy('users.id', 'desc')->get();
          
           $data['page_info'] = pageInfo('Credit Managers List',$request->segment(1));
           return view('leads.cmList')->with($data); 
    }
    
    
   public function assignRM(Request $request){
            
            $validator = Validator::make($request->all(), [
                'userID' => 'required', // Ensure userID exists
                'newAssignedRM' => 'required', // Ensure assignedRM is provided
            ]);
        
         
            if ($validator->passes()) {
                $userID = $request->userID;
                $leadAssignment = DB::table('lms_leads_assignment')->where('userID', $userID)->count();
                
          
                if ($leadAssignment > 0) {
                  $dataExist = [
                        'userID'=>$userID,
                        'name'=>$request->name,
                        'userType'=>$request->userType,
                        'oldAssignedRM'=>$request->oldAssignedRM,
                        'newAssignedRM'=>$request->newAssignedRM,
                        'addedOn'=>dt(),
                        ];
                    DB::table('lms_leads_assignment')->insert($dataExist);
                    actLogs('Leads Assignment','Leads Assigned RM',$dataExist);
                    return response()->json(['response'=>'success','message'=>'RM assigned successfully']);
                }else{
                    $dataNotExist = [
                        'userID'=>$userID,
                        'name'=>$request->name,
                        'userType'=>$request->userType,
                        'oldAssignedRM'=>$request->newAssignedRM,
                        'addedOn'=>dt(),
                        ];
                    DB::table('lms_leads_assignment')->insert($dataNotExist);
                    actLogs('Leads Assignment','Leads Assigned RM',$dataNotExist);
                    return response()->json(['response'=>'success','message'=>'RM assigned successfully']);
                }
            } else {
                return response()->json(['response' => 'failed','error' => $validator->errors()]);
            }
        }

    public function assignCM(Request $request){
            
            $validator = Validator::make($request->all(), [
                'userID' => 'required', // Ensure userID exists
                'newAssignedCM' => 'required', // Ensure assignedRM is provided
            ]);
        
         
            if ($validator->passes()) {
                $userID = $request->userID;
                $leadAssignment = DB::table('lms_leads_assignment')->where('userID', $userID)->count();
                if ($leadAssignment > 0) {
                  $dataExist = [
                        'userID'=>$userID,
                        'name'=>$request->name,
                        'userType'=>$request->userType,
                        'oldAssignedCM'=> $request->oldAssignedCM,
                        'newAssignedCM'=>implode(',',$request->newAssignedCM),
                        'addedOn'=>dt(),
                        ];
                    DB::table('lms_leads_assignment')->insert($dataExist);
                    actLogs('Leads Assignment','Leads Assigned CM',$dataExist);
                    return response()->json(['response'=>'success','message'=>'CM assigned successfully']);
                }else{
                    $dataNotExist = [
                        'userID'=>$userID,
                        'name'=>$request->name,
                        'userType'=>$request->userType,
                        'oldAssignedCM'=>implode(',',$request->newAssignedCM),
                        'addedOn'=>dt(),
                        ];
                    DB::table('lms_leads_assignment')->insert($dataNotExist);
                    actLogs('Leads Assignment','Leads Assigned CM',$dataNotExist);
                    return response()->json(['response'=>'success','message'=>'CM assigned successfully']);
                }
            } else {
                return response()->json(['response' => 'failed','error' => $validator->errors()]);
            }
        }

     public function leadAssignmentRMstatusUpdate(Request $request){ 
            $query =DB::table('users')->where('userID',$request->userID)->update(['leadAssignment'=>$request->status]); 
            if($query){
                actLogs('lead Assignment RM','status updated',$request->all());
                return response()->json(['response'=>'success','message'=>'Lead Assignment status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Lead Assignment status updation failed']);
            }
    }

    public function leadAssignmentCMstatusUpdate(Request $request){ 
            $query =DB::table('users')->where('userID',$request->userID)->update(['leadAssignment'=>$request->status]); 
            if($query){
                actLogs('lead Assignment CM','status updated',$request->all());
                return response()->json(['response'=>'success','message'=>'Lead Assignment status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Lead Assignment status updation failed']);
            }
    }



    public function approvalMatrixLeads(Request $request) {
               
                $query = DB::table('lms_leads')
                        ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                        ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                        ->leftJoin('users as cm_users', 'lms_leads.cmID', '=', 'cm_users.userID') // Left Join with users for cmID
                        ->leftJoin('users as rm_users', 'lms_leads.rmID', '=', 'rm_users.userID')  // Left Join with users for rmID
                        ->select('lms_leads.*', 'lms_contact.*', 'lms_approval.*')
                        ->where('lms_approval.loanAmtApproved', '>' ,50000)
                        ->orderBy('lms_approval.id', 'desc');

                 if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('cm_users.displayName', 'like', "%{$search}%")  // Search in cm_users displayName
                              ->orWhere('rm_users.displayName', 'like', "%{$search}%") // Search in rm_users displayName
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%");
                    });
                }

                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
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
                }

                // Paginate the results
                $leads = $query->paginate(10);
                // Prepare other data needed for the view
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
                $data = compact('leads','page_info','filter','queryParameters');
                 return view('leads.approvalMatrixLeads')->with($data);
            }   
    

}
