<?php

namespace App\Http\Controllers;
use App\Mail\MailSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DateTime;
use Carbon\Carbon;


class ProfileLoanController extends Controller
{
    
    public function profileLoan(Request $request, $leadID) {
        
            // Retrieve profile data for the specific lead
            $profileData = DB::table('lms_leads')
                ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                ->leftJoin('lms_account_details', 'lms_leads.leadID', '=', 'lms_account_details.leadID')
                ->leftJoin('lms_company_details', 'lms_leads.leadID', '=', 'lms_company_details.leadID')
                ->leftJoin('lms_address', function ($join) {
                    $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                         ->where('lms_address.addressType', '=', 'current');
                })
                ->select('lms_leads.leadID', 'lms_leads.contactID', 'lms_leads.utmSource','lms_leads.pdStatus', 'lms_leads.addedOn', 'lms_leads.rmID', 'lms_leads.cmID', 'lms_leads.purpose', 'lms_leads.loanRequired', 'lms_account_details.monthlyIncome','lms_account_details.ifscCode','lms_account_details.bankName','lms_account_details.bankBranch','lms_account_details.accountNo', 'lms_address.city', 'lms_address.state','lms_address.pincode', 'lms_leads.status', 'lms_leads.customerType', 'lms_leads.commingLeadsDate', 'lms_contact.name', 'lms_contact.gender', 'lms_contact.dob', 'lms_contact.mobile', 'lms_contact.email', 'lms_contact.pancard', 'lms_contact.aadharNo', 'lms_contact.redFlag','lms_contact.remarks','lms_contact.redFlagApproved')
                ->where('lms_leads.leadID', $leadID)
                ->orderBy('lms_leads.id', 'desc')
                ->first();

            $esignDoc = DB::table('lms_esigndockyc')->where(['leadID' => $leadID])->orderBy('esignID', 'desc')->first();
            $esigtampDoc = DB::table('lms_estamp')->where(['leadID' => $leadID])->first();
            $videoKycDoc = DB::table('lms_videoKyc')
            ->select('id', 'status', 'requestBy', 'addedOn', 'cmVerified', 'verifiedBy', 'videoFileID', 'updatedOn', 'server')
            ->where('leadID', $leadID)
            ->unionAll(  // Use UNION ALL instead of UNION to include duplicates if necessary
                DB::table('lms_videoKyc_self')
                    ->select('id', 'status', 'requestBy', 'addedOn', 'cmVerified', 'verifiedBy', 'videoFileID', 'updatedOn', 'server')
                    ->where('leadID', $leadID)
            )
            ->orderBy('id', 'desc')  // Apply ORDER BY after UNION to sort the combined result
            ->first();   
            
            $approvalData = DB::table('lms_approval')
            ->where('leadID', $leadID) 
            ->orderBy('id', 'desc') 
            ->first(); 
            
            $loanData = DB::table('lms_loan')
            ->select('loanNo','disbursalAmount','accountNo','ifscCode','bank','bankBranch','enachID','disbursalUtrNo','sheetSendDate','sheetSendTime','status','addedBy','disbursalDate','disburseTime','disbursedBy','remarks')
            ->where('leadID', $leadID) 
            ->orderBy('id', 'desc') 
            ->first();
          
            $repaymentScheduleSanction = DB::table('lms_emi_schedule_sanction')
            ->where('leadID', $leadID)
            ->get();
            
            $repaymentScheduleDisbursed = DB::table('lms_emi_schedule_disbursed')
            ->where('leadID', $leadID)
            ->get();
            
            $kycValidation = DB::table('lms_esigndockyc')
            ->leftJoin('lms_estamp', 'lms_esigndockyc.leadID', '=', 'lms_estamp.leadID')
            ->leftJoin('lms_videoKyc_self', 'lms_esigndockyc.leadID', '=', 'lms_videoKyc_self.leadID')
            ->where('lms_esigndockyc.leadID', $leadID)
            ->where('lms_esigndockyc.status', 'signed')
            ->whereIn('lms_videoKyc_self.status', ['approval_pending', 'approved'])
            ->orderBy('lms_esigndockyc.id', 'desc')
            ->count();
            
            
            
            $currentYear = date('Y');
            $currentMonth = date('m');
            
            // Query to fetch data for the current month and all previous months
            $paidSchedule = DB::table('lms_emi_schedule_disbursed')
                ->where('leadID', $leadID)  // Filter by leadID if needed
                ->whereYear('paymentDate', $currentYear)  // Filter by the current year
                ->whereMonth('paymentDate', '<=', $currentMonth)  // Include previous months and current month
                ->orderBy('paymentDate', 'desc')  // Order by payment date to get chronological data
                ->get();
           
            $collections = DB::table('lms_collection')
                            ->where('leadID', $leadID)
                            ->orderBy('collectedDate', 'desc')
                            ->get()
                            ->keyBy('installmentNo');
        
            $transactions = DB::table('lms_collection')
            ->where('leadID', $leadID)
            ->orderBy('collectedDate', 'desc')
            ->get();    
            
            $pennyData = DB::table('lms_penny_verification')
            ->where('leadID', $leadID)
            ->orderBy('id','desc')
            ->first();
         
            $lastPaymentDetails = DB::table('lms_emi_schedule_disbursed')
            ->select('id','emiAmount','paymentDate','installment')
            ->where('leadID', $leadID)
            ->where('status', 0)
            ->first();

            $preEmiAmountExists = DB::table('lms_pre_emi_payment')
            ->where('leadID', $leadID)
            ->count();
           
            if(!$profileData){
              return  redirect()->route('custom-404');
            }
            // Prepare page info and data for the view
            $page_info = pageInfo('Profile Loan Details', $request->segment(1));
            $data = compact('page_info','profileData','approvalData','repaymentScheduleSanction','paidSchedule','repaymentScheduleDisbursed','collections','loanData','lastPaymentDetails','transactions','kycValidation','pennyData','esignDoc','esigtampDoc','videoKycDoc','preEmiAmountExists');  
            return view('profile.profileLoanDetails')->with($data);
         
    }
    
