<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Mail\MailSender;
use Intervention\Image\Facades\Image;  


class ApiController extends Controller
{
    
  public function getFormData(Request $request){
    $ip = $request->ip();
    $apiData = $request->all();
    $addedOn = dt();

 
    // Extract PAN and Aadhar
    $pan = $apiData['pandetails']['result']['validated_data']['pan_number'];
    $aadhar = $apiData['aadharNo'];
 
 
    // Check if contact exists
    $existingContact = DB::table('lms_contact')
        ->where(['pancard' => $pan, 'aadharNo' => $aadhar])
        ->orderByDesc('id')
        ->first();

    $contactID = $existingContact->contactID ?? uniqueIDGenerator('lms_contact', 'contactID');
    $leadID = uniqueIDGenerator('lms_leads', 'leadID');
    
    $leadRMCMAssignData = $this->assignRMandCMFetch(); // function below
 
    $assignedRM = $leadRMCMAssignData['assignedRM'];
    $assignedRMIndex = $leadRMCMAssignData['assignedRMIndex'];
    $assignedCM = $leadRMCMAssignData['assignedCM'];
    $assignedCMIndex = $leadRMCMAssignData['assignedCMIndex'];

    // Prepare contact data
    $contactData = [
        'contactID' => $contactID,
        'name' => ucwords(strtolower($apiData['pandetails']['result']['validated_data']['full_name'])),
        'pancard' => $pan,
        'aadharNo' => $aadhar,
        'dob' => date('Y-m-d',strtotime($apiData['aadharData']['dob']) ?? null),
        'mobile' => $apiData['mobileNumber'],
        'email' => $apiData['personalEmail'],
        'marrital' => $apiData['maritalStatus'],
        'education' => $apiData['qualification'],
        // 'gender' => $this->getGender($aadharData['gender'] ?? null),
        'gender' =>  null,
        'addedOn' => $addedOn,
    ];

    // Prepare lead data
    $leadData = [
        'leadID' => $leadID,
        'contactID' => $contactID,
        'rmID' => $assignedRM,
        'cmID' => $assignedCM,
        'purpose' => $apiData['loanPurpose'],
        'loanRequired' => $apiData['amountData'],
        'tenure' => $apiData['tenureData'],
        'customerType' => $apiData['employeementStatus'],
        'residentialType' => $apiData['propertyType'],
        'utmSource' => $apiData['utm_source'] ?? 'Website',
        'domainName' => 'cashpey.com',
        'domainURL' => $apiData['domainURL'],
        'commingLeadsDate' => date('Y-m-d'),
        'countedRMID' => $assignedRMIndex,
        'countedCMID' => $assignedCMIndex,
        'status' => 'Fresh',
        'ip' => $ip,
        'addedOn' => $addedOn,
    ];

    // Addresses
    $addresses = [
        $this->formatAddress($leadID, $contactID, 'current', $apiData['currentAddress'], $ip, $addedOn),
        $this->formatAddress($leadID, $contactID, 'permanent', $apiData['permanentAddress'], $ip, $addedOn),
        $this->formatAddress($leadID, $contactID, 'aadhar', [
            'city' => $apiData['aadharData']['dist'] ?? null,
            'state' => $apiData['aadharData']['state'] ?? null,
            'pincode' => $apiData['aadharData']['zip'] ?? null,
            'address' => $apiData['aadharData']['house'] ?? null,
        ], $ip, $addedOn),
    ];


    $accountDetails = [
        'contactID' => $contactID,
        'leadID' => $leadID,
        'accountNo' => $apiData['accountNumber'],
        'bankName' => $apiData['bankDetails']['bankName'],
        'ifscCode' => $apiData['bankDetails']['ifsc'],
        'bankBranch' => $apiData['bankDetails']['branch'],
        'branchCity' => $apiData['bankDetails']['bankState'],
        'monthlyIncome' => $apiData['monthlyIncome'],
        'status' => 'Pending',
        'ip' => $ip,
        'addedOn' => $addedOn,
    ];
    
    $companyDetails = [
        'contactID' => $contactID,
        'leadID' => $leadID,
        'companyName' => $apiData['companyName'],
        'companyType' => $apiData['companyType'],
        'designation' => $apiData['designation'],
        'workExperience' => $apiData['workExperience'], // in months or years, depending on your DB
        'officialEmail' => $apiData['officialEmail'],
        'address' => $apiData['companyAddress'], // assuming it's a string, or format as needed
        'status' => 'Pending',
        'ip' => $ip,
        'addedOn' => $addedOn,
    ];


    // Insert data if not already exists
    if (!$existingContact){
        DB::table('lms_contact')->insert($contactData);
    }else{
        $leadData['alternate_mobile'] = $apiData['mobileNumber'];
        $leadData['alternate_email'] = $apiData['officialEmail'];
    } 
    
    DB::table('lms_leads')->insert($leadData);
    DB::table('lms_address')->insert($addresses);
    DB::table('lms_account_details')->insert($accountDetails);    
    DB::table('lms_company_details')->insert($companyDetails);    
    // Insert documents
    $this->insertDocuments($leadID, $contactID, $apiData['documents'] ?? [], $ip, $addedOn);

    // Insert profile image if available
    if (isset($apiData['profilePic'])) {
        $this->insertSingleDocument($leadID, $contactID, $apiData['profilePic'], 'Profile Image', $ip, $addedOn, 'profilePic');
    }
    
    
    $data = [
        'template' => 'emailTemplate.loanApplication',           // Blade view for the email
        'subject' => 'Confirmation of Loan Application Received- '.$leadID,                         // Email subject
        'templateData' => [                           // Data for the email view
            'leadID' => $leadID,
            'name' => $contactData['name'],
            'gender' => $contactData['gender'],
        ],
        'fromEmail' => 'no-reply@cashpey.com'       // Sender email
    ];

    $toEmail = array($apiData['personalEmail'],$apiData['officialEmail']);
    
    Mail::to($toEmail)->send(new MailSender($data));
  

    // message sent api
    $message = "Dear {$contactData['name']}, Thank you for choosing Cashpey for your credit needs. We're currently reviewing your loan application. Your reference number is {$leadID} . You can expect an update on the status within 48 working hours. We look forward to welcoming you on board soon! Best regards, Team Cashpey";
    $responseNotification = sendMobileNotification($contactData['mobile'], $message, '1007624928515820524');

 
    // if (!$responseNotification['success']) {
    //     return redirect()->back()->with('error', 'Email sent, but SMS failed: ' . ($responseNotification['message'] ?? 'Unknown error'));
    // }

    return response()->json([
        'status' => 'success',
        'appNo' => $leadID,
        'message' => 'Form submitted successfully',
    ], 200);
}

public function getGender($code)
{
    if ($code === 'M') {
        return 'Male';
    } elseif ($code === 'F') {
        return 'Female';
    } else {
        return 'Other';
    }
}

public function formatAddress($leadID, $contactID, $type, $data, $ip, $addedOn)
{
    return [
        'leadID' => $leadID,
        'contactID' => $contactID,
        'addressType' => ucwords($type),
        'city' => $data['city'],
        'state' => $data['state'],
        'pincode' => $data['pincode'],
        'address' => $data['address'],
        'status' => 'Pending',
        'ip' => $ip,
        'addedOn' => $addedOn,
    ];
}

public function insertDocuments($leadID, $contactID, $documents, $ip, $addedOn)
{
    $documentTypes = [
        'panImage' => 'Pancard',
        'aadharFrontBase64' => 'Aadhar Front Image',
        'aadharBackBase64' => 'Aadhar Back Image',
        'profilePic' => 'Profile Pic',
    ];

    foreach ($documentTypes as $key => $type) {
        if (isset($documents[$key])) {
            $this->insertSingleDocument($leadID, $contactID, $documents[$key], $type, $ip, $addedOn, $key);
        }
    }

    // Handle salary slips
    if (!empty($documents['salarySlipDocs']) && is_array($documents['salarySlipDocs'])) {
        foreach ($documents['salarySlipDocs'] as $index => $doc) {
            $this->insertSingleDocument($leadID, $contactID, $doc, 'Salary Slip', $ip, $addedOn, "salarySlip_$index");
        }
    }
    
     if (!empty($documents['bankStatementDocs']) && is_array($documents['bankStatementDocs'])) {
        foreach ($documents['bankStatementDocs'] as $index => $doc) {
            $this->insertSingleDocument($leadID, $contactID, $doc, 'Bank Statement', $ip, $addedOn, "bankStatement_$index");
        }
    }
    
}

public function insertSingleDocument($leadID, $contactID, $base64, $type, $ip, $addedOn, $prefix)
{
    DB::table('lms_documents')->insert([
        'leadID' => $leadID,
        'contactID' => $contactID,
        'documents' => $this->storeBase64Image($base64, $prefix, $contactID),
        'documentsType' => $type,
        'documentsStatus' => 'Pending',
        'ip' => $ip,
        'addedOn' => $addedOn
    ]);
}

public function storeBase64Image($base64String, $namePrefix, $contactID)
{
    [$meta, $data] = explode(',', $base64String);
    $mime = substr($meta, 5, strpos($meta, ';') - 5);
    $image = base64_decode($data);
    $extension = $this->getExtensionFromMime($mime);
    $path = "documentData/{$contactID}/{$namePrefix}.{$extension}";
    Storage::disk('public')->put($path, $image);
    return $path;
}

public function getExtensionFromMime($mimeType)
{
    return match ($mimeType) {
        // Images
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        
        // Documents
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
       
        // Archives
        'application/zip' => 'zip',
        'application/x-rar-compressed' => 'rar',
        'application/x-7z-compressed' => '7z',
      
        // Default fallback
        default => 'bin',
    };
}


        
//   public function checkReloanCustomer(Request $request)
//     {
//         // Get PAN and Aadhar Number from the request (with default values if not provided)
//         $pan = $request->post('pan', 'HVXPS1093G');  // Default to 'HVXPS1093G' if not provided
//         $aadharNo = $request->post('aadharNo', '394600022942');  // Default to '394600022942' if not provided
    
//         // Use Query Builder to fetch the data
//         $contact = DB::table('lms_contact')
//             ->leftJoin('lms_approval', 'lms_contact.contactID', '=', 'lms_approval.contactID')
//             ->leftJoin('lms_loan', 'lms_contact.contactID', '=', 'lms_loan.contactID')
//             ->where(function ($query) use ($pan, $aadharNo) {
//                 $query->where('lms_contact.pancard', $pan)
//                       ->orWhere('lms_contact.aadharNo', $aadharNo);
//             })
//             ->where('lms_approval.status', 'Approved')
//             ->where('lms_loan.status', 'Disbursed')
//             ->select(
//                 'lms_contact.contactID',
//                 'lms_approval.creditedBy',
//                 'lms_contact.pancard',
//                 'lms_contact.aadharNo',
//                 'lms_loan.leadID'
//             )
//             ->orderBy('lms_loan.contactID', 'desc') // Order by contactID descending (or use another field for ordering)
//             ->first(); // Get the first matching result
        
//         // Return response with data if contact exists
//         if ($contact) {
//             return response()->json([
//                 'status' => 'exists',
//                 'contactID' => $contact->contactID,
//                 'creditedBy' => $contact->creditedBy,
//                 'pancard' => $contact->pancard,
//                 'aadharNo' => $contact->aadharNo,
//                 'leadID' => $contact->leadID, // Include leadID in the response
//             ], 200);
//         } else {
//             return response()->json(['status' => 'not_exists'], 404);
//         }
//     }


