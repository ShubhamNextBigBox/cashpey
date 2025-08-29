<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnachController extends Controller
{
    public function enachRegister(Request $request, $leadID)
    {
        $result = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->join('lms_approval', 'lms_approval.leadID', '=', 'lms_leads.leadID')
            ->select(
                'lms_leads.leadID',
                'lms_contact.name',
                'lms_contact.email',
                'lms_contact.mobile',
                DB::raw('ROUND(SUM((lms_approval.emi * lms_approval.tenure)) * 2) AS RepayAmount'),
                'lms_approval.createdDate as sanctionDate',
                'lms_approval.paymentStartDate'
            )
            ->where('lms_approval.status', 'Approved')
            ->where('lms_leads.leadID', $leadID)
            ->groupBy(
                'lms_leads.leadID',
                'lms_contact.name',
                'lms_contact.email',
                'lms_contact.mobile',
                'lms_approval.createdDate',
                'lms_approval.paymentStartDate'
            )
            ->orderBy('lms_approval.id','desc')
            ->first();
 
        $consumerID = $leadID . "CSPY" . rand(100, 999);
        $txnId = "CSPYTXN" . rand(1000000000, 9999999999);
        $cusSanctionDate = \Carbon\Carbon::parse($result->sanctionDate)->format('d-m-Y');
        $cusExpiryDate = now()->addYears(4)->format('d-m-Y');

        $tokenNo = "L1076928|$txnId|1||$consumerID|$result->mobile|$result->email|$cusSanctionDate|$cusExpiryDate|$result->RepayAmount|M|MNTH|||||3153522012AHLHUI";
        $token = hash('sha512', $tokenNo);

        // Enhanced logging using Laravel's Log facade
      Log::channel('enachRequest')->info(
            PHP_EOL . 
            "New Request ---------- eNACH Registration Request ------" . PHP_EOL .
            json_encode([
                'leadID' => $leadID,
                'tokenNo' => $tokenNo,
                'hashedToken' => $token,
                'consumerDetails' => [
                    'consumerID' => $consumerID,
                    'mobile' => $result->mobile,
                    'email' => $result->email,
                    'sanctionDate' => $cusSanctionDate,
                    'expiryDate' => $cusExpiryDate
                ],
                'timestamp' => now()->toDateTimeString()
            ], JSON_PRETTY_PRINT) . PHP_EOL .
            "////////------------ Request Ends ------------- ////" . PHP_EOL
        );

        return view('enach.enachRegistration', [
            'token' => $token,
            'txnId' => $txnId,
            'consumerID' => $consumerID,
            'cusMobile' => $result->mobile,
            'cusEmail' => $result->email,
            'cusSanctionDate' => $cusSanctionDate,
            'cusExpiryDate' => $cusExpiryDate,
            'cusRepayAmount' => $result->RepayAmount
        ]);
    }

    public function enachSuccess(Request $request)
    {
        try {
            $dataResponse = $request->input('msg');

            // Log the raw incoming request first

             Log::channel('enach')->info(
                PHP_EOL . "eNACH Full Response Data ------" . PHP_EOL .
                json_encode($dataResponse, JSON_PRETTY_PRINT) . PHP_EOL .
                "////////------------ Response Ends ------------- ////" . PHP_EOL
            );

            if (empty($dataResponse)) {
                throw new \Exception('Empty response received from payment gateway');
            }

            $str_arr = explode("|", $dataResponse);
            
            if (count($str_arr) < 14) {
                throw new \Exception('Invalid response format from payment gateway');
            }

            $statusCode = $str_arr[0] ?? '';
            $status = $str_arr[1] ?? '';
            $txn_err_msg = $str_arr[2] ?? '';
            $txnID = $str_arr[3] ?? '';
            $clnt_txn_ref = $str_arr[5] ?? '';
            $tpsl_txn_time = $str_arr[8] ?? '';
            $mandate_reg_no = $str_arr[13] ?? '';
            $value = $str_arr[7] ?? '';
            
            // Process the response and save to database
            if ($statusCode == '0300') {
                $cusDetails = explode("~", $value);
                $consumerID = str_replace(['itc', ':', '{'], '', $cusDetails[0]);

                if (count($cusDetails) < 24) {
                    throw new \Exception('Invalid customer details format');
                }

                // Prepare data for logging before DB insert
               $logData = [
                    'status' => $status,
                    'statusCode' => $statusCode,
                    'transaction' => [
                        'txnID' => $txnID,
                        'consumerID' => $consumerID,
                        'errorMessage' => $txn_err_msg
                    ],
                    'customer' => [
                        'leadID' => substr(str_replace(['itc', ':', '{'], '', $cusDetails[0]), 0, -7),
                        'accountDetails' => [
                            'UMRN' => str_replace(['mandateData', ':', '{', 'UMRNNumber'], '', $cusDetails[1]),
                            'accountNo' => str_replace(['account_number', ':'], '', $cusDetails[5]),
                            'IFSC' => str_replace(['IFSCCode', ':'], '', $cusDetails[2])
                        ],
                        'personalDetails' => [
                            'name' => str_replace(['accountHolderName', ':'], '', $cusDetails[14]),
                            'pan' => str_replace(['pan', ':'], '', $cusDetails[21])
                        ]
                    ],
                    'dates' => [
                        'transactionTime' => $tpsl_txn_time,
                        'expiryDate' => str_replace(['expiry_date', ':'], '', $cusDetails[6]),
                        'scheduleDate' => str_replace(['schedule_date', ':'], '', $cusDetails[10])
                    ]
                ];

                // Log the structured data with proper formatting
                Log::channel('enachResponse')->info(
                    PHP_EOL . "eNACH Response Data ------" . PHP_EOL .
                    json_encode($logData, JSON_PRETTY_PRINT) . PHP_EOL .
                    "////////------------ Response Ends ------------- ////" . PHP_EOL
                );

                // Perform the DB insert
                DB::table('lms_enach_register')->insert([
                    'status' => $status,
                    'leadID' => $logData['customer']['leadID'],
                    'statusCode' => $statusCode,
                    'txn_err_msg' => $txn_err_msg,
                    'txnID' => $txnID,
                    'consumerID' => $consumerID,
                    'clnt_txn_ref' => $clnt_txn_ref,
                    'tpsl_txn_time' => $tpsl_txn_time,
                    'UMRNNumber' => $logData['customer']['accountDetails']['UMRN'],
                    'mandate_reg_no' => $mandate_reg_no,
                    'ifscCode' => $logData['customer']['accountDetails']['IFSC'],
                    'accountNo' => $logData['customer']['accountDetails']['accountNo'],
                    'amountType' => str_replace(['amount_type', ':'], '', $cusDetails[3]),
                    'enachAmount' => str_replace(['amount', ':'], '', $cusDetails[8]),
                    'cusName' => $logData['customer']['personalDetails']['name'],
                    'cusPan' => $logData['customer']['personalDetails']['pan'],
                    'cusAccountType' => str_replace(['accountType', ':'], '', $cusDetails[15]),
                    'expiryDate' => $logData['dates']['expiryDate'],
                    'scheduleDate' => $logData['dates']['scheduleDate'],
                    'cusPhoneNumber' => str_replace(['phoneNumber', ':'], '', $cusDetails[22]),
                    'cusEmail' => str_replace(['email', ':', '}'], '', explode("{", $cusDetails[23])[1]),
                    'cusMobile' => str_replace(['mob', ':', '}'], '', explode("{", $cusDetails[23])[2]),
                    'dateTime' => now(),
                ]);

                // Log successful DB insertion
                Log::channel('enach')->info('eNACH Data Successfully Saved', [
                    'leadID' => $logData['customer']['leadID'],
                    'UMRN' => $logData['customer']['accountDetails']['UMRN'],
                    'timestamp' => now()->toDateTimeString()
                ]);
            } else {
                // Log non-success status code
                Log::channel('enach')->info('eNACH Response with Non-Success Status', [
                    'statusCode' => $statusCode,
                    'errorMessage' => $txn_err_msg,
                    'transactionID' => $txnID
                ]);
            }

            return view('enach.thankyou', compact('statusCode', 'status', 'txn_err_msg', 'txnID', 'str_arr'));

        } catch (\Exception $e) {
            Log::channel('enachResponse')->error('eNACH Processing Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'requestData' => $request->all(),
                'timestamp' => now()->toDateTimeString()
            ]);
            
            return view('enach.thankyou')->with('error', $e->getMessage());
        }
    }
}