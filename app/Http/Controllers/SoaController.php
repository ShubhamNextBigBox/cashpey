<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SoaController extends Controller
{
    public function generatePDF(Request $request,$leadID)
    {
         
        
        // Fetch all required data
        $profileData = DB::table('lms_leads')
            ->join('lms_contact', 'lms_leads.contactID', '=', 'lms_contact.contactID')
            ->leftJoin('lms_address', function ($join) {
                    $join->on('lms_leads.leadID', '=', 'lms_address.leadID')
                         ->where('lms_address.addressType', '=', 'current');
            })
            ->select('lms_leads.leadID', 'lms_leads.contactID', 'lms_leads.utmSource', 'lms_leads.addedOn', 
                    'lms_leads.rmID', 'lms_leads.cmID', 'lms_leads.purpose', 'lms_leads.loanRequired', 
                     'lms_address.city', 'lms_address.state', 'lms_address.pincode', 
                    'lms_leads.status', 'lms_leads.customerType', 'lms_leads.commingLeadsDate', 
                    'lms_contact.name', 'lms_contact.gender', 'lms_contact.dob', 'lms_contact.mobile', 
                    'lms_contact.email', 'lms_contact.pancard', 'lms_contact.aadharNo', 'lms_contact.redFlag','lms_address.address',
                    'lms_contact.remarks','lms_contact.redFlagApproved')
            ->where('lms_leads.leadID', $leadID)
            ->orderBy('lms_leads.id', 'desc')
            ->first();

        $reloanCheck = DB::table('lms_loan')
            ->where('contactID', $profileData->contactID)
            ->where('status', 'Disbursed')
            ->count();

        if ($reloanCheck < 2) {
            // Agar 2 se kam Disbursed loans hain
            $previousLoanNo = '--';
        } else {
            // Second-last Disbursed loan ka loanNo uthao
            $previousLoanNo = DB::table('lms_loan')
                ->where('contactID', $profileData->contactID)
                ->where('status', 'Disbursed')
                ->orderBy('id', 'desc')
                ->skip(1) // Skip the most recent
                ->take(1) // Take second most recent
                ->value('loanNo');
        }

        if(!$profileData) {
            return redirect()->route('custom-404');
        }
        
        $approvalData = DB::table('lms_approval')
            ->where('leadID', $leadID) 
            ->orderBy('id', 'desc') 
            ->first(); 
            
        $loanData = DB::table('lms_loan')
            ->select('loanNo','disbursalAmount','accountNo','ifscCode','bank','bankBranch','enachID',
                    'disbursalUtrNo','sheetSendDate','sheetSendTime','status','addedBy','disbursalDate',
                    'disburseTime','disbursedBy','remarks')
            ->where('leadID', $leadID) 
            ->orderBy('id', 'desc') 
            ->first();
            
        $repaymentScheduleDisbursed = DB::table('lms_emi_schedule_disbursed')
            ->where('leadID', $leadID)
            ->get();

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

         $preEmiAmountExists = DB::table('lms_pre_emi_payment')
            ->where('leadID', $leadID)
            ->count();    
        
        // Prepare data for the view
        $data = [
            'profileData' => $profileData,
            'approvalData' => $approvalData,
            'loanData' => $loanData,
            'repaymentScheduleDisbursed' => $repaymentScheduleDisbursed,
            'paidSchedule' => $paidSchedule,
            'collections' => $collections,
            'reloanCheck' => $reloanCheck,
            'previousLoanNo' => $previousLoanNo,
            'preEmiAmountExists' => $preEmiAmountExists,
            'leadID' => $leadID,
            'generationDate' => now()->format('d-M-Y H:i:s')
        ];
        

       return view('soaTemplate.standardSoa', $data);
        // Generate PDF with page numbers
        $pdf = Pdf::loadView('soaTemplate.standardSoa', $data)
            ->setPaper('A4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
    
        return $pdf->stream("loan-statement-{$leadID}.pdf");
    }        
}