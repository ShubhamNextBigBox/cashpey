<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BankStatementController extends Controller
{
    /**
     * Display the bank statement analyser page
     */
    public function index()
    {
        $page_info = pageInfo('Bank Statement analyser', 'Bank Statement analyser');
        $data = compact('page_info');
        return view('bsa.bank-statement')->with($data);
    }


    /**
     * Submit bank statement to Zoop API
     */
    public function submitStatement(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
                'consent' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Get API credentials from environment variable

            $appId = '683d502b558b020028dda41d';
            $apiKey = '6N1E1C6-3J64A92-G01VNJT-D2XDRNX';

            if (!$appId || !$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API credentials not configured'
                ], 500);
            }

            // Prepare multipart form data
            $file = $request->file('file');

            // Create multipart form request to Zoop API
            $response = Http::withHeaders([
                'app-id' => $appId,
                'api-key' => $apiKey,
            ])->attach(
                    'file',
                    file_get_contents($file),
                    $file->getClientOriginalName()
                )->post('https://live.zoop.one/api/v1/in/financial/bankstatementanalyser/advance', [
                        'password' => $request->input('password') ?: '12345',
                        'consent' => 'Y',
                        'consent_text' => 'I hereby declare my consent agreement for fetching my information via ZOOP API'
                    ]);

            // Check response
            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $data['response_message'] ?? 'Failed to submit bank statement'
                ], 400);
            }

            // Return success response
            return response()->json([
                'success' => true,
                'orderId' => $data['result']['order_id'],
                'message' => 'Bank statement submitted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error submitting bank statement: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get analysis results from Zoop API
     */
    public function getResults(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'orderId' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Get API credentials from environment variables
            $appId = '683d502b558b020028dda41d';
            $apiKey = '6N1E1C6-3J64A92-G01VNJT-D2XDRNX';

            if (!$appId || !$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API credentials not configured'
                ], 500);
            }

            // Get order ID from request
            $orderId = $request->input('orderId');

            // Make request to Zoop API
            $response = Http::withHeaders([
                'app-id' => $appId,
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->get("https://live.zoop.one/api/v1/custom/bankstatementanalyser/advance?order_id={$orderId}");

            // Check response
            $data = $response->json();

            if (
                isset($data['response_code']) && $data['response_code'] === '100' &&
                isset($data['result']) && isset($data['result']['excel_report_url'])
            ) {
                return response()->json([
                    'success' => true,
                    'excelReportUrl' => $data['result']['excel_report_url'],
                    'jsonReportUrl' => $data['result']['json_report_url'] ?? null,
                    'data' => $data['result']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Analysis still in progress'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error fetching results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
