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
use GuzzleHttp\Exception\RequestException;
use DateTime;

class ProfileController extends Controller
{


    public function profile(Request $request, $leadID)
    {


        // Retrieve profile data for the specific lead
        $profileData = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->leftJoin('lms_account_details', 'lms_leads.leadID', '=', 'lms_account_details.leadID')
            ->leftJoin('lms_company_details', 'lms_leads.leadID', '=', 'lms_company_details.leadID')
            ->leftJoin('lms_address', function ($join) {
                $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                    ->where('lms_address.addressType', '=', 'current');
            })
            ->select('lms_leads.leadID', 'lms_leads.contactID', 'lms_leads.utmSource','lms_leads.pdStatus','lms_leads.tenure', 'lms_leads.addedOn', 'lms_leads.alternate_mobile', 'lms_leads.rmID', 'lms_leads.cmID', 'lms_leads.purpose', 'lms_leads.loanRequired', 'lms_account_details.monthlyIncome', 'lms_account_details.accountNo', 'lms_account_details.bankName', 'lms_account_details.ifscCode', 'lms_account_details.bankBranch', 'lms_account_details.branchCity', 'lms_address.city', 'lms_address.state', 'lms_company_details.officialEmail', 'lms_address.pincode', 'lms_leads.status', 'lms_leads.customerType', 'lms_leads.commingLeadsDate', 'lms_leads.residentialType','lms_leads.pdStart', 'lms_contact.name', 'lms_contact.gender', 'lms_contact.dob', 'lms_contact.mobile', 'lms_contact.email', 'lms_contact.pancard', 'lms_contact.aadharNo', 'lms_contact.redFlag', 'lms_contact.remarks', 'lms_contact.redFlagApproved')
            ->where('lms_leads.leadID', $leadID)
            ->orderBy('lms_leads.id', 'desc')
            ->first();
     
        if (!$profileData) {
            return redirect()->route('custom-404');
        }
        // Get previous leads for the same contact, excluding the current lead
        $prevLeads = DB::table('lms_leads')
            ->select(
                'lms_leads.leadID',
                'lms_leads.status',
                'lms_approval.loanAmtApproved',
                'lms_approval.adminFee',
                'lms_approval.roi',
                'lms_approval.createdDate',
                DB::raw('(
                        SELECT MAX(collectedDate)
                        FROM lms_collection
                        WHERE lms_collection.leadID = lms_leads.leadID
                    ) AS latestCollectedDate')
            )
            ->leftJoin('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
            ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
            ->where('lms_leads.contactID', $profileData->contactID ?? null)
            ->where('lms_leads.leadID', '!=', $leadID)
            ->orderByDesc(DB::raw('(
                    SELECT MAX(collectedDate)
                    FROM lms_collection
                    WHERE lms_collection.leadID = lms_leads.leadID
                )'))
            ->paginate(5);

        // loan running check

        // First, check if the contactID exists in the lms_collection table
        $contactExists = DB::table('lms_collection')
            ->where('contactID', $profileData->contactID)
            ->exists();  // Check if any record with the given contactID exists

        if ($contactExists) {
            // If contactID exists, proceed to check the status and get the first record
            $loanRunning = DB::table('lms_collection')
                ->where('contactID', $profileData->contactID)
                ->where('status', '!=', 'Closed')
                ->where('status', '!=', 'Payday Preclose')
                ->orderBy('id', 'desc')
                ->first();

            if ($loanRunning) {
                $provideLoan = 0;
            } else {
                $provideLoan = 1;
            }

        } else {
            // Handle the case when the contactID does not exist
            $provideLoan = 1;
        }

        // Count total loans applied, disbursed, and rejected for the contact
        $totalLoanApplied = DB::table('lms_leads')->where('contactID', $profileData->leadID ?? null)->count();
        $totalLoanDisbursed = DB::table('lms_loan')->where(['contactID' => $profileData->contactID ?? null, 'status' => 'Disbursed'])->count();
        $totalLoanRejected = DB::table('lms_leads')->where(['contactID' => $profileData->contactID ?? null, 'status' => 'Rejected'])->count();
        
        
        $pdVerificationData = DB::table('lms_pd_verifications')->where(['leadID' => $profileData->leadID])->orderBy('id','desc')->get();

      
        // Retrieve approval data for the current lead
        $approvalData = DB::table('lms_approval')
            ->select(
                'leadID',
                'loanAmtApproved',
                'tenure',
                'roi',
                'branch',
                'adminFee',
                'monthlyIncome',
                'alternateMobile',
                'officialEmail',
                'cibil',
                'activePL',
                'activeHL',
                'activeCC',
                'activePaydayLoan',
                'outstandingAmount',
                'monthlyObligation',
                'status',
                'rejectionReason',
                'creditedBy',
                'adminGstAmount',
                'employed',
                'loanRequirePurpose',
                'createdDate',
                'residentialType',
                'pfPercentage',
                'pdVerifiedBy',
                'bankName',
                'creditStatus',
                'approvalRemarks',
                'matrixApprovalBy',
                'remark',
                'addedOn'
            )
            ->where('leadID', $leadID)  // Filter by leadID
            ->orderBy('id', 'desc')  // Order by id descending to get the most recent record
            ->first();  // Get the first (most recent) record

        // Retrieve previous approval data for the current lead
        $prevApprovalData = DB::table('lms_approval')
            ->select(
                'loanAmtApproved',
                'tenure',
                'roi',
                'branch',
                'adminFee',
                'monthlyIncome',
                'alternateMobile',
                'officialEmail',
                'cibil',
                'activePL',
                'activeHL',
                'activeCC',
                'activePaydayLoan',
                'outstandingAmount',
                'monthlyObligation',
                'status',
                'rejectionReason',
                'creditedBy',
                'adminGstAmount',
                'employed',
                'loanRequirePurpose',
                'createdDate',
                'residentialType',
                'pfPercentage',
                'bankName',
                'creditStatus',
                'approvalRemarks',
                'matrixApprovalBy',
                'remark'
            )
            ->where('contactID', $profileData->contactID ?? '')
            ->orderBy('id', 'desc') // Order by 'id' in descending order to get the last inserted record
            ->first(); // Get the first (most recent) record

        // Retrieve rejection data for the current lead
        $rejectionData = DB::table('lms_loan_rejection')
            ->select('branch', 'officialEmail', 'cibil', 'rejectionReason', 'remarks', 'addedBy', 'createdDate')
            ->where('leadID', $leadID)
            ->orderBy('id', 'desc')
            ->first();


        // Get address, company details, references, and documents for the contact
        $address = DB::table('lms_address')->select('addressType', 'state', 'city', 'pincode', 'address', 'status', 'id')->where('leadID', $profileData->leadID ?? '')->orderBy('id', 'desc')->get();
        $company = DB::table('lms_company_details')->select('companyName', 'companyType', 'designation', 'workExperience', 'address', 'status', 'id')->where('leadID', $profileData->leadID ?? '')->orderBy('id', 'desc')->get();
        $reference = DB::table('lms_reference')->select('referenceRelation', 'referenceName', 'referenceMobile', 'id')->where('leadID', $profileData->leadID ?? '')->orderBy('id', 'desc')->get();
        $documents = DB::table('lms_documents')->select('documentsType', 'documents', 'documentsPassword', 'documentsStatus', 'docRemarks', 'id')->where('leadID', $profileData->leadID ?? '')->orderBy('id', 'desc')->get();
        $checklist = DB::table('lms_checklist')->select('documentType', 'remark', 'id')->where('leadID', $profileData->leadID ?? '')->orderBy('id', 'desc')->get();
 
        // Get timeline, lead status, ROI, banks, loan data, collection data, and communication data
        $timeline = DB::table('lms_timeline')->select('status', 'addedBy', 'addedOn', 'remarks')->where('leadID', $leadID)->orderBy('id', 'desc')->get();
        $leadStatus = DB::table('lms_leads_status')->select('name')->where('status', 1)->orderBy('id', 'desc')->get();
        $roi = DB::table('lms_roi')->select('roi')->where('status', 1)->orderBy('id', 'desc')->get();

        $banks = DB::table('lms_banks')->select('bank', 'id')->where('status', '1')->distinct()->pluck('id', 'bank');

        $sanctionData = DB::table('lms_approval')->where('leadID', $leadID)->orderBy('id', 'desc')->first();

        $communicationData = DB::table('lms_communication')->select('communicationType', 'operation', 'addedOn', 'addedBy')->orderBy('id', 'desc')->where('leadID', $leadID)->get();

        // Retrieve users from the PD Team and branches
        $pdTeamUsers = DB::table('users')->select('userID', 'displayName')->where(['role' => 'Recovery Executive', 'status' => 1])->orderBy('id', 'desc')->get();


        // $branches = DB::table('lms_cities')->where(['status' => 1])->orderBy('addedOn', 'desc')->get();
        
        $branches = DB::table('lms_cities')
        ->where('status', 1) // check city status first
        ->whereIn('stateID', function ($query) {
            $query->select('stateID')
                  ->from('lms_states')
                  ->where('status', 1); // only active states
        })
        ->orderBy('addedOn', 'desc')
        ->get();

        // Get customer remarks related to the lead
        $customerRemarks = DB::table('lms_customer_remarks')->select('loanNo', 'legalNotice', 'remarksFile', 'commitmentDate', 'addedOn', 'id')->where('leadID', $leadID)->orderBy('id', 'desc')->get();
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

        // Determine credit managers based on user role
        if (Session::get('role') == 'Super Admin') {
            $creditManagers = DB::table('users')->select('userID', 'displayName')->whereIn('role', ['Sr. Credit Manager', 'Credit Manager'])->where('status', 1)->where('leadAssignment', 1)->orderBy('id', 'desc')->get();
        } else {
            $userDetails = DB::table('lms_users_details')->select('reportingManagers')->where('userID', Session::get('userID'))->first();
            $reportingManagers = explode(',', $userDetails->reportingManagers);
            $creditManagers = DB::table('users')->select('userID', 'displayName')->whereIn('userID', $reportingManagers)->whereIn('role', ['Sr. Credit Manager', 'Credit Manager'])->where('status', 1)->where('leadAssignment', 1)->orderBy('id', 'desc')->get();
        }

        // Check if profile data exists, if not, return 404
        if (!$profileData) {
            abort(404); // Lead not found, display 404 error page
        } else {
            // Store lead and contact IDs in session
            // Session::put('leadID', $leadID);
            // Session::put('contactID', $profileData->contactID);

            // Prepare page info and data for the view
            $page_info = pageInfo('Profile', $request->segment(1));
            $data = compact('profileData', 'timeline', 'address', 'company', 'sanctionData', 'reference', 'documents', 'checklist', 'leadStatus', 'page_info', 'prevLeads','pdVerificationData','roi', 'creditManagers', 'esignDoc', 'esigtampDoc', 'videoKycDoc', 'banks', 'approvalData', 'prevApprovalData', 'communicationData', 'pdTeamUsers', 'branches', 'rejectionData', 'totalLoanApplied', 'totalLoanDisbursed', 'totalLoanRejected', 'customerRemarks');
            return view('profile.profileEMI')->with($data);
        }
    }

    public function profileInfoEdit(Request $request)
    {
        // Get the contact ID from the request
        $contactID = $request->contactID;

        // Fetch contact data from the database using the contact ID
        $contactData = DB::table('lms_contact')->where('contactID', $contactID)->first();

        // Check if contact data exists; if not, return an error response
        if (!$contactData) {
            return response()->json(['error' => 'Contact not found'], 404);
        }

        // Return the contact data as a JSON response
        return response()->json($contactData);
    }


    public function profileInfoUpdate(Request $request)
    {
        // Define validation rules for the incoming request

        if (role() == 'CRM Support' || isSuperAdmin()) {
            $rules = [
                'nameOnPancard' => 'required', // Name on the PAN card is required
                'email' => 'required|email', // Email must be a valid email format
                'mobile' => 'required|min:10|max:12', // Mobile number must be between 10 and 12 digits
                'pancard' => 'required|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/', // PAN card format validation
                'adharNumber' => 'required|regex:/^\d{12}$/', // Aadhar number must be 12 digits
                'dob' => 'required|date', // Date of birth is required and must be a valid date
                'gender' => 'required', // Gender is required
            ];
        } elseif (isAdmin()) {
            $rules = [
                'email' => 'required|email', // Email must be a valid email format
                'mobile' => 'required|min:10|max:12', // Mobile number must be between 10 and 12 digits
            ];
        }

        // Conditionally add required rule for remarks if redFlag is set to 1
        if ($request->input('redFlag') == '1') {
            $rules['remarks'] = 'required'; // Remarks are required if redFlag is 1
        } else {
            $rules['remarks'] = 'nullable'; // Remarks are optional if redFlag is not 1
        }

        // Validate the request against the defined rules
        $validator = Validator::make($request->all(), $rules);

        // Check if validation passes
        if ($validator->passes()) {
            // Prepare contact data for updating
            if (role() == 'CRM Support' || isSuperAdmin()) {
                $contactData = [
                    'email' => $request->email,
                    'name' => ucwords($request->nameOnPancard), // Capitalize the name
                    'pancard' => strtoupper($request->pancard), // Convert PAN card to uppercase
                    'aadharNo' => strtoupper($request->adharNumber), // Convert Aadhar number to uppercase
                    'mobile' => $request->mobile,
                    'dob' => date('Y-m-d', strtotime($request->dob)), // Format date of birth
                    'gender' => $request->gender,
                    'redFlag' => $request->redFlag ?? 0,
                    'remarks' => $request->remarks ?? null,
                    'updatedOn' => dt(), // Set the current date and time for update
                ];
            } elseif (isAdmin()) {
                $contactData = [
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'redFlag' => $request->redFlag ?? 0,
                    'remarks' => $request->remarks ?? null,
                    'updatedOn' => dt(), // Set the current date and time for update
                ];
            } elseif (role() == 'Credit Manager' || role() == 'Sr. Credit Manager') {
                $contactData = [
                    'redFlag' => $request->redFlag ?? 0,
                    'remarks' => $request->remarks ?? null,
                    'updatedOn' => dt(), // Set the current date and time for update
                ];
            }


            // If redFlag is set to 1, add additional fields
            if ($request->redFlag == '1') {
                $contactData['redFlagAddedBy'] = Session::get('userID'); // Record who added the red flag
                $contactData['redFlagAddedOn'] = date('Y-m-d'); // Record when the red flag was added
                $contactData['redFlagUpdatedOn'] = date('Y-m-d'); // Record when the red flag was added
            }


            // Update the contact information in the database
            DB::table('lms_contact')->where(['contactID' => $request->contactID])->update($contactData);
            $contactData['contactID'] = $request->contactID; // Add contactID if it's an object or use $contactData['contactID'] if it's already an array

            actLogs('Profile', 'customer profile updated', $contactData);  // Return success response
            return response()->json(['response' => 'success', 'message' => 'Contact updated successfully']);
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }


    public function addTimeline(Request $request)
    {
        // Validate incoming request data
        // $validator = Validator::make($request->all(), [
        //     'status' => 'required', // Status is required
        //     'remarks' => 'required', // Remarks are required
        //     'cmID' => 'required_if:status,Document Received', // cmID is required if status is "Document Received"
        // ], [
        //     'cmID.required_if' => 'The credit manager is required', // Custom error message for cmID
        // ]);

        $validator = Validator::make($request->all(), [
            'status' => 'required', // Status is required
            'remarks' => 'required', // Remarks are required
        ]);


        // Check if validation fails
        if ($validator->fails()) {
            // Redirect back with validation errors and input
            return redirect()->back()->withErrors($validator)->withInput()->with(['active_tab' => $request->active_tab, 'error' => 'Timeline failed to add.']);
        }

        // Prepare timeline data for insertion
        $timelineData = [
            'status' => ucwords($request->status), // Capitalize the status
            'leadID' => $request->leadID, // Get lead ID from session
            'remarks' => ucwords($request->remarks), // Capitalize the remarks
            'date' => date('Y-m-d'), // Current date
            'ip' => $request->ip(), // User's IP address
            'addedOn' => dt(), // Current date and time for addition
            'addedBy' => Session::get('userID') // User ID from session
        ];

        // Prepare data for updating leads
        $updateLeads = [
            // 'cmID' => $request->cmID, // Credit manager ID
            // 'rmID' => Session::get('userID'), // Relationship manager ID from session
            'status' => $request->status, // Status to update
        ];

        actLogs('Profile', 'leads updated', $updateLeads);
        // Update the lead record in the database
        DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => $request->status]);

        // Check if a timeline entry with the same status already exists
        $checkExists = DB::table('lms_timeline')->where(['leadID' => $request->leadID, 'status' => $request->status])->first();
        if ($checkExists) {
            // If exists, redirect back with a failure message
            return redirect()->back()->with('failed', 'Timeline already existed.');
        } else {
            // Otherwise, insert the new timeline entry

            if($request->status=='Not Eligible'){
                $templateData = DB::table('lms_contact')->select('name','mobile','email')->where('contactID', $request->contactID)->orderBy('id','desc')->first();
                  
                $fromEmail = 'info@cashpey.com';
                $template = 'emailTemplate.notEligible';
                $companyName = cmp()->companyName;
                $subject = $companyName . ' - Not Eligible';
                $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
                $receiversEmail = [$templateData->email];
                $ccEmails = array('confirmation@cashpey.com');

 
                Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));
                
                $message = "Dear {$templateData->name}, your loan application (Ref. {$request->leadID}) was not approved based on our internal criteria. Thank you for choosing Cashpey.";
                $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007137248844038124');
            }
            actLogs('Profile', 'timeline added', $timelineData);
            DB::table('lms_timeline')->insert($timelineData);
        }

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Timeline added successfully.');
    }



    public function addAddress(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'addressType' => 'required', // Address type is required
            'pincode' => 'required|numeric|digits:6', // Pincode is required, must be numeric and 6 digits
            'addState' => 'required', // State is required
            'addCity' => 'required', // City is required
            'address' => 'required', // Address is required
            'status' => 'required', // Status is required
        ]);

        // Check if validation passes
        if ($validator->passes()) {
            // Prepare address data for insertion/updating
            $addressData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'addressType' => $request->addressType, // Address type from request
                'state' => $request->addState, // State from request
                'city' => $request->addCity, // City from request
                'pincode' => $request->pincode, // Pincode from request
                'address' => ucwords($request->address), // Capitalize the address
                'status' => $request->status ?? null, // Status from request, or null
                'ip' => $request->ip(), // User's IP address
                'addedOn' => dt(), // Current date and time for addition
                'addedBy' => Session::get('userID') // User ID from session
            ];

            // Check if an address with the given ID already exists
            $count = DB::table('lms_address')->where('id', $request->id)->count();
            if ($count > 0) {
                // If it exists, unset 'addedOn' and set 'updatedOn'
                unset($addressData['addedOn']);
                $addressData['updatedOn'] = dt(); // Set the current date and time for update
                // Update the existing address record
                DB::table('lms_address')->where('id', $request->id)->update($addressData);
                actLogs('Profile', 'address update', $addressData);
            } else {
                // Otherwise, insert the new address record
                actLogs('Profile', 'address added', $addressData);
                DB::table('lms_address')->insert($addressData);
            }

            // Return a success response
            return response()->json(['response' => 'success', 'message' => 'Address added successfully.']);
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }


    public function updateAddByPD(Request $request)
    {
        // Validate the incoming request
        // $request->validate([
        //     'address_id' => 'required|exists:addresses,id',  // Ensure address_id exists in the addresses table
        //     'status' => 'required|string',
        //     'remarks' => 'required|string',
        //     'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Optional image validation
        // ]);


        $addressData = [
            'status' => $request->status,
            'remarks' => $request->remarks,
        ];

        // Check if an image was uploaded
        if ($request->hasFile('image')) {
            // Store the image and get the path
            $imagePath = $request->file('image')->store('address_images', 'public');

            // Optionally delete the old image if necessary
            if ($address->image) {
                Storage::disk('public')->delete($address->image);
            }

            // Save the new image path
            $address->image = $imagePath;
        }

        DB::table('lms_address')->where('id', $request->id)->update($addressData);

        return response()->json(['success' => true, 'message' => 'Data updated successfully']);
    }

    public function editProfileAddress(Request $request)
    {
        $id = $request->id; // Get the address ID from the request
        // Fetch the address data from the database
        $address = DB::table('lms_address')->where('id', $id)->first();

        if ($address) {
            // If the address exists, return JSON response with address details
            return response()->json([
                'success' => true,
                'data' => $address
            ]);
        } else {
            // If the address does not exist, return a failure response
            return response()->json([
                'success' => false,
                'message' => 'Address not found'
            ]);
        }
    }


    public function addressDelete(Request $request)
    {
        $id = $request->id; // Get the address ID from the request

        // Attempt to delete the address with the given ID
        $query = DB::table('lms_address')->where('id', $id)->delete();

        // Check if the deletion was successful
        if ($query) {
            actLogs('Profile', 'address delete', $request->all());
            // Return a success response if the address was deleted
            return response()->json(['response' => 'success', 'message' => 'Address deleted successfully']);
        }

        // Return an error response if the deletion failed
        return response()->json(['response' => 'error', 'message' => 'Address failed to delete']);
    }

    public function addCompany(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'companyAddress' => 'required', // Company address is required
            'companyName' => 'required', // Company name is required
            'status' => 'required', // Status is required
        ]);

        // Check if validation passes
        if ($validator->passes()) {
            // Prepare company data for insertion/updating
            $companyData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'address' => $request->companyAddress, // Company address from request
                'companyName' => $request->companyName, // Company name from request
                'status' => $request->status ?? null, // Status from request, or null
                'ip' => $request->ip(), // User's IP address
                'addedOn' => dt(), // Current date and time for addition
                'addedBy' => Session::get('userID') // User ID from session
            ];

            // Check if a company record with the given ID already exists
            $count = DB::table('lms_company_details')->where('id', $request->id)->count();
            if ($count > 0) {
                // If it exists, unset 'addedOn' and set 'updatedOn'
                unset($companyData['addedOn']);
                $companyData['updatedOn'] = dt(); // Set the current date and time for update
                // Update the existing company record
                actLogs('Profile', 'company update', $companyData);
                DB::table('lms_company_details')->where('id', $request->id)->update($companyData);
            } else {
                // Otherwise, insert the new company record
                actLogs('Profile', 'company added', $companyData);
                DB::table('lms_company_details')->insert($companyData);
            }

            // Return a success response
            return response()->json(['response' => 'success', 'message' => 'Company added successfully.']);
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }

    public function editProfileCompany(Request $request)
    {
        $id = $request->id; // Get the company ID from the request
        // Fetch the company details from the database
        $company = DB::table('lms_company_details')->where('id', $id)->first();

        // Check if the company exists
        if ($company) {
            // Return JSON response with company details
            return response()->json([
                'success' => true,
                'data' => $company
            ]);
        } else {
            // Return JSON response indicating failure (company not found)
            return response()->json([
                'success' => false,
                'message' => 'Company not found'
            ]);
        }
    }

    public function companyDelete(Request $request)
    {
        $id = $request->id; // Get the company ID from the request
        // Attempt to delete the company with the given ID
        $query = DB::table('lms_company_details')->where('id', $id)->delete();

        // Check if the deletion was successful
        if ($query) {
            // Return a success response if the company was deleted
            actLogs('Profile', 'address delete', $request->all());
            return response()->json(['response' => 'success', 'message' => 'Company deleted successfully']);
        }
        // Return an error response if the deletion failed
        return response()->json(['response' => 'error', 'message' => 'Company failed to delete']);
    }

    public function addReference(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'referenceRelation' => 'required', // Reference relation is required
            'referenceName' => 'required', // Reference name is required
            'referenceMobile' => 'required|digits:10', // Reference mobile must be 10 digits
            //  'status' => 'required', // Status is required
        ]);

        // Check if validation passes
        if ($validator->passes()) {
            // Prepare reference data for insertion/updating
            $referenceData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'referenceRelation' => $request->referenceRelation, // Reference relation from request
                'referenceName' => $request->referenceName, // Reference name from request
                'referenceMobile' => $request->referenceMobile, // Reference mobile from request
                'status' => $request->status ?? null, // Status from request, or null
                'ip' => $request->ip(), // User's IP address
                'addedOn' => dt(), // Current date and time for addition
                'addedBy' => Session::get('userID') // User ID from session
            ];

            // Check if a reference record with the given ID already exists
            $count = DB::table('lms_reference')->where('id', $request->id)->count();
            if ($count > 0) {
                // If it exists, unset 'addedOn' and set 'updatedOn'
                unset($referenceData['addedOn']);
                $referenceData['updatedOn'] = dt(); // Set the current date and time for update
                // Update the existing reference record
                actLogs('Profile', 'reference update', $referenceData);
                DB::table('lms_reference')->where('id', $request->id)->update($referenceData);
            } else {
                // Otherwise, insert the new reference record
                actLogs('Profile', 'reference added', $referenceData);
                DB::table('lms_reference')->insert($referenceData);
            }

            // Return a success response
            return response()->json(['response' => 'success', 'message' => 'Reference added successfully.']);
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }


    public function editProfileReference(Request $request)
    {
        $id = $request->id; // Get the reference ID from the request
        // Fetch the reference details from the database
        $reference = DB::table('lms_reference')->where('id', $id)->first();

        // Check if the reference exists
        if ($reference) {
            // Return JSON response with reference details
            return response()->json([
                'success' => true,
                'data' => $reference
            ]);
        } else {
            // Return JSON response indicating failure (reference not found)
            return response()->json([
                'success' => false,
                'message' => 'Reference not found'
            ]);
        }
    }

    public function referenceDelete(Request $request)
    {
        $id = $request->id; // Get the reference ID from the request
        // Attempt to delete the reference with the given ID
        $query = DB::table('lms_reference')->where('id', $id)->delete();

        // Check if the deletion was successful
        if ($query) {
            // Return a success response if the reference was deleted
            actLogs('Profile', 'reference delete', $request->all());
            return response()->json(['response' => 'success', 'message' => 'Reference deleted successfully']);
        }
        // Return an error response if the deletion failed
        return response()->json(['response' => 'error', 'message' => 'Reference failed to delete']);
    }

    public function addDocuments(Request $request)
    {

        // Validate incoming request data based on whether an ID is present
        if (!empty($request->id)) {
            // Check user role, apply role-specific validation rules
            if (isSuperAdmin() || role() == 'Recovery Executive') {
                $validator = Validator::make($request->all(), [
                    'documentsType' => 'required',
                    'documentsStatus' => 'required',
                    'docRemarks' => 'required',
                    'oldDocument' => 'required',
                ]);
            } else { // Replace 'user' with your role
                $validator = Validator::make($request->all(), [
                    'documentsType' => 'required',
                    'oldDocument' => 'nullable', // Optional for regular users
                ]);
            }
        } else {
            if (isSuperAdmin() || role() == 'Recovery Executive') {
                $validator = Validator::make($request->all(), [
                    'documentsType' => 'required',
                    'documentsStatus' => 'required',
                    'docRemarks' => 'required',
                    'oldDocument' => 'nullable',
                ]);
            } else { // Replace 'user' with your role
                $validator = Validator::make($request->all(), [
                    'documentsType' => 'required',
                    'oldDocument' => 'nullable', // Optional for regular users
                ]);
            }
        }

        // Check if validation passes
        if ($validator->passes()) {
            // Handle file upload if a document is provided
            if ($request->hasFile('documents')) {
                $file = $request->file('documents'); // Get the uploaded file
                $documentName = $file->getClientOriginalName(); // Get the original file name

                $contactID = $request->contactID;
                //  Storage::disk('public')->put($filePath, $response);
                $filePath = $file->storeAs('documentData/' . $contactID, $documentName, 'public');

                $documents = $filePath; // Set the document name
            } else {
                // Use the old document name if no new file was uploaded
                $documents = $request->oldDocument ?? null;
            }

            // Prepare documents data for insertion/updating
            $documentsData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'documentsType' => $request->documentsType, // Document type from request
                'documents' => $documents, // Document name
                'documentsPassword' => $request->documentsPassword, // Document password
                'docRemarks' => $request->docRemarks, // Document password
                'documentsStatus' => $request->documentsStatus ?? 'Pending', // Document status
                'ip' => $request->ip(), // User's IP address
                'addedOn' => dt(), // Current date and time for addition
                'addedBy' => Session::get('userID') // User ID from session
            ];

            // Check if a document record with the given ID already exists
            $count = DB::table('lms_documents')->where('id', $request->id)->count();
            if ($count > 0) {
                // If it exists, unset 'addedOn' and set 'updatedOn'
                unset($documentsData['addedOn']);
                $documentsData['updatedOn'] = dt(); // Set the current date and time for update
                // Update the existing document record
                actLogs('Profile', 'document update', $request->all());
                DB::table('lms_documents')->where('id', $request->id)->update($documentsData);
            } else {
                // Otherwise, insert the new document record
                actLogs('Profile', 'document added', $request->all());
                DB::table('lms_documents')->insert($documentsData);
            }
            // Return a success response
            return response()->json(['response' => 'success', 'message' => 'Documents added successfully.']);
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }




    public function updateDocByPD(Request $request)
    {
        $id = $request->docId; // Get the document ID from the request
        // Fetch the document details from the database
        $data = ['documentsStatus' => $request->status, 'docRemarks' => $request->remarks];
        $documents = DB::table('lms_documents')->where('id', $id)->update($data);

        if ($documents) {
            return response()->json(['response' => 'success', 'message' => 'Documents updated successfully.']);
        } else {
            return response()->json(['response' => 'failed', 'message' => 'Failed to update document.']);
        }
    }


    public function editProfileDocument(Request $request)
    {
        $id = $request->id; // Get the document ID from the request
        // Fetch the document details from the database
        $documents = DB::table('lms_documents')->where('id', $id)->first();

        // Check if the document exists
        if ($documents) {
            // Return JSON response with document details
            return response()->json([
                'success' => true,
                'data' => $documents
            ]);
        } else {
            // Return JSON response indicating failure (document not found)
            return response()->json([
                'success' => false,
                'message' => 'Documents not found'
            ]);
        }
    }

    public function documentsDelete(Request $request)
    {
        $id = $request->id; // Get the document ID from the request
        // Attempt to delete the document with the given ID
        $query = DB::table('lms_documents')->where('id', $id)->delete();

        // Check if the deletion was successful
        if ($query) {
            // Return a success response if the document was deleted
            actLogs('Profile', 'document delete', $request->all());
            return response()->json(['response' => 'success', 'message' => 'Documents deleted successfully']);
        }
        // Return an error response if the deletion failed
        return response()->json(['response' => 'error', 'message' => 'Documents failed to delete']);
    }


      public function addPdVerification(Request $request)
        {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'pdRemarks' => 'required',
                'pdStatus'  => 'required',
            ]);
        
            if ($validator->passes()) {
                // Prepare reference data
                $pdData = [
                    'pdRemarks' => $request->pdRemarks,
                    'pdStatus'  => $request->pdStatus,
                    'addedOn' => dt(), // Assuming dt() returns the current timestamp
                ];
        
                // Handle file uploads
                $uploadedFiles = [];
                if ($request->hasFile('pdImages')) {
                    foreach ($request->file('pdImages') as $file) {
                        $filename = $file->getClientOriginalName();
                        $filePath = $file->storeAs('pdDocumentData/' . $request->contactID, $filename, 'public');
                        $uploadedFiles[] = $filePath;
                    }
                    $pdData['pdDocuments'] = json_encode($uploadedFiles, JSON_UNESCAPED_SLASHES);
                }
        
                // Fetch and store only address & city using lat/long
                if ($request->filled(['latitude', 'longitude'])) {
                    $lat = trim($request->latitude);
                    $lng = trim($request->longitude);
                    $apiKey = 'AIzaSyD3LqaRVIfmV8K9ye579ENlY_98vj3S52I';
        
                    $url = "https://maps.google.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";
        
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $response = curl_exec($ch);
                    curl_close($ch);
        
                    $geoData = json_decode($response, true);
        
                    if (!empty($geoData['results'][0])) {
                        $pdData['geoAddress'] = $geoData['results'][0]['formatted_address'] ?? null;
        
                        foreach ($geoData['results'][0]['address_components'] as $component) {
                            if (in_array('locality', $component['types'])) {
                                $pdData['geoCity'] = $component['long_name'];
                                break;
                            }
                        }
                    }
                }
        
                // Check if the record with the given leadID already exists
                $existingRecord = DB::table('lms_pd_verifications')->where('leadID', $request->leadID)->orderBy('id','desc')->first();
        
                // if ($existingRecord) {
                //     // If a record exists, update it
                //     DB::table('lms_pd_verifications')->where('leadID', $request->leadID)->update($pdData);
                //     DB::table('lms_leads')->where('leadID', $request->leadID)->update(['pdStatus'=>$request->pdStatus]);
                //     $message = 'FV Verification updated successfully.';
                // } else {
                    // If no record exists, insert a new one
                    $pdData['leadID'] = $request->leadID;
                    DB::table('lms_pd_verifications')->insert($pdData);
                    DB::table('lms_leads')->where('leadID', $request->leadID)->update(['pdStatus'=>$request->pdStatus]);
                    $message = 'FV Verification added successfully.';
                //}
        
                // Log action
                actLogs('Profile', 'FV verification update', $pdData);
        
                // Return success response
                return response()->json(['response' => 'success', 'message' => $message]);
            } else {
                // Return validation errors
                return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
            }
        }

    

    // public function editProfileReference(Request $request)
    // {
    //     $id = $request->id; // Get the reference ID from the request
    //     // Fetch the reference details from the database
    //     $reference = DB::table('lms_reference')->where('id', $id)->first();

    //     // Check if the reference exists
    //     if ($reference) {
    //         // Return JSON response with reference details
    //         return response()->json([
    //             'success' => true,
    //             'data' => $reference
    //         ]);
    //     } else {
    //         // Return JSON response indicating failure (reference not found)
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Reference not found'
    //         ]);
    //     }
    // }

    // public function referenceDelete(Request $request)
    // {
    //     $id = $request->id; // Get the reference ID from the request
    //     // Attempt to delete the reference with the given ID
    //     $query = DB::table('lms_reference')->where('id', $id)->delete();

    //     // Check if the deletion was successful
    //     if ($query) {
    //         // Return a success response if the reference was deleted
    //         actLogs('Profile', 'reference delete', $request->all());
    //         return response()->json(['response' => 'success', 'message' => 'Reference deleted successfully']);
    //     }
    //     // Return an error response if the deletion failed
    //     return response()->json(['response' => 'error', 'message' => 'Reference failed to delete']);
    // }


    public function addChecklist(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'documentType' => 'required', // Company address is required
            'remark' => 'required', // Company name is required
        ]);

        // Check if validation passes
        if ($validator->passes()) {
            // Prepare  checking data for insertion/updating
            $checklistData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'documentType' => $request->documentType, // Company address from request
                'remark' => $request->remark,
                'ip' => $request->ip(), // User's IP address
                'addedOn' => dt(), // Current date and time for addition
                'addedBy' => Session::get('userID') // User ID from session
            ];

            // Check if a company record with the given ID already exists
            $count = DB::table('lms_checklist')->where('id', $request->id)->count();
            if ($count > 0) {
                // If it exists, unset 'addedOn' and set 'updatedOn'
                unset($checklistData['addedOn']);
                $checklistData['updatedOn'] = dt(); // Set the current date and time for update
                // Update the existing company record
                actLogs('Profile', 'checklist update', $checklistData);
                DB::table('lms_checklist')->where('id', $request->id)->update($checklistData);
            } else {
                // Otherwise, insert the new company record
                actLogs('Profile', 'checklist added', $checklistData);
                DB::table('lms_checklist')->insert($checklistData);
            }

            // Return a success response
            return response()->json(['response' => 'success', 'message' => 'Checklist added successfully.']);
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }

    public function editProfileChecklist(Request $request)
    {
        $id = $request->id; // Get the company ID from the request
        // Fetch the company details from the database
        $checklist = DB::table('lms_checklist')->where('id', $id)->first();

        // Check if the company exists
        if ($checklist) {
            // Return JSON response with company details
            return response()->json([
                'success' => true,
                'data' => $checklist
            ]);
        } else {
            // Return JSON response indicating failure (company not found)
            return response()->json([
                'success' => false,
                'message' => 'Checklist not found'
            ]);
        }
    }

    public function checklistDelete(Request $request)
    {
        $id = $request->id; // Get the company ID from the request
        // Attempt to delete the company with the given ID
        $query = DB::table('lms_checklist')->where('id', $id)->delete();

        // Check if the deletion was successful
        if ($query) {
            // Return a success response if the company was deleted
            actLogs('Profile', 'checklist delete', $request->all());
            return response()->json(['response' => 'success', 'message' => 'Checklist deleted successfully']);
        }
        // Return an error response if the deletion failed
        return response()->json(['response' => 'error', 'message' => 'Checklist failed to delete']);
    }

    public function addSanction(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'loanAmtApproved' => 'required', // Approved loan amount is required
            'confirmLoanAmtApproved' => 'required|same:loanAmtApproved', // Confirmation of loan amount must match
            'roi' => 'required', // Rate of interest is required
            'emiDate' => 'required', // Repayment date is required
            'officialEmail' => 'required', // Official email is required
            'alternateMobile' => 'required|digits:10', // Alternate mobile must be 10 digits
            'pf' => 'required', // PF is required
            'fiChoice' => 'required',
            'pdPerson' => [
                'required_if:fiChoice,0'
            ],
            'emiDate' => 'required',
            'loanTenure' => 'required',
            'adminFee' => 'required', // Admin fee is required
            'monthlyIncome' => 'required', // Monthly income is required
            'cibilScore' => 'required|digits:3', // CIBIL score must be exactly 3 digits
            'monthlyObligation' => 'required', // Monthly obligation is required
            'residential' => 'required', // Residential type is required
            'employeeType' => 'required', // Employee type is required
            'purpose' => 'required', // Purpose of loan is required
            'bank' => 'required', // Bank details are required
            'branch' => 'required', // branch details are required
            'remark' => 'required', // Remarks are required
        ]);

        // Check if validation passes
        if ($validator->passes()) {

            $adminFee = round($request->adminFee);
            $adminGstAmount = $adminFee * (18 / 100);
            $adminGstAmount = round($adminGstAmount);

            $stampDuty = stampDuty($request->branch); // check helper
             
            $totalAmountDeducted = $adminFee + $adminGstAmount + $stampDuty;
            $disbursementAmount = $request->loanAmtApproved - $totalAmountDeducted;

            // // Get current date and specified repayment date
            $disburseddate = date("Y-m-d");

            // Check approval matrix
            $approvalMatrixData = approvalMatrix(); //Helper
            $creditStatus = 'Approved';
            $pendingApprovalListShowTo = 0;

            // Check if loan amount falls within approval matrix
            // if ($approvalMatrixData) {
            //     if ($request->loanAmtApproved >= $approvalMatrixData->rangeFrom && $request->loanAmtApproved <= $approvalMatrixData->rangeTo) {
            //         $creditStatus = 'Pending For Approval';
            //         $pendingApprovalListShowTo = $approvalMatrixData->users;
            //     }
            // }


            // Extract the input values
            $loanAmount = $request->loanAmtApproved;
            $interestRate = $request->roi;
            $disbursementDate = date('Y-m-d');
            $loanTenure = $request->loanTenure;
            $repaymentDay = $request->emiDate;

            // Ensure the loan amount and interest rate are numbers
            $loanAmount = floatval($loanAmount);
            $interestRate = floatval($interestRate);
            $loanTenure = intval($loanTenure);
            $repaymentDay = intval($repaymentDay);

            // Convert the disbursement date into a DateTime object
            $disbursementDateObj = new \DateTime($disbursementDate);

            // Determine EMI start date based on the repayment day
            $disbursementDay = (int) $disbursementDateObj->format('d');
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


            if($daysDifference > 30){
               $disbursalAmount = $disbursementAmount - $preEmiInterest; 
             }elseif ($daysDifference < 30) {
              $preEmiInterest = 0;
              $disbursalAmount = $disbursementAmount;
            }else{
             $disbursalAmount = $disbursementAmount;
             }    
                                                               
          
            // Generate EMI Schedule (for table)
            $emiSchedule = [];
            $openingBalance = $loanAmount;
            $paymentDate = clone $emiStartDateObj;

            $paymentStartDate = $paymentDate->format('Y-m-d');

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
                    'contactID' => $request->contactID, // Contact ID from the request
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
            $emiData = compact('interestRate', 'disbursementDate', 'emiStartDateObj', 'emi', 'emiSchedule', 'preEmiInterest', 'preEmiInterestDays', 'totalInterestAmount');

            if ($request->fiChoice == 1) {
                $pdVerifiedBy = null;
            } else {
                $pdVerifiedBy = $request->pdPerson;
            }

            // Merge the EMI data into the preSanctionData array after disbursedDate
            $sanctionData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'branch' => $request->branch, // Branch from request
                'productType' => cmp()->companyName, // Product type from company
                'loanAmtApproved' => $request->loanAmtApproved, // Approved loan amount
                'balLoanAmtApproved' => $request->loanAmtApproved, // Balance loan amount
                'confirmLoanAmtApproved' => $request->confirmLoanAmtApproved, // Confirmed approved amount
                'roi' => $request->roi, // Rate of interest
                'tenure' => $request->loanTenure, // Calculated tenure
                'repayDay' => $repaymentDay, // Repayment day
                'interestRate' => round($emiData['interestRate']),
                'disbursementDate' => $emiData['disbursementDate'],
                'disbursementAmount' => round($disbursalAmount),
                'paymentStartDate' => $paymentStartDate,
                'paymentEndDate' => end($emiSchedule)['paymentDate'],
                'emi' => round($emiData['emi']),
                'preEmiInterest' => round($emiData['preEmiInterest']),
                'preEmiInterestDays' => round($emiData['preEmiInterestDays']),
                'preEmiInterestDaysDiff' => round($daysDifference),
                'totalInterestAmount' => round($emiData['totalInterestAmount']),
                'officialEmail' => $request->officialEmail, // Official email
                'alternateMobile' => $request->alternateMobile, // Alternate mobile number
                'pfPercentage' => $request->pf, // PF percentage
                'adminFee' => $request->adminFee, // Admin fee
                'GstOfAdminFee' => '18', // Admin GST
                'adminGstAmount' => $adminGstAmount, // Calculated GST amount
                'stampDuty' => $stampDuty,
                'bankName' => is_array($request->bank) ? implode(',', $request->bank) : $request->bank, // Bank name(s)
                'monthlyIncome' => $request->monthlyIncome, // Monthly income
                'cibil' => $request->cibilScore, // CIBIL score
                'monthlyObligation' => $request->monthlyObligation, // Monthly obligation
                'residentialType' => $request->residential, // Residential type
                'employed' => $request->employeeType, // Employee type
                'creditStatus' => $creditStatus, // Credit status
                'loanRequirePurpose' => $request->purpose, // Purpose of loan
                'pdVerification' => $request->fiChoice,
                'pdVerifiedBy' => $request->pdPerson,
                'remark' => $request->remark, // Remarks
                //'pendingListShow' => $pendingApprovalListShowTo, // Pending approval list
                'status' => 'Pending For Approval', // Set status to approved
                'creditedBy' => Session::get('userID'), // User who credited
                'createdDate' => date('Y-m-d'), // Current date
                'ip' => $request->ip(), // User's IP address
                'addedOn' => dt(), // Assuming dt() returns the current datetime
                'addedBy' => Session::get('userID') // User ID from session
            ];


            // Check if a sanction record already exists for the lead
            $checkExists = DB::table('lms_approval')->where('leadID', $request->leadID)->count();
            if ($checkExists) {
                // Return error if data already exists
                return response()->json(['response' => 'exist', 'error' => 'This data already exists']);
            } else {
                // Insert the new sanction record
                actLogs('Profile', 'pre sanction added', $sanctionData);
                DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Pending For Approval']);
                DB::table('lms_approval')->insert($sanctionData);
                DB::table('lms_emi_schedule_sanction')->insert($emiSchedule);
                actLogs('Profile', 'repayment schedule added', $emiSchedule);
                // Update lead status to approved
                actLogs('Profile', 'leads updated', 'sanction status approved');
                // Return success response
                return response()->json(['response' => 'success', 'message' => 'Sanction added successfully.']);
            }
        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }



    public function rejectSanction(Request $request) {
            // Validate incoming request data
            $validator = Validator::make($request->all(), [
                'branch' => 'required',
                'officialEmail' => 'required',
                'cibilScore' => 'required',
                'rejectionReason' => 'required',
                'remarks' => 'required',
            ]);
        
            if ($validator->passes()) {
                // Prepare rejection data
                $rejectionData = [
                    'leadID' => $request->leadID, // Lead ID from session
                    'contactID' => $request->contactID, // Contact ID from session
                    'branch' => $request->branch,
                    'officialEmail' => $request->officialEmail,
                    'cibil' => $request->cibilScore,
                    'status' => 'Rejected',
                    'rejectionReason' => json_encode($request->rejectionReason),
                    'remarks' => $request->remarks,
                    'createdDate' => date('Y-m-d'), // Current date
                    'ip' => $request->ip(), // User's IP address
                    'addedOn' => dt(), // Assuming dt() returns the current datetime
                    'addedBy' => Session::get('userID'), // User ID from session
                ];
        
                // Check if a rejection record already exists for the lead
                $checkExists = DB::table('lms_loan_rejection')->where('leadID', $request->leadID)->count();
                $contactFetch = DB::table('lms_contact')->select('name','mobile')->where('contactID', $request->contactID)->orderBy('id','desc')->first();
                if ($checkExists) {
                    return response()->json(['response' => 'exist', 'error' => 'This data already exists']);
                } else {
                    // Insert rejection data
                    actLogs('Profile', 'rejection added', $rejectionData);
                    DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Rejected']);
                    
                    DB::table('lms_loan_rejection')->insert($rejectionData);
                    
                    // Update lead status to rejected
                    actLogs('Profile', 'leads updated', 'status rejected');
                    
                    $message = "Dear {$contactFetch->name}, your loan application (Ref. {$request->leadID}) was not approved based on our internal criteria. Thank you for choosing Cashpey.";
                    $responseNotification = sendMobileNotification($contactFetch->mobile, $message, '1007137248844038124');
                  return response()->json(['response' => 'success', 'message' => 'Sanction rejected successfully.']);
                }
            } else {
                // Return validation errors
                return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
            }
        }

    public function updateSanction(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'loanAmtApproved' => 'required', // Approved loan amount is required
            'confirmLoanAmtApproved' => 'required|same:loanAmtApproved', // Confirmation of loan amount must match
            'roi' => 'required', // Rate of interest is required
            'emiDate' => 'required', // Repayment date is required
            'officialEmail' => 'required', // Official email is required
            'alternateMobile' => 'required|digits:10', // Alternate mobile must be 10 digits
            'pf' => 'required', // PF is required
            'fiChoice' => 'required',
            'pdPerson' => [
                'required_if:fiChoice,0'
            ],
            'adminFee' => 'required', // Admin fee is required
            'monthlyIncome' => 'required', // Monthly income is required
            'cibilScore' => 'required|digits:3', // CIBIL score must be exactly 3 digits
            'monthlyObligation' => 'required', // Monthly obligation is required
            'residential' => 'required', // Residential type is required
            'employeeType' => 'required', // Employee type is required
            'purpose' => 'required', // Purpose of loan is required
            'bank' => 'required', // Bank details are required
            'remark' => 'required', // Remarks are required
        ]);

        // Check if validation passes
        if ($validator->passes()) {

            // Calculate admin GST amount
            $adminFee = round($request->adminFee);
            $adminGstAmount = $adminFee * (18 / 100);
            $adminGstAmount = round($adminGstAmount);

            $stampDuty = stampDuty($request->branch); // check helper
            $totalAmountDeducted = $adminFee + $adminGstAmount + $stampDuty;
            $disbursementAmount = $request->loanAmtApproved - $totalAmountDeducted;


            // Get current date and specified repayment date
            $disburseddate = date("Y-m-d");

            // Check approval matrix
            $approvalMatrixData = approvalMatrix(); //Helper
            $creditStatus = $request->creditStatus == 'Rejected' ? 'Rejected' : 'Approved';

            $pendingApprovalListShowTo = 0;

            // Check if loan amount falls within approval matrix
            // if ($approvalMatrixData) {
            //     if ($request->loanAmtApproved >= $approvalMatrixData->rangeFrom && $request->loanAmtApproved <= $approvalMatrixData->rangeTo) {
            //         $creditStatus = 'Pending For Approval';
            //         $pendingApprovalListShowTo = $approvalMatrixData->users;
            //     }
            // }


            // Extract the input values
            $loanAmount = $request->loanAmtApproved;
            $interestRate = $request->roi;
            $disbursementDate = date('Y-m-d');
            $loanTenure = $request->loanTenure;
            $repaymentDay = $request->emiDate;

            // Ensure the loan amount and interest rate are numbers
            $loanAmount = floatval($loanAmount);
            $interestRate = floatval($interestRate);
            $loanTenure = intval($loanTenure);
            $repaymentDay = intval($repaymentDay);

            // Convert the disbursement date into a DateTime object
            $disbursementDateObj = new \DateTime($disbursementDate);

            // Determine EMI start date based on the repayment day
            $disbursementDay = (int) $disbursementDateObj->format('d');
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

            if($daysDifference > 30){
               $disbursalAmount = $disbursementAmount - $preEmiInterest; 
            }elseif ($daysDifference < 30) {
              $preEmiInterest = 0;
              $disbursalAmount = $disbursementAmount;
            }else{
             $disbursalAmount = $disbursementAmount;
            }    
                                                               
            // Generate EMI Schedule (for table)
            $emiSchedule = [];
            $openingBalance = $loanAmount;
            $paymentDate = clone $emiStartDateObj;

            $paymentStartDate = $paymentDate->format('Y-m-d');

            for ($i = 1; $i <= $loanTenure; $i++) {
                // Calculate the interest for this EMI
                $monthlyInterest = round($openingBalance * $monthlyInterestRate);

                // Calculate the principal part of the EMI
                $principal = round($emi - $monthlyInterest);

                // Calculate the closing balance after the payment
                $closingBalance = round($openingBalance - $principal);

                // Ensure that the closing balance doesn't go negative or zero
                if ($closingBalance < 0) {
                    $closingBalance = 0; // Set a minimum balance threshold (or set it to 0)
                }

                // Store the schedule details
                $emiSchedule[] = [
                    'leadID' => $request->leadID, // Lead ID from the request
                    'contactID' => $request->contactID, // Contact ID from the request
                    'paymentDate' => $paymentDate->format('Y-m-d'), // Formatted payment date
                    'openingBalance' => round($openingBalance), // Opening balance for this EMI payment
                    'emiAmount' => round($emi), // EMI amount to be paid
                    'principalAmount' => round($principal), // Principal amount for this EMI
                    'interestAmount' => round($monthlyInterest), // Interest amount for this EMI
                    'closingBalance' => $closingBalance, // Closing balance after this EMI
                    'addedOn' => dt(), // Date and time when this entry was added (assumed dt() is a helper function)
                ];

                // Update balance and move to next month
                $openingBalance = $closingBalance;
                $paymentDate->modify('+1 month');
            }


            // Calculate total interest (sum of all interest amounts in the schedule)
            $totalInterestAmount = array_sum(array_column($emiSchedule, 'interestAmount'));

            // Define the EMI data
            $emiData = compact('interestRate', 'disbursementDate', 'emiStartDateObj', 'emi', 'emiSchedule', 'preEmiInterest', 'preEmiInterestDays', 'totalInterestAmount');

            if ($request->fiChoice == 1) {
                $pdVerifiedBy = null;
            } else {
                $pdVerifiedBy = $request->pdPerson;
            }



            // Merge the EMI data into the preSanctionData array after disbursedDate
            $sanctionData = [
                'leadID' => $request->leadID, // Get lead ID from session
                'contactID' => $request->contactID, // Get contact ID from session
                'branch' => $request->branch, // Branch from request
                'productType' => cmp()->companyName, // Product type from company
                'loanAmtApproved' => $request->loanAmtApproved, // Approved loan amount
                'balLoanAmtApproved' => $request->loanAmtApproved, // Balance loan amount
                'confirmLoanAmtApproved' => $request->confirmLoanAmtApproved, // Confirmed approved amount
                'roi' => $request->roi, // Rate of interest
                'tenure' => $request->loanTenure, // Calculated tenure
                'repayDay' => $repaymentDay, // Repayment day
                'interestRate' => round($emiData['interestRate']),
                'disbursementDate' => $emiData['disbursementDate'],
                'disbursementAmount' => round($disbursalAmount),
                'paymentStartDate' => $paymentStartDate,
                'paymentEndDate' => end($emiSchedule)['paymentDate'],
                'emi' => round($emiData['emi']),
                'preEmiInterest' => round($emiData['preEmiInterest']),
                'preEmiInterestDays' => round($emiData['preEmiInterestDays']),
                'preEmiInterestDaysDiff' => round($daysDifference),
                'totalInterestAmount' => round($emiData['totalInterestAmount']),
                'officialEmail' => $request->officialEmail, // Official email
                'alternateMobile' => $request->alternateMobile, // Alternate mobile number
                'pfPercentage' => $request->pf, // PF percentage
                'adminFee' => $request->adminFee, // Admin fee
                'GstOfAdminFee' => "18", // Admin GST
                'adminGstAmount' => $adminGstAmount, // Calculated GST amount
                'stampDuty' => $stampDuty,
                'bankName' => is_array($request->bank) ? implode(',', $request->bank) : $request->bank, // Bank name(s)
                'monthlyIncome' => $request->monthlyIncome, // Monthly income
                'cibil' => $request->cibilScore, // CIBIL score
                'monthlyObligation' => $request->monthlyObligation, // Monthly obligation
                'residentialType' => $request->residential, // Residential type
                'employed' => $request->employeeType, // Employee type
                //  'creditStatus' => $creditStatus, // Credit status
                'loanRequirePurpose' => $request->purpose, // Purpose of loan
                'pdVerification' => $request->fiChoice,
                'pdVerifiedBy' => $pdVerifiedBy,
                'remark' => $request->remark, // Remarks
                //  'pendingListShow' => $pendingApprovalListShowTo, // Pending approval list
                'creditedBy' => Session::get('userID'), // User who credited
                'createdDate' => date('Y-m-d'), // Current date
                'ip' => $request->ip(), // User's IP address
                'addedBy' => Session::get('userID') // User ID from session
            ];


            if ($creditStatus == 'Rejected') {
                $sanctionData['status'] = 'Rejected';
                DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Rejected']);
                $checkExists = DB::table('lms_approval')->where('leadID', $request->leadID)->update($sanctionData);
                DB::table('lms_emi_schedule_sanction')->where('leadID', $request->leadID)->delete();
                actLogs('Profile', 'sanction emi data deleted', $emiSchedule);
                actLogs('Profile', 'sanction updated', $sanctionData);
                return response()->json(['response' => 'success', 'message' => 'Sanction updated successfully.']);
            } else {
                $sanctionData['status'] = 'Pending For Approval';
                $checkExists = DB::table('lms_approval')->where('leadID', $request->leadID)->update($sanctionData);
                actLogs('Profile', 'sanction updated', $sanctionData);
                DB::table('lms_emi_schedule_sanction')->where('leadID', $request->leadID)->delete();
                DB::table('lms_emi_schedule_sanction')->insert($emiSchedule);
                actLogs('Profile', 'sanction repayment schedule updated', $emiSchedule);
                return response()->json(['response' => 'success', 'message' => 'Sanction updated successfully.']);
            }

        } else {
            // Return validation errors if validation fails
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }





    // public function rejectSanction(Request $request) {
    //     // Validate incoming request data
    //     $validator = Validator::make($request->all(), [
    //         'branch' => 'required',
    //         'officialEmail' => 'required',
    //         'cibilScore' => 'required',
    //         'rejectionReason' => 'required',
    //         'remarks' => 'required',
    //     ]);

    //     if ($validator->passes()) {
    //         // Prepare rejection data
    //         $rejectionData = [
    //             'leadID' => $request->leadID, // Lead ID from session
    //             'contactID' => $request->contactID, // Contact ID from session
    //             'branch' => $request->branch,
    //             'officialEmail' => $request->officialEmail,
    //             'cibil' => $request->cibilScore,
    //             'status' => 'Rejected',
    //             'rejectionReason' => json_encode($request->rejectionReason),
    //             'remarks' => $request->remarks,
    //             'createdDate' => date('Y-m-d'), // Current date
    //             'ip' => $request->ip(), // User's IP address
    //             'addedOn' => dt(), // Assuming dt() returns the current datetime
    //             'addedBy' => Session::get('userID'), // User ID from session
    //         ];

    //         // Check if a rejection record already exists for the lead
    //         $checkExists = DB::table('lms_loan_rejection')->where('leadID', $request->leadID)->count();
    //         if ($checkExists) {
    //             return response()->json(['response' => 'exist', 'error' => 'This data already exists']);
    //         } else {
    //             // Insert rejection data
    //             actLogs('Profile', 'rejection added', $rejectionData);
    //             DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => 'Rejected']);

    //             DB::table('lms_loan_rejection')->insert($rejectionData);

    //             // Update lead status to rejected
    //             actLogs('Profile', 'leads updated', 'status rejected');

    //             // $templateData = DB::table('lms_leads')
    //             //     ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
    //             //     ->select('lms_contact.name', 'lms_contact.email','lms_contact.mobile', 'lms_leads.leadID', 'lms_leads.commingLeadsDate')
    //             //     ->where(['lms_leads.leadID' => $request->leadID])
    //             //     ->first();

    //             // $fromEmail = 'sanction@creditpey.com';    
    //             // $template = 'emailTemplate.rejection';
    //             // $companyName = cmp()->companyName;
    //             // $subject = $companyName.' - Application Rejected';
    //             // $mailData = compact('template', 'subject', 'templateData','fromEmail');
    //             // $receiversEmail = [$templateData->email, $request->officialEmail];
    //             // $ccEmails = array('confirmation@cashpey.com');

    //             // try {
    //             //     Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));

    //             //     $communicationData = [
    //             //         'leadID' => $request->leadID,
    //             //         'communicationType' => 'Email',
    //             //         'operation' => 'Application Rejected Mail Sent',
    //             //         'ip' => $request->ip(),
    //             //         'addedOn' => dt(), // Assuming dt() returns the current datetime
    //             //         'addedBy' => Session::get('userID'),
    //             //     ];

    //             //     // Insert communication data into the database
    //             //     actLogs('Profile', 'communication added', $communicationData);
    //             //     DB::table('lms_communication')->insert($communicationData);


    //             // } catch (\Exception $e) {
    //             //     return response()->json(['response' => 'failed', 'error' => $e->getMessage()]);
    //             // }
    //           return response()->json(['response' => 'success', 'message' => 'Sanction rejected successfully.']);
    //         }
    //     } else {
    //         // Return validation errors
    //         return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
    //     }
    // }

   public function sendEsignRequest(Request $request, $leadID, $contactID)
    {

        $docRequestBy = Session::get('userID');
        $docRequestByName = getUserNameById('users', 'userID', $docRequestBy, 'displayName');

        // Check for the latest record with loanStatus 'Rejected'
        $latestEntry = DB::table('lms_esigndockyc')
            ->where('contactID', $contactID)
            ->where('leadID', $leadID)
            ->orderBy('id', 'desc')
            ->first();

        if ($latestEntry && $latestEntry->loanStatus === 'Rejected') {
            // Proceed with fetching approval contact data
            $approvalContactData = DB::table('lms_contact')
                ->join('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
                ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                // ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                ->leftJoin('lms_address', function ($join) {
                    $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                        ->where('lms_address.addressType', '=', 'current');
                })
                ->select(
                    'lms_contact.name',
                    'lms_contact.email',
                    'lms_contact.mobile',
                    'lms_contact.pancard',
                    'lms_leads.leadID',
                    'lms_address.state',
                    'lms_address.city',
                    'lms_address.address',
                    'lms_leads.commingLeadsDate',
                    'lms_approval.loanAmtApproved',
                    'lms_approval.tenure',
                    'lms_approval.stampDuty',
                    'lms_approval.preEmiInterest',
                    'lms_approval.paymentStartDate',
                    'lms_approval.totalInterestAmount',
                    'lms_approval.roi',
                    'lms_approval.repayDay',
                    'lms_approval.createdDate',
                    'lms_approval.loanRequirePurpose',
                    'lms_approval.adminFee',
                    'lms_approval.adminGstAmount',
                    'lms_approval.emi',
                    'lms_approval.pfPercentage',
                    'lms_approval.loanNo',
                    'lms_approval.disbursementAmount',
                    'lms_approval.preEmiInterestDaysDiff'
                )
                ->where('lms_contact.contactID', $contactID)
                ->where('lms_leads.leadID', $leadID)
                ->first();

            $name = $approvalContactData->name;
            $mobile = $approvalContactData->mobile;
            $email = $approvalContactData->email;
            $pan = strtoupper($approvalContactData->pancard);
            $state = getUserNameById('lms_states', 'stateID', $approvalContactData->state, 'stateName');
            $addess = $approvalContactData->address;
            $applyLoanDate = $approvalContactData->commingLeadsDate;
            $preEmiInterest = $approvalContactData->preEmiInterest;

            $loanNo = $approvalContactData->loanNo;
            $loanAmtApproved = $approvalContactData->loanAmtApproved;
            $tenure = $approvalContactData->tenure;
            $roi = $approvalContactData->roi;
            $applicationId = $approvalContactData->leadID;
            $repayDayInt = $approvalContactData->repayDay;
            $sanction = $approvalContactData->createdDate;
            $adminFee = $approvalContactData->adminFee;
            $GstOfAdminFee = $approvalContactData->adminGstAmount;
            $pfPercentage = $approvalContactData->pfPercentage;
            //$totalAmountDeducted = $adminFee + $GstOfAdminFee;

            $repayDay = '';
            if ($repayDayInt == 2) {
                $repayDay = '2nd of every month';
                $repayDayNormal = '2nd';
            } elseif ($repayDayInt == 5) {
                $repayDay = '5th of every month';
                $repayDayNormal = '5th';
            } elseif ($repayDayInt == 7) {
                $repayDay = '7th of every month';
                $repayDayNormal = '7th';
            } elseif ($repayDayInt == 10) {
                $repayDay = '10th of every month';
                $repayDayNormal = '10th';
            }


            $stampDuty = stampDuty($approvalContactData->stampDuty); // check helper
            $totalAmountDeducted = $adminFee + $GstOfAdminFee + $stampDuty;

            $loanRequirePurpose = $approvalContactData->loanRequirePurpose;

            $pf = $adminFee + $GstOfAdminFee;

            $SanctionDate = date("jS M, Y", strtotime($sanction));

            $repayDate = date('d-m-Y', strtotime($approvalContactData->paymentStartDate));
            
            $day = date('j', strtotime($approvalContactData->paymentStartDate));
            $suffix = date('S', strtotime($approvalContactData->paymentStartDate));
            $repayDateFormat = $day . $suffix;

            if ($approvalContactData->preEmiInterestDaysDiff > 30) {
              //  $disbursalAmount = $approvalContactData->disbursementAmount - $preEmiInterest;
               
                $totalAmountDeducted = $adminFee + $GstOfAdminFee + $stampDuty + $preEmiInterest;
            } elseif ($approvalContactData->preEmiInterestDaysDiff = 30) {
                $preEmiInterest = 0;
            } else {
               // $disbursalAmount = $approvalContactData->disbursementAmount;
                $totalAmountDeducted = $adminFee + $GstOfAdminFee + $stampDuty;
            }

            $disbursalAmount = round($approvalContactData->disbursementAmount);
            $repayAmount = $approvalContactData->emi;
            $interest = $approvalContactData->totalInterestAmount;


            $finalAPR = $this->calculateAPR($loanAmtApproved, $repayAmount, $roi, $totalAmountDeducted, $tenure, $disbursalAmount);

            $emiSum = $repayAmount * $tenure; // Assuming EMI is the monthly repayment amount


            // Fetch eStamp ID from DB
            $estamp = DB::table('lms_estamp')->where('leadID', $leadID)->first();
            $estamp_id = $estamp->eStampId ?? null;

            $addressbranch = DB::table('lms_approval')
                ->where('leadID', $leadID)
                ->value('branch');

            $stateID = DB::table('lms_cities')
                ->where('cityID', $addressbranch)
                ->value('stateID');


            if (!$estamp_id) {
                return redirect()->back()->with('error', 'eStamp ID not found for this lead.');
            }

            if ($stateID == 17) {
                // For other states, use the eStamp ID from the latest entry
                $template_id = '683ef980d88f933ebef68932'; // Template ID for Kannad

            } elseif ($stateID == 31) {
                $template_id = '684019b8d88f933ebefea5b0'; // Template ID for Tamil
            } else {
                // For other states, use the eStamp ID from the latest entry
                $template_id = '683850e52bd7b173037cef97';
            }

            $signCoordinates = [];

            function addCoords(&$arr, $pages, $x, $y, $specialY = [])
            {
                foreach ($pages as $page) {
                    $arr[] = [
                        "x_coord" => $x,
                        "y_coord" => $specialY[$page] ?? $y,
                        "page_num" => $page
                    ];
                }
            }

            if ($stateID == 17) {
                addCoords(
                    $signCoordinates,
                    [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 33, 34, 36],
                    78,
                    137,
                    [36 => 150]
                );
                $signCoordinates[] = ["x_coord" => 139, "y_coord" => 295, "page_num" => 25];
                $signCoordinates[] = ["x_coord" => 112, "y_coord" => 439, "page_num" => 32];
                $signCoordinates[] = ["x_coord" => 119, "y_coord" => 338, "page_num" => 35];

            } elseif ($stateID == 31) {
                addCoords($signCoordinates, range(2, 27), 101, 160);
                $signCoordinates[] = ["x_coord" => 71, "y_coord" => 314, "page_num" => 28];
                addCoords($signCoordinates, [29, 30, 31, 32, 33, 34, 36, 37, 39], 101, 160);
                $signCoordinates[] = ["x_coord" => 111, "y_coord" => 274, "page_num" => 35];
                $signCoordinates[] = ["x_coord" => 118, "y_coord" => 305, "page_num" => 38];

            } else {
                for ($page = 2; $page <= 49; $page++) {
                    if (!in_array($page, [38, 48])) {
                        $signCoordinates[] = ["x_coord" => 37, "y_coord" => 111, "page_num" => $page];
                    }
                }
                $signCoordinates[] = ["x_coord" => 101, "y_coord" => 196, "page_num" => 38];
                $signCoordinates[] = ["x_coord" => 166, "y_coord" => 274, "page_num" => 48];
            }


            // Prepare headers
            $headers = [
                'app-id' => '683d502b558b020028dda41d',
                'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
            ];

            $postAllData = [
                "template_id" => $template_id,
                "template_data" => [
                    [
                        "borrower" => (string) $name,
                        "per_address" => (string) $addess,
                        "curr_address" => (string) $addess,
                        "borrower_phone_no" => (string) $mobile,
                        "borrower_pan" => (string) $pan,
                        "execution_date" => (string) date('d-m-Y'),
                        "loan_amount_sanctioned" => (string) $loanAmtApproved,
                        "interest_rate" => (string) $roi,
                        "tenure_of_loan" => (string) $tenure,
                        "total_interest_amount" => (string) $interest,
                        "emi" => (string) $repayAmount,
                        "stamp_duty_amount" => (string) $stampDuty,
                        "annual_percentage_rate" => (string) $finalAPR,
                        "sum_of_all_emi" => (string) $emiSum,
                        "processing_fee" => (string) $pf,
                        "gst" => "18%",
                        "address_for_notices" => (string) $addess,
                        "emi_due_date_in_months" => (string) $repayDateFormat,
                        "borrower_email" => (string) $email,
                        "borrower_email_address" => (string) $email,
                        "borrower_contact_no" => (string) $mobile,
                        "borrower_address" => (string) $addess,
                        "borrower_full_name" => (string) $name,
                        "borrower_fullname" => (string) $name,
                        "auth_date" => (string) date('d-m-Y'),
                        "lender" => "Naman Commodities Private Limited.",
                        "loan_amount" => (string) $loanAmtApproved,
                        "loan_no" => (string) $loanNo,
                        "due_date" => (string) $repayDay,
                        "borrower_name_eng" => (string) $name,
                        "borrower_name_hindi" => (string) $name,
                        "name_of_borrower" => (string) $name,
                        "loan_ref_no" => (string) $loanNo,
                        "loan_amt" => (string) $loanAmtApproved,
                        "pre_emi" => (string) $preEmiInterest,
                        "disbursal_amount" => (string) $disbursalAmount,
                        "purpose" => (string) $loanRequirePurpose,
                        "rate_of_interest_per" => (string) $roi,
                        "tenure" => (string) $tenure,
                        "emi_amount" => (string) $repayAmount,
                        "first_emi_date" => (string) $repayDate,
                        "emi_due_date" => (string) $repayDay,
                        "mode_of_loan_payment" => "e-NACH",
                        "mode_of_disbursal" => "Online Transfer"
                    ]
                ],
                "signers" => [
                    [
                        "signer_name" => $name,
                        "signer_email" => $email,
                        "signer_purpose" => "Loan Agreement Esigning",
                        "signer_auth_type" => "AADHAAR",
                        "sign_coordinates" => $signCoordinates
                    ]
                ],
                "txn_expiry_min" => "10080",
                "white_label" => "Y",
                "estamp_version" => "v2",
                "estamp_required" => true,
                "estamp_id" => $estamp_id,
                "redirect_url" => "https://eniu908oufgo.x.pipedream.net",
                "response_url" => "https://eniu908oufgo.x.pipedream.net",
                "esign_type" => "MISC",
                "send_invite" => true,
                "email_template" => [
                    "org_name" => "Zoop.One"
                ]

            ];


            $response = Http::withHeaders($headers)->post('https://live.zoop.one/contract/esign/v5/init', $postAllData);

            if (!$response->successful()) {
                return redirect()->back()->with('error', 'Failed to initiate eSign');
            }

            $result = $response->json();

            // Store response details
            $documentId = $result['requests']['request_id'] ?? null;
            $status = 'Requested';
            $fileName = 'Agreement.pdf';
            $created_at = now();

            // Insert into lms_esigndockyc

            $esignData = [
                'esignID' => randomNo(100000, 999999),
                'contactID' => $contactID,
                'leadID' => $leadID,
                'loanAmtApproved' => $loanAmtApproved,
                'roiPercentage' => $roi,
                'loanRepayDate' => $repayDay,
                'offEmail' => $email,
                'pfPercentage' => $pfPercentage,
                'adminFee' => $pf,
                'documentId' => $documentId,
                'name' => $name,
                'mobile' => $mobile,
                'email' => $email,
                'fileName' => $fileName,
                'docRequestBy' => $docRequestBy,
                'docRequestByName' => $docRequestByName,
                'status' => $agreement_status,
                'loanStatus' => 'Approved',
                'addedOn' => $created_at,
            ];

            DB::table('lms_esigndockyc')->insert($esignData);

            $communicationData = [
                'leadID' => $leadID,
                'communicationType' => 'Email',
                'operation' => 'E-Sign Mail Sent',
                'ip' => $request->ip(),
                'addedOn' => dt(),
                'addedBy' => Session::get('userID')
            ];

            actLogs('Profile', 'communication added', $communicationData);
            actLogs('Profile', 'E-Sign Requested', $esignData);
            DB::table('lms_communication')->insert($communicationData);

            return redirect()->back()->with('success', 'E-Sign Mail Sent Successfull');

        } else if (!$latestEntry) {

            // Proceed with fetching approval contact data
            $approvalContactData = DB::table('lms_contact')
                ->join('lms_leads', 'lms_contact.contactID', '=', 'lms_leads.contactID')
                ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                // ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
                ->leftJoin('lms_address', function ($join) {
                    $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                        ->where('lms_address.addressType', '=', 'current');
                })
                ->select(
                    'lms_contact.name',
                    'lms_contact.email',
                    'lms_contact.mobile',
                    'lms_contact.pancard',
                    'lms_leads.leadID',
                    'lms_address.state',
                    'lms_address.city',
                    'lms_address.address',
                    'lms_leads.commingLeadsDate',
                    'lms_approval.loanAmtApproved',
                    'lms_approval.tenure',
                    'lms_approval.stampDuty',
                    'lms_approval.preEmiInterest',
                    'lms_approval.paymentStartDate',
                    'lms_approval.totalInterestAmount',
                    'lms_approval.roi',
                    'lms_approval.repayDay',
                    'lms_approval.createdDate',
                    'lms_approval.loanRequirePurpose',
                    'lms_approval.adminFee',
                    'lms_approval.adminGstAmount',
                    'lms_approval.emi',
                    'lms_approval.pfPercentage',
                    'lms_approval.loanNo',
                    'lms_approval.disbursementAmount',
                    'lms_approval.preEmiInterestDaysDiff'
                )
                ->where('lms_contact.contactID', $contactID)
                ->where('lms_leads.leadID', $leadID)
                ->first();

            $name = $approvalContactData->name;
            $mobile = $approvalContactData->mobile;
            $email = $approvalContactData->email;
            $pan = strtoupper($approvalContactData->pancard);
            $state = getUserNameById('lms_states', 'stateID', $approvalContactData->state, 'stateName');
            $addess = $approvalContactData->address;
            $applyLoanDate = $approvalContactData->commingLeadsDate;
            $preEmiInterest = $approvalContactData->preEmiInterest;

            $loanNo = $approvalContactData->loanNo;
            $loanAmtApproved = $approvalContactData->loanAmtApproved;
            $tenure = $approvalContactData->tenure;
            $roi = $approvalContactData->roi;
            $applicationId = $approvalContactData->leadID;
            $repayDayInt = $approvalContactData->repayDay;
            $sanction = $approvalContactData->createdDate;
            $adminFee = $approvalContactData->adminFee;
            $GstOfAdminFee = $approvalContactData->adminGstAmount;
            $pfPercentage = $approvalContactData->pfPercentage;
            //$totalAmountDeducted = $adminFee + $GstOfAdminFee;

            $repayDay = '';
            if ($repayDayInt == 2) {
                $repayDay = '2nd of every month';
                $repayDayNormal = '2nd';
            } elseif ($repayDayInt == 5) {
                $repayDay = '5th of every month';
                $repayDayNormal = '5th';
            } elseif ($repayDayInt == 7) {
                $repayDay = '7th of every month';
                $repayDayNormal = '7th';
            } elseif ($repayDayInt == 10) {
                $repayDay = '10th of every month';
                $repayDayNormal = '10th';
            }


            $stampDuty = stampDuty($approvalContactData->stampDuty); // check helper
            $totalAmountDeducted = $adminFee + $GstOfAdminFee + $stampDuty;

            $loanRequirePurpose = $approvalContactData->loanRequirePurpose;

            $pf = $adminFee + $GstOfAdminFee;

            $SanctionDate = date("jS M, Y", strtotime($sanction));

            $repayDate = date('d-m-Y', strtotime($approvalContactData->paymentStartDate));
            
            $day = date('j', strtotime($approvalContactData->paymentStartDate));
            $suffix = date('S', strtotime($approvalContactData->paymentStartDate));
            $repayDateFormat = $day . $suffix;
            $disbursalAmount = round($approvalContactData->disbursementAmount);

            if ($approvalContactData->preEmiInterestDaysDiff > 30) {
              //  $disbursalAmount = $approvalContactData->disbursementAmount - $preEmiInterest;
                $totalAmountDeducted = $adminFee + $GstOfAdminFee + $stampDuty + $preEmiInterest;
            } elseif ($approvalContactData->preEmiInterestDaysDiff = 30) {
                $preEmiInterest = 0;
            } else {
               // $disbursalAmount = $approvalContactData->disbursementAmount;
                $totalAmountDeducted = $adminFee + $GstOfAdminFee + $stampDuty;
            }

          //  $disbursalAmount = $approvalContactData->disbursementAmount;
            $repayAmount = $approvalContactData->emi;
            $interest = $approvalContactData->totalInterestAmount;


            $finalAPR = $this->calculateAPR($loanAmtApproved, $repayAmount, $roi, $totalAmountDeducted, $tenure, $disbursalAmount);

            $emiSum = $repayAmount * $tenure; // Assuming EMI is the monthly repayment amount


            // Fetch eStamp ID from DB
            $estamp = DB::table('lms_estamp')->where('leadID', $leadID)->first();
            $estamp_id = $estamp->eStampId ?? null;

            if (!$estamp_id) {
                return redirect()->back()->with('error', 'eStamp ID not found for this lead.');
            }

            $addressbranch = DB::table('lms_approval')
                ->where('leadID', $leadID)
                ->value('branch');

            $stateID = DB::table('lms_cities')
                ->where('cityID', $addressbranch)
                ->value('stateID');


            if (!$estamp_id) {
                return redirect()->back()->with('error', 'eStamp ID not found for this lead.');
            }

            if ($stateID == 17) {
                // For other states, use the eStamp ID from the latest entry
                $template_id = '683ef980d88f933ebef68932'; // Template ID for Kannad

            } elseif ($stateID == 31) {
                $template_id = '684019b8d88f933ebefea5b0'; // Template ID for Tamil
            } else {
                // For other states, use the eStamp ID from the latest entry
                $template_id = '683850e52bd7b173037cef97';
            }

            $signCoordinates = [];

            function addCoords(&$arr, $pages, $x, $y, $specialY = [])
            {
                foreach ($pages as $page) {
                    $arr[] = [
                        "x_coord" => $x,
                        "y_coord" => $specialY[$page] ?? $y,
                        "page_num" => $page
                    ];
                }
            }

            if ($stateID == 17) {
                addCoords(
                    $signCoordinates,
                    [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 26, 27, 28, 29, 30, 33, 34, 36],
                    78,
                    137,
                    [36 => 150]
                );
                $signCoordinates[] = ["x_coord" => 139, "y_coord" => 295, "page_num" => 25];
                $signCoordinates[] = ["x_coord" => 112, "y_coord" => 439, "page_num" => 32];
                $signCoordinates[] = ["x_coord" => 119, "y_coord" => 338, "page_num" => 35];

            } elseif ($stateID == 31) {
                addCoords($signCoordinates, range(2, 27), 101, 160);
                $signCoordinates[] = ["x_coord" => 71, "y_coord" => 314, "page_num" => 28];
                addCoords($signCoordinates, [29, 30, 31, 32, 33, 34, 36, 37, 39], 101, 160);
                $signCoordinates[] = ["x_coord" => 111, "y_coord" => 274, "page_num" => 35];
                $signCoordinates[] = ["x_coord" => 118, "y_coord" => 305, "page_num" => 38];

            } else {
                for ($page = 2; $page <= 49; $page++) {
                    if (!in_array($page, [38, 48])) {
                        $signCoordinates[] = ["x_coord" => 37, "y_coord" => 111, "page_num" => $page];
                    }
                }
                $signCoordinates[] = ["x_coord" => 101, "y_coord" => 196, "page_num" => 38];
                $signCoordinates[] = ["x_coord" => 166, "y_coord" => 274, "page_num" => 48];
            }

            // Prepare headers
            $headers = [
                'app-id' => '683d502b558b020028dda41d',
                'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
            ];

            $postAllData = [
                "template_id" => $template_id,
                "template_data" => [
                    [
                        "borrower" => (string) $name,
                        "per_address" => (string) $addess,
                        "curr_address" => (string) $addess,
                        "borrower_phone_no" => (string) $mobile,
                        "borrower_pan" => (string) $pan,
                        "execution_date" => (string) date('d-m-Y'),
                        "loan_amount_sanctioned" => (string) $loanAmtApproved,
                        "interest_rate" => (string) $roi,
                        "tenure_of_loan" => (string) $tenure,
                        "total_interest_amount" => (string) $interest,
                        "emi" => (string) $repayAmount,
                        "stamp_duty_amount" => (string) $stampDuty,
                        "annual_percentage_rate" => (string) $finalAPR,
                        "sum_of_all_emi" => (string) $emiSum,
                        "processing_fee" => (string) $pf,
                        "gst" => "18%",
                        "address_for_notices" => (string) $addess,
                        "emi_due_date_in_months" => (string) $repayDateFormat,
                        "borrower_email" => (string) $email,
                        "borrower_contact_no" => (string) $mobile,
                        "borrower_address" => (string) $addess,
                        "borrower_full_name" => (string) $name,
                        "auth_date" => (string) date('d-m-Y'),
                        "lender" => "Naman Commodities Private Limited.",
                        "loan_amount" => (string) $loanAmtApproved,
                        "loan_no" => (string) $loanNo,
                        "due_date" => (string) $repayDay,
                        "borrower_name_eng" => (string) $name,
                        "borrower_name_hindi" => (string) $name,
                        "name_of_borrower" => (string) $name,
                        "loan_ref_no" => (string) $loanNo,
                        "loan_amt" => (string) $loanAmtApproved,
                        "pre_emi" => (string) $preEmiInterest,
                        "disbursal_amount" => (string) $disbursalAmount,
                        "purpose" => (string) $loanRequirePurpose,
                        "rate_of_interest_per" => (string) $roi,
                        "tenure" => (string) $tenure,
                        "emi_amount" => (string) $repayAmount,
                        "first_emi_date" => (string) $repayDate,
                        "emi_due_date" => (string) $repayDay,
                        "mode_of_loan_payment" => "e-NACH",
                        "mode_of_disbursal" => "Online Transfer"
                    ]
                ],
                "signers" => [
                    [
                        "signer_name" => $name,
                        "signer_email" => $email,
                        "signer_purpose" => "Loan Agreement Esigning",
                        "signer_auth_type" => "AADHAAR",
                        "sign_coordinates" => $signCoordinates
                    ]
                ],
                "txn_expiry_min" => "10080",
                "white_label" => "Y",
                "estamp_version" => "v2",
                "estamp_required" => true,
                "estamp_id" => $estamp_id,
                "redirect_url" => "https://eniu908oufgo.x.pipedream.net",
                "response_url" => "https://eniu908oufgo.x.pipedream.net",
                "esign_type" => "MISC",
                "send_invite" => true,
                "email_template" => [
                    "org_name" => "Zoop.One"
                ]

            ];

            $response = Http::withHeaders($headers)->post('https://live.zoop.one/contract/esign/v5/init', $postAllData);


            // Handle the response
            if ($response->successful()) {
                $result = $response->json();

                $documentId = $result['requests'][0]['request_id'] ?? null;
                $signingUrl = $result['requests'][0]['signing_url'] ?? null;
                $agreement_status = 'requested';
                $fileName = 'Agreement.pdf';
                $created_at = now();

                // Save to DB
                $esignData = [
                    'esignID' => randomNo(100000, 999999),
                    'contactID' => $contactID,
                    'leadID' => $leadID,
                    'loanAmtApproved' => $loanAmtApproved,
                    'roiPercentage' => $roi,
                    'loanRepayDate' => $repayDay,
                    'offEmail' => $email,
                    'pfPercentage' => $pfPercentage,
                    'adminFee' => $pf,
                    'documentId' => $documentId,
                    'name' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'fileName' => $fileName,
                    'docRequestBy' => $docRequestBy,
                    'docRequestByName' => $docRequestByName,
                    'status' => $agreement_status,
                    'loanStatus' => 'Approved',
                    'addedOn' => $created_at,
                ];

                DB::table('lms_esigndockyc')->insert($esignData);

                $communicationData = [
                    'leadID' => $leadID,
                    'communicationType' => 'Email',
                    'operation' => 'E-Sign Mail Sent',
                    'ip' => $request->ip(),
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                ];

                actLogs('Profile', 'communication added', $communicationData);
                actLogs('Profile', 'E-Sign Requested', $esignData);
                DB::table('lms_communication')->insert($communicationData);

                return redirect()->back()->with('success', 'E-Sign Mail Sent Successfull');
            } else {
                // Get and show error from Zoop response
                return response()->json([
                    'request_body' => $postAllData,
                    'response' => $response->json(),
                ]);
                $errorMessage = $response->json()['response_message'] ?? 'Unknown error occurred.';
                return redirect()->back()->with('error', ' API Error: ' . $errorMessage);
            }
        } else {
            return redirect()->back()->with('error', 'No previous entry found with loanStatus "Rejected".');
        }
    }


    public function esignDocVerify(Request $request, $leadID)
    {
        $docRequestBy = Session::get('userID');

        try {
            // Fetch document info
            $document = DB::table('lms_esigndockyc')
                ->where('leadID', $leadID)
                ->orderBy('esignID', 'desc')
                ->first();

            if (!$document) {
                return response()->json(['error' => 'Document not found'], 404);
            }

            $requestId = $document->documentId;
            $contactID = $document->contactID ?? 'unknown';
            $fileName = $leadID . '_Agreement.pdf';

            // Step 1: Check audit trail
            $auditTrailResponse = Http::withHeaders([
                'app-id' => '683d502b558b020028dda41d',
                'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
            ])->get('https://live.zoop.one/contract/esign/v5/fetch/audit-trail', [
                        'request_id' => $requestId
                    ]);

            if (!$auditTrailResponse->ok()) {
                return redirect()->back()->with('error', 'Document have not been singed');

            }

            $trailData = $auditTrailResponse->json();
            if (!isset($trailData['trails'])) {
                return response()->json(['error' => 'Invalid audit trail response'], 500);
            }

            $isSigned = collect($trailData['trails'])->contains(fn($trail) => $trail['activity'] === 'ESIGN_COMPLETED');

            // Update signature status
            DB::table('lms_esigndockyc')
                ->where('esignID', $document->esignID)
                ->update([
                    'status' => $isSigned ? 'signed' : 'requested',
                    'updatedOn' => now()
                ]);

            // Step 2: If signed, download document
            if ($isSigned) {
                $docResponse = Http::withHeaders([
                    'app-id' => '683d502b558b020028dda41d',
                    'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
                ])->get('https://live.zoop.one/contract/esign/v5/fetch/request', [
                            'request_id' => $requestId
                        ]);

                if (!$docResponse->ok()) {
                    \Log::error("Failed to fetch signed document for request ID: $requestId");
                    return response()->json(['error' => 'Failed to fetch signed document'], 500);
                }

                $result = $docResponse->json();
                if (!isset($result['document']['signed_url'])) {
                    \Log::error("Signed URL missing in response for requestId: $requestId", $result);
                    return response()->json(['error' => 'Signed document URL missing'], 500);
                }

                $signedUrl = $result['document']['signed_url'];

                // Use Laravel HTTP client to download the PDF
                $pdfResponse = Http::timeout(30)->get($signedUrl);
                if (!$pdfResponse->successful()) {
                    return response()->json(['error' => 'Could not download signed document'], 500);
                }

                // Save PDF to storage
                $filePath = "documentData/{$contactID}/{$fileName}";
                Storage::disk('public')->put($filePath, $pdfResponse->body());

                // Save path in DB
                DB::table('lms_esigndockyc')
                    ->where('esignID', $document->esignID)
                    ->update([
                        'fileName' => $fileName
                    ]);

                // Log action
                actLogs('Profile', 'E-Sign document verified and downloaded', [
                    'leadID' => $leadID,
                    'docID' => $requestId,
                    'ip' => $request->ip(),
                    'DownloadOn' => now(),
                    'downloadBy' => $docRequestBy
                ]);

                return redirect()->back()->with('success', 'E-Sign Verified and Document Downloaded successfully.');
            } else {
                actLogs('Profile', 'E-Sign Status Checked', [
                    'leadID' => $leadID,
                    'docID' => $requestId,
                    'ip' => $request->ip(),
                    'checkedOn' => now(),
                    'checkedBy' => $docRequestBy
                ]);

                return redirect()->back()->with('success', 'E-Sign status checked successfully.');
            }

        } catch (\Exception $e) {
            \Log::error('Error in esignDocVerify: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred during verification'], 500);
        }
    }
 
    //         public function sendVideoKycRequest(Request $request, $leadID, $contactID) {

    //             $docRequestBy = Session::get('userID');
