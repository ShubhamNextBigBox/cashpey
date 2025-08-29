<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
 

class RedFlagController extends Controller
{
    public function redFlagList(Request $request) {
        
           
           $query = DB::table('lms_contact')
                    // Join with a subquery to get the latest lead for each contact
                    ->join(
                        DB::raw('(
                            SELECT contactID, leadID
                            FROM lms_leads
                            WHERE id IN (
                                SELECT MAX(id) FROM lms_leads GROUP BY contactID
                            )
                        ) AS latest_leads'), 'lms_contact.contactID', '=', 'latest_leads.contactID')
                    // Filter by redflag
                    ->where('lms_contact.redflag', '1')
                    // Select necessary fields from both tables
                    ->select(
                        'lms_contact.contactID',
                        'lms_contact.name',
                        'lms_contact.email',
                        'lms_contact.mobile',
                        'lms_contact.pancard',
                        'lms_contact.redFlagApproved',
                        'lms_contact.remarks',
                        'lms_contact.redFlagAddedOn',
                        'latest_leads.leadID'
                    )
                    // Order by leadID in descending order
                    ->orderByDesc('latest_leads.leadID'); // Assuming 'leadID' is the most reliable for sorting
                
                // Handling search filter
                if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_contact.name', 'like', "%{$search}%")
                              ->orWhere('lms_contact.email', 'like', "%{$search}%")
                              ->orWhere('lms_contact.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_contact.pancard', 'like', "%{$search}%")
                              ->orWhere('latest_leads.leadID', 'like', "%{$search}%");
                    });
                }
 
                if ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_contact.redFlagAddedOn', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_contact.redFlagAddedOn', $today);
                } elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_contact.redFlagAddedOn', [$sevenDaysAgo, $today]);
                }elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_contact.redFlagAddedOn', '=', date('m'))
                          ->whereYear('lms_contact.redFlagAddedOn', '=', date('Y'));
                }elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_contact.redFlagAddedOn', '=', $lastMonth)
                          ->whereYear('lms_contact.redFlagAddedOn', '=', $lastMonthYear);
                }elseif($request->filter == 'exportAll'){
 
                    // Create a new Spreadsheet object
                        $spreadsheet = new Spreadsheet();
                        $sheet = $spreadsheet->getActiveSheet();
                        
                        // Set column headings
                        $sheet->setCellValue('A1', 'ContactID');
                        $sheet->setCellValue('B1', 'Name');
                        $sheet->setCellValue('C1', 'Email');
                        $sheet->setCellValue('D1', 'Mobile');
                        $sheet->setCellValue('E1', 'Pancard');
                        $sheet->setCellValue('F1', 'Loan Approval');
                        $sheet->setCellValue('G1', 'Added By');
                        $sheet->setCellValue('H1', 'Approved By');
                        $sheet->setCellValue('I1', 'Remarks');
                        $sheet->setCellValue('J1', 'Added On');
                        $row = 2; // Start row for data

                        // Query Builder chunking
                        DB::table('lms_contact')
                        ->orderBy('id','desc') // Ensure you specify a column to order by
                        ->chunk(500, function ($records) use ($sheet, &$row) {
                            foreach ($records as $record) {

                                if($record->redFlagApproved=='1'){
                                    $approvalStatus = 'Approved';
                                }else{
                                    $approvalStatus = 'N/A';
                                }

                                $sheet->setCellValue('A' . $row, $record->contactID);
                                $sheet->setCellValue('B' . $row, $record->name);
                                $sheet->setCellValue('C' . $row, $record->email);
                                $sheet->setCellValue('D' . $row, $record->mobile);
                                $sheet->setCellValue('E' . $row, $record->pancard);
                               
                                $sheet->setCellValue('F' . $row, $approvalStatus);
                                $sheet->setCellValue('G' . $row, getUserNameById('users', 'userID', $record->redFlagAddedBy, 'displayName'));
                                $sheet->setCellValue('H' . $row, getUserNameById('users', 'userID', $record->redFlagApprovalBy, 'displayName'));
                                $sheet->setCellValue('I' . $row, $record->remarks);
                                $sheet->setCellValue('J' . $row, $record->redFlagAddedOn);
                                $row++;
                            }
                        });


                        // // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = 'exported_redflag_data.xlsx';

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
                        $sheet->setCellValue('A1', 'ContactID');
                        $sheet->setCellValue('B1', 'Name');
                        $sheet->setCellValue('C1', 'Email');
                        $sheet->setCellValue('D1', 'Mobile');
                        $sheet->setCellValue('E1', 'Pancard');
                        $sheet->setCellValue('F1', 'Loan Approval');
                        $sheet->setCellValue('G1', 'Added By');
                        $sheet->setCellValue('H1', 'Approved By');
                        $sheet->setCellValue('I1', 'Remarks');
                        $sheet->setCellValue('J1', 'Added On');
                        $row = 2; // Start row for data

                        // Query Builder chunking
                        DB::table('lms_contact')
                        ->orderBy('id','desc') // Ensure you specify a column to order by
                        ->whereBetween('lms_contact.redFlagAddedOn', [$fromDate, $toDate])
                        ->chunk(500, function ($records) use ($sheet, &$row) {
                           foreach ($records as $record) {

                                if($record->redFlagApproved=='1'){
                                    $approvalStatus = 'Approved';
                                }else{
                                    $approvalStatus = 'N/A';
                                }

                                $sheet->setCellValue('A' . $row, $record->contactID);
                                $sheet->setCellValue('B' . $row, $record->name);
                                $sheet->setCellValue('C' . $row, $record->email);
                                $sheet->setCellValue('D' . $row, $record->mobile);
                                $sheet->setCellValue('E' . $row, $record->pancard);
                               
                                $sheet->setCellValue('F' . $row, $approvalStatus);
                                $sheet->setCellValue('G' . $row, getUserNameById('users', 'userID', $record->redFlagAddedBy, 'displayName'));
                                $sheet->setCellValue('H' . $row, getUserNameById('users', 'userID', $record->redFlagApprovalBy, 'displayName'));
                                $sheet->setCellValue('I' . $row, $record->remarks);
                                $sheet->setCellValue('J' . $row, $record->redFlagAddedOn);
                                $row++;
                            }
                        });

                        // Write the spreadsheet to a file
                        $writer = new Xlsx($spreadsheet);
                        $fileName = 'exported_redflag_data.xlsx';

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
                
                if(role()=='Credit Manager'){
                    $userID = getUserID(); //helper to fetch current user logged in ID
                    $query->where(function($query) use ($userID) {
                        $query->where('lms_contact.redFlagAddedBy', $userID);
                    });
                }

                // Paginate the results
                $leads = $query->paginate(10);
                $queryParameters = $request->query();
                $filter = $request->filter;
                $page_info = pageInfo($request->segment(1), $request->segment(1));
                $data = compact('leads','page_info', 'filter','queryParameters');
                return view('redFlag.list')->with($data);
            
    }    


    public function redFlagApprovalStatusUpdate(Request $request){

            $data['redFlagApprovalBy'] = Session::get('userID');
            $data['redFlagApproved'] = $request->status;
            $data['redFlagUpdatedOn'] = dt();
            $query =DB::table('lms_contact')->where('contactID',$request->contactID)->update($data); 
            if($query){
                actLogs('Red Flag','approval status update',$data);
                return response()->json(['response'=>'success','message'=>'Red Flag status updated successfully']);
             }
            else{   
               return response()->json(['response'=>'error','message'=>'Red Flag status updation failed']);
            }
    }
}