     public function profileCheck(Request $request, $leadID) {
        
        
            // Retrieve profile data for the specific lead
            $profileData = DB::table('lms_leads')
                ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
                ->select('lms_leads.leadID', 'lms_leads.contactID', 'lms_leads.utmSource', 'lms_leads.addedOn', 'lms_leads.rmID', 'lms_leads.cmID', 'lms_leads.purpose', 'lms_leads.loanRequired', 'lms_leads.monthlyIncome', 'lms_leads.city', 'lms_leads.state', 'lms_leads.pincode', 'lms_leads.status', 'lms_leads.customerType', 'lms_leads.commingLeadsDate', 'lms_contact.name', 'lms_contact.gender', 'lms_contact.dob', 'lms_contact.mobile', 'lms_contact.email', 'lms_contact.pancard', 'lms_contact.aadharNo', 'lms_contact.redFlag','lms_contact.remarks','lms_contact.redFlagApproved')
                ->where('lms_leads.leadID', $leadID)
                ->orderBy('lms_leads.id', 'desc')
                ->first();
                
            $approvalData = DB::table('lms_approval')
            ->select(
                'leadID','clientID','loanID'
            )
            ->where('leadID', $leadID)  // Filter by leadID
            ->orderBy('id', 'desc')  // Order by id descending to get the most recent record
            ->first();  // Get the first (most recent) record
            
          
          if(!$profileData){
              return  redirect()->route('custom-404');
            }
       
            // Prepare page info and data for the view
            $page_info = pageInfo('Profile Loan Details', $request->segment(1));
            $data = compact('page_info','profileData');  
            return view('profile.check')->with($data);
         
    }
    
    
      public function approvedToFinal(Request $request){

            $validator = Validator::make($request->all(), [
                'status' => 'required',
                'leadID' => 'required',
                'remarks' => 'required'
            ]);
        
            // Handle validation failure
            if ($validator->fails()) {
                return response()->json([
                    'response' => 'failed',
                    'error' => $validator->errors()
                ]);
            }
            // Check if the sanction has already been approved
           

            if($request->status=='Approved'){
 
                $combinedBranchcode = branchCodeFetch($request->leadID); // check helper     
                $ln = loanNoGenerate($request->leadID); //check helper returning array value loanNo & digits
                    // Update the sanction status in the database
                $sanctionUpdateData = [
                    'loanNo' => $ln['loanNo'],
                    'digits' => $ln['digits'],
                    'branchCode' => $combinedBranchcode,
                    'status' => 'Approved',
                    'creditStatus' => 'Approved',
                    'finalRemarks' => $request->remarks,
                    'updatedOn' => now(), // Use `now()` to get current timestamp
                ];
                    // If successful, update database and log the action
                DB::table('lms_approval')->where('leadID', $request->leadID)->update($sanctionUpdateData);
                DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Approved']);
                // Log the action (optional)
                $sanctionUpdateData['leadID'] = $request->leadID;
                actLogs('Profile', 'sanction updated', $sanctionUpdateData);
                
                 // Respond with success
                return response()->json([
                    'response' => 'success',
                    'message' => 'Sanction approved successfully.'
                ]);

            }else{
                $sanctionUpdateData = [
                    'status' => 'Rejected',
                    'creditStatus' => 'Approved',
                    'finalRemarks' => $request->remarks,
                    'updatedOn' => now(), // Use `now()` to get current timestamp
                ];
                    // If successful, update database and log the action
                DB::table('lms_approval')->where('leadID', $request->leadID)->update($sanctionUpdateData);
                DB::table('lms_leads')->where('leadID',$request->leadID)->update(['status' => 'Rejected']);
                // Log the action (optional)
                $sanctionUpdateData['leadID'] = $request->leadID;
                actLogs('Profile', 'sanction updated', $sanctionUpdateData);
                
                $message = "Dear {{$templateData->name}}, your loan application (Ref. {$leadID}) was not approved based on our internal criteria. Thank you for choosing Cashpey.";
                $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007137248844038124');
                // Respond with success
                return response()->json([
                    'response' => 'success',
                    'message' => 'Sanction rejected successfully.'
                ]);
            }
        }
        
        
       public function pennyVerification(Request $request){
            // Validate the form data
            $validator = Validator::make($request->all(), [
                'pennycustName' => 'required|string',
                'pennyMobile' => 'required|digits:10',
                'pennyIfscCode' => 'required|string',
                'pennyBankName' => 'required|string',
                'pennyAccountNo' => 'required|numeric',
                'pennyCnfrmAccountNo' => 'required|same:pennyAccountNo',
                'leadID' => 'required',
                'contactID' => 'required'
            ]);
        
            // Handle validation failure
            if ($validator->fails()) {
                return response()->json([
                    'response' => 'failed',
                    'error' => $validator->errors()
                ]);
            }
        
            // Check if a record already exists for this leadID
            $exists = DB::table('lms_penny_verification')
                ->where('leadID', $request->leadID)
                ->exists();
        
            if ($exists) {
                return response()->json([
                    'response' => 'exist',
                    'message' => 'Penny verification already submitted for this lead.'
                ]);
            }
        
            // Prepare data for insertion
            $pennyData = [
                'leadID' => $request->leadID,
                'contactID' => $request->contactID,
                'customerName' => $request->pennycustName,
                'mobile' => $request->pennyMobile,
                'ifscCode' => $request->pennyIfscCode,
                'bankName' => $request->pennyBankName,
                'accountNo' => $request->pennyAccountNo,
                'ip' => $request->ip(),
                'addedBy' => Session::get('userID'),
                'addedOn' => dt()
            ];
        
            // Insert the data into your penny verification table
            DB::table('lms_penny_verification')->insert($pennyData);
        
            // Log activity if needed
            actLogs('Profile', 'Penny verification submitted', $pennyData);
        
            // Respond with success
            return response()->json([
                'response' => 'success',
                'message' => 'Penny verification submitted successfully.'
            ]);
        }


