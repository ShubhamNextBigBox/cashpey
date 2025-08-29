<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 

class EsignVideoKycController extends Controller
{
    public function esignList(Request $request) {
        
           
           $query = DB::table('lms_esigndockyc')
                    ->join('lms_leads','lms_leads.leadID', '=', 'lms_esigndockyc.leadID')
                    ->select(
                        'lms_esigndockyc.esignID',
                        'lms_esigndockyc.leadID',
                        'lms_esigndockyc.documentId',
                        'lms_esigndockyc.name',
                        'lms_esigndockyc.email',
                        'lms_esigndockyc.mobile',
                        'lms_esigndockyc.loanAmtApproved',
                        'lms_esigndockyc.docRequestByName',
                        'lms_esigndockyc.loanStatus',
                        'lms_esigndockyc.status',
                        'lms_esigndockyc.addedOn',
                        'lms_leads.status as leadStatus'
                    )
                    // Order by leadID in descending order
                    ->orderByDesc('lms_esigndockyc.id'); // Assuming 'leadID' is the most reliable for sorting
                
                // Handling search filter
                if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_esigndockyc.name', 'like', "%{$search}%")
                              ->orWhere('lms_esigndockyc.email', 'like', "%{$search}%")
                              ->orWhere('lms_esigndockyc.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_esigndockyc.leadID', 'like', "%{$search}%");
                    });
                }
 
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_esigndockyc.addedOn', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_esigndockyc.addedOn', $today);
                } elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_esigndockyc.addedOn', [$sevenDaysAgo, $today]);
                }elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_esigndockyc.addedOn', '=', date('m'))
                          ->whereYear('lms_esigndockyc.addedOn', '=', date('Y'));
                }elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_esigndockyc.addedOn', '=', $lastMonth)
                          ->whereYear('lms_esigndockyc.addedOn', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'Lead ID');
                        $sheet->setCellValue('B1', 'E-sign ID');
                        $sheet->setCellValue('C1', 'Name');
                        $sheet->setCellValue('D1', 'Email');
                        $sheet->setCellValue('E1', 'Mobile');
                        $sheet->setCellValue('F1', 'Loan Amount');
                        $sheet->setCellValue('G1', 'Requested By');
                        $sheet->setCellValue('H1', 'Status');
                        $sheet->setCellValue('I1', 'Date');
                        $row = 2; // Start row for data

                        $query->chunk(500, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, $record->documentId);
                                $sheet->setCellValue('C' . $row, $record->name);
                                $sheet->setCellValue('D' . $row, $record->email);
                                $sheet->setCellValue('E' . $row, $record->mobile);
                                $sheet->setCellValue('F' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('G' . $row, $record->docRequestByName);
                                $sheet->setCellValue('H' . $row, $record->status);
                                $sheet->setCellValue('I' . $row, $record->addedOn);
                                $row++;
                            }
                        });


                        // // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'exported_kyc-esign_data.xlsx';

                        actLogs('KYC Data','E-sign all data export',$request->all());
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
                        $sheet->setCellValue('B1', 'E-sign ID');
                        $sheet->setCellValue('C1', 'Name');
                        $sheet->setCellValue('D1', 'Email');
                        $sheet->setCellValue('E1', 'Mobile');
                        $sheet->setCellValue('F1', 'Loan Amount');
                        $sheet->setCellValue('G1', 'Requested By');
                        $sheet->setCellValue('H1', 'Status');
                        $sheet->setCellValue('I1', 'Date');
                        $row = 2; // Start row for data

                        // Query Builder chunking
                        $query->whereBetween('lms_esigndockyc.addedOn', [$fromDate, $toDate])
                        ->chunk(500, function ($records) use ($sheet, &$row) {
                           foreach ($records as $record) {
                                $sheet->setCellValue('A' . $row, $record->leadID);
                                $sheet->setCellValue('B' . $row, $record->documentId);
                                $sheet->setCellValue('C' . $row, $record->name);
                                $sheet->setCellValue('D' . $row, $record->email);
                                $sheet->setCellValue('E' . $row, $record->mobile);
                                $sheet->setCellValue('F' . $row, $record->loanAmtApproved);
                                $sheet->setCellValue('G' . $row, $record->docRequestByName);
                                $sheet->setCellValue('H' . $row, $record->status);
                                $sheet->setCellValue('I' . $row, $record->addedOn);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = cmp()->companyName.'_'.'exported_kyc_esign_data.xlsx';

                        actLogs('KYC Data','E-sign Date range export',$request->all());
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
                $leads = $query->paginate(10);
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo($request->segment(2), $request->segment(1));
                $data = compact('leads','page_info', 'filter','queryParameters');
                return view('esignVideoKyc.esignList')->with($data);
                
            
    }    


    public function esignKycResendUpdate(Request $request){
        
            $data['docRequestUpdateBy'] = Session::get('userID');
            $data['loanStatus'] = $request->status;
            $data['status'] = $request->status;
            $query =DB::table('lms_esigndockyc')->where('esignID',$request->esignID)->update($data); 
            if($query){
                actLogs('KYC','e-sign resend mail status update',$request->all());
                return response()->json(['response'=>'success','message'=>'E-Sign resend mail status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'E-Sign resend mail status updation failed']);
            }
    }
    
   
public function videoList(Request $request) {
    // Fetch data from the first table (lms_videoKyc)
    $videoKycQuery = DB::table('lms_videoKyc')
        ->leftJoin('lms_leads', 'lms_leads.leadID', '=', 'lms_videoKyc.leadID')
        ->select(
            'lms_videoKyc.id',
            'lms_videoKyc.kycID',
            'lms_videoKyc.kycRequestID',
            'lms_videoKyc.leadID',
            'lms_videoKyc.customer_name AS customer_name',
            'lms_videoKyc.customer_identifier AS customer_identifier',
            'lms_videoKyc.cmVerified',
            'lms_videoKyc.verifiedBy',
            'lms_videoKyc.requestBy',
            'lms_videoKyc.status',
            'lms_videoKyc.addedOn',
            'lms_leads.status as leadStatus',
            DB::raw("'lms_videoKyc' as source_table")
        );

    // Fetch data from the second table (lms_videoKyc_self)
    $videoKycSelfQuery = DB::table('lms_videoKyc_self')
        ->leftJoin('lms_leads', 'lms_leads.leadID', '=', 'lms_videoKyc_self.leadID')
        ->select(
            'lms_videoKyc_self.id',
            'lms_videoKyc_self.kycID',
            'lms_videoKyc_self.kycRequestID',
            'lms_videoKyc_self.leadID',
            'lms_videoKyc_self.customer_name AS customer_name',
            'lms_videoKyc_self.customer_email AS customer_identifier',
            'lms_videoKyc_self.cmVerified',
            'lms_videoKyc_self.verifiedBy',
            'lms_videoKyc_self.requestBy',
            'lms_videoKyc_self.status',
            'lms_videoKyc_self.addedOn',
            'lms_leads.status as leadStatus',
            DB::raw("'lms_videoKyc_self' as source_table")
        );

    // Apply role-based filtering for Credit Managers
    if (role() == 'Sr. Credit Manager' || role() == 'Credit Manager') {
        $userID = getUserID(); // Helper to fetch current logged-in user ID
        $videoKycQuery->where(function($query) use ($userID) {
            $query->where('lms_leads.rmID', $userID)
                  ->orWhere('lms_leads.cmID', $userID);
        });
        $videoKycSelfQuery->where(function($query) use ($userID) {
            $query->where('lms_leads.rmID', $userID)
                  ->orWhere('lms_leads.cmID', $userID);
        });
    }

    // Execute the queries and get the results
    $videoKycData = $videoKycQuery->get();
    $videoKycSelfData = $videoKycSelfQuery->get();

    // Merge both datasets into a single collection
    $mergedData = $videoKycData->merge($videoKycSelfData);

    // Apply search filter if requested
    if ($request->filter == 'sortBySearch' && !empty($request->search)) {
        $search = $request->search;
        $mergedData = $mergedData->filter(function ($item) use ($search) {
            // Search across customer_name, customer_identifier, and leadID
            return stripos($item->customer_name, $search) !== false ||
                   stripos($item->customer_identifier, $search) !== false ||
                   stripos($item->leadID, $search) !== false;
        });
    }

    // Apply date filters
    if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
        $dates = explode(' - ', $request->searchRange);
        $fromDate = date('Y-m-d', strtotime($dates[0]));
        $toDate = date('Y-m-d', strtotime($dates[1]));
        $mergedData = $mergedData->filter(function ($item) use ($fromDate, $toDate) {
            $addedOn = date('Y-m-d', strtotime($item->addedOn));
            return $addedOn >= $fromDate && $addedOn <= $toDate;
        });
    } elseif ($request->filter == 'sortByToday') {
        $today = date('Y-m-d');
        $mergedData = $mergedData->filter(function ($item) use ($today) {
            return date('Y-m-d', strtotime($item->addedOn)) == $today;
        });
    } elseif ($request->filter == 'sortByWeek') {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $mergedData = $mergedData->filter(function ($item) use ($sevenDaysAgo, $today) {
            $addedOn = date('Y-m-d', strtotime($item->addedOn));
            return $addedOn >= $sevenDaysAgo && $addedOn <= $today;
        });
    } elseif ($request->filter == 'sortByThisMonth') {
        $currentMonth = date('m');
        $currentYear = date('Y');
        $mergedData = $mergedData->filter(function ($item) use ($currentMonth, $currentYear) {
            $addedOn = date('Y-m-d', strtotime($item->addedOn));
            return date('m', strtotime($addedOn)) == $currentMonth &&
                   date('Y', strtotime($addedOn)) == $currentYear;
        });
    } elseif ($request->filter == 'sortByLastMonth') {
        $lastMonth = date('m') - 1;
        $lastMonthYear = date('Y');
        if ($lastMonth == 0) {
            $lastMonth = 12;
            $lastMonthYear = date('Y') - 1;
        }
        $mergedData = $mergedData->filter(function ($item) use ($lastMonth, $lastMonthYear) {
            $addedOn = date('Y-m-d', strtotime($item->addedOn));
            return date('m', strtotime($addedOn)) == $lastMonth &&
                   date('Y', strtotime($addedOn)) == $lastMonthYear;
        });
    }

    // Sort the merged data by addedOn in descending order
    $mergedData = $mergedData->sortByDesc('addedOn');

    // Paginate the merged data manually
    $page = $request->get('page', 1); // Get the current page from the request
    $perPage = 10; // Number of items per page
    $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
        $mergedData->forPage($page, $perPage),
        $mergedData->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // Export logic
    if ($request->filter == 'exportAll') {
        // Export all data
        $exportData = $mergedData;
    } elseif ($request->filter == 'exportByDate' && !empty($request->exportRange)) {
        // Apply date range filter for export
        $dates = explode(' - ', $request->exportRange);
        $fromDate = date('Y-m-d', strtotime($dates[0]));
        $toDate = date('Y-m-d', strtotime($dates[1]));
        $exportData = $mergedData->filter(function ($item) use ($fromDate, $toDate) {
            $addedOn = date('Y-m-d', strtotime($item->addedOn));
            return $addedOn >= $fromDate && $addedOn <= $toDate;
        });
    } else {
        // Default export data (empty if no export filter is applied)
        $exportData = collect();
    }

    // If export is requested, generate the spreadsheet
    if ($request->filter == 'exportAll' || $request->filter == 'exportByDate') {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column headings
        $sheet->setCellValue('A1', 'leadID');
        $sheet->setCellValue('B1', 'KYC ID');
        $sheet->setCellValue('C1', 'Name');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Requested By');
        $sheet->setCellValue('F1', 'CM Approval Status');
        $sheet->setCellValue('G1', 'KYC Status');
        $sheet->setCellValue('H1', 'Date');
    
        $row = 2; // Start row for data
    
        // Loop through the export data and add to the spreadsheet
        foreach ($exportData as $record) {
            $sheet->setCellValue('A' . $row, $record->leadID);
            $sheet->setCellValue('B' . $row, $record->kycRequestID);
            $sheet->setCellValue('C' . $row, $record->customer_name);
            $sheet->setCellValue('D' . $row, $record->customer_identifier);
            $sheet->setCellValue('E' . $row, getUserNameById('users', 'userID', $record->requestBy, 'displayName'));
            $sheet->setCellValue('F' . $row, getUserNameById('users', 'userID', $record->verifiedBy, 'displayName'));
            $sheet->setCellValue('G' . $row, $record->status);
            $sheet->setCellValue('H' . $row, $record->addedOn);
            $row++;
        }
    
        // Write the spreadsheet to a file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'exported_kyc_video_data.xlsx';
    
        actLogs('KYC Data', 'Video Kyc export', $request->all());
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

    // Prepare data for the view
    $queryParameters = $request->query();
    $filter = $request->filter;
    $page_info = pageInfo($request->segment(2), $request->segment(1));
    $leads= $paginatedData;
    $data = compact('leads', 'page_info', 'filter', 'queryParameters');

    return view('esignVideoKyc.videoKycList')->with($data);
}
   
    public function videoKycResendUpdate(Request $request){
        $data['requestUpdateBy'] = Session::get('userID');
        $data['status'] = $request->status;
        $data['updatedOn'] = dt();
        
        // Try to update lms_videoKyc table
        $query = DB::table('lms_videoKyc')->where('kycID', $request->kycID)->update($data);
        
        if ($query) {
            // If successful, log and return success response
            actLogs('KYC', 'Video KYC resend mail status update', $request->all());
            return response()->json(['response' => 'success', 'message' => 'Video KYC resend mail status updated successfully']);
        } else {
            // If no rows updated, try updating lms_videoKycSelf
            $querySelf = DB::table('lms_videoKyc_self')->where('kycID', $request->kycID)->update($data);
            
            if ($querySelf) {
                // If update in lms_videoKycSelf is successful, log and return success response
                actLogs('KYC', 'Video KYC resend mail status update in lms_videoKycSelf', $request->all());
                return response()->json(['response' => 'success', 'message' => 'Video KYC resend mail status updated in lms_videoKycSelf successfully']);
            } else {
                // If both updates fail, return error response
                return response()->json(['response' => 'error', 'message' => 'Video KYC resend mail status updation failed']);
            }
        }
    }

}