//             $docRequestByName = getUserNameById('users', 'userID', $docRequestBy, 'displayName');

    //             // Check if a record with the same leadID already exists in the lms_videoKyc table
//             $existingRequest = DB::table('lms_videoKyc')
//                                  ->where('leadID', $leadID)
//                                  ->first();

    //             // If a record already exists for the same leadID, skip the API request
//             if ($existingRequest) {
//                 return redirect()->back()->with('error', 'Video-KYC request already sent for this lead.');
//             }

    //             // Get contact details
//             $contact = DB::table('lms_approval')
//                 ->leftJoin('lms_contact', 'lms_approval.contactID', '=', 'lms_contact.contactID')
//                 ->select('lms_contact.name', 'lms_contact.email', 'lms_contact.mobile', 'lms_approval.officialEmail')
//                 ->where('lms_contact.contactID', $contactID)  // Corrected table alias
//                 ->orderBy('lms_approval.id', 'desc')
//                 ->first();

    //             $email = $contact->email;           // email from contact table
//             $name = $contact->name;             // name from contact table

    //             $name = trim($name); // Remove leading and trailing spaces
//             $name = preg_replace('/[^a-zA-Z\s]/', '', $name); // Remove non-alphabetical characters except spaces
//             $name = ucwords(strtolower($name)); // Capitalize the first letter of each wo
//             $mobile = $contact->mobile;         // mobile from contact table
//             // $officialEmail = $contact->officialEmail;  // officialEmail from lms_approval table

    //             // if (!empty($officialEmail)) {
