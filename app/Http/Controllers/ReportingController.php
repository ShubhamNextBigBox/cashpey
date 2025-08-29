<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;


class ReportingController extends Controller
{
     public function leadWiseReporting(Request $request, $reportingType = null) {
        
        $activeTab = $reportingType ?? 'disbursed';
      
        $reportingType = slugToText($activeTab);  // Helper  

 
        if($reportingType=='Disbursed'){
 
            $query = DB::table('lms_contact')
                  ->join('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
                  ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                  ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                  ->leftJoin('lms_address', function ($join) {
                            $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                                 ->where('lms_address.addressType', '=', 'current');
                   })
                  ->where('lms_loan.status','Disbursed')
                  ->join('users', 'lms_loan.addedBy', '=', 'users.userID')
                  ->select('lms_loan.leadID', 'lms_loan.loanNo', 'lms_approval.branch','lms_approval.monthlyObligation','lms_loan.addedBy','lms_approval.creditedBy','lms_loan.disburseTime' ,'lms_contact.name', 'lms_contact.gender','lms_contact.redFlag','lms_contact.dob', 'lms_contact.mobile', 'lms_contact.email', 'lms_contact.aadharNo', 'lms_contact.pancard', 'lms_approval.loanAmtApproved', 'lms_approval.roi', 'lms_approval.tenure', 'lms_approval.adminFee', 'lms_approval.monthlyIncome','lms_approval.adminGstAmount','lms_approval.emi', 'lms_approval.cibil', 'lms_loan.disbursalDate', 'lms_approval.repayDay', 'lms_leads.status','lms_leads.customerType','lms_leads.commingLeadsDate', 'lms_address.state', 'lms_loan.accountNo','lms_approval.employed', 'lms_loan.accountType', 'lms_loan.ifscCode', 'lms_loan.bank','lms_loan.enachID' ,'lms_loan.bankBranch', 'lms_loan.disbursalUtrNo', 'lms_approval.pdVerifiedBy','lms_leads.utmSource','lms_leads.commingLeadsDate','users.displayName')
                  ->orderBy('lms_loan.disbursalDate', 'DESC');
                   
        }elseif($reportingType=='Collection'){
             $query = DB::table('lms_contact')
                ->join('lms_collection', 'lms_contact.contactID', '=', 'lms_collection.contactID')
                ->join('lms_approval', 'lms_approval.leadID', '=', 'lms_collection.leadID')
                ->leftJoin('lms_leads', 'lms_leads.leadID', '=', 'lms_collection.leadID')
                ->leftJoin('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                ->select('lms_collection.leadID','lms_collection.loanNo','lms_loan.loanNo','lms_contact.name', 'lms_contact.email', 'lms_contact.mobile', 'lms_contact.pancard', 'lms_collection.collectedAmount', 'lms_collection.collectedMode', 'lms_collection.collectedDate', 'lms_collection.collectionSource', 'lms_collection.collectionUtrNo','lms_collection.status', 'lms_collection.remark',  'lms_approval.branch','lms_approval.loanAmtApproved', 'lms_approval.repayDay')
                ->orderBy('lms_collection.collectedDate', 'DESC');
        }
         

                // Apply search filter if sortBySearch filter is selected and search term is provided
                if ($request->filter == 'sortBySearch' && !empty($request->search) && $reportingType=='Disbursed') {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('users.displayName', 'like', "%{$search}%")  // Search in cm_users 
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%")
                              ->orWhere('lms_loan.loanNo', 'like', "%{$search}%");
                    });
                }
                
                  if ($request->filter == 'sortBySearch' && !empty($request->search) && $reportingType=='Collection') {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('lms_leads.leadID', 'like', "%{$search}%")
                              ->orWhere('lms_loan.loanNo', 'like', "%{$search}%");
                    });
                }

                // Apply date range filter based on sortByDate filter and searchRange
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    if($reportingType=='Disbursed'){    
                        $query->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate]);
                    }
                   if ($reportingType == 'Collection') {
                        $query->whereBetween(DB::raw('DATE(lms_collection.collectedDate)'), [$fromDate, $toDate]);
                    }
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                     if($reportingType=='Disbursed'){    
                       $query->whereDate('lms_loan.disbursalDate', $today); 
                     }
                      if ($reportingType == 'Collection') {
                        // Manually compare the date portion of collectedDate
                        $query->whereRaw('DATE(lms_collection.collectedDate) = ?', [$today]);
                    }
                } elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                     if($reportingType=='Disbursed'){    
                         $query->whereBetween('lms_loan.disbursalDate', [$sevenDaysAgo, $today]);  
                     }
                     if ($reportingType == 'Collection') {    
                        // We need to compare only the date part of collectedDate
                        $query->whereBetween(DB::raw('DATE(lms_collection.collectedDate)'), [$sevenDaysAgo, $today]);
                    }
                }elseif ($request->filter == 'sortByThisMonth') {
                     if($reportingType=='Disbursed'){    
                        $query->whereMonth('lms_loan.disbursalDate', '=', date('m'))
                                ->whereYear('lms_loan.disbursalDate', '=', date('Y'));
                         
                     }
                    if ($reportingType == 'Collection') {    
                        // Filter for the current month and year for collectedDate
                        $query->whereRaw("MONTH(lms_collection.collectedDate) = ? AND YEAR(lms_collection.collectedDate) = ?", [date('m'), date('Y')]);

                    }
                }elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    
                     if($reportingType=='Disbursed'){    
                        $query->whereMonth('lms_loan.disbursalDate', '=', $lastMonth)
                             ->whereYear('lms_loan.disbursalDate', '=', $lastMonthYear);
                     }
                     if($reportingType=='Collection'){    
                        $query->whereRaw("DATE(lms_collection.collectedDate) = ?", [date('Y-m-d', strtotime($lastMonthYear . '-' . $lastMonth . '-01'))]);
                     }
                }elseif($request->filter == 'exportAll'){
 
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        
                         if($reportingType=='Disbursed'){
                            // Set column headings
                            $sheet->setCellValue('A1', 'LeadID'); // Lead ID
                            $sheet->setCellValue('B1', 'Loan No'); // Loan No
                            $sheet->setCellValue('C1', 'Branch'); // Branch
                            $sheet->setCellValue('D1', 'Name'); // Name
                            $sheet->setCellValue('E1', 'Credit By'); // Credit By
                            $sheet->setCellValue('F1', 'PD By'); // PD By
                            $sheet->setCellValue('G1', 'Gender'); // Gender
                            $sheet->setCellValue('H1', 'DOB'); // DOB
                            $sheet->setCellValue('I1', 'Email'); // Email
                            $sheet->setCellValue('J1', 'Mobile'); // Mobile
                            $sheet->setCellValue('K1', 'Pancard'); // Pancard
                            $sheet->setCellValue('L1', 'Aadhar No'); // Aadhar No
                            $sheet->setCellValue('M1', 'Employed'); // Employed
                            $sheet->setCellValue('N1', 'Monthly Income'); // Monthly Income
                            $sheet->setCellValue('O1', 'Monthly Obligation'); // Monthly Obligation
                            $sheet->setCellValue('P1', 'Loan Amount'); // Loan Amount
                            $sheet->setCellValue('Q1', 'EMI Amount'); // EMI Amount
                            $sheet->setCellValue('R1', 'Tenure'); // Tenure
                            $sheet->setCellValue('S1', 'ROI'); // ROI
                            $sheet->setCellValue('T1', 'AccountNo'); // AccountNo
                            $sheet->setCellValue('U1', 'Bank IFSC'); // Bank IFSC
                            $sheet->setCellValue('V1', 'Bank'); // Bank
                            $sheet->setCellValue('W1', 'Bank Branch'); // Bank Branch
                            $sheet->setCellValue('X1', 'Enach Details'); // Enach Details
                            $sheet->setCellValue('Y1', 'Disbursal Reference No'); // Disbursal Reference No
                            $sheet->setCellValue('Z1', 'Disbursed By Bank'); // Disbursed By Bank
                            $sheet->setCellValue('AA1', 'Disbursal Date'); // Disbursal Date
                            $sheet->setCellValue('AB1', 'Admin Fee'); // Admin Fee
                            $sheet->setCellValue('AC1', 'Cibil'); // Cibil
                            $sheet->setCellValue('AD1', 'GSTOfAdminFee'); // GSTOfAdminFee
                            $sheet->setCellValue('AE1', 'UTM Source'); // UTM Source
                            $sheet->setCellValue('AF1', 'State'); // State
                            $sheet->setCellValue('AG1', 'Red Flag'); // Red Flag
                            $sheet->setCellValue('AH1', 'Status'); // Status
                            $sheet->setCellValue('AI1', 'Lead Coming Date'); // Lead Coming Date

    
    
                            $row = 2; // Start row for data
    
                            // Query Builder chunking


                      $query->chunk(5000, function ($records) use ($sheet, &$row) {
                                 foreach ($records as $record) {
                                    $sheet->setCellValue('A' . $row, $record->leadID); // LeadID
                                    $sheet->setCellValue('B' . $row, $record->loanNo); // Loan No
                                    $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName')); // Branch
                                    $sheet->setCellValue('D' . $row, ucwords($record->name)); // Name
                                    $sheet->setCellValue('E' . $row, getUserNameById('users','userID',$record->creditedBy ,'displayName')); // Credit By
                                    $sheet->setCellValue('F' . $row, getUserNameById('users','userID',$record->pdVerifiedBy ,'displayName')); // PD By
                                    $sheet->setCellValue('G' . $row, $record->gender); // Gender

                                    // Format DOB
                                    $sheet->setCellValue('H' . $row, df($record->dob)); // DOB
                                    $sheet->setCellValue('I' . $row, $record->email); // Email
                                    $sheet->setCellValue('J' . $row, $record->mobile); // Mobile
                                    $sheet->setCellValue('K' . $row, $record->pancard); // Pancard
                                    $sheet->setCellValue('L' . $row, $record->aadharNo); // Aadhar No
                                    $sheet->setCellValue('M' . $row, $record->employed); // Employed
                                    $sheet->setCellValue('N' . $row, nf($record->monthlyIncome)); // Monthly Income
                                    $sheet->setCellValue('O' . $row, nf($record->monthlyObligation)); // Monthly Obligation
                                    $sheet->setCellValue('P' . $row, nf($record->loanAmtApproved)); // Loan Amount
                                    $sheet->setCellValue('Q' . $row, nf($record->emi)); // EMI Amount
                                    $sheet->setCellValue('R' . $row, $record->tenure); // Tenure
                                    $sheet->setCellValue('S' . $row, $record->roi.' %'); // ROI
                                    $sheet->setCellValue('T' . $row, $record->accountNo); // Account No
                                    $sheet->setCellValue('U' . $row, $record->ifscCode); // IFSC Code
                                    $sheet->setCellValue('V' . $row, $record->bank); // Bank
                                    $sheet->setCellValue('W' . $row, $record->bankBranch); // Bank Branch
                                    $sheet->setCellValue('X' . $row, $record->enachID); // Enach ID
                                    $sheet->setCellValue('Y' . $row, $record->disbursalUtrNo); // Disbursal UTR No
                                    $sheet->setCellValue('Z' . $row, $record->bank); // Disbursed By Bank

                                    // Formatting Disbursal Date
                                    $sheet->setCellValue('AA' . $row, df($record->disbursalDate).' '.$record->disburseTime); // Disbursal Date
                                    $sheet->setCellValue('AB' . $row, nf($record->adminFee)); // Admin Fee
                                    $sheet->setCellValue('AC' . $row, $record->cibil); // Cibil
                                    $sheet->setCellValue('AD' . $row, nf($record->adminGstAmount)); // GST Of Admin Fee
                                    $sheet->setCellValue('AE' . $row, $record->utmSource ?? '-'); // UTM Source
                                    $sheet->setCellValue('AF' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName')); // State
                                    $sheet->setCellValue('AG' . $row, $record->redFlag == 0 ? 'No' : 'Yes'); // Red Flag
                                    $sheet->setCellValue('AH' . $row, $record->status); // Status
                                    $sheet->setCellValue('AI' . $row, df($record->commingLeadsDate)); // Lead Coming Date

                                    // Move to the next row
                                    $row++;

                        }
                    });

                            // Write the spreadsheet to a file
                            $writer = new Xlsx($spreadsheet);
                            $fileName = cmp()->companyName.'_'.'Disbursed_Reporting_Data.xlsx';
                            
                            $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                            actLogs('leads','Disbursed Reporting Data (All Export)',$logData);
    
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
                         
                        if($reportingType=='Collection'){
                            // Set column headings
                            $sheet->setCellValue('A1', 'LeadID'); // Column for LeadID
                            $sheet->setCellValue('B1', 'Loan No'); // Column for Loan No
                            $sheet->setCellValue('C1', 'Branch'); // Column for Branch
                            $sheet->setCellValue('D1', 'Name'); // Column for Name
                            $sheet->setCellValue('E1', 'Email'); // Column for Email
                            $sheet->setCellValue('F1', 'Mobile'); // Column for Mobile
                            $sheet->setCellValue('G1', 'Pancard'); // Column for Pancard
                            $sheet->setCellValue('H1', 'Loan Amount'); // Column for Loan Amount
                            $sheet->setCellValue('I1', 'Collected Amount'); // Column for Collected Amount
                            $sheet->setCellValue('J1', 'Collected Mode'); // Column for Collected Mode
                            $sheet->setCellValue('K1', 'Reference No'); // Column for Reference No
                            $sheet->setCellValue('L1', 'Remark'); // Column for Remark
                            $sheet->setCellValue('M1', 'Collection Source'); // Column for Collection Source
                            $sheet->setCellValue('N1', 'Status'); // Column for Status
                            $sheet->setCellValue('O1', 'Collected Date'); // Column for Collected Date

    
                            $row = 2; // Start row for data
    
                            // Query Builder chunking
                            $query->chunk(5000, function ($records) use ($sheet, &$row) {
                                foreach ($records as $record) {
                                        $sheet->setCellValue('A' . $row, $record->leadID); // LeadID
                                        $sheet->setCellValue('B' . $row, $record->loanNo); // Loan No
                                        $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName')); // Branch
                                        $sheet->setCellValue('D' . $row, $record->name); // Name
                                        $sheet->setCellValue('E' . $row, $record->email); // Email

                                        // Ensure mobile is treated as a string (with leading zeros if needed)
                                        $sheet->setCellValueExplicit('F' . $row, str_pad($record->mobile, 10, '0', STR_PAD_LEFT), DataType::TYPE_STRING);

                                        // Ensure Pancard is treated as a string (if needed)
                                        $sheet->setCellValueExplicit('G' . $row, str_pad($record->pancard, 10, '0', STR_PAD_LEFT), DataType::TYPE_STRING);

                                        $sheet->setCellValue('H' . $row, nf($record->loanAmtApproved)); // Loan Amount
                                        $sheet->setCellValue('I' . $row, nf($record->collectedAmount)); // Collected Amount
                                        $sheet->setCellValue('J' . $row, $record->collectedMode); // Collected Mode

                                        // Ensure Collection UTR No is treated as a string (with leading zeros if needed)
                                        $sheet->setCellValueExplicit('K' . $row, str_pad($record->collectionUtrNo, 10, '0', STR_PAD_LEFT), DataType::TYPE_STRING);

                                        $sheet->setCellValue('L' . $row, $record->remark); // Remark
                                        $sheet->setCellValue('M' . $row, $record->collectionSource); // Collection Source
                                        $sheet->setCellValue('N' . $row, $record->status); // Status

                                        // Collected Date formatting to match the table's date format
                                        $formattedDate2 = date('Y-m-d', strtotime($record->collectedDate));
                                        $excelDate2 = Date::PHPToExcel($formattedDate2);
                                        $sheet->setCellValue('O' . $row, $excelDate2); // Collected Date
                                        $sheet->getStyle('O' . $row)
                                            ->getNumberFormat()
                                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY); // Apply desired date format

                                        $row++;
                                    }
                            });
    
                            // Write the spreadsheet to a file
                            $writer = new Xlsx($spreadsheet);
                            $fileName = cmp()->companyName.'_'.'Collection_Reporting_Data.xlsx';
                            
                            $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                            actLogs('leads','Collection Reporting Data (All Export)',$logData);
    
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
                     
                }elseif($request->filter == 'exportByDate' && !empty($request->exportRange)){
 
                    $dates = explode(' - ', $request->exportRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                  
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                         if($reportingType=='Disbursed'){
                            // Set column headings
                                  $sheet->setCellValue('A1', 'LeadID'); // Lead ID
                            $sheet->setCellValue('B1', 'Loan No'); // Loan No
                            $sheet->setCellValue('C1', 'Branch'); // Branch
                            $sheet->setCellValue('D1', 'Name'); // Name
                            $sheet->setCellValue('E1', 'Credit By'); // Credit By
                            $sheet->setCellValue('F1', 'PD By'); // PD By
                            $sheet->setCellValue('G1', 'Gender'); // Gender
                            $sheet->setCellValue('H1', 'DOB'); // DOB
                            $sheet->setCellValue('I1', 'Email'); // Email
                            $sheet->setCellValue('J1', 'Mobile'); // Mobile
                            $sheet->setCellValue('K1', 'Pancard'); // Pancard
                            $sheet->setCellValue('L1', 'Aadhar No'); // Aadhar No
                            $sheet->setCellValue('M1', 'Employed'); // Employed
                            $sheet->setCellValue('N1', 'Monthly Income'); // Monthly Income
                            $sheet->setCellValue('O1', 'Monthly Obligation'); // Monthly Obligation
                            $sheet->setCellValue('P1', 'Loan Amount'); // Loan Amount
                            $sheet->setCellValue('Q1', 'EMI Amount'); // EMI Amount
                            $sheet->setCellValue('R1', 'Tenure'); // Tenure
                            $sheet->setCellValue('S1', 'ROI'); // ROI
                            $sheet->setCellValue('T1', 'AccountNo'); // AccountNo
                            $sheet->setCellValue('U1', 'Bank IFSC'); // Bank IFSC
                            $sheet->setCellValue('V1', 'Bank'); // Bank
                            $sheet->setCellValue('W1', 'Bank Branch'); // Bank Branch
                            $sheet->setCellValue('X1', 'Enach Details'); // Enach Details
                            $sheet->setCellValue('Y1', 'Disbursal Reference No'); // Disbursal Reference No
                            $sheet->setCellValue('Z1', 'Disbursed By Bank'); // Disbursed By Bank
                            $sheet->setCellValue('AA1', 'Disbursal Date'); // Disbursal Date
                            $sheet->setCellValue('AB1', 'Admin Fee'); // Admin Fee
                            $sheet->setCellValue('AC1', 'Cibil'); // Cibil
                            $sheet->setCellValue('AD1', 'GSTOfAdminFee'); // GSTOfAdminFee
                            $sheet->setCellValue('AE1', 'UTM Source'); // UTM Source
                            $sheet->setCellValue('AF1', 'State'); // State
                            $sheet->setCellValue('AG1', 'Red Flag'); // Red Flag
                            $sheet->setCellValue('AH1', 'Status'); // Status
                            $sheet->setCellValue('AI1', 'Lead Coming Date'); // Lead Coming Date
        
        
                                $row = 2; // Start row for data
        
                                // Query Builder chunking
                                $query->whereBetween('lms_loan.disbursalDate', [$fromDate, $toDate])
                                      ->chunk(5000, function ($records) use ($sheet, &$row) {
                                   foreach ($records as $record) {
                                    $sheet->setCellValue('A' . $row, $record->leadID); // LeadID
                                    $sheet->setCellValue('B' . $row, $record->loanNo); // Loan No
                                    $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName')); // Branch
                                    $sheet->setCellValue('D' . $row, ucwords($record->name)); // Name
                                    $sheet->setCellValue('E' . $row, getUserNameById('users','userID',$record->creditedBy ,'displayName')); // Credit By
                                    $sheet->setCellValue('F' . $row, getUserNameById('users','userID',$record->pdVerifiedBy ,'displayName')); // PD By
                                    $sheet->setCellValue('G' . $row, $record->gender); // Gender

                                    // Format DOB
                                    $sheet->setCellValue('H' . $row, df($record->dob)); // DOB
                                    $sheet->setCellValue('I' . $row, $record->email); // Email
                                    $sheet->setCellValue('J' . $row, $record->mobile); // Mobile
                                    $sheet->setCellValue('K' . $row, $record->pancard); // Pancard
                                    $sheet->setCellValue('L' . $row, $record->aadharNo); // Aadhar No
                                    $sheet->setCellValue('M' . $row, $record->employed); // Employed
                                    $sheet->setCellValue('N' . $row, nf($record->monthlyIncome)); // Monthly Income
                                    $sheet->setCellValue('O' . $row, nf($record->monthlyObligation)); // Monthly Obligation
                                    $sheet->setCellValue('P' . $row, nf($record->loanAmtApproved)); // Loan Amount
                                    $sheet->setCellValue('Q' . $row, nf($record->emi)); // EMI Amount
                                    $sheet->setCellValue('R' . $row, $record->tenure); // Tenure
                                    $sheet->setCellValue('S' . $row, $record->roi.' %'); // ROI
                                    $sheet->setCellValue('T' . $row, $record->accountNo); // Account No
                                    $sheet->setCellValue('U' . $row, $record->ifscCode); // IFSC Code
                                    $sheet->setCellValue('V' . $row, $record->bank); // Bank
                                    $sheet->setCellValue('W' . $row, $record->bankBranch); // Bank Branch
                                    $sheet->setCellValue('X' . $row, $record->enachID); // Enach ID
                                    $sheet->setCellValue('Y' . $row, $record->disbursalUtrNo); // Disbursal UTR No
                                    $sheet->setCellValue('Z' . $row, $record->bank); // Disbursed By Bank

                                    // Formatting Disbursal Date
                                    $sheet->setCellValue('AA' . $row, df($record->disbursalDate).' '.$record->disburseTime); // Disbursal Date
                                    $sheet->setCellValue('AB' . $row, nf($record->adminFee)); // Admin Fee
                                    $sheet->setCellValue('AC' . $row, $record->cibil); // Cibil
                                    $sheet->setCellValue('AD' . $row, nf($record->adminGstAmount)); // GST Of Admin Fee
                                    $sheet->setCellValue('AE' . $row, $record->utmSource ?? '-'); // UTM Source
                                    $sheet->setCellValue('AF' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName')); // State
                                    $sheet->setCellValue('AG' . $row, $record->redFlag == 0 ? 'No' : 'Yes'); // Red Flag
                                    $sheet->setCellValue('AH' . $row, $record->status); // Status
                                    $sheet->setCellValue('AI' . $row, df($record->commingLeadsDate)); // Lead Coming Date

                                    // Move to the next row
                                    $row++;


                        }
                            });
    
                            // Write the spreadsheet to a file
                            $writer = new Xlsx($spreadsheet);
                            $fileName = cmp()->companyName.'_'.'Disbursed_Reporting_Data.xlsx';
                            
                            $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                            actLogs('leads','Disbursed Reporting Data (Date Range Export)',$logData);
    
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
                         
                          if($reportingType=='Collection'){
                            // Set column headings
                            $sheet->setCellValue('A1', 'LeadID'); // Column for LeadID
                            $sheet->setCellValue('B1', 'Loan No'); // Column for Loan No
                            $sheet->setCellValue('C1', 'Branch'); // Column for Branch
                            $sheet->setCellValue('D1', 'Name'); // Column for Name
                            $sheet->setCellValue('E1', 'Email'); // Column for Email
                            $sheet->setCellValue('F1', 'Mobile'); // Column for Mobile
                            $sheet->setCellValue('G1', 'Pancard'); // Column for Pancard
                            $sheet->setCellValue('H1', 'Loan Amount'); // Column for Loan Amount
                            $sheet->setCellValue('I1', 'Collected Amount'); // Column for Collected Amount
                            $sheet->setCellValue('J1', 'Collected Mode'); // Column for Collected Mode
                            $sheet->setCellValue('K1', 'Reference No'); // Column for Reference No
                            $sheet->setCellValue('L1', 'Remark'); // Column for Remark
                            $sheet->setCellValue('M1', 'Collection Source'); // Column for Collection Source
                            $sheet->setCellValue('N1', 'Status'); // Column for Status
                            $sheet->setCellValue('O1', 'Collected Date'); // Column for Collected Date


    
    
                            $row = 2; // Start row for data
    
                            // Query Builder chunking
                          $query->whereDate('lms_collection.collectedDate', '>=', $fromDate)  // Filter from date
                                  ->whereDate('lms_collection.collectedDate', '<=', $toDate)  // Filter to date
                                  ->chunk(5000, function ($records) use ($sheet, &$row) {
                                      foreach ($records as $record) {
                                        $sheet->setCellValue('A' . $row, $record->leadID); // LeadID
                                        $sheet->setCellValue('B' . $row, $record->loanNo); // Loan No
                                        $sheet->setCellValue('C' . $row, getUserNameById('lms_cities', 'cityID', $record->branch, 'cityName')); // Branch
                                        $sheet->setCellValue('D' . $row, $record->name); // Name
                                        $sheet->setCellValue('E' . $row, $record->email); // Email

                                        // Ensure mobile is treated as a string (with leading zeros if needed)
                                        $sheet->setCellValueExplicit('F' . $row, str_pad($record->mobile, 10, '0', STR_PAD_LEFT), DataType::TYPE_STRING);

                                        // Ensure Pancard is treated as a string (if needed)
                                        $sheet->setCellValueExplicit('G' . $row, str_pad($record->pancard, 10, '0', STR_PAD_LEFT), DataType::TYPE_STRING);

                                        $sheet->setCellValue('H' . $row, nf($record->loanAmtApproved)); // Loan Amount
                                        $sheet->setCellValue('I' . $row, nf($record->collectedAmount)); // Collected Amount
                                        $sheet->setCellValue('J' . $row, $record->collectedMode); // Collected Mode

                                        // Ensure Collection UTR No is treated as a string (with leading zeros if needed)
                                        $sheet->setCellValueExplicit('K' . $row, str_pad($record->collectionUtrNo, 10, '0', STR_PAD_LEFT), DataType::TYPE_STRING);

                                        $sheet->setCellValue('L' . $row, $record->remark); // Remark
                                        $sheet->setCellValue('M' . $row, $record->collectionSource); // Collection Source
                                        $sheet->setCellValue('N' . $row, $record->status); // Status

                                        // Collected Date formatting to match the table's date format
                                        $formattedDate2 = date('Y-m-d', strtotime($record->collectedDate));
                                        $excelDate2 = Date::PHPToExcel($formattedDate2);
                                        $sheet->setCellValue('O' . $row, $excelDate2); // Collected Date
                                        $sheet->getStyle('O' . $row)
                                            ->getNumberFormat()
                                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY); // Apply desired date format

                                        $row++;
                                    }

                            });

    
                            // Write the spreadsheet to a file
                            $writer = new Xlsx($spreadsheet);
                            $fileName = cmp()->companyName.'_'.'Collection_Reporting_Data.xlsx';
                            
                            $logData = array_merge($request->all(), ['exportBy' => getUserNameById('users', 'userID', getUserID(), 'displayName')]);
                            actLogs('leads','Collection Reporting Data (Date Range Export)',$logData);
    
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
                     
                     
                }


                // Paginate the results
                $leads = $query->paginate(10);

                // Prepare other data needed for the view
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo($reportingType, $request->segment(1));
                $data = compact('activeTab','leads', 'page_info', 'filter','queryParameters');

                return view('reporting.allReportingData')->with($data);
        
        
    }
   
}
