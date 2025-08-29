<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\helper\S3Helper;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
   // Method to get authentication token
   public function getToken()
   {
      $email = 'sunny.nandan@nextbigbox.in';
      $secret_key = '8kzBDAd3DpegX0ZHJ^4yIFYS%';

      $response = Http::post('https://app.nextbigbox.co.in/api/verifyToken', [
         'email' => $email,
         'secret_key' => $secret_key,
      ]);

      if ($response->ok()) {
         return $response->json('authorisation.token');
      }

      abort(401, 'Unable to authenticate with the API.');
   }

   // Method to generate request for a workflow task
   public function generateRequest(Request $request)
   {
      $token = $this->getToken();
       
      $response = Http::withToken($token)->post('https://app.nextbigbox.co.in/api/workFlowTask', [
         'workFlowID' => $request->workFlowID ?? 'WI11012025821716',
         'name' => $request->name ?? 'shubham',
         'email' => $request->email ?? 'shubhamnbb2512@gmail.com',
         'phone' => $request->phone ?? 9910124567,
         'linkExpiryDate' => $request->linkExpiryDate ?? '11-01-2025',
      ]);

      $result = $response->json();
      echo "<pre>";
      print_r($result);
      die();
   }

   // Method to get request details for a specific task
   public function RequestDetails(Request $request)
   {
      $token = $this->getToken();

      $response = Http::withToken($token)->get('https://app.nextbigbox.co.in/api/WorkFlowTaskPreview', [
         'workFlowID' => $request->input('workFlowID') ?? 'WI11012025821716',
         'taskID' => $request->input('taskID') ?? 'WORKTASK11012025410042',
      ]);

      $result = $response->json();
      echo "<pre>";
      print_r($result);
      die();
   }

   // Method to download a file from S3
   public function getWorkTaskFile(Request $request)
   {
      $token = $this->getToken();

      $response = Http::withToken($token)->post('https://app.nextbigbox.co.in/api/downloadFileFromS3', [
         'url' => $request->input('url'),
      ]);

      $result = $response->json();
      echo "<pre>";
      print_r($result);
      die();
   }
}