//             //     $email = $officialEmail;
//             // }

    //             // Prepare post data
//             $postData = [
//                 'customer_identifier' => $email,
//                 'customer_name' => $name,
//                 'template_name' => "DTD_CPY",
//                 'notify_customer' => true,
//                 'generate_access_token' => true
//             ];

    //             // Make API request
//             $response = Http::withHeaders([
//                 'Authorization' => 'Basic QUNLMjQxMTA0MTUzNjA1MDgxWU9GV1IxNERLWTkyTDkgOjRER1I4U01RTkY5UDREWkU0Q0xaWDM3UDFBQkVOVTFS',
//                 'Content-Type' => 'application/json'
//             ])->post('https://api.digio.in/client/kyc/v2/request/with_template', $postData);

    //             $result = $response->json();


    //             // Extract result data
//             $kycID = $result['id'];
//             $created_at = $result['created_at'];
//             $status = $result['status'];
//             $customer_identifier = $result['customer_identifier'];
//             $customer_name = $result['customer_name'];

    //             // Insert into kyc_requests table
//             $videoKycData = DB::table('lms_videoKyc')->insert([
//                 'kycID' => randomNo(100000, 999999),
//                 'contactID' => $contactID,
//                 'leadID' => $leadID,
//                 'customer_identifier' => $customer_identifier,
//                 'customer_name' => $customer_name,
//                 'kycRequestID' => $kycID,
//                 'activeInactive' => '0',
//                 'status' => $status,
//                 'requestBY' => $docRequestBy,
//                 'created_at' => $created_at,
//                 'addedOn' => dt()
//             ]);

    //             if ($videoKycData) {
