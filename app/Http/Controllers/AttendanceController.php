<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    
    public function attendanceList(Request $request) {
                $query = DB::table('lms_attendance')
                        ->select('lms_attendance.id','lms_attendance.userID','lms_attendance.attendanceDate','lms_attendance.signIN','lms_attendance.signOut')
                        ->join('users', 'lms_attendance.userID', '=', 'users.userID')
                        ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
                        ->where('users.status',1)
                        ->orderBy('lms_attendance.id','desc');

                if ($request->filter == 'sortBySearch' && !empty($request->search)) {
                    $search = $request->search;
                    $query->where(function ($query) use ($search) {
                        $query->where('lms_users_details.fullName', 'like', "%{$search}%")
                              ->orWhere('lms_users_details.mobile', 'like', "%{$search}%")
                              ->orWhere('lms_users_details.email', 'like', "%{$search}%");
                    });
                }elseif ($request->filter == 'sortByDate' && !empty($request->searchRange)) {
                    $dates = explode(' - ', $request->searchRange);
                    $fromDate = date('Y-m-d', strtotime($dates[0]));
                    $toDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereBetween('lms_attendance.attendanceDate', [$fromDate, $toDate]);
                } elseif ($request->filter == 'sortByToday') {
                    $today = date('Y-m-d');
                    $query->whereDate('lms_attendance.attendanceDate', $today);
                } elseif ($request->filter == 'sortByWeek') {
                    $today = date('Y-m-d');
                    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
                    $query->whereBetween('lms_attendance.attendanceDate', [$sevenDaysAgo, $today]);
                }elseif ($request->filter == 'sortByThisMonth') {
                    $query->whereMonth('lms_attendance.attendanceDate', '=', date('m'))
                          ->whereYear('lms_attendance.attendanceDate', '=', date('Y'));
                }elseif ($request->filter == 'sortByLastMonth') {
                    $lastMonth = date('m') - 1;
                    $lastMonthYear = date('Y');
                    if ($lastMonth == 0) {
                        $lastMonth = 12;
                        $lastMonthYear = date('Y') - 1;
                    }
                    $query->whereMonth('lms_attendance.attendanceDate', '=', $lastMonth)
                          ->whereYear('lms_attendance.attendanceDate', '=', $lastMonthYear);
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
                        actLogs('attendance list','exported all',$request->all());
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
                        $fileName = 'exported_attendance_data.xlsx';

                        actLogs('attendance list','exported date wise',$request->all());
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
                $attendanceList = $query->paginate(10);
                $queryParameters = $request->query();
                $filter = $request->filter;
                $users = DB::table('users')->where('role', '!=', 'Super Admin')->where('status', '=', 1)->orderBy('id', 'desc')->get();
                $page_info = pageInfo($request->segment(2), $request->segment(1));
                $data = compact('attendanceList','page_info','users','filter','queryParameters');
                return view('attendance.list')->with($data);
            
    }


    public function showAttendanceLog(Request $request){

       $attendanceLogs =  DB::table('lms_attendance_log')->select('userID','attendanceDate','time','punchType','address')->where(['userID'=>$request->userID,'attendanceDate'=>$request->attendanceDate])->orderBy('id','desc')->get();

       $attendanceData =  DB::table('lms_attendance')->select('userID','attendanceDate','signIN','signOut')->where(['userID'=>$request->userID,'attendanceDate'=>$request->attendanceDate])->orderBy('id','desc')->first();

       $punchInFirst =  DB::table('lms_attendance_log')->select('userID','time','punchType')->where(['userID'=>$request->userID,'attendanceDate'=>$request->attendanceDate,'punchType'=>'punchIN'])->first();

       $punchOutLast =  DB::table('lms_attendance_log')->select('userID','time','punchType')->where(['userID'=>$request->userID,'attendanceDate'=>$request->attendanceDate,'punchType'=>'punchOut'])->first();
 
                $punchInLogs = '';
                $punchOutLogs = '';

                 $timeDifference = '-';
                if (isset($attendanceData->signIN)) {
                    $signIN = new \DateTime($attendanceData->signIN);
                    $signOut = isset($attendanceData->signOut) ? new \DateTime($attendanceData->signOut) : null;

                    if ($signOut) {
                        $interval = $signIN->diff($signOut);
                        $timeDifference = $interval->format('%h hours %i minutes');
                    }
                }

                                     
                        // Separate punch in and punch out logs
                        foreach ($attendanceLogs as $logs) {
                            if ($logs->punchType == 'punchIN') {
                                $punchInLogs .= '
                                <div class="card ribbon-box mb-2">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-success float-end">
                                            <i class="ri-fingerprint-line"></i> Punch In
                                        </div>
                                        <h5 class="text-success float-start mt-0">'.$logs->time.'</h5>
                                        <div class="ribbon-content">
                                           '.$logs->address.'
                                        </div>
                                    </div>
                                </div>';
                            } else {
                                $punchOutLogs .= '
                                <div class="card ribbon-box mb-2">
                                    <div class="card-body">
                                        <div class="ribbon ribbon-danger float-end">
                                            <i class="ri-fingerprint-line"></i> Punch Out
                                        </div>
                                        <h5 class="text-danger float-start mt-0">'.$logs->time.'</h5>
                                        <div class="ribbon-content">
                                            '.$logs->address.'
                                        </div>
                                    </div>
                                </div>';
                            }
                        }

                        // Handle empty values
                        $punchInFirstTime = $punchInFirst ? $punchInFirst->time : 'No punch in recorded';
                        $punchOutLastTime = $punchOutLast ? $punchOutLast->time : 'No punch out recorded';

                        $output = '
                        <div class="row">
                            <div class="col-md-6">
                                <div class="punch-in-logs">
                                    '.$punchInLogs.'
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="punch-out-logs">
                                    '.$punchOutLogs.'
                                </div>
                            </div>
                        </div>
                        <div class="card mb-2 mt-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h5 class="text-success text-decoration-underline mt-0 mb-2">First Punch In</h5>
                                        <h6 class="text-dark mt-0">'.$punchInFirstTime.'</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-danger text-decoration-underline mt-0 mb-2">Last Punch Out</h5>
                                        <h6 class="text-dark mt-0">'.$punchOutLastTime.'</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <h5 class="text-info text-decoration-underline mt-0 mb-2">Total Working Hours</h5>
                                        <h6 class="text-dark mt-0">'.$timeDifference.'</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>';

                        echo $output;
    }


     public function attendanceAdd(Request $request){
   

        // Validate the request
        $validator = Validator::make($request->all(), [
            'employee' => 'required',
            'attendanceDate' => 'required',
            'punchIn' => 'required',
            'punchOut' => 'required',
        ]);

        if ($validator->passes()) {

            // Prepare the data for insertion
            $data = [ 
                'userID' => $request->employee,
                'attendanceDate' =>date('Y-m-d',strtotime($request->attendanceDate)),
                'signIN' => $request->punchIn,
                'signOut' => $request->punchOut,
                'signInAddress' => 'Manual Added',
                'city' =>'Manual Added',
                'updatedBy' => Session::get('userID'),
                'addedOn' => date('Y-m-d h:i:s a')   
            ];

            try {
                $checkExists =  DB::table('lms_attendance')->where(['userID'=>$request->employee,'attendanceDate'=>date('Y-m-d',strtotime($request->attendanceDate))])->orderBy('id','desc')->first();
                if($checkExists){
                    DB::table('lms_attendance')->where(['userID'=>$request->employee,'attendanceDate'=>date('Y-m-d',strtotime($request->attendanceDate))])->update($data);
                }else{
                    DB::table('lms_attendance')->insert($data);
                }
                actLogs('attendance','attendance added',$data);
                return response()->json(['response' => 'success', 'message' => 'Attendance Added successfully']);
            } catch (\Exception $e) {
                return response()->json(['response' => 'failed', 'message' => 'Attendance Added failed']);
            }

        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }


    public function attendanceEdit(Request $request){
   
        // Validate the request
        $validator = Validator::make($request->all(), [
            'employee' => 'required',
            'attendanceDate' => 'required',
            'punchIn' => 'required',
            'punchOut' => 'required',
        ]);

        if ($validator->passes()) {

            // Prepare the data for insertion
           $data = [ 
                'attendanceDate' =>date('Y-m-d',strtotime($request->attendanceDate)),
                'signIN' => $request->punchIn,
                'signOut' => $request->punchOut,
                'signInAddress' => 'Manual Added',
                'signOutAddress' => 'Manual Added',
                'city' =>'Manual Added',
                'updatedBy' => Session::get('userID'),
                'updatedOn' => date('Y-m-d h:i:s a'),
            ];

            try {
                DB::table('lms_attendance')->where(['id'=>$request->updateID])->update($data);
                 actLogs('attendance','attendance edit',$data);
                return response()->json(['response' => 'success', 'message' => 'Attendance edited successfully']);
            } catch (\Exception $e) {
                return response()->json(['response' => 'failed', 'message' => 'Attendance edit failed']);
            }

        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }
}
