<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\MailSender;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class MailController extends Controller
{

    public function mailTest()
    {
        $template = 'emailTemplate.sendStatusMail';
        $subject = 'Hello';
        $data = compact('template', 'subject');
        Mail::to('confirmation@cashpey.com')->send(new MailSender($data));
        return response()->json(['message' => 'Email sent successfully']);
    }

    public function sendStatus(Request $request)
    {
        $leadID = $request->leadID;

        $templateData = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->select('lms_contact.name','lms_contact.mobile','lms_contact.gender', 'lms_contact.email', 'lms_leads.leadID', 'lms_leads.cmID')
            ->where(['lms_leads.leadID' => $leadID])
            ->first();

        $docEmailFetch = DB::table('lms_users_details')
            ->where('userID', $templateData->cmID)
            ->pluck('documentEmail')
            ->first();

        if (!empty($docEmailFetch)) {
            $fromEmailDB = $docEmailFetch;
        } else {
            $fromEmailDB = 'info@cashpey.com';
        }

        $fromEmail = $fromEmailDB;
        $template = 'emailTemplate.sendStatusMail';
        $companyName = cmp()->companyName;
        $subject = $companyName . ' - Documents Required for Sanctioning of Your Loan - '.$leadID;
        $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
        $receiversEmail = array($templateData->email);
        $ccEmails = array('confirmation@cashpey.com');

        if (optionalModules('Email Process')) {

            $mailData = $templateData;
            $emailContent = View::make('emailTemplate.sendStatusMail', compact('mailData', 'companyName'))->render();
            $fromEmailAPI = 'info@cashpey.com';
            $payload = [
                'from' => [
                    'email' => $fromEmailAPI,
                    'name' => $companyName
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'html',
                        'value' => $emailContent
                    ]
                ],
                'personalizations' => [
                    [
                        'to' => [
                            [
                                'email' => $mailData->email,
                                'name' => $mailData->name
                            ]
                        ],
                        'cc' => collect($ccEmails)->map(function ($email) {
                            return [
                                'email' => $email,
                                'name' => 'CC Recipient'
                            ];
                        })->toArray()
                    ]
                ],
                'reply_to' => $fromEmail,
                'settings' => [
                    'open_track' => true,
                    'click_track' => true,
                    'unsubscribe_track' => true
                ]
            ];

            try {
                $response = Http::withHeaders([
                    'api_key' => 'e0845a0c57a6142783b7295590c66ef3',
                    'Content-Type' => 'application/json',
                ])->post('https://emailapi.netcorecloud.net/v5.1/mail/send', $payload);

                if ($response->successful()) {
                    $communicationData = [
                        'leadID' => $leadID,
                        'communicationType' => 'Email',
                        'operation' => 'Documents Needed for Sanctioning of Your Loan Mail 1 Sent',
                        'ip' => $request->ip(),
                        'addedOn' => now(),
                        'addedBy' => Session::get('userID')
                    ];

                    actLogs('Profile', 'communication added', $communicationData);
                    DB::table('lms_communication')->insert($communicationData);

                    $message = "Dear {$templateData->name}, please upload the required loan documents via our secure portal . Check your email for details. – Team Cashpey";
                    $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007609590858262473');   
                    return redirect()->back()->with('success', 'Status email sent successfully.');
                } else {
                    Log::error('Email API Error: ' . $response->body());
                    return redirect()->back()->with('error', 'Failed to send the status email. Please try again.');
                }
            } catch (\Exception $e) {
                Log::error('Exception: ' . $e->getMessage());
                return redirect()->back()->with('error', 'An error occurred while sending the email: ' . $e->getMessage());
            }

        } else {
            try {
                Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));

                $communicationData = [
                    'leadID' => $leadID,
                    'communicationType' => 'Email',
                    'operation' => 'Documents Needed for Sanctioning of Your Loan Mail 1 Sent',
                    'ip' => $request->ip(),
                    'addedOn' => dt(),
                    'addedBy' => Session::get('userID')
                ];

                actLogs('Profile', 'communication added', $communicationData);
                DB::table('lms_communication')->insert($communicationData);

                $message = "Dear {$templateData->name}, please upload the required loan documents via our secure portal . Check your email for details. – Team Cashpey";
                $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007609590858262473');    
                return redirect()->back()->with('success', 'Status email sent successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Status email failed to send.');
            }
        }

        

    }


    public function sendStatusDocument(Request $request)
    {

        $leadID = $request->leadID;

        $templateData = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->select('lms_contact.name','lms_contact.mobile','lms_contact.gender', 'lms_contact.email', 'lms_leads.leadID', 'lms_leads.cmID')
            ->where(['lms_leads.leadID' => $leadID])
            ->first();

        $docEmailFetch = DB::table('lms_users_details')
            ->where('userID', $templateData->cmID)
            ->pluck('documentEmail')
            ->first();


        if (!empty($docEmailFetch)) {
            $fromEmailDB = $docEmailFetch;
        } else {
            $fromEmailDB = 'info@cashpey.com';
        }

        $fromEmail = $fromEmailDB;
        $template = 'emailTemplate.sendStatusEmailDoc';
        $companyName = cmp()->companyName;
        $subject = $companyName . ' - Documents Needed for Sanctioning of Your Loan';
        $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
        $receiversEmail = array($templateData->email);
        $ccEmails = array('confirmation@cashpey.com');

        if (optionalModules('Email Process')) {
            $mailData = $templateData;
            $emailContent = View::make('emailTemplate.sendStatusEmailDoc', compact('mailData', 'companyName', 'subject'))->render();
            $fromEmailAPI = 'info@cashpey.com';
            // Prepare the payload for the email API request
            $payload = [
                'from' => [
                    'email' => $fromEmailAPI,
                    'name' => $companyName
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'html',
                        'value' => $emailContent // Using the rendered Blade template content here
                    ]
                ],
                'personalizations' => [
                    [
                        'to' => [
                            [
                                'email' => $templateData->email,
                                'name' => $templateData->name
                            ]
                        ],
                        'cc' => collect($ccEmails)->map(function ($email) {
                            return [
                                'email' => $email,
                                'name' => 'CC Recipient'
                            ];
                        })->toArray()
                    ]
                ],
                'reply_to' => $fromEmail,
                'settings' => [
                    'open_track' => true,
                    'click_track' => true,
                    'unsubscribe_track' => true
                ]
            ];

            try {
                // Send the email via API
                $response = Http::withHeaders([
                    'api_key' => 'e0845a0c57a6142783b7295590c66ef3', // Replace with your actual API key
                    'Content-Type' => 'application/json',
                ])->post('https://emailapi.netcorecloud.net/v5.1/mail/send', $payload);

                // Check if the response is successful
                if ($response->successful()) {
                    // Communication logging (if needed)
                    $communicationData = [
                        'leadID' => $leadID,
                        'communicationType' => 'Email',
                        'operation' => 'Documents Needed for Sanctioning of Your Loan Mail 2 Sent',
                        'ip' => $request->ip(),
                        'addedOn' => now(), // Use Laravel's helper to get current datetime
                        'addedBy' => Session::get('userID')
                    ];

                    // Insert communication data into the database
                    actLogs('Profile', 'communication added', $communicationData);
                    DB::table('lms_communication')->insert($communicationData);

                    // Return a success response
                    return redirect()->back()->with('success', 'Status document email sent successfully.');
                } else {
                    // Log the error from the response body
                    Log::error('Email API Error: ' . $response->body());
                    return redirect()->back()->with('error', 'Failed to send the status document email. Please try again.');
                }
            } catch (\Exception $e) {
                // Catch any exceptions (e.g., network issues, API errors)
                Log::error('Exception: ' . $e->getMessage());
                return redirect()->back()->with('error', 'An error occurred while sending the email: ' . $e->getMessage());
            }
        } else {

            try {

                // Send the email
                Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));

                $communicationData = [
                    'leadID' => $leadID,
                    'communicationType' => 'Email',
                    'operation' => 'Documents Needed for Sanctioning of Your Loan Mail 2 Sent',
                    'ip' => $request->ip(),
                    'addedOn' => dt(), // Assuming dt() returns the current datetime
                    'addedBy' => Session::get('userID')
                ];

                // Insert communication data into the database
                actLogs('Profile', 'communication added', $communicationData);
                DB::table('lms_communication')->insert($communicationData);
                // Return a success response

                return redirect()->back()->with('success', 'Status document email sent successfully.');
            } catch (\Exception $e) {
                // Return an error response
                return redirect()->back()->with('error', 'Status document email failed to send.');
            }
        }

        $message = "Dear {$templateData->name}, please upload the required loan documents via our secure portal . Check your email for details. – Team Cashpey";
        $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007609590858262473');
    }


    public function sanctionApproval(Request $request)
    {
        $leadID = $request->leadID;

        // Fetch the template data
         $templateData = DB::table('lms_approval')
            ->join('lms_contact', 'lms_approval.contactID', '=', 'lms_contact.contactID')
            ->leftJoin('lms_address', function ($join) {
                        $join->on('lms_approval.contactID', '=', 'lms_approval.contactID')
                             ->where('lms_address.addressType', '=', 'current')
                             ->orderBy('lms_address.id','desc');
            })
            ->select('lms_approval.leadID', 'lms_approval.loanAmtApproved', 'lms_approval.officialEmail', 
                     'lms_approval.adminFee', 'lms_approval.roi', 'lms_approval.tenure', 
                     'lms_approval.GstOfAdminFee', 'lms_approval.createdDate','lms_approval.emi','lms_approval.repayDay','lms_approval.paymentStartDate','lms_approval.contactID', 
                     'lms_contact.name','lms_contact.mobile','lms_contact.email','lms_contact.gender','lms_address.city','lms_address.state','lms_address.pincode')
            ->where(['lms_approval.status' => 'Pending For Approval', 'lms_approval.leadID' => $leadID])
            ->orderBy('lms_approval.id','desc')
            ->first();  
            
        // Prepare mail data
        $fromEmail = 'sanction@cashpey.com';
        $template = 'emailTemplate.approvedEmail';
        $companyName = cmp()->companyName;
        $subject = $companyName . ' - Pre-Approval - '.$leadID;
        $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
        $receiversEmail = array($templateData->email, $templateData->officialEmail);
        $ccEmails = array('confirmation@cashpey.com');

        if (optionalModules('Email Process')) {
            $mailData = $templateData;
            $emailContent = View::make($template, compact('mailData', 'companyName', 'fromEmail', 'subject'))->render();
            $fromEmailAPI = 'sanction@cashpey.com';
            // Prepare the payload for the email API request
            $payload = [
                'from' => [
                    'email' => $fromEmailAPI,
                    'name' => $companyName
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'html',
                        'value' => $emailContent // Rendered Blade content
                    ]
                ],
                'personalizations' => [
                    [
                        'to' => collect($receiversEmail)
                            ->filter(function ($email) {
                                return filter_var($email, FILTER_VALIDATE_EMAIL); // Only keep valid emails
                            })
                            ->map(function ($email) use ($templateData) {
                                return [
                                    'email' => $email,
                                    'name' => $templateData->name
                                ];
                            })
                            ->values() // Resets array keys
                            ->toArray(),
                        'cc' => collect($ccEmails)->map(function ($email) {
                            return [
                                'email' => $email,
                                'name' => 'CC Recipient'
                            ];
                        })->toArray()
                    ]
                ],
                'reply_to' => $fromEmail,
                'settings' => [
                    'open_track' => true,
                    'click_track' => true,
                    'unsubscribe_track' => true
                ]
            ];

            try {
                // Send the email via API
                $response = Http::withHeaders([
                    'api_key' => 'e0845a0c57a6142783b7295590c66ef3', // Replace with your actual API key
                    'Content-Type' => 'application/json',
                ])->post('https://emailapi.netcorecloud.net/v5.1/mail/send', $payload);

                // Check if the response is successful
                if ($response->successful()) {
                    // Communication logging (if needed)
                    $communicationData = [
                        'leadID' => $templateData->leadID,
                        'communicationType' => 'Email',
                        'operation' => 'Sanction Mail Sent',
                        'ip' => $request->ip(),
                        'addedOn' => now(), // Current datetime
                        'addedBy' => Session::get('userID')
                    ];

                    // Insert communication data into the database
                    actLogs('Profile', 'communication added', $communicationData);
                    DB::table('lms_communication')->insert($communicationData);

                    // Return success response

                    $message = "Dear {$templateData->name}, your loan (Ref: {$templateData->leadID}) is pre-approved, subject to verification. Check your email for details. – Team Cashpey";
                    $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007233899311202177');
                    
                    return response()->json(['response' => 'success', 'message' => 'Pre approval email sent successfully!']);
                } else {
                    // Log the error from the response body
                    Log::error('Email API Error: ' . $response->body());
                    return response()->json(['response' => 'failed', 'message' => 'Failed to send the email.']);
                }
            } catch (\Exception $e) {
                // Catch any exceptions (e.g., network issues, API errors)
                Log::error('Exception: ' . $e->getMessage());
                return response()->json(['response' => 'failed', 'message' => 'Failed to send the email.']);
            }

        } else {

            try {
                // Send the email
                Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));

                $communicationData = [
                    'leadID' => $templateData->leadID,
                    'communicationType' => 'Email',
                    'operation' => 'Sanction Mail Sent',
                    'ip' => $request->ip(),
                    'addedOn' => dt(), // Assuming dt() returns the current datetime
                    'addedBy' => Session::get('userID')
                ];

                // Insert communication data into the database
                actLogs('Profile', 'communication added', $communicationData);
                DB::table('lms_communication')->insert($communicationData);

                $message = "Dear {$templateData->name}, your loan (Ref: {$templateData->leadID}) is pre-approved, subject to verification. Check your email for details. – Team Cashpey";
                $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007233899311202177');
               
                // Return a success response
                return response()->json(['response' => 'success', 'message' => 'Pre approval email sent successfully!']);
            } catch (\Exception $e) {
                // Return an error response
                return response()->json(['response' => 'failed', 'message' => 'Failed to send the email.']);
            }
        }
    }


    public function sanctionRejection(Request $request)
    {

        $leadID = $request->leadID;
        $templateData = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
            ->select(
                'lms_contact.name',
                'lms_contact.email',
                'lms_contact.mobile',
                'lms_contact.gender',
                'lms_leads.leadID',
                'lms_leads.commingLeadsDate',
                'lms_approval.officialEmail'
            )
            ->where(['lms_leads.leadID' => $leadID])
            ->first();


        // Check if officialEmail is NULL and send only to email if it is
        if (is_null($templateData->officialEmail)) {
            // Only send to the primary email if officialEmail is NULL
            $receiversEmail = [$templateData->email];
        } else {
            // Send to both emails (primary and official)
            $receiversEmail = [$templateData->email, $templateData->officialEmail];
        }



        $fromEmail = 'sanction@cashpey.com';
        $template = 'emailTemplate.rejection';
        $companyName = cmp()->companyName;
        $subject = $companyName . ' - Rejection of Application ID - '.$leadID;
        $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
        $ccEmails = array('confirmation@cashpey.com');

        if (optionalModules('Email Process')) {
            $mailData = $templateData;
            $emailContent = View::make($template, compact('mailData', 'companyName'))->render();
            $fromEmailAPI = 'sanction@cashpey.com';
            // Prepare the payload for the email API request
            $payload = [
                'from' => [
                    'email' =>  $fromEmailAPI,
                    'name' => $companyName,
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'html',
                        'value' => $emailContent // Rendered Blade content
                    ]
                ],
                'personalizations' => [
                    [
                      'to' => collect($receiversEmail)
                        ->filter(function ($email) {
                            return filter_var($email, FILTER_VALIDATE_EMAIL); // Only keep valid emails
                        })
                        ->map(function ($email) use ($templateData) {
                            return [
                                'email' => $email,
                                'name' => $templateData->name
                            ];
                        })
                        ->values() // Resets array keys
                        ->toArray(),
                        'cc' => collect($ccEmails)->map(function ($email) {
                            return [
                                'email' => $email,
                                'name' => 'CC Recipient'
                            ];
                        })->toArray()
                    ],
                ],
                'reply_to' => $fromEmail,
                'settings' => [
                    'open_track' => true,
                    'click_track' => true,
                    'unsubscribe_track' => true,
                ]
            ];

            try {
                // Send the email via API
                $response = Http::withHeaders([
                    'api_key' => 'e0845a0c57a6142783b7295590c66ef3', // Replace with your actual API key
                    'Content-Type' => 'application/json',
                ])->post('https://emailapi.netcorecloud.net/v5.1/mail/send', $payload);

                // Check if the response is successful
                if ($response->successful()) {
                    // Log the communication in the database
                    $communicationData = [
                        'leadID' => $leadID,
                        'communicationType' => 'Email',
                        'operation' => 'Sanction Rejected Mail Sent',
                        'ip' => $request->ip(),
                        'addedOn' => now(),
                        'addedBy' => Session::get('userID'),
                    ];

                    // Insert communication data into the database
                    actLogs('Profile', 'communication added', $communicationData);
                    DB::table('lms_communication')->insert($communicationData);

                    $message = "Dear {{$templateData->name}}, your loan application (Ref. {$leadID}) was not approved based on our internal criteria. Thank you for choosing Cashpey.";
                    $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007137248844038124');
                    // Return a success response
                    return response()->json(['response' => 'success', 'message' => 'Sanction rejection email sent successfully!']);
                } else {
                    // Log the error from the response body
                    Log::error('Email API Error: ' . $response->body());
                    return response()->json(['response' => 'failed', 'message' => 'Failed to send the email. Please try again.']);
                }
            } catch (\Exception $e) {
                // Catch any exceptions (e.g., network issues, API errors)
                Log::error('Exception: ' . $e->getMessage());
                return response()->json(['response' => 'failed', 'message' => 'An error occurred while sending the email: ' . $e->getMessage()]);
            }
        } else {
            try {
                Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));

                $communicationData = [
                    'leadID' => $leadID,
                    'communicationType' => 'Email',
                    'operation' => 'Sanction Rejected Mail Sent',
                    'ip' => $request->ip(),
                    'addedOn' => dt(), // Assuming dt() returns the current datetime
                    'addedBy' => Session::get('userID'),
                ];

                // Insert communication data into the database
                actLogs('Profile', 'communication added', $communicationData);
                DB::table('lms_communication')->insert($communicationData);


                $message = "Dear {{$templateData->name}}, your loan application (Ref. {$leadID}) was not approved based on our internal criteria. Thank you for choosing Cashpey.";
                $responseNotification = sendMobileNotification($templateData->mobile, $message, '1007137248844038124');
                return response()->json(['response' => 'success', 'message' => 'Sanction rejection email sent successfully!']);
            } catch (\Exception $e) {
                return response()->json(['response' => 'failed', 'message' => 'Failed to send the email.']);
            }
        }
    }



      public function welcomeLetter(Request $request) {
        $leadID = $request->leadID;
    
        $templateData = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->join('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
            ->join('lms_loan', 'lms_leads.leadID', '=', 'lms_loan.leadID')
            ->leftjoin('lms_esigndockyc', 'lms_approval.leadID', '=', 'lms_esigndockyc.leadID')
            ->select(
                'lms_leads.leadID',
                'lms_approval.loanAmtApproved',
                'lms_esigndockyc.addedOn',
                'lms_approval.tenure',
                'lms_approval.emi',
                'lms_approval.roi',
                'lms_approval.paymentStartDate',
                'lms_contact.name',
                'lms_contact.email',
                'lms_contact.contactID',
                'lms_contact.gender',
                'lms_loan.loanNo',
                'lms_approval.officialEmail'
            )
            ->where('lms_approval.status', 'Approved')
            ->where('lms_esigndockyc.status', 'signed')
            ->where('lms_approval.leadID', $leadID)
            ->orderBy('lms_approval.id', 'desc')
            ->first();
    
        if (!$templateData) {
           return redirect()->back()->with('error', 'Welcome email failed to send. E-nach/E-stamp is pending');
        }
    
        $repaymentScheduleSanction = DB::table('lms_emi_schedule_sanction')
            ->where('leadID', $leadID)
            ->get();
    
        // Get single eStamp document
        $estampDoc = DB::table('lms_estamp')
            ->select('fileName')
            ->where('leadID', $leadID)
            ->orderBy('id', 'desc')
            ->first();
            
        $esignDoc = DB::table('lms_esigndockyc')
            ->select('fileName')
            ->where('leadID', $leadID)
            ->where('status', 'signed')
            ->orderBy('id', 'desc')
            ->first();    
      
        // Generate PDF attachment
        $pdf = $this->generateRepaymentSchedulePDF($templateData, $repaymentScheduleSanction);
        $pdfFileName = 'Repayment_Schedule_' . $templateData->leadID . '.pdf';
    
        // Prepare eStamp attachment (single file)
        $estampAttachment = null;
        if ($estampDoc && $templateData->contactID) {
            // Assuming the eStamp file is stored in storage/app/public
            $filePath = storage_path('app/public/documentData/' . $templateData->contactID . '/' . $esignDoc->fileName);
    
            // Check if the file exists
            if (file_exists($filePath)) {
                // Get file content
                $fileContent = file_get_contents($filePath);
    
                // Generate the public URL for external access
                $filePath = Storage::url('documentData/' . $templateData->contactID . '/' . $esignDoc->fileName);
    
                // Log the public URL for debugging
                Log::info('eStamp file found at: ' . $filePath);  // This is the public URL
                $estampAttachment = [
                    'content' => $fileContent,
                    'name' => $esignDoc->fileName,
                    'mime' => 'application/pdf', // Adjust the mime type as necessary
                ];
            } else {
                Log::error('E-Stamp & E-aggrement file not found at: ' . $filePath);  // Internal path error
            }
        }
    
        // Check if officialEmail is NULL and send only to email if it is
        if (is_null($templateData->officialEmail)) {
            $receiversEmail = [$templateData->email];
        } else {
            $receiversEmail = [$templateData->email, $templateData->officialEmail];
        }
    
        $fromEmail = 'sanction@cashpey.com';
        $template = 'emailTemplate.welcome';
        $companyName = cmp()->companyName;
        $subject = $companyName . ' - Welcome Letter - '.$leadID;
        $mailData = compact('template', 'subject', 'templateData', 'fromEmail', 'repaymentScheduleSanction', 'pdf', 'pdfFileName', 'estampAttachment');
    
        $ccEmails = array('confirmation@cashpey.com');
    
        if (optionalModules('Email Process')) {
            // For API email sending with attachments
            $emailContent = View::make($template, compact('mailData', 'companyName'))->render();
            $fromEmailAPI = 'sanction@cashpey.com';
    
            // Prepare attachments for API
            $attachments = [
                [
                    'content' => base64_encode($pdf),
                    'filename' => $pdfFileName,
                    'type' => 'application/pdf',
                    'disposition' => 'attachment'
                ]
            ];
    
            // Add eStamp document to attachments if exists
            if (!empty($estampAttachment)) {
                $attachments[] = [
                    'content' => base64_encode($estampAttachment['content']),
                    'filename' => $estampAttachment['name'],
                    'type' => $estampAttachment['mime'],
                    'disposition' => 'attachment'
                ];
            }
    
            // Prepare the payload for the email API request
            $payload = [
                'from' => [
                    'email' => $fromEmailAPI,
                    'name' => $companyName,
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'html',
                        'value' => $emailContent
                    ]
                ],
                'attachments' => $attachments,
                'personalizations' => [
                    [
                        'to' => collect($receiversEmail)
                            ->filter(function ($email) {
                                return filter_var($email, FILTER_VALIDATE_EMAIL);
                            })
                            ->map(function ($email) use ($templateData) {
                                return [
                                    'email' => $email,
                                    'name' => $templateData->name
                                ];
                            })
                            ->values()
                            ->toArray(),
                        'cc' => collect($ccEmails)->map(function ($email) {
                            return [
                                'email' => $email,
                                'name' => 'CC Recipient'
                            ];
                        })->toArray()
                    ],
                ],
                'reply_to' => $fromEmail,
                'settings' => [
                    'open_track' => true,
                    'click_track' => true,
                    'unsubscribe_track' => true,
                ]
            ];
    
            try {
                // Send the email via API
                $response = Http::withHeaders([
                    'api_key' => 'e0845a0c57a6142783b7295590c66ef3',
                    'Content-Type' => 'application/json',
                ])->post('https://emailapi.netcorecloud.net/v5.1/mail/send', $payload);
    
                if ($response->successful()) {
                    $communicationData = [
                        'leadID' => $leadID,
                        'communicationType' => 'Email',
                        'operation' => 'Welcome Mail Sent',
                        'ip' => $request->ip(),
                        'addedOn' => now(),
                        'addedBy' => Session::get('userID'),
                    ];
    
                    actLogs('Profile', 'communication added', $communicationData);
                    DB::table('lms_communication')->insert($communicationData);
    
                   return redirect()->back()->with('success', 'Welcome email sent successfully.');
                } else {
                    Log::error('Email API Error: ' . $response->body());
                   return redirect()->back()->with('error', 'Welcome email failed to send.');
                }
            } catch (\Exception $e) {
                Log::error('Exception: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Welcome email failed to send.');
            }
        } else {
            try {
                // Add attachments to the MailSender instance
                $mail = new MailSender($mailData);
                Mail::to($receiversEmail)
                    ->cc($ccEmails)
                    ->send($mail);
    
                $communicationData = [
                    'leadID' => $leadID,
                    'communicationType' => 'Email',
                    'operation' => 'Welcome Mail Sent',
                    'ip' => $request->ip(),
                    'addedOn' => now(),
                    'addedBy' => Session::get('userID'),
                ];
    
                actLogs('Profile', 'communication added', $communicationData);
                DB::table('lms_communication')->insert($communicationData);
                return redirect()->back()->with('success', 'Welcome email sent successfully.');
  
            } catch (\Exception $e) {
                Log::error('Email sending error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Welcome email failed to send.');
            }
        }
    }
    
    private function generateRepaymentSchedulePDF($templateData, $repaymentScheduleSanction)
    {
        $pdf = \PDF::loadView('emailTemplate.repaymentSchedulePdf', [
            'templateData' => $templateData,
            'repaymentScheduleSanction' => $repaymentScheduleSanction
        ]);
        
        return $pdf->output();
    }
    
        
        
    public function addCommunicationMail(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'communicationType' => 'required',
            'operation' => 'required', // The validation rule for operation
        ], [
            'operation.required' => 'Email type is required', // Custom error message for the operation field
        ]);


        if ($validator->passes()) {
            // Prepare communication data for insertion
            $leadID = $request->leadID;
            $companyName = cmp()->companyName;
            $operation = $request->operation;
            if ($operation == 'Tomorrow Reminder') {
                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_loan.loanNo', 'lms_contact.name', 'lms_approval.emi', 'lms_contact.email', 'lms_approval.officialEmail','lms_contact.gender')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.tomorrowPaydayReminder';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - Tomorrow Reminder -'.$leadID;
            } elseif ($operation == 'Special Reminder') {
                // Your action for Special Reminder
                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_loan.disbursalDate', 'lms_loan.repayDate', 'lms_contact.name', 'lms_loan.repayAmount', 'lms_contact.email', 'lms_approval.officialEmail','lms_contact.gender')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.specialReminder';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - Loan Repayment Reminder -'.$leadID;

            } elseif ($operation == 'Loan Repayment Reminder') {
                // Your action for Special Reminder 2
               $templateData = DB::table('lms_loan')
                ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                ->leftJoin('lms_emi_schedule_disbursed', function($join) {
                    $join->on('lms_loan.leadID', '=', 'lms_emi_schedule_disbursed.leadID')
                         ->where('lms_emi_schedule_disbursed.status', '=', 0);
                })
                ->select(
                    'lms_approval.loanAmtApproved', 
                    'lms_loan.disbursalDate', 
                    'lms_loan.loanNo', 
                    'lms_contact.name', 
                    'lms_approval.emi', 
                    'lms_contact.email', 
                    'lms_approval.officialEmail',
                    'lms_contact.gender',
                    'lms_emi_schedule_disbursed.paymentDate'
                )
                ->where('lms_leads.leadID', $leadID)
                ->orderBy('lms_emi_schedule_disbursed.paymentDate', 'asc') // min date pehle aayegi
                ->first();

                $template = 'emailTemplate.specialReminder';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - Loan Repayment Reminder -'.$leadID;
            } elseif ($operation == 'E-Mandate Bounce') {

                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_loan.disbursalDate', 'lms_loan.repayDate', 'lms_contact.name', 'lms_loan.repayAmount', 'lms_contact.email', 'lms_approval.officialEmail','lms_contact.gender')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.eMandateBounce';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - E-Mandate Bounce -'.$leadID;

            } elseif ($operation == 'Benefits of Repaying') {
                // Your action for Benefits of Repaying
            } elseif ($operation == "Don't Cash Payment") {
                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_contact.name', 'lms_contact.email', 'lms_approval.officialEmail')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.dontMakeCashPayment';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . " - Don't Make Cash Payment -".$leadID;

            } elseif ($operation == 'Normal Reminder') {
                // Your action for Normal Reminder
            } elseif ($operation == 'E-Mandate Mail') {
                // Your action for E-Mandate Mail
            } elseif ($operation == 'Repeated Commitments') {
                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_contact.name', 'lms_contact.email', 'lms_approval.officialEmail','lms_contact.gender')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.repeatedCommitments';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - Repeated Commitments -'.$leadID;
            } elseif ($operation == 'Align Visit') {
                // Your action for Align Visit
            } elseif ($operation == 'No Answer') {
                // Your action for No Answer
                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_approval.loanAmtApproved', 'lms_loan.repayDate', 'lms_contact.name', 'lms_contact.email', 'lms_approval.officialEmail','lms_contact.gender')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.noAnswer';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - No Answer -'.$leadID;
            } elseif ($operation == 'Credit Reminder') {
                // Your action for Credit Reminder
              $templateData = DB::table('lms_loan')
                ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Changed to leftJoin
                ->leftJoin('lms_emi_schedule_disbursed', function($join) {
                    $join->on('lms_loan.leadID', '=', 'lms_emi_schedule_disbursed.leadID')
                         ->whereMonth('lms_emi_schedule_disbursed.paymentDate', '=', date('m')) // Current month filter
                         ->whereYear('lms_emi_schedule_disbursed.paymentDate', '=', date('Y')); // Current year filter
                })
                ->select(
                    'lms_approval.loanAmtApproved', 
                    'lms_loan.disbursalDate', 
                    'lms_loan.loanNo', 
                    'lms_contact.name', 
                    'lms_approval.emi', 
                    'lms_contact.email', 
                    'lms_approval.officialEmail',
                    'lms_contact.gender',
                    'lms_emi_schedule_disbursed.paymentDate' // Include the paymentDate from the disbursed table
                )
                ->where(['lms_leads.leadID' => $leadID])
                ->first();
              
                $template = 'emailTemplate.creditReminder';
                $fromEmail = 'collection@cashpey.com';
                $subject = $companyName . ' - Gentle Reminder: Your Upcoming Loan repayment amount -'.$leadID;
            } elseif ($operation == 'Settlement') {
                $templateData = DB::table('lms_loan')
                    ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                    ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                    ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID') // Change to leftJoin
                    ->select('lms_approval.loanAmtApproved', 'lms_loan.loanNo', 'lms_contact.name', 'lms_loan.repayAmount', 'lms_contact.email', 'lms_approval.officialEmail', 'lms_loan.disbursalDate', 'lms_loan.repayDate','lms_contact.gender')
                    ->where(['lms_leads.leadID' => $leadID])
                    ->first();
                $template = 'emailTemplate.settlement';
                $fromEmail = 'info@cashpey.com';
                $subject = $companyName . ' - Settlement -'.$leadID;
            } elseif ($operation == 'NOC') {
                $templateData = DB::table('lms_loan')
                ->leftJoin('lms_contact', 'lms_loan.contactID', '=', 'lms_contact.contactID')
                ->leftJoin('lms_leads', 'lms_loan.leadID', '=', 'lms_leads.leadID')
                ->leftJoin('lms_approval', 'lms_leads.leadID', '=', 'lms_approval.leadID')
                ->leftJoin('lms_emi_schedule_disbursed', function($join) {
                    $join->on('lms_loan.leadID', '=', 'lms_emi_schedule_disbursed.leadID');
                })
                ->select(
                    'lms_approval.loanAmtApproved', 
                    'lms_loan.disbursalDate', 
                    'lms_loan.loanNo', 
                    'lms_contact.name', 
                    'lms_approval.emi', 
                    'lms_contact.email', 
                    'lms_approval.officialEmail',
                    'lms_contact.gender',
                    'lms_emi_schedule_disbursed.paymentDate'
                )
                ->where('lms_leads.leadID', $leadID)
                ->orderByDesc('lms_emi_schedule_disbursed.paymentDate') // bas ye line se order by ho gaya
                ->first(); // sirf top 1 (latest) milega
 
                $template = 'emailTemplate.noc';
                $fromEmail = 'info@cashpey.com';
                $subject = $companyName . ' - NOC -'.$leadID;
            }

            // if (is_null($templateData->officialEmail)) {
            // // Only send to the primary email if officialEmail is NULL
            //     $receiversEmail = [$templateData->email];
            // } else {
            //     // Send to both emails (primary and official)
            //     $receiversEmail = [$templateData->email, $templateData->officialEmail];
            // }

            $receiversEmail = [$templateData->email];

            $fromEmail = $fromEmail;
            $template = $template;
            $mailData = compact('template', 'subject', 'templateData', 'fromEmail');
            $ccEmails = array('confirmation@cashpey.com');

            if (optionalModules('Email Process')) {
                // Render the email content using the Blade template
                $mailData = $templateData;
                $emailContent = View::make($template, compact('mailData', 'companyName', 'subject'))->render();

                if ($operation == 'NOC' || $operation == 'Settlement'){
                    $fromEmailAPI = 'info@cashpey.com';
                    $fromEmail = 'info@cashpey.com';
                } else {
                    $fromEmailAPI = 'collection@sdnc.co.in';
                    $fromEmail = 'collection@sdnc.co.in';
                }

                // Prepare the payload for the external email API request
                $payload = [
                    'from' => [
                        'email' => $fromEmailAPI,
                        'name' => $companyName,
                    ],
                    'subject' => $subject,
                    'content' => [
                        [
                            'type' => 'html',
                            'value' => $emailContent,
                        ]
                    ],
                    'personalizations' => [
                        [
                            'to' => [
                                [
                                    'email' => $templateData->email,
                                    'name' => $templateData->name
                                ]
                            ],
                            'cc' => collect($ccEmails)->map(function ($email) {
                                return [
                                    'email' => $email,
                                    'name' => 'CC Recipient'
                                ];
                            })->toArray()
                        ],
                    ],
                    'reply_to' => $fromEmail,
                    'settings' => [
                        'open_track' => true,
                        'click_track' => true,
                        'unsubscribe_track' => true,
                    ]
                ];

                try {
                    // Send the email via the external API
                    $response = Http::withHeaders([
                        'api_key' => 'e0845a0c57a6142783b7295590c66ef3',
                        'Content-Type' => 'application/json',
                    ])->post('https://emailapi.netcorecloud.net/v5.1/mail/send', $payload);

                    // Check if the response is successful
                    if ($response->successful()) {
                        // Log the communication in the database
                        $communicationData = [
                            'leadID' => $leadID,
                            'communicationType' => 'Email',
                            'operation' => $operation . ' Mail Sent',
                            'ip' => $request->ip(),
                            'addedOn' => now(),
                            'addedBy' => Session::get('userID'),
                        ];

                        // Insert communication data into the database
                        actLogs('Profile', 'communication added', $communicationData);
                        DB::table('lms_communication')->insert($communicationData);

                        return response()->json(['response' => 'success', 'message' => $operation . ' email sent successfully!']);
                    } else {
                        // Log the error from the response body
                        Log::error('Email API Error: ' . $response->body());
                        return response()->json(['response' => 'failed', 'message' => 'Failed to send the email. Please try again.']);
                    }
                } catch (\Exception $e) {
                    // Catch any exceptions (e.g., network issues, API errors)
                    Log::error('Exception: ' . $e->getMessage());
                    return response()->json(['response' => 'failed', 'message' => 'An error occurred while sending the email: ' . $e->getMessage()]);
                }
            } else {

                try {
                    Mail::to($receiversEmail)->cc($ccEmails)->send(new MailSender($mailData));


                    $communicationData = [
                        'leadID' => $leadID,
                        'communicationType' => 'Email',
                        'operation' => $operation . ' Mail Sent',
                        'ip' => $request->ip(),
                        'addedOn' => dt(), // Assuming dt() returns the current datetime
                        'addedBy' => Session::get('userID'),
                    ];

                   
                    actLogs('Profile', 'communication added', $communicationData);
                    DB::table('lms_communication')->insert($communicationData);

                   return response()->json(['response' => 'success', 'message' => $operation . ' email sent successfully!']);
                } catch (\Exception $e) {
                    return response()->json(['response' => 'failed', 'message' => 'Failed to send the email.']);
                }

            }
        } else {
            return response()->json(['response' => 'failed', 'error' => $validator->errors()]);
        }

    }

}