//                 $communicationData = [
//                     'leadID' => $leadID,
//                     'communicationType' => 'Mail',
//                     'operation' => 'Video-KYC Mail Sent',
//                     'ip' => $request->ip(),
//                     'addedOn' => dt(), // Assuming dt() returns the current datetime
//                     'addedBy' => Session::get('userID')
//                 ];

    //                 // Insert communication data into the database
//                 actLogs('Profile', 'communication added', $communicationData);
//                 actLogs('Profile', 'Video-KYC Requested', $videoKycData);
//                 DB::table('lms_communication')->insert($communicationData);

    //                 return redirect()->back()->with('success', 'Video-KYC request sent successfully.');
//             } else {
//                 return redirect()->back()->with('error', 'Video-KYC request failed.');
//             }
//         }


    //         public function videokycDownload(Request $request){

    //             $leadID = $request->leadID;
//             $kycRequest = DB::table('lms_videoKyc')
//                     ->where('leadID', $leadID)
//                     ->orderBy('kycID', 'desc')
//                     ->first();  // Get the first result sorted by kycID in descending order


    //             if ($kycRequest) {
//                 $kycRequestID = $kycRequest->kycRequestID;
//                 $kycID = $kycRequest->kycID;

    //                 // Send the API request to get KYC response
//                 $response = Http::withHeaders([
//                     'Authorization' => 'Basic QUNLMjQxMTA0MTUzNjA1MDgxWU9GV1IxNERLWTkyTDkgOjRER1I4U01RTkY5UDREWkU0Q0xaWDM3UDFBQkVOVTFS',
//                     'Content-Type' => 'application/json',
//                 ])
//                 ->post('https://api.digio.in/client/kyc/v2/' . $kycRequestID . '/response');



    //                 // Check if the response is successful