        public function pendingToDisburse(Request $request){
            // Check if the sanction has already been approved
            $validator = Validator::make($request->all(), [
                'disburseAmount' => 'required', // Address type is required
                'accountNo' => 'required', // Pincode is required, must be numeric and 6 digits
                'ifscCode' => 'required', // Pincode is required, must be numeric and 6 digits
                'bankName' => 'required', // State is required
                'branch' => 'required', // City is required
                'enachID' => 'required|min:6|unique:lms_loan,enachID', // Unique enachID in the specified table
                'leadID' => 'required', // Status is required
            ]);
 
            // Check if validation passes
            if ($validator->passes()) {
                // Prepare loan number and get approval data
                $loanNo = cmp()->loanNoPrefixes . $request->leadID . date('his');
                $getApprovalData = DB::table('lms_approval')->select('leadID','contactID','loanNo')->where('leadID', $request->leadID)->orderBy('id','desc')->first();
 
                // Prepare disbursal data for insertion
                $disbursedData = [
                    'loanNo' => $getApprovalData->loanNo,
                    'leadID' => $getApprovalData->leadID,
                    'contactID' => $getApprovalData->contactID,
                    'accountNo' => $request->accountNo,
                    'bank' => $request->bankName,
                    'ifscCode' => $request->ifscCode,
                    'bankBranch' => $request->branch,
                    'enachID' => $request->enachID,
                    'disbursalAmount' => $request->disburseAmount,
                    'sheetSendDate' => Carbon::now()->format('Y-m-d'),
                    'sheetSendTime' => Carbon::now()->format('h:i:s'),
                    'status' => 'Pending For Disburse',
                    'ip' => $request->ip(),
                    'addedOn' => dt(), // Assuming dt() returns the current datetime
                    'addedBy' => Session::get('userID')
                ];


                // Check if the loan record already exists
                $checkExists = DB::table('lms_loan')->where(['leadID'=>$getApprovalData->leadID])->count();
                if ($checkExists) {
                    return response()->json(['response' => 'exist', 'message' => 'This loan is already pending for disburse']);
                } else {
                    // Insert disbursal data
                   
                    DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Pending For Disburse']);
                    DB::table('lms_loan')->insert($disbursedData);
                    actLogs('Profile','disbursal sheet added',$disbursedData);
                    // Update lead status to disbursal
                    actLogs('Profile','leads status update disbursal sheet send',$request->leadID);                  
                    return response()->json(['response' => 'success', 'message' => 'Disburse updated successfully.']);
                }
            } else {
                // Return validation errors if validation fails
                return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
            }
        }
         public function approvedToDisburse(Request $request){
            // Check if the sanction has already been approved
            $validator = Validator::make($request->all(), [
                'disbursalUtrNo' => 'required|unique:lms_loan,disbursalUtrNo',
                'remarks' => 'required', 
                'leadID' => 'required', // Status is required
            ]);

            if($validator->passes()){
                $checkExists = DB::table('lms_loan')->where(['leadID' => $request->leadID, 'status' => 'Disbursed'])->orderBy('id', 'desc')->first();
                
                if ($checkExists) {
                    return redirect()->back()->with('success', 'Loan already disbursed.');
                } else {
                        $checkExistsUtr = DB::table('lms_loan')->where(['leadID' => $request->leadID, 'disbursalUtrNo' => $request->disbursalUtrNo])->orderBy('id', 'desc')->first();
                        if($checkExistsUtr){
                            return response()->json(['response' => 'exist', 'message' => 'This UTR Number is already existed.']);
                        }else{
                            
                            $sanctionData = DB::table('lms_approval')->select('loanNo','contactID','loanAmtApproved','disbursementAmount','tenure','repayDay','roi')->where('leadID',$request->leadID)->orderBy('id', 'desc')->first();
                            $disbursedData = [
                                'loanNo' => $sanctionData->loanNo,
                                'disbursalAmount' => $sanctionData->disbursementAmount,
                                'disbursalUtrNo' => $request->disbursalUtrNo,
                                'disbursalDate' => Carbon::now()->format('Y-m-d'),
                                'disburseTime' => Carbon::now()->format('h:i:s'),
                                'status' => 'Disbursed',
                                'disbursedBy' => Session::get('userID'),
                                'remarks' => $request->remarks
                            ];
                           
                          
                            $disbursementDate =  Carbon::now()->format('Y-m-d');
                            $loanTenure =  $sanctionData->tenure;
                            $repaymentDay = $sanctionData->repayDay;
                            $loanAmount = $sanctionData->loanAmtApproved;
                            $interestRate = $sanctionData->roi;
                            
                            // Ensure the loan amount and interest rate are numbers
                            $loanAmount = floatval($loanAmount);
                            $interestRate = floatval($interestRate);
                            $loanTenure = intval($loanTenure);
                            $repaymentDay = intval($repaymentDay);
                            
                            // Convert the disbursement date into a DateTime object
                            $disbursementDateObj = new \DateTime($disbursementDate);
                            
                            // Determine EMI start date based on the repayment day
                            $disbursementDay = (int)$disbursementDateObj->format('d');
                            if ($disbursementDay < 15) {
                                $emiStartDateObj = clone $disbursementDateObj;
                                $emiStartDateObj->modify('first day of next month');
                            } else {
                                $emiStartDateObj = clone $disbursementDateObj;
                                $emiStartDateObj->modify('first day of next month')->modify('+1 month');
                            }
                            
                            // Adjust the EMI start date to the selected repayment day
                            $emiStartDateObj->setDate(
                                $emiStartDateObj->format('Y'),
                                $emiStartDateObj->format('m'),
                                $repaymentDay
                            );
                            
                            // Ensure the EMI start date is valid
                            if (!checkdate($emiStartDateObj->format('m'), $emiStartDateObj->format('d'), $emiStartDateObj->format('Y'))) {
                                $emiStartDateObj->modify('last day of this month');
                            }
                            
                            // Calculate Days Difference
                            $daysDifference = $disbursementDateObj->diff($emiStartDateObj)->days;
                            
                            if ($daysDifference > 49) {
                               
                            }
                            
                            // Pre-EMI Interest Days Calculation
                            if ($daysDifference == 30) {
                                $preEmiInterestDays = 0;
                            } elseif ($daysDifference < 30) {
                                $preEmiInterestDays = 30 - $daysDifference;
                            } else {
                                $preEmiInterestDays = $daysDifference - 30;
                            }
                            
                            // Pre-EMI Interest Calculation
                            $preEmiInterest1 = ($loanAmount * $interestRate / 100 / 365) * $preEmiInterestDays;
                            $preEmiInterest = round($preEmiInterest1, 2);
                            
                            // EMI Calculation
                            if ($loanTenure > 0) { // Ensure the tenure is greater than zero
                                $monthlyInterestRate = $interestRate / 100 / 12;
                                $emi = round(($loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $loanTenure)) / (pow(1 + $monthlyInterestRate, $loanTenure) - 1), 2);
                            } else {
                                $emi = 0;
                            }
                            
                            // Generate EMI Schedule (for table)
                            $emiSchedule = [];
                            $openingBalance = $loanAmount;
                            $paymentDate = clone $emiStartDateObj;
                            
                            $paymentStartDate =  $paymentDate->format('Y-m-d');
                            
                            for ($i = 1; $i <= $loanTenure; $i++) {
                                // Calculate the interest for this EMI
                                $monthlyInterest = round($openingBalance * $monthlyInterestRate, 2);
                                
                                // Calculate the principal part of the EMI
                                $principal = round($emi - $monthlyInterest, 2);
                                
                                // Calculate the closing balance after the payment
                                $closingBalance = round($openingBalance - $principal, 2);
                            
                                // Store the schedule details
                                $emiSchedule[] = [
                                    'leadID' => $request->leadID, // Lead ID from the request
                                    'contactID' => $sanctionData->contactID, // Contact ID from the request
                                    'installment' => $i, // Contact ID from the request
                                    'paymentDate' => $paymentDate->format('Y-m-d'), // Formatted payment date
                                    'openingBalance' => round($openingBalance), // Opening balance for this EMI payment
                                    'emiAmount' => round($emi), // EMI amount to be paid
                                    'principalAmount' => round($principal), // Principal amount for this EMI
                                    'interestAmount' => round($monthlyInterest), // Interest amount for this EMI
                                    'closingBalance' => round($closingBalance), // Closing balance after this EMI
                                    'addedOn' => dt(), // Date and time when this entry was added (assumed dt() is a helper function)
                                ];
                            
                                // Update balance and move to next month
                                $openingBalance = $closingBalance;
                                $paymentDate->modify('+1 month');
                            }
                            
                            
                            // Calculate total interest (sum of all interest amounts in the schedule)
                               $totalInterestAmount = array_sum(array_column($emiSchedule, 'interestAmount'));
                            
                            // Define the EMI data
                               $emiData = compact('emi','preEmiInterest', 'preEmiInterestDays','emiSchedule', 'totalInterestAmount');
                               $paymentEndDate = end($emiSchedule)['paymentDate'];

                                $updateSanction = [
                                    'emi' => round($emiData['emi']),
                                    'paymentStartDate' => $paymentStartDate,
                                    'paymentEndDate' => $paymentEndDate,
                                    'preEmiInterest' => round($emiData['preEmiInterest']),
                                    'preEmiInterestDays' => round($emiData['preEmiInterestDays']),
                                    'preEmiInterestDaysDiff' => round($daysDifference),
                                    'totalInterestAmount' => round($emiData['totalInterestAmount'])
                                ];
                                
                                DB::table('lms_approval')->where('leadID', $request->leadID)->update($updateSanction);
                                DB::table('lms_emi_schedule_disbursed')->where('leadID', $request->leadID)->delete();
                                DB::table('lms_emi_schedule_disbursed')->insert($emiSchedule);
                                DB::table('lms_loan')->where('leadID', $request->leadID)->update($disbursedData);
                                DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Disbursed']);
                                
                                
                                $templateData = DB::table('lms_loan')
                                ->join('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                                ->join('lms_approval', 'lms_loan.leadID', '=', 'lms_approval.leadID')
                                ->select('lms_loan.loanNo','lms_loan.leadID','lms_loan.bank','lms_loan.accountNo','lms_loan.disbursalUtrNo','lms_approval.paymentStartDate','lms_contact.name','lms_contact.gender','lms_contact.email','lms_contact.mobile','lms_approval.officialEmail')
                                ->where('lms_loan.status', 'Disbursed')
                                ->where('lms_loan.leadID',$request->leadID)
                                ->orderBy('lms_loan.id','desc')
                                ->first();
                                
                                $fromEmail = 'disbursal@cashpey.com';        
                                $template = 'emailTemplate.disbursal';
                                $companyName = cmp()->companyName;
                                $subject = $companyName.' - Loan Disbursement Confirmation ('.$templateData->loanNo.')';
                                $mailData = compact('template', 'subject', 'templateData','fromEmail');
                                $receiversEmail = [$templateData->email, $templateData->officialEmail];
                                $ccEmails = array('confirmation@cashpey.com');
                    
                              
                                try {
                                   Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));
                                    
                                    $communicationData = [
                                        'leadID' => $request->leadID,
                                        'communicationType' => 'Email',
                                        'operation' => 'Disbural Mail Sent',
                                        'ip' => $request->ip(),
                                        'addedOn' => dt(), // Assuming dt() returns the current datetime
                                        'addedBy' => Session::get('userID'),
                                    ];
                                    
                                    // Insert communication data into the database
                                    actLogs('Profile', 'communication added', $communicationData);
                                    DB::table('lms_communication')->insert($communicationData);    
                                }
                                 catch (\Exception $e) {
                                        return response()->json(['response' => 'failed', 'message' => 'Failed to send the email.']);
                                    }
                                // Log the action (optional)
                                $disbursedData['leadID'] =  $request->leadID;
                                actLogs('Profile', 'disbured updated', $disbursedData);

                                $loanTenureMonths = $loanTenure . ' Months';
                                
                                $message = "Dear {$templateData->name}, your loan of Rs.{$loanAmount} at {$sanctionData->roi}% for {$loanTenureMonths} has been disbursed. EMI Rs.{$emiData['emi']}. Ensure funds on due date to avoid charges. –Team Cashpey";
                                $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007649077736958699');   
                                
                                return response()->json(['response' => 'success', 'message' => 'Loan disbursed successfully.']);
                    }
                }
                    
                }else {
                    // Return validation errors if validation fails
                    return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
                }
        }
    
        
         
      public function addRepayment(Request $request){
            // Check if the sanction has already been approved
            $validator = Validator::make($request->all(), [
                'dueDate' => 'required',  
                'repayAmount' => 'required',  
                'repaymentID' => 'required',
                'collectionMode' => 'required',  
                'collectionUtrNo' => 'required',  
                'collectedDate' => 'required',  
                'collectionSource' => 'required',  
                'status' => 'required',  
                'remark' => 'required',
                'leadID' => 'required',  
            ]);

 
            // Check if validation passes
            if ($validator->passes()) {
                
                $loanData = DB::table('lms_loan')->select('loanNo','contactID')->where(['leadID'=>$request->leadID])->orderBy('id','desc')->first();  
                
                
                $enachID =  enachNoGenerate($request->leadID,$loanData->loanNo); //check helper
                
                // // Prepare disbursal data for insertion
                $collectionData = [
                    'collectionID' => $request->repaymentID,
                    'loanNo' => $loanData->loanNo,
                    'leadID' => $request->leadID,
                    'contactID' => $loanData->contactID,
                    'enachID' =>$enachID,
                    'installmentNo' => $request->installmentNo,
                    'dueDate' => $request->dueDate,
                    'collectionUtrNo' => $request->collectionUtrNo,
                    'collectedAmount' => $request->repayAmount,
                    'collectedDate' => $request->collectedDate,
                    'collectionSource' => $request->collectionSource,
                    'collectedMode' => $request->collectionMode,
                    'paymentType' => 'Manual',
                    'status' => $request->status,
                    'remark' => $request->remark,
                    'ip' => $request->ip(),
                    'addedOn' => dt(), // Assuming dt() returns the current datetime
                    'addedBy' => Session::get('userID')
                ];

         
                // // Check if the payment record already exists
                $checkExists = DB::table('lms_collection')->where(['leadID'=>$request->leadID,'collectionID'=>$request->repaymentID])->count();
                if ($checkExists) {
                    return response()->json(['response' => 'exist', 'message' => 'This repayment is already paid']);
                } else {
                    // Insert collection data
                   
                    DB::table('lms_emi_schedule_disbursed')->where('id', $request->repaymentID)->update(['status' => 1]);
                    DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => $request->status]);
                    DB::table('lms_collection')->insert($collectionData);
                    actLogs('Profile','collection added',$collectionData);
                    return response()->json(['response' => 'success', 'message' => 'Repayment added successfully.']);
                }
            } else {
                // Return validation errors if validation fails
                return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
            }
        }
        
    public function addEmiInterestAmount(Request $request){
        $validator = Validator::make($request->all(), [
            'interestAmount' => 'required',
            'paymentDate' => 'required',
            'paymentMode' => 'required',
            'paymentUtrNo' => 'required',
            'remark' => 'required',
            'leadID' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }

        // Fetch loan & contactID using leadID
        $loanData = DB::table('lms_loan')
            ->select('loanNo', 'contactID')
            ->where('leadID', $request->leadID)
            ->orderByDesc('id')
            ->first();

        if (!$loanData) {
            return response()->json(['response' => 'failed', 'message' => 'Loan not found for this lead.']);
        }

        // Check duplicate (optional)
        $exists = DB::table('lms_pre_emi_payment')
            ->where('leadID', $request->leadID)
            ->count();

        if ($exists) {
            return response()->json(['response' => 'exist', 'message' => 'This interest payment is already added.']);
        }

        // Insert into lms_pre_emi_payment
        $data = [
            'leadID' => $request->leadID,
            'contactID' => $loanData->contactID,
            'loanNo' => $loanData->loanNo,
            'amount' => $request->interestAmount,
            'paymentDate' => $request->paymentDate,
            'paymentMode' => $request->paymentMode,
            'paymentUtrNo' => $request->paymentUtrNo,
            'addedOn' => dt()
        ];

        DB::table('lms_pre_emi_payment')->insert($data);

        // Log (optional)
        actLogs('Pre-EMI', 'Interest payment added',$data);

        return response()->json(['response' => 'success', 'message' => 'Interest repayment added successfully.']);
    }

    public function acceptByCustomer(Request $request, $leadID)
    {
        // Check if the sanction has already been approved
        $checkExists = DB::table('lms_approval')
                        ->where(['leadID' => $leadID, 'status' => 'Customer Approved'])
                        ->orderBy('id', 'desc')
                        ->first();
        
        $alreadyApproved = false;
        $approvalDate = now();
        
        if ($checkExists) {
            $alreadyApproved = true;
            $approvalDate = $checkExists->updatedOn ?? now();
        } else {
            // Update the sanction status in the database
            $checkPending = DB::table('lms_approval')
                        ->select('status')
                        ->where(['leadID' => $leadID, 'status' => 'Pending For Approval'])
                        ->orderBy('id', 'desc')
                        ->first();
     
           if($checkPending->status=='Pending For Approval'){
                $sanctionUpdateData = [
                    'status' => 'Customer Approved',
                    'updatedOn' => $approvalDate,
                ];
               
                DB::table('lms_approval')->where('leadID', $leadID)->update($sanctionUpdateData);
                DB::table('lms_leads')->where('leadID', $leadID)->update(['status' => 'Customer Approved','pdStart'=>1]);
                
                // Log the action
                $sanctionUpdateData['leadID'] = $leadID;
                actLogs('Profile', 'pre approval accepted by customer', $sanctionUpdateData);
           }else{
            $alreadyApproved = true;
            $approvalDate = $checkExists->updatedOn ?? now();
           }

        }
        
        $page_info = pageInfo('Sanction Approved', $request->segment(1));
        $data = compact('page_info', 'alreadyApproved', 'approvalDate');
        
        return view('sanction.customerApproved')->with($data);
    }

}