    // fetching rmID from reloan data
    // public function getRmID(Request $request)
    // {
    //     // Assuming the 'leadID' is passed via request
    //      = $request->post('leadID') ?? '289310'; 
 
    //     $rmID = DB::table('lms_leads')
    //               ->where('leadID', )
    //               ->value('rmID');

    //     // Check if we found the rmID
    //     if ($rmID) {
    //         return response()->json([
    //             'status' => 'success',
    //             'rmID' => $rmID
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Lead not found'
    //         ], 404);
    //     }
    // }



    // Method to check if a contact exists by pan or aadhar if not the reloan customer
    // public function checkCustomerExist(Request $request)
    // {
    //     // Validate that both pancard and aadharNo are provided and are strings
    //     // $request->validate([
    //     //     'pan' => 'required|string',
    //     //     'aadharNo' => 'required|string'
    //     // ]);

    //     $pan = $request->post('pan', 'HVXPS1093G');   
    //     $aadharNo = $request->post('aadharNo', '394600022942');  
    

    //     // Use Query Builder to perform the query
    //     $contact = DB::table('lms_contact')
    //                 ->select('contactID', 'pancard', 'aadharNo')
    //                 ->where('pancard', $pan)
    //                 ->orWhere('aadharNo', $aadharNo)
    //                 ->orderByDesc('contactID')
    //                 ->first();