//                 if ($response->successful()) {
//                     $result = $response->json();


    //                     $checkStatus = $result['status'];

    //                     if ($checkStatus == "approved" || $checkStatus == "approval_pending") {
//                         // Extract data from the response
//                         $responseID = $result['id'];
//                         $status = $result['status'];
//                         $videoFileID = $result['actions'][0]['file_id'];
//                         $updatedDate = $result['updated_at'];
//                       // $videoLocation = $result['actions'][0]['sub_actions'][0]['details']['address'];

    //                         if (isset($result['actions'][0]['sub_actions'][0]['details']['address'])) {
//                                 $videoLocation = $result['actions'][0]['sub_actions'][0]['details']['address'];
//                             } else {
//                                 // Handle the case where 'details' or 'address' doesn't exist
//                                 $videoLocation = null; // or any default value or error message you prefer
//                             }

    //                         // User side Aadhar card verification
//                         $frontAadharCard = $result['actions'][1]['file_id'];
//                         $backAadharCard = $result['actions'][1]['sub_file_id'];
//                         $aadharDob = $result['actions'][1]['id_card_data_response']['dob'];
//                         $aadharNO = $result['actions'][1]['id_card_data_response']['id_no'];
//                         $aadharCustomerName = $result['actions'][1]['id_card_data_response']['name'];
//                         $aadharFatherName = $result['actions'][1]['id_card_data_response']['fathers_name'];
//                         $aadharAddress = $result['actions'][1]['id_card_data_response']['address'];
//                         $aadharIDTypes = $result['actions'][1]['id_card_data_response']['id_type'];

    //                         // User side Pan card verification
