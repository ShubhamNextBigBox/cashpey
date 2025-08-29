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

class CollectionController extends Controller
{
   public function partPaymentLeads(Request $request){
        $query = DB::table('lms_leads')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->join('lms_collection', 'lms_leads.leadID', '=', 'lms_collection.leadID')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->where(['lms_collection.status'=>'Part Payment'])
                    ->select('lms_collection.id','lms_leads.leadID','lms_leads.contactID','lms_collection.status','lms_contact.name','lms_contact.mobile','lms_contact.email','lms_contact.pancard','lms_collection.collectedAmount','lms_collection.collectedMode','lms_collection.collectedDate','lms_collection.collectionUtrNo','lms_collection.discountAmount','lms_collection.settlementAmount','lms_collection.addedOn')
                    ->distinct('lms_collection.id')
                    ->orderBy('lms_collection.id','DESC');


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
            $query->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]);
        } elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $query->whereDate('lms_collection.collectedDate', $today);
        }elseif ($request->filter == 'sortByWeek') {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $query->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);
        }elseif ($request->filter == 'sortByThisMonth') {
            $query->whereMonth('lms_collection.collectedDate', '=', date('m'))
                  ->whereYear('lms_collection.collectedDate', '=', date('Y'));
        } elseif ($request->filter == 'sortByLastMonth') {
            $lastMonth = date('m') - 1;
            $lastMonthYear = date('Y');
            if ($lastMonth == 0) {
                $lastMonth = 12;
                $lastMonthYear = date('Y') - 1;
            }
            $query->whereMonth('lms_collection.collectedDate', '=', $lastMonth)
                  ->whereYear('lms_collection.collectedDate', '=', $lastMonthYear);
        }elseif ($request->filter == 'exportAll') {
                
                 // Create a new Spreadsheet object
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    // Set new headers based on your updated requirements
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Name');
                    $sheet->setCellValue('C1', 'Email');
                    $sheet->setCellValue('D1', 'Mobile');
                    $sheet->setCellValue('E1', 'Pancard');
                    $sheet->setCellValue('F1', 'Payment Amount');
                    $sheet->setCellValue('G1', 'Payment Mode');
                    $sheet->setCellValue('H1', 'Payment Date');
                    $sheet->setCellValue('I1', 'Reference No.');
                    $sheet->setCellValue('J1', 'Discount Amount');
                    $sheet->setCellValue('K1', 'Settlement Amount');
                    $sheet->setCellValue('L1', 'Status');
                    $sheet->setCellValue('M1', 'Created Date');
                    
                    // Start populating data from row 2
                    $row = 2;
                    
                    // Fetch data in chunks
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $arr) {
                            // Fill the data in the spreadsheet
                            $sheet->setCellValue('A' . $row, $arr->leadID); // Lead ID
                            $sheet->setCellValue('B' . $row, ucwords($arr->name)); // Name (formatted)
                            $sheet->setCellValue('C' . $row, $arr->email); // Email
                            $sheet->setCellValue('D' . $row, $arr->mobile); // Mobile
                            $sheet->setCellValue('E' . $row, $arr->pancard); // Pancard
                            $sheet->setCellValue('F' . $row, nf($arr->collectedAmount)); // Payment Amount (formatted)
                            $sheet->setCellValue('G' . $row, $arr->collectedMode); // Payment Mode
                            
                            $formattedDate = date('Y-m-d', strtotime($arr->collectedDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('H' . $row, $excelDate);  
                            $sheet->getStyle('H' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            $sheet->setCellValueExplicit('I' . $row, str_pad($arr->collectionUtrNo,7, '', STR_PAD_LEFT), DataType::TYPE_STRING);
                            $sheet->setCellValue('J' . $row, nf($arr->discountAmount)); // Discount Amount (formatted)
                            $sheet->setCellValue('K' . $row, nf($arr->settlementAmount)); // Settlement Amount (formatted)
                            $sheet->setCellValue('L' . $row, $arr->status); // Status
                            $formattedDate = date('Y-m-d', strtotime($arr->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Cash_Pending_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Cash_Pending_Exported Leads (All Export)',$logData);
                    
                    // Return the response for the download
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
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Name');
                    $sheet->setCellValue('C1', 'Email');
                    $sheet->setCellValue('D1', 'Mobile');
                    $sheet->setCellValue('E1', 'Pancard');
                    $sheet->setCellValue('F1', 'Payment Amount');
                    $sheet->setCellValue('G1', 'Payment Mode');
                    $sheet->setCellValue('H1', 'Payment Date');
                    $sheet->setCellValue('I1', 'Reference No.');
                    $sheet->setCellValue('J1', 'Discount Amount');
                    $sheet->setCellValue('K1', 'Settlement Amount');
                    $sheet->setCellValue('L1', 'Status');
                    $sheet->setCellValue('M1', 'Created Date');
                    
                    // Start populating data from row 2
                    $row = 2;
                    
                    // Fetch data in chunks
                      $query->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $arr) {
                            // Fill the data in the spreadsheet
                            $sheet->setCellValue('A' . $row, $arr->leadID); // Lead ID
                            $sheet->setCellValue('B' . $row, ucwords($arr->name)); // Name (formatted)
                            $sheet->setCellValue('C' . $row, $arr->email); // Email
                            $sheet->setCellValue('D' . $row, $arr->mobile); // Mobile
                            $sheet->setCellValue('E' . $row, $arr->pancard); // Pancard
                            $sheet->setCellValue('F' . $row, nf($arr->collectedAmount)); // Payment Amount (formatted)
                            $sheet->setCellValue('G' . $row, $arr->collectedMode); // Payment Mode
                            
                            $formattedDate = date('Y-m-d', strtotime($arr->collectedDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('H' . $row, $excelDate);  
                            $sheet->getStyle('H' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $sheet->setCellValueExplicit('I' . $row, str_pad($arr->collectionUtrNo,7, '', STR_PAD_LEFT), DataType::TYPE_STRING);

                            $sheet->setCellValue('J' . $row, nf($arr->discountAmount)); // Discount Amount (formatted)
                            $sheet->setCellValue('K' . $row, nf($arr->settlementAmount)); // Settlement Amount (formatted)
                            $sheet->setCellValue('L' . $row, $arr->status); // Status
                            
                            $formattedDate = date('Y-m-d', strtotime($arr->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $row++;
                        }
                    });
                    
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Cash_Pending_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Cash_Pending_Exported Leads (Date Range Export)',$logData);
                    
                    // Return the response for the download
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


        if (isSuperAdmin() || isAdmin()) {
            // No user-specific filter needed for admins
        }elseif(role()=='Credit Manager' || role()=='Sr. Credit Manager') {
            $userID = getUserID(); //helper to fetch current user logged in ID
            $query->where(function($query) use ($userID) {
                $query->Where('lms_leads.cmID', $userID);
            });
        }elseif(role()=='Recovery Manager') {
            $userID = getUserID(); // Fetch current logged-in user ID
            $userDetails = DB::table('lms_users_details')->select('branch')->where('userID',$userID)->orderBy('id','desc')->first();
            $branchIDs = explode(',', $userDetails->branch);
            $query->where(function($query) use ($branchIDs) {
                $query->whereIn('lms_approval.branch', $branchIDs);
            });
        }
        // Paginate the results
        $leads = $query->paginate(10);
        // Prepare other data needed for the view
        $queryParameters = $request->query();
        $filter = $request->filter;
        $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
        $data = compact('leads','page_info','filter','queryParameters');
        return view('collection.partPaymentLeads')->with($data); 
    }        

   public function closedLeads(Request $request){
        $query = DB::table('lms_leads')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->join('lms_collection', 'lms_leads.leadID', '=', 'lms_collection.leadID')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->whereIn('lms_collection.status',['Closed'])
                    ->select('lms_leads.leadID','lms_leads.contactID','lms_collection.status','lms_contact.name','lms_contact.mobile','lms_contact.email','lms_contact.pancard','lms_collection.collectedAmount','lms_collection.collectedMode','lms_collection.collectedDate','lms_collection.collectionUtrNo','lms_collection.discountAmount','lms_collection.settlementAmount','lms_collection.addedOn')
                    ->orderBy('lms_collection.id','DESC');

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
            $query->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]);
        } elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $query->whereDate('lms_collection.collectedDate', $today);
        }elseif ($request->filter == 'sortByWeek') {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $query->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);
        } elseif ($request->filter == 'sortByThisMonth') {
            $query->whereMonth('lms_collection.collectedDate', '=', date('m'))
                  ->whereYear('lms_collection.collectedDate', '=', date('Y'));
        } elseif ($request->filter == 'sortByLastMonth') {
            $lastMonth = date('m') - 1;
            $lastMonthYear = date('Y');
            if ($lastMonth == 0) {
                $lastMonth = 12;
                $lastMonthYear = date('Y') - 1;
            }
            $query->whereMonth('lms_collection.collectedDate', '=', $lastMonth)
                  ->whereYear('lms_collection.collectedDate', '=', $lastMonthYear);
        }elseif ($request->filter == 'exportAll') {
                
                 // Create a new Spreadsheet object
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    // Set new headers based on your updated requirements
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Name');
                    $sheet->setCellValue('C1', 'Email');
                    $sheet->setCellValue('D1', 'Mobile');
                    $sheet->setCellValue('E1', 'Pancard');
                    $sheet->setCellValue('F1', 'Payment Amount');
                    $sheet->setCellValue('G1', 'Payment Mode');
                    $sheet->setCellValue('H1', 'Payment Date');
                    $sheet->setCellValue('I1', 'Reference No.');
                    $sheet->setCellValue('J1', 'Discount Amount');
                    $sheet->setCellValue('K1', 'Settlement Amount');
                    $sheet->setCellValue('L1', 'Status');
                    $sheet->setCellValue('M1', 'Created Date');
                    
                    // Start populating data from row 2
                    $row = 2;
                    
                    // Fetch data in chunks
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $arr) {
                            // Fill the data in the spreadsheet
                            $sheet->setCellValue('A' . $row, $arr->leadID); // Lead ID
                            $sheet->setCellValue('B' . $row, ucwords($arr->name)); // Name (formatted link)
                            $sheet->setCellValue('C' . $row, $arr->email); // Email
                            $sheet->setCellValue('D' . $row, $arr->mobile); // Mobile
                            $sheet->setCellValue('E' . $row, $arr->pancard); // Pancard
                            $sheet->setCellValue('F' . $row, nf($arr->collectedAmount)); // Payment Amount (formatted)
                            $sheet->setCellValue('G' . $row, $arr->collectedMode); // Payment Mode
                            
                            // Format the Payment Date (collectedDate)
                            $formattedDate = date('Y-m-d', strtotime($arr->collectedDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('H' . $row, $excelDate);  
                            $sheet->getStyle('H' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Format Reference No. (collectionUtrNo) to be a string with padding
                            $sheet->setCellValueExplicit('I' . $row, str_pad($arr->collectionUtrNo, 7, '', STR_PAD_LEFT), DataType::TYPE_STRING);
                            
                            // Format Discount Amount (discountAmount)
                            $sheet->setCellValue('J' . $row, nf($arr->discountAmount)); // Discount Amount (formatted)
                            
                            // Format Settlement Amount (settlementAmount)
                            $sheet->setCellValue('K' . $row, nf($arr->settlementAmount)); // Settlement Amount (formatted)
                            
                            // Status
                            $sheet->setCellValue('L' . $row, $arr->status); // Status
                            
                            // Format Created Date (addedOn)
                            $formattedDate = date('Y-m-d', strtotime($arr->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Move to the next row
                            $row++;
                        }
                    });
                    
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Cash_Pending_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Cash_Pending_Exported Leads (All Export)',$logData);
                    
                    // Return the response for the download
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
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Name');
                    $sheet->setCellValue('C1', 'Email');
                    $sheet->setCellValue('D1', 'Mobile');
                    $sheet->setCellValue('E1', 'Pancard');
                    $sheet->setCellValue('F1', 'Payment Amount');
                    $sheet->setCellValue('G1', 'Payment Mode');
                    $sheet->setCellValue('H1', 'Payment Date');
                    $sheet->setCellValue('I1', 'Reference No.');
                    $sheet->setCellValue('J1', 'Discount Amount');
                    $sheet->setCellValue('K1', 'Settlement Amount');
                    $sheet->setCellValue('L1', 'Status');
                    $sheet->setCellValue('M1', 'Created Date');
                    
                    // Start populating data from row 2
                    $row = 2;
                    
                    // Fetch data in chunks
                      $query->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $arr) {
                            // Fill the data in the spreadsheet
                            $sheet->setCellValue('A' . $row, $arr->leadID); // Lead ID
                            $sheet->setCellValue('B' . $row, ucwords($arr->name)); // Name (formatted link)
                            $sheet->setCellValue('C' . $row, $arr->email); // Email
                            $sheet->setCellValue('D' . $row, $arr->mobile); // Mobile
                            $sheet->setCellValue('E' . $row, $arr->pancard); // Pancard
                            $sheet->setCellValue('F' . $row, nf($arr->collectedAmount)); // Payment Amount (formatted)
                            $sheet->setCellValue('G' . $row, $arr->collectedMode); // Payment Mode
                            
                            // Format the Payment Date (collectedDate)
                            $formattedDate = date('Y-m-d', strtotime($arr->collectedDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('H' . $row, $excelDate);  
                            $sheet->getStyle('H' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Format Reference No. (collectionUtrNo) to be a string with padding
                            $sheet->setCellValueExplicit('I' . $row, str_pad($arr->collectionUtrNo, 7, '', STR_PAD_LEFT), DataType::TYPE_STRING);
                            
                            // Format Discount Amount (discountAmount)
                            $sheet->setCellValue('J' . $row, nf($arr->discountAmount)); // Discount Amount (formatted)
                            
                            // Format Settlement Amount (settlementAmount)
                            $sheet->setCellValue('K' . $row, nf($arr->settlementAmount)); // Settlement Amount (formatted)
                            
                            // Status
                            $sheet->setCellValue('L' . $row, $arr->status); // Status
                            
                            // Format Created Date (addedOn)
                            $formattedDate = date('Y-m-d', strtotime($arr->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Move to the next row
                            $row++;
                        }
                    });
                    
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Cash_Pending_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Cash_Pending_Exported Leads (Date Range Export)',$logData);
                    
                    // Return the response for the download
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



       if (isSuperAdmin() || isAdmin()) {
            // No user-specific filter needed for admins
        }elseif(role()=='Credit Manager' || role()=='Sr. Credit Manager') {
            $userID = getUserID(); //helper to fetch current user logged in ID
            $query->where(function($query) use ($userID) {
                $query->Where('lms_leads.cmID', $userID);
            });
        }elseif(role()=='Recovery Manager') {
            $userID = getUserID(); // Fetch current logged-in user ID
            $userDetails = DB::table('lms_users_details')->select('branch')->where('userID',$userID)->orderBy('id','desc')->first();
            $branchIDs = explode(',', $userDetails->branch);
            $query->where(function($query) use ($branchIDs) {
                $query->whereIn('lms_approval.branch', $branchIDs);
            });
        }
        // Paginate the results
        $leads = $query->paginate(10);
        // Prepare other data needed for the view
        $queryParameters = $request->query();
        $filter = $request->filter;
        $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
        $data = compact('leads','page_info','filter','queryParameters');
        return view('collection.closedLeads')->with($data); 
    }        
    
   public function settlementLeads(Request $request){

        $query = DB::table('lms_leads')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->join('lms_collection', 'lms_leads.leadID', '=', 'lms_collection.leadID')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->where('lms_collection.status','Settlement')
                    ->select('lms_leads.leadID','lms_leads.contactID','lms_collection.status','lms_contact.name','lms_contact.mobile','lms_contact.email','lms_contact.pancard','lms_collection.collectedAmount','lms_collection.collectedMode','lms_collection.collectedDate','lms_collection.collectionUtrNo','lms_collection.discountAmount','lms_collection.settlementAmount','lms_collection.addedOn')
                    ->orderBy('lms_collection.id','DESC');


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
            $query->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate]);
        } elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $query->whereDate('lms_collection.collectedDatee', $today);
        }elseif ($request->filter == 'sortByWeek') {
            $today = date('Y-m-d');
            $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
            $query->whereBetween('lms_collection.collectedDate', [$sevenDaysAgo, $today]);
        } elseif ($request->filter == 'sortByThisMonth') {
            $query->whereMonth('lms_collection.collectedDate', '=', date('m'))
                  ->whereYear('lms_collection.collectedDate', '=', date('Y'));
        } elseif ($request->filter == 'sortByLastMonth') {
            $lastMonth = date('m') - 1;
            $lastMonthYear = date('Y');
            if ($lastMonth == 0) {
                $lastMonth = 12;
                $lastMonthYear = date('Y') - 1;
            }
            $query->whereMonth('lms_collection.collectedDate', '=', $lastMonth)
                  ->whereYear('lms_collection.collectedDate', '=', $lastMonthYear);
        }elseif ($request->filter == 'exportAll') {
                
                 // Create a new Spreadsheet object
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    
                    // Set new headers based on your updated requirements
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Name');
                    $sheet->setCellValue('C1', 'Email');
                    $sheet->setCellValue('D1', 'Mobile');
                    $sheet->setCellValue('E1', 'Pancard');
                    $sheet->setCellValue('F1', 'Payment Amount');
                    $sheet->setCellValue('G1', 'Payment Mode');
                    $sheet->setCellValue('H1', 'Payment Date');
                    $sheet->setCellValue('I1', 'Reference No.');
                    $sheet->setCellValue('J1', 'Discount Amount');
                    $sheet->setCellValue('K1', 'Settlement Amount');
                    $sheet->setCellValue('L1', 'Status');
                    $sheet->setCellValue('M1', 'Created Date');
                    
                    // Start populating data from row 2
                    $row = 2;
                    
                    // Fetch data in chunks
                    $query->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $arr) {
                            // Fill the data in the spreadsheet
                             $sheet->setCellValue('A' . $row, $arr->leadID);
        
                                // Name with link and formatted as ucwords
                                $sheet->setCellValue('B' . $row, ucwords($arr->name));
                                
                                // Email
                                $sheet->setCellValue('C' . $row, $arr->email);
                                
                                // Mobile
                                $sheet->setCellValue('D' . $row, $arr->mobile);
                                
                                // Pancard
                                $sheet->setCellValue('E' . $row, $arr->pancard);
                                
                                // Payment Amount (formatted)
                                $sheet->setCellValue('F' . $row, nf($arr->collectedAmount));
                                
                                // Payment Mode
                                $sheet->setCellValue('G' . $row, $arr->collectedMode);
                                
                                // Format Payment Date (collectedDate)
                                $formattedDate = date('Y-m-d', strtotime($arr->collectedDate));
                                $excelDate = Date::PHPToExcel($formattedDate);
                                $sheet->setCellValue('H' . $row, $excelDate);
                                $sheet->getStyle('H' . $row)
                                    ->getNumberFormat()
                                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                                
                                // Reference No. (collectionUtrNo) - padded to 7 digits and treated as a string
                                $sheet->setCellValueExplicit('I' . $row, str_pad($arr->collectionUtrNo, 7, '0', STR_PAD_LEFT), DataType::TYPE_STRING);
                                
                                // Discount Amount (formatted)
                                $sheet->setCellValue('J' . $row, nf($arr->discountAmount));
                                
                                // Settlement Amount (formatted)
                                $sheet->setCellValue('K' . $row, nf($arr->settlementAmount));
                                
                                // Status
                                $sheet->setCellValue('L' . $row, $arr->status);
                                
                                // Format Created Date (addedOn)
                                $formattedDate = date('Y-m-d', strtotime($arr->addedOn));
                                $excelDate = Date::PHPToExcel($formattedDate);
                                $sheet->setCellValue('M' . $row, $excelDate);
                                $sheet->getStyle('M' . $row)
                                    ->getNumberFormat()
                                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                                
                                // Move to the next row
                                $row++;
                        }
                    });
                    
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Cash_Pending_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Cash_Pending_Exported Leads (All Export)',$logData);
                    
                    // Return the response for the download
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
                    $sheet->setCellValue('A1', 'Lead ID');
                    $sheet->setCellValue('B1', 'Name');
                    $sheet->setCellValue('C1', 'Email');
                    $sheet->setCellValue('D1', 'Mobile');
                    $sheet->setCellValue('E1', 'Pancard');
                    $sheet->setCellValue('F1', 'Payment Amount');
                    $sheet->setCellValue('G1', 'Payment Mode');
                    $sheet->setCellValue('H1', 'Payment Date');
                    $sheet->setCellValue('I1', 'Reference No.');
                    $sheet->setCellValue('J1', 'Discount Amount');
                    $sheet->setCellValue('K1', 'Settlement Amount');
                    $sheet->setCellValue('L1', 'Status');
                    $sheet->setCellValue('M1', 'Created Date');
                    
                    // Start populating data from row 2
                    $row = 2;
                    
                    // Fetch data in chunks
                      $query->whereBetween('lms_collection.collectedDate', [$fromDate, $toDate])->chunk(5000, function ($records) use ($sheet, &$row) {
                        foreach ($records as $arr) {
                            // Fill the data in the spreadsheet
                            $sheet->setCellValue('A' . $row, $arr->leadID); // Lead ID
                            $sheet->setCellValue('B' . $row, ucwords($arr->name)); // Name (formatted link)
                            $sheet->setCellValue('C' . $row, $arr->email); // Email
                            $sheet->setCellValue('D' . $row, $arr->mobile); // Mobile
                            $sheet->setCellValue('E' . $row, $arr->pancard); // Pancard
                            $sheet->setCellValue('F' . $row, nf($arr->collectedAmount)); // Payment Amount (formatted)
                            $sheet->setCellValue('G' . $row, $arr->collectedMode); // Payment Mode
                            
                            // Format the Payment Date (collectedDate)
                            $formattedDate = date('Y-m-d', strtotime($arr->collectedDate));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('H' . $row, $excelDate);  
                            $sheet->getStyle('H' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Format Reference No. (collectionUtrNo) to be a string with padding
                            $sheet->setCellValueExplicit('I' . $row, str_pad($arr->collectionUtrNo, 7, '', STR_PAD_LEFT), DataType::TYPE_STRING);
                            
                            // Format Discount Amount (discountAmount)
                            $sheet->setCellValue('J' . $row, nf($arr->discountAmount)); // Discount Amount (formatted)
                            
                            // Format Settlement Amount (settlementAmount)
                            $sheet->setCellValue('K' . $row, nf($arr->settlementAmount)); // Settlement Amount (formatted)
                            
                            // Status
                            $sheet->setCellValue('L' . $row, $arr->status); // Status
                            
                            // Format Created Date (addedOn)
                            $formattedDate = date('Y-m-d', strtotime($arr->addedOn));  
                            $excelDate = Date::PHPToExcel($formattedDate);
                            $sheet->setCellValue('M' . $row, $excelDate);  
                            $sheet->getStyle('M' . $row)
                                ->getNumberFormat()
                                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            
                            // Move to the next row
                            $row++;
                        }
                    });
                    
                    // Create the file
                    $writer = new Xlsx($spreadsheet);
                    $fileName = cmp()->companyName.'_'.'Cash_Pending_Exported_Leads_Data.xlsx';
                    
                    // Log the export action
                    $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                    actLogs('leads','Cash_Pending_Exported Leads (Date Range Export)',$logData);
                    
                    // Return the response for the download
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

        
        if (isSuperAdmin() || isAdmin()) {
            // No user-specific filter needed for admins
        }elseif(role()=='Credit Manager' || role()=='Sr. Credit Manager') {
            $userID = getUserID(); //helper to fetch current user logged in ID
            $query->where(function($query) use ($userID) {
                $query->Where('lms_leads.cmID', $userID);
            });
        }elseif(role()=='Recovery Manager') {
            $userID = getUserID(); // Fetch current logged-in user ID
            $userDetails = DB::table('lms_users_details')->select('branch')->where('userID',$userID)->orderBy('id','desc')->first();
            $branchIDs = explode(',', $userDetails->branch);
            $query->where(function($query) use ($branchIDs) {
                $query->whereIn('lms_approval.branch', $branchIDs);
            });
        }
        // Paginate the results
        $leads = $query->paginate(10);
        // Prepare other data needed for the view
        $queryParameters = $request->query();
        $filter = $request->filter;
        $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
        $data = compact('leads','page_info','filter','queryParameters');
        return view('collection.settlementLeads')->with($data); 
    }        
    
     public function settleToClosedLeads(Request $request){
          
        $query = DB::table('lms_leads')
                    ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                    ->join('lms_collection', 'lms_leads.leadID', '=', 'lms_collection.leadID')
                    ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                    ->where('lms_collection.status','Settle to Closed')
                    ->select('lms_leads.leadID','lms_leads.contactID','lms_collection.status','lms_contact.name','lms_contact.mobile','lms_contact.email','lms_contact.pancard','lms_collection.collectedAmount','lms_collection.collectedMode','lms_collection.collectedDate','lms_collection.collectionUtrNo','lms_collection.discountAmount','lms_collection.settlementAmount','lms_collection.addedOn')
                    ->orderBy('lms_collection.id','DESC');


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
            $query->whereBetween('lms_leads.commingLeadsDate', [$fromDate, $toDate]);
        } elseif ($request->filter == 'sortByToday') {
            $today = date('Y-m-d');
            $query->whereDate('lms_leads.commingLeadsDate', $today);
        }elseif ($request->filter == 'sortByWeek') {
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
        
       if (isSuperAdmin() || isAdmin()) {
            // No user-specific filter needed for admins
        }elseif(role()=='Credit Manager' || role()=='Sr. Credit Manager') {
            $userID = getUserID(); //helper to fetch current user logged in ID
            $query->where(function($query) use ($userID) {
                $query->Where('lms_leads.cmID', $userID);
            });
        }elseif(role()=='Recovery Manager') {
            $userID = getUserID(); // Fetch current logged-in user ID
            $userDetails = DB::table('lms_users_details')->select('branch')->where('userID',$userID)->orderBy('id','desc')->first();
            $branchIDs = explode(',', $userDetails->branch);
            $query->where(function($query) use ($branchIDs) {
                $query->whereIn('lms_approval.branch', $branchIDs);
            });
        }
        
        // Paginate the results
        $leads = $query->paginate(10);
        // Prepare other data needed for the view
        $queryParameters = $request->query();
        $filter = $request->filter;
        $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
        $data = compact('leads','page_info','filter','queryParameters');
        return view('collection.settledToClosedLeads')->with($data); 
    }        


   public function emiPending(Request $request)
{
    $sub = DB::table('lms_emi_schedule_disbursed')
        ->select('leadID', DB::raw('MIN(paymentDate) as paymentDate'))
        ->where('status', 0)
        ->groupBy('leadID');

    $query = DB::table('lms_contact')
        ->distinct()
        ->select(
            'lms_contact.contactID',
            'lms_contact.redFlag',
            'lms_contact.name',
            'lms_contact.mobile',
            'lms_contact.email',
            'lms_contact.pancard',
            'lms_leads.leadID',
            'lms_approval.employed',
            'lms_approval.branch',
            'lms_approval.loanAmtApproved',
            'lms_approval.tenure',
            'lms_approval.roi',
            'lms_approval.repayDay',
            'lms_approval.emi',
            'lms_approval.creditedBy',
            'lms_approval.pdVerifiedBy',
            'lms_approval.adminFee',
            'lms_approval.alternateMobile',
            'lms_approval.officialEmail',
            'lms_approval.monthlyIncome',
            'lms_approval.cibil',
            'lms_approval.status',
            'lms_loan.loanNo',
            'lms_loan.disbursalDate',
            'lms_loan.disburseTime',
            'users.displayName',
            'lms_approval.createdDate',
            'emi_min.paymentDate as nextPaymentDate'  
        )
        ->join('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
        ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
        ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
        ->leftJoinSub($sub, 'emi_min', function($join) {
            $join->on('lms_leads.leadID', '=', 'emi_min.leadID');
        })
        ->join('users', 'lms_approval.creditedBy', '=', 'users.userID')
        ->whereIn('lms_leads.status', ['Disbursed', 'EMI Running'])
        ->where('lms_loan.status', 'Disbursed')
        ->orderBy('lms_leads.leadID', 'desc');

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

    if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime($dates[0]));
        $toDate = date('Y-m-d', strtotime($dates[1]));
        $query->whereBetween('emi_min.paymentDate', [$fromDate, $toDate]);
    } elseif ($request->filter == 'sortByToday') {
        $today = date('Y-m-d');
        $query->whereDate('lms_loan.nextPaymentDate', $today);
    } elseif ($request->filter == 'sortByWeek') {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $query->whereBetween('emi_min.paymentDate', [$sevenDaysAgo, $today]);
    } elseif ($request->filter == 'sortByThisMonth') {
        $query->whereMonth('emi_min.paymentDate', '=', date('m'))
              ->whereYear('emi_min.paymentDate', '=', date('Y'));
    } elseif ($request->filter == 'sortByLastMonth') {
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $query->whereMonth('emi_min.paymentDate', '=', $lastMonth)
              ->whereYear('emi_min.paymentDate', '=', $lastMonthYear);
    } elseif ($request->filter == 'exportAll') {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Lead ID');
        $sheet->setCellValue('B1', 'Loan No');
        $sheet->setCellValue('C1', 'Branch');
        $sheet->setCellValue('D1', 'Loan Type');
        $sheet->setCellValue('E1', 'Name');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Mobile');
        $sheet->setCellValue('H1', 'Pancard');
        $sheet->setCellValue('I1', 'Employed');
        $sheet->setCellValue('J1', 'Loan Amount');
        $sheet->setCellValue('K1', 'ROI');
        $sheet->setCellValue('L1', 'EMI Amount');
        $sheet->setCellValue('M1', 'Tenure');
        $sheet->setCellValue('N1', 'Sanction By');
        $sheet->setCellValue('O1', 'PD By');
        $sheet->setCellValue('P1', 'Legal Status');
        $sheet->setCellValue('Q1', 'Red Flag');
        $sheet->setCellValue('R1', 'Disbursal Date');
        $sheet->setCellValue('S1', 'EMI Date');
        $sheet->setCellValue('T1', 'EMI Status');
        $sheet->setCellValue('U1', 'EMI Amount Due');
        $sheet->setCellValue('V1', 'Installment No');
        
        $row = 2;
        
        $leadIDs = $query->pluck('lms_leads.leadID')->toArray();
        
        $emiRecords = DB::table('lms_emi_schedule_disbursed')
            ->whereIn('leadID', $leadIDs)
            ->where('status', 0)
            ->orderBy('leadID')
            ->orderBy('paymentDate')
            ->get();
        
        $leadDetails = $query->get()->keyBy('leadID');
        
        foreach ($emiRecords as $emi) {
            if (isset($leadDetails[$emi->leadID])) {
                $arr = $leadDetails[$emi->leadID];
                
                $sheet->setCellValue('A' . $row, $arr->leadID);
                $sheet->setCellValue('B' . $row, $arr->loanNo);
                $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $arr->branch, 'cityName'));
                $sheet->setCellValue('D' . $row, 'Personal Loan');
                $sheet->setCellValue('E' . $row, $arr->name);
                $sheet->setCellValue('F' . $row, $arr->email);
                $sheet->setCellValue('G' . $row, $arr->mobile);
                $sheet->setCellValue('H' . $row, $arr->pancard);
                $sheet->setCellValue('I' . $row, $arr->employed);
                $sheet->setCellValue('J' . $row, $arr->loanAmtApproved);
                $sheet->setCellValue('K' . $row, $arr->roi);
                $sheet->setCellValue('L' . $row, nf($arr->emi));
                $sheet->setCellValue('M' . $row, $arr->tenure);
                
                $sheet->setCellValue('N' . $row, getUserNameById('users', 'userID', $arr->creditedBy, 'displayName'));
                $sheet->setCellValue('O' . $row, getUserNameById('users', 'userID', $arr->pdVerifiedBy, 'displayName'));
                $sheet->setCellValue('P' . $row, 'N/A');
                $sheet->setCellValue('Q' . $row, $arr->redFlag == 0 ? 'No' : 'Yes');
                
                $formattedDate2 = date('Y-m-d', strtotime($arr->disbursalDate));  
                $excelDate2 = Date::PHPToExcel($formattedDate2);
                $sheet->setCellValue('R' . $row, $excelDate2);  
                $sheet->getStyle('R' . $row)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                
                $formattedDate3 = date('Y-m-d', strtotime($emi->paymentDate));  
                $excelDate3 = Date::PHPToExcel($formattedDate3);
                $sheet->setCellValue('S' . $row, $excelDate3);  
                $sheet->getStyle('S' . $row)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                
                $sheet->setCellValue('T' . $row, 'Pending');
                $sheet->setCellValue('U' . $row, nf($emi->emiAmount));
                $sheet->setCellValue('V' . $row, $emi->installment);
                
                $row++;
            }
        }
        
        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName.'_'.'Pending_EMI_Schedule_Data.xlsx';
        
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads','Pending_EMI_Schedule_Exported (All Records)',$logData);
        
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
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'Lead ID');
        $sheet->setCellValue('B1', 'Loan No');
        $sheet->setCellValue('C1', 'Branch');
        $sheet->setCellValue('D1', 'Loan Type');
        $sheet->setCellValue('E1', 'Name');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Mobile');
        $sheet->setCellValue('H1', 'Pancard');
        $sheet->setCellValue('I1', 'Employed');
        $sheet->setCellValue('J1', 'Loan Amount');
        $sheet->setCellValue('K1', 'ROI');
        $sheet->setCellValue('L1', 'EMI Amount');
        $sheet->setCellValue('M1', 'Tenure');
        $sheet->setCellValue('N1', 'Sanction By');
        $sheet->setCellValue('O1', 'PD By');
        $sheet->setCellValue('P1', 'Legal Status');
        $sheet->setCellValue('Q1', 'Red Flag');
        $sheet->setCellValue('R1', 'Disbursal Date');
        $sheet->setCellValue('S1', 'EMI Date');
        $sheet->setCellValue('T1', 'EMI Status');
        $sheet->setCellValue('U1', 'EMI Amount Due');
        $sheet->setCellValue('V1', 'Installment No');
        
        $row = 2;
        
        $leadIDs = $query->whereBetween('emi_min.paymentDate', [$fromDate, $toDate])
            ->pluck('lms_leads.leadID')
            ->toArray();
        
        $emiRecords = DB::table('lms_emi_schedule_disbursed')
            ->whereIn('leadID', $leadIDs)
            ->where('status', 0)
            ->whereBetween('paymentDate', [$fromDate, $toDate])
            ->orderBy('leadID')
            ->orderBy('paymentDate')
            ->get();
        
        $leadDetails = $query->whereBetween('emi_min.paymentDate', [$fromDate, $toDate])
            ->get()
            ->keyBy('leadID');
        
        foreach ($emiRecords as $emi) {
            if (isset($leadDetails[$emi->leadID])) {
                $arr = $leadDetails[$emi->leadID];
                
                $sheet->setCellValue('A' . $row, $arr->leadID);
                $sheet->setCellValue('B' . $row, $arr->loanNo);
                $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $arr->branch, 'cityName'));
                $sheet->setCellValue('D' . $row, 'Personal Loan');
                $sheet->setCellValue('E' . $row, $arr->name);
                $sheet->setCellValue('F' . $row, $arr->email);
                $sheet->setCellValue('G' . $row, $arr->mobile);
                $sheet->setCellValue('H' . $row, $arr->pancard);
                $sheet->setCellValue('I' . $row, $arr->employed);
                $sheet->setCellValue('J' . $row, $arr->loanAmtApproved);
                $sheet->setCellValue('K' . $row, $arr->roi);
                $sheet->setCellValue('L' . $row, nf($arr->emi));
                $sheet->setCellValue('M' . $row, $arr->tenure);
                
                $sheet->setCellValue('N' . $row, getUserNameById('users', 'userID', $arr->creditedBy, 'displayName'));
                $sheet->setCellValue('O' . $row, getUserNameById('users', 'userID', $arr->pdVerifiedBy, 'displayName'));
                $sheet->setCellValue('P' . $row, 'N/A');
                $sheet->setCellValue('Q' . $row, $arr->redFlag == 0 ? 'No' : 'Yes');
                
                $formattedDate2 = date('Y-m-d', strtotime($arr->disbursalDate));  
                $excelDate2 = Date::PHPToExcel($formattedDate2);
                $sheet->setCellValue('R' . $row, $excelDate2);  
                $sheet->getStyle('R' . $row)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                
                $formattedDate3 = date('Y-m-d', strtotime($emi->paymentDate));  
                $excelDate3 = Date::PHPToExcel($formattedDate3);
                $sheet->setCellValue('S' . $row, $excelDate3);  
                $sheet->getStyle('S' . $row)
                    ->getNumberFormat()
                    ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                
                $sheet->setCellValue('T' . $row, 'Pending');
                $sheet->setCellValue('U' . $row, nf($emi->emiAmount));
                $sheet->setCellValue('V' . $row, $emi->installment);
                
                $row++;
            }
        }
        
        $writer = new Xlsx($spreadsheet);
        $fileName = cmp()->companyName.'_'.'Pending_EMI_Schedule_Data.xlsx';
        
        $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
        actLogs('leads','Pending_EMI_Schedule_Exported (Date Range)',$logData);
        
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

    if (isSuperAdmin() || isAdmin()) {
        // No filter for admins
    } elseif(role()=='Credit Manager' || role()=='Sr. Credit Manager') {
        $userID = getUserID();
        $query->where('lms_leads.cmID', $userID);
    } elseif(role()=='Recovery Manager') {
        $userID = getUserID();
        $userDetails = DB::table('lms_users_details')->select('branch')->where('userID',$userID)->orderBy('id','desc')->first();
        $branchIDs = explode(',', $userDetails->branch);
        $query->whereIn('lms_approval.branch', $branchIDs);
    }
           
    $leads = $query->paginate(10);
    
    $queryParameters = $request->query();
    $filter = $request->filter;
    $page_info = pageInfo(slugToText($request->segment(2)),$request->segment(1));
    $data = compact('leads','page_info','filter','queryParameters');
    return view('collection.emiPending')->with($data); 
}


    public function getRepaymentScheduleData(Request $request)
    {
        // Get the leadID from the request
        $leadID = $request->input('leadID');
        
        // Fetch repayment schedule data from the database
        $repaymentScheduleDisbursed = DB::table('lms_emi_schedule_disbursed')
            ->where('leadID', $leadID)
            ->get();
 
        if ($repaymentScheduleDisbursed->isNotEmpty()) {
            // Return the data as JSON
            return response()->json([
                'success' => true,
                'data' => $repaymentScheduleDisbursed
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No repayment schedule found for the given lead ID.'
            ]);
        }
    }
  

}