    //     // Check if we found any contact
    //     if ($contact) {
    //         return response()->json([
    //             'status' => 'success',
    //             'data' => $contact
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'No contact found'
    //         ], 404);
    //     }
    // }
    
    
   public function assignRMandCMFetch(){
        // Fetch RM List
        $rmList = DB::table('users')
            ->join('lms_users_details', 'users.userID', '=', 'lms_users_details.userID')
            ->leftJoin(
                DB::raw('(
                    SELECT userID, oldAssignedCM, newAssignedCM, id
                    FROM lms_leads_assignment
                    WHERE id = (
                        SELECT MAX(id)
                        FROM lms_leads_assignment AS l2
                        WHERE l2.userID = lms_leads_assignment.userID
                    )
                ) AS latest_assignments'),  // Alias added here
                'users.userID', '=', 'latest_assignments.userID'  // Referencing the alias in the join condition
            )
            ->whereIn('users.role', ['Relationship Manager'])
            ->where('users.status', 1)
            ->where('users.leadAssignment', 1)
            ->orderByDesc('users.id')
            ->get();

        // Fetch last assigned RM index
        $lastAssignedRMIndexRow = DB::table('lms_leads')
            ->whereNotNull('countedRMID')
            ->whereNotNull('countedCMID')
            ->orderByDesc('id')
            ->limit(1)
            ->value('countedRMID');

        // Default value for last assigned RM if not found
        $lastAssignedRMIndex = $lastAssignedRMIndexRow ?? -1;

        // Handle RM assignment
        if ($rmList->count() > 0) { // Check if RM list is not empty using count()
 
            $newRMIndex = ($lastAssignedRMIndex + 1) % $rmList->count();  // Cycle through RM list
            $assignedRM = $rmList[$newRMIndex]->userID;

 
           // Determine the reporting managers (credit manager according to assigned RM)
        
            $reportingManagers = !empty($rmList[$newRMIndex]->newAssignedCM)
                ? $rmList[$newRMIndex]->newAssignedCM
                : $rmList[$newRMIndex]->oldAssignedCM;

            $reportingManagers = explode(',', $reportingManagers);

            // Fetch Credit Managers based on reporting managers
            $quotedManagers = array_map(function ($manager) {
                return $manager;  // No need to wrap in single quotes for whereIn
            }, $reportingManagers);

            // Fetch Credit Managers (CM)
            $cmList = DB::table('users')
                ->whereIn('userID', $quotedManagers)  // Directly use the array
                ->whereIn('role', ['Sr. Credit Manager', 'Credit Manager'])
                ->where('status', 1)
                ->where('leadAssignment', 1)
                ->orderByDesc('id')
                ->get();
 
            // Assign CM
            if ($cmList->isEmpty()) {
                $assignedCM = 'CPY0046'; // Default CM if no CM found
                $newCMIndex = null;
            } else {
                // Fetch last assigned CM index for the assigned RM
                $lastAssignedCMIndexRow = DB::table('lms_leads')
                    ->where('rmID', $assignedRM)
                    ->whereNotNull('countedRMID')
                    ->whereNotNull('countedCMID')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->select('countedCMID')
                    ->first();

                $lastAssignedCMIndex = $lastAssignedCMIndexRow ? $lastAssignedCMIndexRow->countedCMID : -1;
                // Cycle through CM list
                $newCMIndex = ($lastAssignedCMIndex + 1) % $cmList->count();
                $assignedCM = $cmList[$newCMIndex]->userID;
            }
        } else {
            $assignedRM = 'CPY004'; // Default RM if no RM found
            $newRMIndex = null;
            $assignedCM = 'CPY005'; // Default CM if no CM found
            $newCMIndex = null;
        }


        // Return RM and CM assignment with their respective indexes
       return [
            'assignedRM' => $assignedRM,
            'assignedRMIndex' => $newRMIndex,
            'assignedCM' => $assignedCM,
            'assignedCMIndex' => $newCMIndex
        ];
    }
    
    
    