//                         $panCard = $result['actions'][2]['file_id'];
//                         $panDob = $result['actions'][2]['id_card_data_response']['dob'];
//                         $panNO = $result['actions'][2]['id_card_data_response']['id_no'];
//                         $panCustomerName = $result['actions'][2]['id_card_data_response']['name'];
//                         $panFatherName = $result['actions'][2]['id_card_data_response']['fathers_name'];
//                         $panIDTypes = $result['actions'][2]['id_card_data_response']['id_type'];

    //                         // Prepare the data for inserting into the database
//                         $dataRecord = [
//                             'contactID' => $kycRequest->contactID,
//                             'leadID' => $leadID,
//                             'videoFileID' => $videoFileID,
//                             'kycLocation' => $videoLocation,
//                             'frontAadharCard' => $frontAadharCard,
//                             'backAadharCard' => $backAadharCard,
//                             'panCard' => $panCard,
//                             'aadharDob' => $aadharDob,
//                             'aadharNO' => $aadharNO,
//                             'aadharCustomerName' => $aadharCustomerName,
//                             'aadharFatherName' => $aadharFatherName,
//                             'aadharAddress' => $aadharAddress,
//                             'aadharIDTypes' => $aadharIDTypes,
//                             'panDob' => $panDob,
//                             'panNO' => $panNO,
//                             'panCustomerName' => $panCustomerName,
//                             'panFatherName' => $panFatherName,
//                             'panIDTypes' => $panIDTypes,
//                             'status' => $status,
//                             'updatedOn' => $updatedDate
//                         ];



    //                         // Update the record in the database if videoFileID exists
//                         if ($videoFileID != '') {
//                             DB::table('lms_videoKyc')
//                                 ->where('leadID', $leadID)
//                                 ->where('kycID', $kycID)
//                                 ->update($dataRecord);
//                         }

    //                         // If KYC request ID is the same or status is 'approval_pending' or 'approved'
//                         if ($kycRequestID == $responseID || $status == 'approval_pending' || $status == 'approved') {
//                                 // Set up the cURL request to fetch the video file from the Digio API
//                                 $url = 'https://api.digio.in/client/kyc/v2/media/' . $videoFileID;
//                                 $headers = [
//                                     'Authorization: Basic QUNLMjQxMTA0MTUzNjA1MDgxWU9GV1IxNERLWTkyTDkgOjRER1I4U01RTkY5UDREWkU0Q0xaWDM3UDFBQkVOVTFS',
//                                     'Content-Type: application/json',
//                                 ];

    //                                 // Initialize cURL session
//                                 $ch = curl_init();

    //                                 // Set the cURL options
//                                 curl_setopt($ch, CURLOPT_URL, $url);  // Set the URL
//                                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
//                                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  // Set the headers

    //                                 // Execute the cURL request and get the response
//                                 $response = curl_exec($ch);

    //                                 // Check if there was an error with the cURL request
//                                 if (curl_errno($ch)) {
//                                     curl_close($ch);
//                                     return response()->json(['response' => 'error', 'message' => 'Failed to retrieve video.']);
//                                 }

    //                                 // Get the response status code
//                                 $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //                                 // Close the cURL session
//                                 curl_close($ch);

    //                                 // If the request was successful, process the response
//                                 if ($statusCode == 200) {
//                                     // Encode the video data into base64
//                                     $videoData = base64_encode($response);

    //                                     // Embed video in HTML and create download link
//                                     $responseData = '<center><video controls style="height:174px;margin-left: -6px;">
//                                                         <source src="data:video/mpeg;base64,' . $videoData . '" type="video/mp4">
//                                                       </video><center>';
//                                     $responseData .= '<a class="float-right btn btn-success btn-sm" style="margin: 4px;width: 65%;border-radius: 22px 21px 21px 21px;" 
//                                                       href="data:video/mpeg;base64,' . $videoData . '" download="video.mpg">Download</a>';

    //                                     // Output the video HTML and download link
//                                     echo $responseData;
//                                 } else {
//                                     return response()->json(['response' => 'error', 'message' => 'Failed to retrieve video.']);
//                                 }
//                             }

    //                     }else{
//                          return response()->json(['response' => 'failed', 'message' => 'KYC pending by customer.']);  
//                     }
//                 } else {
//                      return response()->json(['response' => 'failed', 'message' => 'Failed to get KYC response from API']);
//                 }
//             } else {
//                 return response()->json(['message' => 'KYC request not found'], 404);
//             }
//         }    

    //       public function kycView(Request $request, $leadID)
//     {
//         // Retrieve the latest KYC request for the given leadID
//         $kycRequest = DB::table('lms_videoKyc')
//             ->where('leadID', $leadID)
//             ->orderBy('kycID', 'desc')
//             ->first(); // Get the first result sorted by kycID in descending order

    //         // Check if KYC request exists
//         if (!$kycRequest) {
//             return response()->json(['error' => 'KYC request not found'], 404);
//         }

    //         // Pass necessary data to the view
//         $page_info = pageInfo('KYC Details', $request->segment(1));
//         $kycData = compact('page_info', 'kycRequest');

    //         return view('profile.kycView')->with($kycData);
//     }


    //   public function videokycCMApproval(Request $request){
//     // Find the most recent KYC request for the given leadID
//     $kycRequest = DB::table('lms_videoKyc')
//         ->select('kycID','leadID')
//         ->where('leadID', $request->leadID)
//         ->orderBy('kycID', 'desc') // Order by kycID to get the last record
//         ->first(); // Get the last record

    //     // Check if KYC request exists
//     if ($kycRequest) {

    //         // Update the last record for the specific leadID
//         $updated = DB::table('lms_videoKyc')
//             ->where('kycID', $kycRequest->kycID) // Ensure we're updating the correct record
//             ->update([
//                 'cmVerified' => 1, // Set cmVerified to 1 (approved)
//                 'verifiedBy' => getUserID(), // Assuming getUserID() returns the current user's ID
//             ]);

    //         // Check if the update was successful
//         if ($updated) {
//             // Log the action
//             actLogs('Profile', 'kyc approval update by CM', $kycRequest);

    //             return response()->json(['response' => 'success', 'message' => 'KYC Approved successfully.']);
//         } else {
//             return response()->json(['response' => 'failed', 'message' => 'Failed to update KYC approval.']);
//         }
//     } else {
//         return response()->json(['response' => 'failed', 'message' => 'No KYC request found for the given Lead ID.']);
//     }
// }

    //     public function fetchMedia($mediaId)
//         {
//             // The external API URL
//             $url = "https://api.digio.in/client/kyc/v2/media/{$mediaId}";

    //             // Initialize cURL session
//             $ch = curl_init();

    //             // Set cURL options
//             curl_setopt($ch, CURLOPT_URL, $url);  // Set the URL to fetch
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the transfer as a string
//             curl_setopt($ch, CURLOPT_HTTPHEADER, [
//                 'Authorization: Basic QUNLMjQxMTA0MTUzNjA1MDgxWU9GV1IxNERLWTkyTDkgOjRER1I4U01RTkY5UDREWkU0Q0xaWDM3UDFBQkVOVTFS',
//                 'Content-Type: application/json',
//             ]);

    //             // Execute cURL request
//             $response = curl_exec($ch);

    //             // Check if the cURL request was successful
//             if ($response === false) {
//                 $error = curl_error($ch);  // Capture any error
//                 curl_close($ch);  // Close cURL session

    //                 // Return error response
//                 return response()->json([
//                     'success' => false,
//                     'message' => $error,
//                 ]);
//             }

    //             // Get the HTTP status code
//             $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //             // Close cURL session
//             curl_close($ch);

    //             // If the response is successful (status code 200)
//             if ($statusCode === 200) {
//                 return response()->json([
//                     'success' => true,
//                     'body' => base64_encode($response)  // Base64 encode the response body
//                 ]);
//             }

    //             // If the response was not successful, return an error
//             return response()->json([
//                 'success' => false,
//                 'error' => $statusCode,
//                 'message' => $response  // Return the error message from the API
//             ]);
//         }



    public function sendVideoKycRequest(Request $request, $leadID, $contactID)
    {

        $docRequestBy = Session::get('userID');
        $docRequestByName = getUserNameById('users', 'userID', $docRequestBy, 'displayName');

        // Check if a record with the same leadID already exists in the lms_videoKyc table
        $existingRequest = DB::table('lms_videoKyc_self')
            ->where('leadID', $leadID)
            ->first();

        // If a record already exists for the same leadID, skip the API request
        if ($existingRequest) {
            return redirect()->back()->with('error', 'Video-KYC request already sent for this lead.');
        }

        // Get contact details
        $contact = DB::table('lms_approval')
            ->leftJoin('lms_contact', 'lms_approval.contactID', '=', 'lms_contact.contactID')
            ->select('lms_contact.name', 'lms_contact.email', 'lms_contact.mobile', 'lms_approval.officialEmail')
            ->where('lms_contact.contactID', $contactID)  // Corrected table alias
            ->orderBy('lms_approval.id', 'desc')
            ->first();

        $email = $contact->email;           // email from contact table
        $name = $contact->name;             // name from contact table

        $name = trim($name); // Remove leading and trailing spaces
        $name = preg_replace('/[^a-zA-Z\s]/', '', $name); // Remove non-alphabetical characters except spaces
        $name = ucwords(strtolower($name)); // Capitalize the first letter of each wo
        $mobile = $contact->mobile;         // mobile from contact table

        // Check helper for this getToken 

        $token = getToken();
        $workFlowID = 'WI26042025257854';
        $currentDate = date('Y-m-d'); // Get the current date
        $linkExpiryDate = date('d-m-Y', strtotime($currentDate . ' +1 day'));

        $response = Http::withToken($token)->post('https://app.nextbigbox.co.in/api/workFlowTask', [
            'workFlowID' => $workFlowID,
            'name' => $name,
            'email' => $email,
            'phone' => $mobile,
            'linkExpiryDate' => $linkExpiryDate,
        ]);

        $result = $response->json();


        // Extract result data
        $kycID = randomNo(100000, 999999);
        $created_at = date('Y-m-d');
        if ($result['status'] == 'Pending') {
            $status = 'requested';
        } else {
            $status = 'requested';
        }
        $customer_identifier = $result['customer_email'];
        $customer_name = $result['customer_name'];
        $kycRequestID = $result['taskID'];
        $workFlowID = $result['workFlowID'];

        // Insert into kyc_requests table
        $videoKycData = DB::table('lms_videoKyc_self')->insert([
            'kycID' => $kycID,
            'contactID' => $contactID,
            'leadID' => $leadID,
            'customer_email' => $customer_identifier,
            'workFlowID' => $workFlowID,
            'customer_name' => $customer_name,
            'kycRequestID' => $kycRequestID,
            'activeInactive' => '0',
            'status' => $status,
            'server' => 'self',
            'requestBY' => $docRequestBy,
            'created_at' => $created_at,
            'addedOn' => dt()
        ]);


        if ($videoKycData) {
            $communicationData = [
                'leadID' => $leadID,
                'communicationType' => 'Mail',
                'operation' => 'Video-KYC Mail Sent',
                'ip' => $request->ip(),
                'addedOn' => dt(), // Assuming dt() returns the current datetime
                'addedBy' => Session::get('userID')
            ];

            // Insert communication data into the database
            actLogs('Profile', 'communication added', $communicationData);
            actLogs('Profile', 'Video-KYC Requested', $videoKycData);
            DB::table('lms_communication')->insert($communicationData);

            return redirect()->back()->with('success', 'Video-KYC request sent successfully.');
        } else {
            return redirect()->back()->with('error', 'Video-KYC request failed.');
        }
    }

    public function videokycDownload(Request $request)
    {

        $leadID = $request->leadID;

        // Try fetching from the lms_videoKyc_self table
        $kycRequest = DB::table('lms_videoKyc_self')
            ->select('leadID', 'kycRequestID', 'contactID', 'workFlowID', 'kycID', DB::raw("'lms_videoKyc_self' as table_name"))
            ->where('leadID', $leadID)
            ->orderBy('id', 'desc')
            ->first();

        // If no record found in lms_videoKyc_self, check in lms_videoKyc table
        if (!$kycRequest) {
            $kycRequest = DB::table('lms_videoKyc')
                ->select('leadID', 'kycRequestID', 'contactID', 'workFlowID', 'kycID', DB::raw("'lms_videoKyc' as table_name"))
                ->where('leadID', $leadID)
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($kycRequest->table_name == 'lms_videoKyc_self') {

            $kycRequestID = $kycRequest->kycRequestID;
            $workFlowID = $kycRequest->workFlowID;
            $kycID = $kycRequest->kycID;

            $token = getToken();

            $response = Http::withToken($token)->get('https://app.nextbigbox.co.in/api/WorkFlowTaskPreview', [
                'workFlowID' => $workFlowID,
                'taskID' => $kycRequestID,
            ]);


            // Check if the response is successful
            if ($response->successful()) {
                $result = $response->json();

                $checkStatus = $result['workTask']['workFlowTask']['status'];

                if ($checkStatus == "Approved" || $checkStatus == "Pending for Approval") {
                    // Extract data from the response

                    $responseID = $result['workTask']['workFlowTask']['taskID'];
                    $status = $result['workTask']['workFlowTask']['status'];
                    $videoFileID = $result['workTask']['selfVideoKycTask'][0]['videoUrl'];
                    $updatedDate = $result['workTask']['workFlowTask']['created_at'];

                    if (isset($result['workTask']['selfVideoKycTask'][0]['geoTaggingStatus']) == 1) {
                        $videoLocation = $result['workTask']['selfVideoKycTask'][0]['location'];
                    } else {
                        // Handle the case where 'details' or 'address' doesn't exist
                        $videoLocation = null; // or any default value or error message you prefer
                    }

                    // User side Aadhar card verification
                    // pan at 0 index
                    $panFrontImage = $result['workTask']['identityProofsTask'][0]['frontFilePath'];
                    //  $panBackImage = $result['workTask']['identityProofsTask'][0]['backFilePath'];
                    $panIdentityDob = $result['workTask']['identityProofsTask'][0]['dob'];
                    $panIdentityNO = $result['workTask']['identityProofsTask'][0]['idNO'];
                    $panIdentityCustomerName = $result['workTask']['identityProofsTask'][0]['name'];
                    $panIdentityFatherName = $result['workTask']['identityProofsTask'][0]['fatherName'];
                    $panIdentityAddress = $result['workTask']['identityProofsTask'][0]['address'];
                    $panIdentityIDTypes = $result['workTask']['identityProofsTask'][0]['identificationType'];

                    // aadhar at 1 index
                    $aadharFrontImage = $result['workTask']['identityProofsTask'][1]['frontFilePath'];
                    $aadharBackImage = $result['workTask']['identityProofsTask'][1]['backFilePath'];
                    $aadharIdentityDob = $result['workTask']['identityProofsTask'][1]['dob'];
                    $aadharIdentityNO = $result['workTask']['identityProofsTask'][1]['idNO'];
                    $aadharIdentityCustomerName = $result['workTask']['identityProofsTask'][1]['name'];
                    $aadharIdentityFatherName = $result['workTask']['identityProofsTask'][1]['fatherName'];
                    $aadharIdentityAddress = $result['workTask']['identityProofsTask'][1]['address'];
                    $aadharIdentityIDTypes = $result['workTask']['identityProofsTask'][1]['identificationType'];

                    if ($checkStatus == 'Pending for Approval') {
                        $kycstatus = 'approval_pending';
                    } elseif ($checkStatus == 'Approved') {
                        $kycstatus = 'approved';
                    } elseif ($checkStatus == 'Pending') {
                        $kycstatus = 'requested';
                    } else {
                        $kycstatus = 'expired';
                    }
                    // Prepare the data for inserting into the database
                    $dataRecord = [
                        'contactID' => $kycRequest->contactID,
                        'leadID' => $leadID,
                        'videoFileID' => $videoFileID,
                        'kycLocation' => $videoLocation,
                        'frontImage' => $aadharFrontImage,
                        'backImage' => $aadharBackImage,
                        'identityDob' => $aadharIdentityDob,
                        'identityNO' => $aadharIdentityNO,
                        'identityCustomerName' => $aadharIdentityCustomerName,
                        'identityFatherName' => $aadharIdentityFatherName,
                        'identityAddress' => $aadharIdentityAddress,
                        'identityIDType' => $aadharIdentityIDTypes,
                        'panCard' => $panFrontImage,
                        'panDob' => $panIdentityDob,
                        'panNO' => $panIdentityNO,
                        'panCustomerName' => $panIdentityCustomerName,
                        'panFatherName' => $panIdentityFatherName,
                        'panIDTypes' => $panIdentityIDTypes,
                        'status' => $kycstatus,
                        'updatedOn' => $updatedDate
                    ];


                    // Update the record in the database if videoFileID exists
                    if ($videoFileID != '') {
                        DB::table('lms_videoKyc_self')
                            ->where('leadID', $leadID)
                            ->where('kycID', $kycID)
                            ->update($dataRecord);
                    }

                    // If KYC request ID is the same or status is 'approval_pending' or 'approved'
                    if ($kycRequestID == $responseID || $status == 'Pending for Approval' || $status == 'Approved') {
                        $token = getToken();
                        $response = Http::withToken($token)->post('https://app.nextbigbox.co.in/api/downloadFileFromS3', [
                            'url' => $videoFileID,
                        ]);

                        $result = $response->json();



                        // Check if the response contains a valid URL (assuming the URL is returned in 'videoURL' or similar field)
                        if (!empty($result['fileContent'])) {
                            // Base64-encoded video content
                            $videoData = $result['fileContent'];

                            // Embed the video using base64 data in the <video> tag
                            $responseData = '<video controls style="height:174px; margin: 0 auto;">
                                                <source src="data:video/webm;base64,' . $videoData . '" type="video/webm">
                                            </video>
                                            <br>
                                            <a class="btn btn-success btn-sm" style="margin: 4px;width:55%;border-radius: 22px 21px 21px 21px;" 
                                               href="data:video/webm;base64,' . $videoData . '" download="' . $result['fileName'] . '">Download</a>';
                            // Output the video HTML and download link
                            echo $responseData;
                        } else {
                            return response()->json(['response' => 'error', 'message' => 'Failed to retrieve video.']);
                        }
                    }

                } else {
                    return response()->json(['response' => 'failed', 'message' => 'KYC pending by customer.']);
                }
            } else {
                return response()->json(['response' => 'failed', 'message' => 'Failed to get KYC response from API']);
            }
        } else if ($kycRequest->table_name == 'lms_videoKyc') {

            $kycRequestID = $kycRequest->kycRequestID;
            $kycID = $kycRequest->kycID;

            // Send the API request to get KYC response
            $response = Http::withHeaders([
                'Authorization' => 'Basic QUNLMjQxMTA0MTUzNjA1MDgxWU9GV1IxNERLWTkyTDkgOjRER1I4U01RTkY5UDREWkU0Q0xaWDM3UDFBQkVOVTFS',
                'Content-Type' => 'application/json',
            ])
                ->post('https://api.digio.in/client/kyc/v2/' . $kycRequestID . '/response');



            // Check if the response is successful
            if ($response->successful()) {
                $result = $response->json();


                $checkStatus = $result['status'];

                if ($checkStatus == "approved" || $checkStatus == "approval_pending") {
                    // Extract data from the response
                    $responseID = $result['id'];
                    $status = $result['status'];
                    $videoFileID = $result['actions'][0]['file_id'];
                    $updatedDate = $result['updated_at'];
                    // $videoLocation = $result['actions'][0]['sub_actions'][0]['details']['address'];

                    if (isset($result['actions'][0]['sub_actions'][0]['details']['address'])) {
                        $videoLocation = $result['actions'][0]['sub_actions'][0]['details']['address'];
                    } else {
                        // Handle the case where 'details' or 'address' doesn't exist
                        $videoLocation = null; // or any default value or error message you prefer
                    }

                    // User side Aadhar card verification
                    $frontAadharCard = $result['actions'][1]['file_id'];
                    $backAadharCard = $result['actions'][1]['sub_file_id'];
                    $aadharDob = $result['actions'][1]['id_card_data_response']['dob'];
                    $aadharNO = $result['actions'][1]['id_card_data_response']['id_no'];
                    $aadharCustomerName = $result['actions'][1]['id_card_data_response']['name'];
                    $aadharFatherName = $result['actions'][1]['id_card_data_response']['fathers_name'];
                    $aadharAddress = $result['actions'][1]['id_card_data_response']['address'];
                    $aadharIDTypes = $result['actions'][1]['id_card_data_response']['id_type'];

                    // User side Pan card verification
                    $panCard = $result['actions'][2]['file_id'];
                    $panDob = $result['actions'][2]['id_card_data_response']['dob'];
                    $panNO = $result['actions'][2]['id_card_data_response']['id_no'];
                    $panCustomerName = $result['actions'][2]['id_card_data_response']['name'];
                    $panFatherName = $result['actions'][2]['id_card_data_response']['fathers_name'];
                    $panIDTypes = $result['actions'][2]['id_card_data_response']['id_type'];

                    // Prepare the data for inserting into the database
                    $dataRecord = [
                        'contactID' => $kycRequest->contactID,
                        'leadID' => $leadID,
                        'videoFileID' => $videoFileID,
                        'kycLocation' => $videoLocation,
                        'frontAadharCard' => $frontAadharCard,
                        'backAadharCard' => $backAadharCard,
                        'panCard' => $panCard,
                        'aadharDob' => $aadharDob,
                        'aadharNO' => $aadharNO,
                        'aadharCustomerName' => $aadharCustomerName,
                        'aadharFatherName' => $aadharFatherName,
                        'aadharAddress' => $aadharAddress,
                        'aadharIDTypes' => $aadharIDTypes,
                        'panDob' => $panDob,
                        'panNO' => $panNO,
                        'panCustomerName' => $panCustomerName,
                        'panFatherName' => $panFatherName,
                        'panIDTypes' => $panIDTypes,
                        'status' => $status,
                        'updatedOn' => $updatedDate
                    ];


                    // Update the record in the database if videoFileID exists
                    if ($videoFileID != '') {
                        DB::table('lms_videoKyc')
                            ->where('leadID', $leadID)
                            ->where('kycID', $kycID)
                            ->update($dataRecord);
                    }

                    // If KYC request ID is the same or status is 'approval_pending' or 'approved'
                    if ($kycRequestID == $responseID || $status == 'approval_pending' || $status == 'approved') {
                        // Set up the cURL request to fetch the video file from the Digio API
                        $url = 'https://api.digio.in/client/kyc/v2/media/' . $videoFileID;
                        $headers = [
                            'Authorization: Basic QUNLMjQxMTA0MTUzNjA1MDgxWU9GV1IxNERLWTkyTDkgOjRER1I4U01RTkY5UDREWkU0Q0xaWDM3UDFBQkVOVTFS',
                            'Content-Type: application/json',
                        ];

                        // Initialize cURL session
                        $ch = curl_init();

                        // Set the cURL options
                        curl_setopt($ch, CURLOPT_URL, $url);  // Set the URL
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return the response as a string
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  // Set the headers

                        // Execute the cURL request and get the response
                        $response = curl_exec($ch);


                        // Check if there was an error with the cURL request
                        if (curl_errno($ch)) {
                            curl_close($ch);
                            return response()->json(['response' => 'error', 'message' => 'Failed to retrieve video.']);
                        }

                        // Get the response status code
                        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        // Close the cURL session
                        curl_close($ch);

                        // If the request was successful, process the response
                        if ($statusCode == 200) {
                            // Encode the video data into base64
                            $videoData = base64_encode($response);

                            // Embed video in HTML and create download link
                            $responseData = '<center><video controls style="height:174px;margin-left: -6px;">
                                                        <source src="data:video/mpeg;base64,' . $videoData . '" type="video/mp4">
                                                      </video><center>';
                            $responseData .= '<a class="float-right btn btn-success btn-sm" style="margin: 4px;width: 65%;border-radius: 22px 21px 21px 21px;" 
                                                      href="data:video/mpeg;base64,' . $videoData . '" download="video.mpg">Download</a>';

                            // Output the video HTML and download link
                            echo $responseData;
                        } else {
                            return response()->json(['response' => 'error', 'message' => 'Failed to retrieve video.']);
                        }
                    }

                } else {
                    return response()->json(['response' => 'failed', 'message' => 'KYC pending by customer.']);
                }
            } else {
                return response()->json(['response' => 'failed', 'message' => 'Failed to get KYC response from API']);
            }
        } else {
            return response()->json(['message' => 'KYC request not found'], 404);
        }
    }

    public function kycView(Request $request, $leadID)
    {
        // Retrieve the latest KYC request for the given leadID
        $kycRequest = DB::table('lms_videoKyc')
            ->where('leadID', $request->leadID)
            ->orderBy('id', 'desc') // Order by kycID to get the last record
            ->first(); // Get the last record

        // If not found in lms_videoKyc, check in lms_videokyc_self
        if (!$kycRequest) {
            $kycRequest = DB::table('lms_videoKyc_self')
                ->where('leadID', $request->leadID)
                ->orderBy('id', 'desc') // Order by kycID to get the last record
                ->first(); // Get the last record
        }


        if ($kycRequest->server == 'self') {
            $view = 'kycViewSelf';
        } else {
            $view = 'kycView';
        }

        // Pass necessary data to the view
        $page_info = pageInfo('KYC Details', $request->segment(1));
        $kycData = compact('page_info', 'kycRequest');

        return view('profile.' . $view)->with($kycData);
    }

    public function videokycCMApproval(Request $request)
    {


        // Find the most recent KYC request for the given leadID in lms_videoKyc
        $kycRequest = DB::table('lms_videoKyc')
            ->select('kycID', 'leadID', 'kycRequestID')
            ->where('leadID', $request->leadID)
            ->orderBy('id', 'desc') // Order by kycID to get the last record
            ->first(); // Get the last record

        // If not found in lms_videoKyc, check in lms_videokyc_self
        if (!$kycRequest) {
            $kycRequest = DB::table('lms_videoKyc_self')
                ->select('kycID', 'leadID', 'kycRequestID')
                ->where('leadID', $request->leadID)
                ->orderBy('id', 'desc') // Order by kycID to get the last record
                ->first(); // Get the last record
        }

        // Check if KYC request exists in either table
        if ($kycRequest) {
            // Update the last record for the specific leadID
            $updated = DB::table('lms_videoKyc')
                ->where('kycRequestID', $kycRequest->kycRequestID) // Ensure we're updating the correct record
                ->update([
                    'cmVerified' => 1, // Set cmVerified to 1 (approved)
                    'verifiedBy' => getUserID(), // Assuming getUserID() returns the current user's ID
                ]);

            // If not found in lms_videoKyc, update the record in lms_videokyc_self
            if (!$updated) {


                $token = getToken();
                $workFlowID = 'WI26042025257854';

                $response = Http::withToken($token)->post('https://app.nextbigbox.co.in/api/updateWorkTaskStatus', [
                    'workFlowID' => $workFlowID,
                    'taskID' => $kycRequest->kycRequestID,
                    'status' => 'Approved'
                ]);

                $result = $response->json();
                $updated = DB::table('lms_videoKyc_self')
                    ->where('kycRequestID', $kycRequest->kycRequestID)
                    ->update([
                        'cmVerified' => 1,
                        'status' => 'approved',
                        'verifiedBy' => getUserID(),
                    ]);

            }

            // Check if the update was successful
            if ($updated) {
                // Log the action
                actLogs('Profile', 'kyc approval update by CM', $kycRequest);

                return response()->json(['response' => 'success', 'message' => 'KYC Approved successfully.']);
            } else {
                return response()->json(['response' => 'failed', 'message' => 'Failed to update KYC approval.']);
            }
        } else {
            return response()->json(['response' => 'failed', 'message' => 'No KYC request found for the given Lead ID.']);
        }
    }

    public function fetchMediaNbb(Request $request)
    {
        // Get the media ID from the POST request
        $mediaId = $request->input('mediaId');

        // Get the token using the helper function (ensure this function is defined)
        $token = getToken();

        try {
            // Make the POST request to the NextBigBox API
            $response = Http::withToken($token)->post('https://app.nextbigbox.co.in/api/downloadFileFromS3', [
                'url' => $mediaId,
            ]);

            // Get the status code
            $statusCode = $response->status();

            // If the response is successful (status code 200)
            if ($statusCode === 200) {
                // Get the response body and decode the JSON
                $data = json_decode($response->body(), true);

                // Check if fileContent is present
                if (isset($data['fileContent'])) {
                    $fileContent = $data['fileContent'];

                    // If fileContent is already base64 encoded, just return it
                    return response()->json([
                        'success' => true,
                        'body' => $fileContent
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'File content not found.'
                    ]);
                }
            }

            // If the request was not successful, return an error
            return response()->json([
                'success' => false,
                'error' => $statusCode,
                'message' => $response->json()
            ]);
        } catch (\Exception $e) {
            // Handle exceptions and return the error message
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function sendEnachRequest(Request $request, $leadID)
    {


        $templateData = DB::table('lms_contact')
            ->join('lms_approval', 'lms_contact.contactID', '=', 'lms_approval.contactID')
            ->select('lms_contact.gender','lms_contact.name', 'lms_contact.email', 'lms_approval.leadID')
            ->where('lms_approval.status', 'Approved')
            ->where('lms_approval.leadID', $leadID)
            ->orderBy('lms_approval.id', 'desc')
            ->first();
 
        $fromEmail = 'sanction@cashpey.com';
        $template = 'emailTemplate.enach';
        $companyName = cmp()->companyName;
        $subject = $companyName . ' - E-Nach Registration -' . $leadID;
        $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
        $receiversEmail = [$templateData->email];

        $ccEmails = array('confirmation@cashpey.com');


        try {
            Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));

            $communicationData = [
                'leadID' => $leadID,
                'communicationType' => 'Email',
                'operation' => 'E-Nach Registration Mail Sent',
                'ip' => $request->ip(),
                'addedOn' => dt(), // Assuming dt() returns the current datetime
                'addedBy' => Session::get('userID'),
            ];

            // Insert communication data into the database
            actLogs('Profile', 'communication added', $communicationData);
            DB::table('lms_communication')->insert($communicationData);
            return redirect()->back()->with('success', 'E-Nach registration request sent successfully.');
        } catch (\Exception $e) {
            return response()->json(['response' => 'failed', 'message' => 'Failed to send the e-nach registration request.']);
        }


    }

   public function initiateEstamp(Request $request, $leadID)
    {
        $existingEstamp = DB::table('lms_estamp')->where('leadID', $leadID)->first();
        if ($existingEstamp) {
            return redirect()->back()->with('info', 'eStamp already initiated for this lead.');
        }

        // Get contact and approval info
        $templateData = DB::table('lms_contact')
            ->join('lms_approval', 'lms_contact.contactID', '=', 'lms_approval.contactID')
            ->select('lms_contact.name', 'lms_contact.email', 'lms_approval.leadID', 'lms_contact.contactID')
            ->where('lms_approval.status', 'Approved')
            ->where('lms_approval.leadID', $leadID)
            ->orderBy('lms_approval.id', 'desc')
            ->first();

        if (!$templateData) {
            return redirect()->back()->with('error', 'Lead not found or not approved.');
        }


        //   $stateCode = $stateLookupResponse['data'][0]['code'];
        $stateCode = "DL";
        $articleResponse = Http::withHeaders([
            'app-id' => '683d502b558b020028dda41d',
            'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
        ])->get("https://live.zoop.one/contract/estamp/v2/fetch/articles?stateCode[0]=" . $stateCode);

        if (!$articleResponse->successful() || empty($articleResponse['data'])) {
            return $request->ajax()
                ? response()->json(['response' => 'error', 'message' => 'Failed to fetch article list from Zoop API.'])
                : redirect()->back()->with('error', 'Failed to fetch article list from Zoop API.');
        }

        // Find the articleId for "General Agreement"
        $articleList = $articleResponse['data'];
        $articleId = null;

        foreach ($articleList as $article) {
            if (strtolower(trim($article['description'])) === 'general agreement') {
                $articleId = $article['id'];
                break;
            }
        }

        if (!$articleId) {
            return $request->ajax()
                ? response()->json(['response' => 'error', 'message' => 'General Agreement article not found for this state.'])
                : redirect()->back()->with('error', 'General Agreement article not found for this state.');
        }

        //  Step 2: Prepare payload for eStamp
        $payload = [
            "stateCode" => $stateCode,
            "firstPartyName" => "Naman Commodities Private Limited.",
            "secondPartyName" => $templateData->name,
            "stampDutyPaidBy" => "SECOND_PARTY",
            "refId" => "ESTAMP_" . $leadID,
            "series" => [
                [
                    "stampDutyAmount" => 100,
                    "stampType" => "ESTAMP",
                    "articleId" => $articleId,
                    "purpose" => "Loan Agreement",
                    "considerationPrice" => 1000
                ]
            ]
        ];

        //  Step 3: Call the Zoop v2 init API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'app-id' => '683d502b558b020028dda41d',
            'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
        ])->post('https://live.zoop.one/contract/estamp/v2/init', $payload);

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'eStamp API v2 failed: ' . $response->body());
        }

        $responseData = $response->json();

        if (!isset($responseData['data']['orderId'])) {
            return redirect()->back()->with('error', 'Order ID not received from eStamp API.');
        }

        $orderId = $responseData['data']['orderId'];

        // Step 4: Store in DB
        DB::table('lms_estamp')->insert([
            'leadID' => $leadID,
            'contactID' => $templateData->contactID,
            'requestId' => $orderId, // compatibility: using requestId field
            'fileName' => '',
            'addedBy' => Session::get('userID'),
            'addedOn' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'response' => 'success',
                'message' => 'eStamp initiated successfully. Order ID saved.'
            ]);
        }

        return redirect()->back()->with('success', 'eStamp initiated successfully. Order ID saved.');

    }

    public function fetchEstampDocument(Request $request, $leadID)
    {
        $estamp = DB::table('lms_estamp')->where('leadID', $leadID)->first();

        if (!$estamp || empty($estamp->requestId)) {
            return redirect()->back()->with('error', 'Order ID not found for this lead.');
        }

        $orderId = $estamp->requestId;

        // Call the updated Zoop API
        $response = Http::withHeaders([
            'app-id' => '683d502b558b020028dda41d',
            'api-key' => '6N1E1C6-3J64A92-G01VNJT-D2XDRNX',
        ])->get("https://live.zoop.one/contract/estamp/v2/fetch/request/{$orderId}");

        if (!$response->successful()) {
            return redirect()->back()->with('error', 'Failed to fetch eStamp data from Zoop.');
        }

        $responseData = $response->json();

        // Handle IN_PROGRESS status
        if (!empty($responseData['success']) && isset($responseData['data']['status']) && $responseData['data']['status'] == 'IN_PROGRESS' || $responseData['data']['status'] == 'PENDING') {
            $message = 'E-stamp order is in process. Please try again later.';
            if ($request->ajax()) {
                return response()->json([
                    'response' => 'success',
                    'message' => $message
                ]);
            }
            return redirect()->back()->with('info', $message);
        }

        if (empty($responseData['success']) || empty($responseData['data']['stampCertificateUrl'])) {
            return redirect()->back()->with('info', 'Stamped document not yet available. Please try again later.');
        }

        $documentUrl = $responseData['data']['stampCertificateUrl'];
        $eStampId = $responseData['data']['eStampId'];
        $documentName = "{$leadID}_estamped.pdf";

        // Download the PDF
        $pdfResponse = Http::timeout(30)->get($documentUrl);
        if (!$pdfResponse->successful()) {
            Log::error("Failed to download stamped document", ['url' => $documentUrl]);
            return redirect()->back()->with('error', 'Failed to download stamped document.');
        }

        // Save file temporarily
        $tempFile = tmpfile();
        fwrite($tempFile, $pdfResponse->body());
        $meta = stream_get_meta_data($tempFile);
        $tempFilePath = $meta['uri'];

        $fileObject = new \Illuminate\Http\UploadedFile(
            $tempFilePath,
            $documentName,
            'application/pdf',
            null,
            true
        );

        // Upload the file
        $filePath = $fileObject->storeAs("documentData/{$estamp->contactID}", $documentName, 'public');

        // Update DB with file name and eStampId
        DB::table('lms_estamp')
            ->where('leadID', $leadID)
            ->update([
                'fileName' => $documentName,
                'eStampId' => $eStampId
            ]);

        if ($request->ajax()) {
            return response()->json([
                'response' => 'success',
                'message' => 'Stamped document saved successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Stamped document saved successfully.');
    }

    public function addCollection(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'collectedAmount' => 'required',
            'penaltyAmount' => 'required',
            'collectionMode' => 'required',
            'collectionUtrNo' => 'required|unique:lms_collection,collectionUtrNo',
            'collectionDate' => 'required',
            'waveOff' => 'required',
            'settlementAmount' => 'required',
            'collectionSource' => 'required',
            'status' => 'required',
            'remark' => 'required',
        ]);

        if ($validator->passes()) {
            // Fetch loan and approval details
            $loanDetails = DB::table('lms_loan')->where('leadID', $request->leadID)->first();
            $approvalDetails = DB::table('lms_approval')->where('leadID', $request->leadID)->first();

            $loanAmtApproved = $approvalDetails->balLoanAmtApproved;
            $roi = $approvalDetails->roi;
            $createdDateTime = new DateTime($approvalDetails->createdDate);
            $currentDateTime = new DateTime(date('Y-m-d'));

            // Calculate the difference
            $interval = $createdDateTime->diff($currentDateTime);

            $tenure = $interval->days;

            $percentInterest = $loanAmtApproved * ($roi / 100); // Calculate roi % of the loan amount
            $totalInterest = $percentInterest * $tenure;

            $balanceAmount = $loanAmtApproved - round($totalInterest);


            // Prepare collection data for insertion
            $collectionData = [
                'collectionID' => randomNo(100000, 999999),
                'leadID' => $request->leadID,
                'contactID' => $request->contactID,
                'loanNo' => $loanDetails->loanNo,
                'collectedAmount' => $request->collectedAmount,
                'penaltyAmount' => $request->penaltyAmount,
                'interestAmount' => round($totalInterest),
                'collectedMode' => $request->collectionMode,
                'collectedDate' => date('Y-m-d h:i:s a', strtotime($request->collectionDate . ' ' . now()->toTimeString())),
                'collectionUtrNo' => $request->collectionUtrNo,
                'collectionSource' => $request->collectionSource,
                'discountAmount' => $request->waveOff,
                'settlementAmount' => $request->settlementAmount,
                'remark' => $request->remark,
                'status' => $request->status,
                'ip' => $request->ip(),
                'addedOn' => dt(), // Assuming dt() returns the current datetime
                'addedBy' => Session::get('userID')
            ];

            // Insert collection data and update lead status

            DB::table('lms_collection')->insert($collectionData);
            DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => $request->status]);
            DB::table('lms_approval')->where('leadID', $request->leadID)->update(['balLoanAmtApproved' => $balanceAmount]);
            if ($request->status == 'Closed' || $request->status == 'Payday Preclose') {
                DB::table('lms_contact')->where('contactID', $request->contactID)->update(['redFlagApproved' => 0]);
            }

            actLogs('Profile', 'collection added', $collectionData);
            actLogs('Profile', 'approval status updated ', $request->leadID);
            actLogs('Profile', 'leads status update ', $request->leadID);

            return response()->json(['response' => 'success', 'message' => 'Collection added successfully.']);
        } else {
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }

    public function updateCollection(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'collectedAmount' => 'required',
            'penaltyAmount' => 'required',
            'collectionMode' => 'required',
            'collectionUtrNo' => 'required',
            'collectionDate' => 'required',
            'waveOff' => 'required',
            'settlementAmount' => 'required',
            'collectionSource' => 'required',
            'status' => 'required',
            'remark' => 'required',
            'collectionID' => 'required',
        ]);

        $loanDetails = DB::table('lms_loan')->where('leadID', $request->leadID)->first();
        $approvalDetails = DB::table('lms_approval')->where('leadID', $request->leadID)->first();

        $loanAmtApproved = $approvalDetails->balLoanAmtApproved;
        $roi = $approvalDetails->roi;
        $createdDateTime = new DateTime($approvalDetails->createdDate);
        $currentDateTime = new DateTime(date('Y-m-d'));

        // Calculate the difference
        $interval = $createdDateTime->diff($currentDateTime);

        $tenure = $interval->days;

        $percentInterest = $loanAmtApproved * ($roi / 100); // Calculate roi % of the loan amount
        $totalInterest = $percentInterest * $tenure;

        $balanceAmount = $loanAmtApproved - round($totalInterest);

        if ($validator->passes()) {
            // Prepare collection data for update
            $collectionData = [
                'collectedAmount' => $request->collectedAmount,
                'penaltyAmount' => $request->penaltyAmount,
                'interestAmount' => round($totalInterest),
                'collectedMode' => $request->collectionMode,
                'collectedDate' => date('Y-m-d', strtotime($request->collectionDate)),
                'collectionUtrNo' => $request->collectionUtrNo,
                'collectionSource' => $request->collectionSource,
                'discountAmount' => $request->waveOff,
                'settlementAmount' => $request->settlementAmount,
                'remark' => $request->remark,
                'status' => $request->status,
                'updatedOn' => dt(), // Assuming dt() returns the current datetime
            ];

            // Update collection details and lead status

            DB::table('lms_collection')->where('collectionID', $request->collectionID)->update($collectionData);
            DB::table('lms_leads')->where('leadID', $request->leadID)->update(['status' => $request->status]);
            DB::table('lms_approval')->where('leadID', $request->leadID)->update(['balLoanAmtApproved' => $balanceAmount]);
            if ($request->status == 'Closed' || $request->status == 'Payday Preclose') {
                DB::table('lms_contact')->where('contactID', $request->contactID)->update(['redFlagApproved' => 0]);
            }

            $collectionData['leadID'] = $request->leadID;
            actLogs('Profile', 'collection update', $collectionData);
            actLogs('Profile', 'approval status updated ', $request->leadID);
            actLogs('Profile', 'leads status update ', $request->leadID);

            return response()->json(['response' => 'success', 'message' => 'Collection updated successfully.']);
        } else {
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }

    public function editProfileCollection(Request $request)
    {
        // Fetch collection details by ID
        $collection = DB::table('lms_collection')->where('id', $request->id)->first();

        if ($collection) {
            // Return JSON response with collection details
            return response()->json(['success' => true, 'data' => $collection]);
        } else {
            // Return JSON response indicating failure (collection not found)
            return response()->json(['success' => false, 'message' => 'Collection not found']);
        }
    }



    public function addCommunicationMail(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'communicationType' => 'required',
            'operation' => 'required',
        ]);

        if ($validator->passes()) {
            // Prepare communication data for insertion
            $communicationData = [
                'leadID' => $request->leadID,
                'communicationType' => $request->communicationType,
                'operation' => $request->operation,
                'ip' => $request->ip(),
                'addedOn' => dt(), // Assuming dt() returns the current datetime
                'addedBy' => Session::get('userID')
            ];

            // Insert communication data into the database
            actLogs('Profile', 'communication added', $communicationData);
            DB::table('lms_communication')->insert($communicationData);
            return response()->json(['response' => 'success', 'message' => 'Communication added successfully.']);
        } else {
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }

    public function addCustomerRemarks(Request $request)
    {
        // Validate request based on the presence of an ID
        $rules = [
            'loanNo' => 'required',
            'legalNotice' => 'required',
            'commitmentDate' => 'required',
            'remark' => 'required',
        ];

        if (!empty($request->id)) {
            $rules = [
                'documentsType' => 'required',
                'documentsStatus' => 'required',
                'oldDocument' => 'required',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            // Handle file upload if exists
            if ($request->hasFile('remarksFile')) {
                $file = $request->file('remarksFile');
                $documentName = $file->getClientOriginalName();
                $file->storeAs('public/documentData/', $documentName);
                $documents = $documentName;
            } else {
                $documents = $request->oldDocument ?? null;
            }

            // Prepare documents data for insertion or update
            $documentsData = [
                'leadID' => $request->leadID,
                'contactID' => $request->contactID,
                'loanNo' => $request->loanNo,
                'remarksFile' => $documents,
                'legalNotice' => $request->legalNotice,
                'commitmentDate' => $request->commitmentDate,
                'remark' => $request->remark,
                'addedOn' => dt(),
                'addedBy' => Session::get('userID')
            ];

            // Check if we are updating or inserting
            if ($request->id) {
                // Update existing record
                unset($documentsData['addedOn']);
                $documentsData['updatedOn'] = dt();
                actLogs('Profile', 'customer remaks update', $documentsData);
                DB::table('lms_customer_remarks')->where('id', $request->id)->update($documentsData);
            } else {
                // Insert new record
                actLogs('Profile', 'customer remaks added', $documentsData);
                DB::table('lms_customer_remarks')->insert($documentsData);
            }
            return response()->json(['response' => 'success', 'message' => 'Customer remarks added successfully.']);
        } else {
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }
    }



    public function getPdPerson(Request $request)
    {
        $users = DB::table('lms_users_details')
            ->select('users.displayName', 'users.userID', 'lms_users_details.branch')
            ->leftJoin('users', 'lms_users_details.userID', '=', 'users.userID')
            ->where('users.role', 'Recovery Executive')
            ->where('users.status', 1)
            ->get();

        // Filter in PHP
        $filtered = $users->filter(function ($user) use ($request) {
            $branches = explode(',', $user->branch);
            return in_array($request->branchID, $branches);
        })->values();

        if ($filtered->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No PD persons found',
                'pdPersons' => []
            ], 200);
        }

            return response()->json([
                'success' => true,
                'pdPersons' => $filtered
            ], 200);

       
    }


  public function calculateAPR($principalAmount, $emi, $roi, $fees, $tenure, $disbursedAmt){
    // Round inputs explicitly (optional)
    $emi = round($emi);
    $fees = round($fees);
    $disbursedAmt = round($disbursedAmt);

    $initialOutflow = round($disbursedAmt + $fees);

    $cashFlows = [-$initialOutflow];

    for ($i = 1; $i <= $tenure; $i++) {
        $cashFlows[] = $emi; // assuming EMI already rounded
    }

    $guess = 0.2;
    $tolerance = 1e-8;
    $maxIter = 1000;
    $rate = $guess;

    for ($iter = 0; $iter < $maxIter; $iter++) {
        $npv = 0.0;
        $derivative = 0.0;

        foreach ($cashFlows as $period => $cashFlow) {
            $npv += $cashFlow / pow(1 + $rate, $period);
            if ($period > 0) {
                $derivative -= $period * $cashFlow / pow(1 + $rate, $period + 1);
            }
        }

        if (abs($derivative) < $tolerance) {
            break;
        }

        $newRate = $rate - ($npv / $derivative);

        if (abs($newRate - $rate) < $tolerance) {
            $rate = $newRate;
            break;
        }

        $rate = $newRate;
    }

    if ($rate <= -1) {
        return null;
    }

    $apr = pow(1 + $rate, 12) - 1;

    return round($apr * 100);  // APR rounded to nearest integer %
}


}