    public function getStates(){
        
        $states = DB::table('lms_states')
            ->where('status', 1)
            ->orderBy('stateName', 'asc')
            ->get(['stateID as id', 'stateName as name']);
    
        if ($states->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No active states found.',
                'data' => []
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Active states fetched successfully.',
            'data' => $states
        ]);
    }
    
    public function getCities(Request $request)
    {
        $stateID = $request->input('stateID');
         
        $cities = DB::table('lms_cities')
            ->where('stateID', $stateID)
            ->orderBy('cityName', 'asc')
            ->get(['cityID as id', 'cityName as name']);

        if ($cities->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No cities found.',
                'data' => $stateID
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cities fetched successfully.',
            'data' => $cities
        ]);
    }

    ////////////////////////////////// API's For Pan,Adhaar Verification OTP ////////////////////////////////////////
    
    
    
    public function pancardVerification(Request $request){
        
        $response = Http::withHeaders([
            'x-parse-rest-api-id' => 'nextbigboxpvtltd_docvms_liv',
            'x-parse-rest-api-key' => '31caadde213172318b2e89c120dcbf42',
            'Content-Type' => 'application/json',
        ])->post('https://in-pan-verify.signdesk.com/api/panverification', [
            'reference_id' => $request->reference_id,
            'source_type' => $request->source_type,
            'source' => $request->source,
        ]);

        return response()->json($response->json(), $response->status());
    }
    
   public function sendAadhaarOtp(Request $request){
       
        $referenceId = $request->input('reference_id');
        $aadhaarNumber = $request->input('source');
    
        $url = 'https://in-aadhaarxml-verify.signdesk.com/api/requestOTP';
    
        $headers = [
            'x-parse-rest-api-key' => '31caadde213172318b2e89c120dcbf42',
            'x-parse-application-id' => 'nextbigboxpvtltd_docvms_liv',
            'Content-Type' => 'application/json',
        ];
    
        $body = [
            'reference_id' => $referenceId,
            'source' => $aadhaarNumber,
        ];
    
        try {
            $response = Http::withHeaders($headers)->post($url, $body);
            $result = $response->json();
    
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'reference_id' => $result['reference_id'] ?? null,
                    'transaction_id' => $result['transaction_id'] ?? null,
                    'message' => $result['message'] ?? 'OTP request sent successfully.',
                ]);
            }
    
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Request failed.'
            ], 400);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while requesting OTP.'
            ], 500);
        }
    }



public function sendMobileOTP(Request $request)
{
    $mobileNumber = $request->input('mobileNumber');
    $otp = $request->input('otp');

    // Prepare the message by inserting the OTP dynamically
    $message = "Your OTP verification code from CashPey is $otp. Do not share it with anyone. This is a product of Naman Commodities Pvt. Ltd.";

    $url = 'https://msgn.mtalkz.com/api';

    $postData = [
        'apikey'     => '94AI4YTvTXb18i3U',            // mtalkz API key
        'senderid'   => 'CSHPEY',                      // Sender ID (6 characters)
        'number'     => $mobileNumber,                 // Mobile number
        'message'    => $message,                      // Dynamic OTP message
        'templateid' => '1007177873491706315',         // DLT template ID
        'format'     => 'json'
    ];

    try {
        // Send the POST request to the API
        $response = Http::post($url, $postData);
        $result = $response->json();

        // Check if the response indicates success
        if (isset($result['status']) && $result['status'] === 'OK') {

            DB::table('lms_enquiry')->insert(['mobile' => $mobileNumber]);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
                'data' => $otp
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP.',
            'data' => $result
        ]);

    } catch (\Exception $e) {
        // Catch any errors and return a response
        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP.',
            'error' => $e->getMessage()
        ]);
    }
}


public function updateStage(Request $request)
{
    $mobileNumber = $request->input('mobileNumber');
    $stage = $request->input('stage');
    DB::table('lms_enquiry')->where('mobile',$mobileNumber)->update(['stage' => $stage]);
}

    // public function resendMobileOTP(Request $request){
    //     $mobileNumber = $request->input('mobile');  // Mobile number to resend OTP
    //     $retryType = $request->input('retrytype');  // 'text' or 'voice' for retry type (Optional)
    
    //     // OTP Sending URL for Mtalkz
    //     $url = 'https://msgn.mtalkz.com/api';
    
    //     // Generate a new OTP (you can pass the same OTP if you have it stored from previous step)
    //     $otp = rand(100000, 999999);  // Generate random OTP (or retrieve it from the database if stored)
    
    //     $postData = [
    //         'apikey'     => '94AI4YTvTXb18i3U',  // Your Mtalkz API key
    //         'senderid'   => 'CSHPEY',  // Sender ID (6 characters)
    //         'number'     => $mobileNumber,  // Mobile number to send OTP
    //         'message'    => "Your OTP verification code from CashPey is $otp. Do not share it with anyone. This is a product of Naman Commodities Pvt. Ltd.",
    //         'templateid' => '1007177873491706315',  // Your Mtalkz template ID
    //         'format'     => 'json',  // Format response as JSON
    //     ];
    
    //     // Optionally, handle retry type here if needed
    //     if ($retryType) {
    //         $postData['retrytype'] = $retryType; // Specify whether it's 'text' or 'voice' retry
    //     }
    
    //     try {
    //         // Send POST request to Mtalkz API
    //         $response = Http::post($url, $postData);
    //         $result = $response->json();
    
    //         // Check if the API response indicates success
    //         if (isset($result['status']) && strtolower($result['status']) === 'ok') {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'OTP resent successfully.',
    //                 'data' => $result
    //             ], 200);
    //         }
    
    //         // Return failure if OTP resend fails
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to resend OTP.',
    //             'data' => $result
    //         ], 400);
    
    //     } catch (\Exception $e) {
    //         // Catch any errors and return failure message
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to resend OTP.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    
    // public function resendMobileOTP(Request $request){
    //     $mobileNumber =  $request->input('mobile');
    //     $type = $request->input('retrytype'); // 'text' or 'voice'
    //     $url = 'https://control.msg91.com/api/v5/otp/retry';
    
    //     try {
    //         $response = Http::post($url, [
    //             'authkey' => '393226AsHmSej34641d3746P1',
    //             'retrytype' => $type,
    //             'mobile' => $mobileNumber,
    //         ]);
    
    //         $result = $response->json();
    
    //         if (isset($result['type']) && $result['type'] === 'success') {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => $result['message'] ?? 'OTP resent successfully.'
    //             ], 200);
    //         }
    
    //         return response()->json([
    //             'success' => false,
    //             'message' => $result['message'] ?? 'Failed to resend OTP.'
    //         ], 400);
    
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to resend OTP.'
    //         ], 500);
    //     }
    // }


//   public function sendMobileOTP(Request $request){
//     $mobileNumber = $request->input('mobileNumber');
//     $otp = $request->input('otp');

//     $url = 'https://api.msg91.com/api/v5/otp';

//     $postData = [
//         'template_id' => '64461f39d6fc0579d35c3cb2',
//         'mobile' => $mobileNumber,
//         'authkey' => '393226AsHmSej34641d3746P1',
//         'otp_expiry' => '2',
//         'otp_length' => '6',
//         'otp' => $otp,
//     ];

//     try {
//         $response = Http::withHeaders([
//             'Content-Type' => 'application/json',
//         ])->post($url, $postData);

//         $result = $response->json();

//         if (isset($result['type']) && $result['type'] === 'success') {
//             return response()->json([
//                 'success' => true,
//                 'message' => $result['message'] ?? 'OTP sent successfully.'
//             ]);
//         }

//         return response()->json([
//             'success' => false,
//             'message' => $result['message'] ?? 'Failed to send OTP.'
//         ]);

//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Failed to send OTP.'
//         ]);
//     }
// }

    public function verifyAadhaarOtp(Request $request){
    $referenceId = $request->input('reference_id');
    $transactionId = $request->input('transaction_id');
    $otp = $request->input('otp');

    $url = 'https://in-aadhaarxml-verify.signdesk.com/api/submitOTP';

    $headers = [
        'x-parse-rest-api-key' => '31caadde213172318b2e89c120dcbf42',
        'x-parse-application-id' => 'nextbigboxpvtltd_docvms_liv',
        'Content-Type' => 'application/json',
    ];

    $body = [
        'reference_id' => $referenceId,
        'transaction_id' => $transactionId,
        'otp' => $otp,
    ];

    try {
        $response = Http::withHeaders($headers)->post($url, $body);
        $result = $response->json();

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'OTP verification failed.'
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong while verifying OTP.'
        ], 500);
    }
}

}