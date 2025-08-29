@php error_reporting(0) @endphp
<!DOCTYPE html>
<html>
   <head>
      <title>Sanction Approval Letter</title>
      <style>
         .ptext{
         margin:0cm;
         font-size:16px;
         font-family:"Calibri",sans-serif;
         text-align:center;
         }
         .ptext2{
         margin:0cm;
         font-size:16px;
         font-family:"Calibri",sans-serif;
         text-align:justify;
         order:none;
         padding:0cm;
         order:none;
         padding:0cm;
         }
      </style>
   </head>
   <body data-new-gr-c-s-loaded="14.980.0">
      <div style="width: 100%;table-layout: fixed;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;">
         <div class="" style="max-width: 100%;margin: 0 auto; padding: 10px;">
            <table width="100%" border="0" align="" cellpadding="0" cellspacing="0">
               <!-- START HEADER/BANNER -->
               <tbody>
                  <!-- Head-section start here -->
                  <tr>
                     <td align="center">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="center" valign="top">
                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                       <tbody>
                                          <tr>
                                             <td height="20"></td>
                                          </tr>
                                          <tr>
                                             <td align="center">
                                                <p style="margin-top:0pt; margin-bottom:0pt;font-size:11pt;">
                                                   <span style="height:0pt; display:block; position:absolute; z-index:-65538;">&nbsp;</span>
                                                </p>
                                                <p>
                                                   <img src="https://app.creditpey.co.in/storage/logo/creditpey-logo.jpeg" width="auto" height="64px;" alt="" style="margin: 0 auto; display: block; text-align:center;">
                                                </p>
                                                <p class="ptext">Register Under RBI <b> RBI B-14.01877 </b></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td height="10"></td>
                                          </tr>
                                          <tr>
                                             <td align="center">
                                                <p class="ptext">A Unit Of Digner Finlease And Investment Private Limited. </p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td height="5"></td>
                                          </tr>
                                          <tr>
                                             <td align="center">
                                                <div style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;border:none;border-bottom:solid windowtext 1.0pt;padding:0cm 0cm 5pt 0cm;'>
                                                   <p class="ptext">{{cmp()->address}}</p>
                                                </div>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td height="10"></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td height="15"></td>
                  </tr>
                  <!-- Head-section end here -->
                  <!-- body start from here -->
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <thead>
                              <tr>
                                 <th align="center">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'><br></p>
                                    <p class="ptext"><strong><span style="color:#595959;">Loan Sanction Letter&nbsp;</span></strong></p>
                                    <p class="ptext"><strong><span style="color:#595959;">&nbsp;</span></strong></p>
                                 </th>
                              </tr>
                              <!-- <tr>
                                 <td height="10"></td>
                                 </tr> -->
                           </thead>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="left">
                                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;text-align: left;'><strong><span style="color:#595959;"> Dear {{ ucwords($mailData->name) }}, </span></strong></p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="left">
                                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><strong><span style="color:#595959;">&nbsp;</span></strong></p>
                                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">Thank you for your Loan Application made through {{cmp()->domain}}</span></p>
                                    <p style='margin:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">&nbsp;</span></p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">1. We are pleased to sanction you a loan as per the enclosed terms and details.</span></p>
                                    </span></p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <td height="20"></td>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="center">
                                    <p class="ptext"><strong><span style="color:#595959;">Schedule of Loan Sanction Terms&nbsp;</span></strong></p>
                                    <br>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <td height="20"></td>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="center">
                                    <table style="width:467.55pt;border-collapse:collapse;border:none;">
                                       <tbody>
                                          @php
                                          $loanAmtApproved   = $mailData->loanAmtApproved;
                                          $adminFees         = $mailData->adminFee;
                                          $roi               = $mailData->roi;
                                          $tenure            = $mailData->tenure;
                                          $GstOfAdminFee     = $adminFees*$mailData->GstOfAdminFee/100;
                                          $totalAmountDeducted = $adminFees+$GstOfAdminFee;
                                          $TotaldisburedAmount= $loanAmtApproved-$totalAmountDeducted;
                                          $intrest=$loanAmtApproved*$roi/100*365; // 365 days added
                                          $apr=$adminFees+$intrest;
                                          $finalApr=$apr/$loanAmtApproved*100;
                                          @endphp
                                          <tr>
                                             <td style="width: 28.1pt;border: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><strong><span style="color:#595959;">SN</span></strong></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: 1pt solid windowtext;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-image: initial;border-left: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><strong><span style="color:#595959;">ITEM</span></strong></p>
                                             </td>
                                             <td style="width: 9cm;border-top: 1pt solid windowtext;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-image: initial;border-left: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><strong><span style="color:#595959;">Details</span></strong></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">1</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Application ID</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:#4472C4;">&nbsp;{{$mailData->leadID}}</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">2</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Sanction Date</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{df($mailData->createdDate)}}</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">3</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Principal Loan Amount</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{nf($mailData->loanAmtApproved)}}</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">4</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Rate of Interest&nbsp;</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{$mailData->roi}} % per day</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">5</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Processing Fees &nbsp;</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{nf($mailData->adminFee)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">6</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">GST Fee &nbsp;</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{nf($GstOfAdminFee)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">7</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Amount to be Disbursed&nbsp;</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{nf($TotaldisburedAmount)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">8</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Due Date&nbsp;</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;{{df($mailData->repayDate)}}</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">9</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Repayment Amount</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#4472C4;">&nbsp;
                                                   {{nf($mailData->loanAmtApproved+($mailData->loanAmtApproved*$mailData->roi*$mailData->tenure/100))}}/-</span>
                                                </p>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td height="20"></td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="">
                                    <p class="ptext"><strong><span style="color:#595959;">&nbsp;</span></strong></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">2. Please note that this sanction letter is issued in response to your loan application and based on the information you have provided in your application. </span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">3. We don’t encourage you to take a loan paying high interest if you are not confident that you would be able to repay on time.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">4. You are under no obligation to accept the sanctioned loan. </span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">5. Please think carefully and understand what this offer means to you and if you are confident you will be able to repay this loan.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">6. If you are comfortable then reply to this mail as a mark of acceptance of the loan offer and its terms and conditions. </span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">7. If you do not accept the sanction letter and the enclosed agreement your loan application will be considered withdrawn and you will not be liable to pay any processing fees.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">8. Penal interest as applicable will be charged. </span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">9. E-mandate/cheque bounce charges of Rs.1000.00/- applicable for each bouncing.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">10. The loan will be disbursed to your designated bank account after confirmation of your acceptance of this sanction letter.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">11. You will be required to approve an e-mandate for automatic payment of your dues.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;color:red;'>
                                       <span style="color:#595959;">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:red;">12. The e-mandate and/or e-nach will be activated on your due date and you are expected to honour the mandate without fail. If you don’t pay your due amount on time, <br>   the Lender has an absolute right to hit the e-mandate and/or e-nach multiple times to the bank accounts of the Borrower without any exception till the time the Borrower <br>   pays  the complete repayment along with penalties to the Lender.</span></p> </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">13. All disputes under Delhi jurisdiction</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">14. That the cheque given (if given) by the Borrower to the Lender shall be destroyed by the Lender upon written communication on email to be sent to the Lender by the Borrower. From the receipt of written communication, the cheque will be deemed to be destroyed by the Lender after the elapse of 45 days from the date of written communication, without any exceptions. Further, if any amount is pending for payment from the Borrower to the Lender then the Lender reserves the right to preserve the cheque given for subsequent presentation.</span></p>
                                    </span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">15. Please reply to this mail as acceptance of the terms and conditions of the loan.</span></p>
                                    </span></p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="center">
                                    <p> <span style="">  a.  Date: <b>  <?=date("jS F Y")?>, </b>  </span>     Name of the regulated entity: <b> Digner Finlease And Investment Private Limited., </b> <span style=""> Applicant Name: <b>  <?=$name[0]['name']?> </b></span></p>
                                    <br>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="center">
                                    <table style="width:467.55pt;border-collapse:collapse;border:none;">
                                       <tbody style="white-space: nowrap;">
                                          <tr>
                                             <td style="width: 28.1pt;border: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><strong><span style="color:#595959;">SN</span></strong></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: 1pt solid windowtext;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-image: initial;border-left: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><strong><span style="color:#595959;">Parameter</span></strong></p>
                                             </td>
                                             <td style="width: 9cm;border-top: 1pt solid windowtext;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-image: initial;border-left: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><strong><span style="color:#595959;">Details (illustrative) </span></strong></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(i)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Loan Amount (amount to be disbursed/to be disbursed to the borrower) (in Rupees)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($mailData->loanAmtApproved)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(ii)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Total interest charges during the entire tenure of the loan (in Rupees) @ {{$mailData->roi}}% per day for 30 days
                                                   </span>
                                                </p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($mailData->loanAmtApproved*$roi/100*$tenure)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(iii)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Other up-front charges, if any </span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; Nil </span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(a)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Processing fees, if any (in Rupees)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($adminFees)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(b)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Insurance charges, if any (in Rupees)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; Nil</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(c)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Others (if any) (in Rupees) (GST applicability)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($GstOfAdminFee)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(iv)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Net Disbursed Amount</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($TotaldisburedAmount)}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(v)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Total Amount to be paid by the borrower (sum of (i), (ii) and (iii)) (in Rupees)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($mailData->loanAmtApproved+($mailData->loanAmtApproved*$mailData->roi*$mailData->tenure/100))}}
                                                   /-</span>
                                                </p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(vi)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Annual Percentage Rate- Effective annualized interest rate <br>  (in percentage) (computed on net disbursed amount using IRR approach and reducing balance method).</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span>&nbsp;{{$finalApr}}%</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(vii)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Tenure of the Loan (in months/days)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{$tenure}} days</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(viii)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"> Repayment frequency of the borrower </span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; 1</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(ix)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">Number of Instalments of repayment (in Rupees)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; 1</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(x)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"> Amount of each instalment of repayment (in Rupees)</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;{{nf($mailData->loanAmtApproved+($mailData->loanAmtApproved*$mailData->roi*$mailData->tenure/100))}}/-</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td  colspan="3"style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"><b>  Details about Contingent Charges</b></span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(xi)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"> Rate of annualized penal charges in case of delayed payments (if any).</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; 1.25 % per day</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td  colspan="3"style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"><b>  Other disclosures</b></span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(xii)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"> Cooling off/look-up period during which borrower shall not be charged any penalty on prepayment of loan.</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; Nil</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(xiii)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"> Details of LSP acting as recovery agent and authorized to approach the borrower.</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp; Nil</span></p>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="width: 28.1pt;border-right: 1pt solid windowtext;border-bottom: 1pt solid windowtext;border-left: 1pt solid windowtext;border-image: initial;border-top: none;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;">(xiv)</span></p>
                                             </td>
                                             <td style="width: 184.3pt;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="color:#595959;"> Name, Designation, address and phone number of nodal grievance redressal officer designated specifically <br>    to deal with Fintech/digital lending related complaints/issues</span></p>
                                             </td>
                                             <td style="width: 9cm;border-top: none;border-left: none;border-bottom: 1pt solid windowtext;border-right: 1pt solid windowtext;padding: 0cm 5.4pt;vertical-align: top;">
                                                <p class="ptext2"><span style="font-size:15px;color:black;">&nbsp;  Ms. Madhusree Garai <br>    Grievance Redressal Officer <br>    (Mob:  +91-97736 01641)</span></p>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <td height="20"></td>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">15. 3 Days Cooling Period.</span></p>
                                    </span></p><br>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">16. That the Company and the Borrower has mutually agreed to execute the Sanction Mail in the prescribed language, i.e. English. In case the Borrower requires the Sanction Mail in their respective vernacular language for better understanding of the Sanction Mail, the same can be requested by the Borrower to the Company and the same shall be provided by the Company to the Borrower.</span></p>
                                    </span></p><br>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="center">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:14px;font-family:"Calibri",sans-serif;'><span style="color:#595959;"><a href="{{ url('customer-approval/' . $mailData->leadID) }}" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; font-family: Arial, sans-serif; font-size: 15px; text-align: center; cursor: pointer;">Accept</a></span></p>
                                    </span></p><br>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td align="center">
                        <table  width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                           <tbody>
                              <tr>
                                 <td align="">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'>
                                       <span style="color:#595959;">
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;"><b>WE SUGGEST YOU GO THROUGH THE TERMS AND CONDITIONS CAREFULLY ALL OVER AGAIN SO THERE IS NO DOUBT IN YOUR MIND THAT YOU ARE ACTUALLY <br>    COMFORTABLE TAKING THIS LOAN AND CAN PAY BACK ON ITS DUE DATE.</b></span></p> </span></p><br>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><strong><span style="color:#595959;"><b style="color:black;">Note :</b>Paying this loan back on and before due date may entitle you to a higher amount of loan next time. Along with benefits of reduced rates of interest and other charges.</span></strong></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">&nbsp;</span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">Best Regards,</span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">&nbsp;</span></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">Credit Manager,</span></p>
                                    <p style='margin-top:10px;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><strong><span style="color:#595959;">{{cmp()->companyName}}</span></strong></p>
                                    <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><span style="color:#595959;">&nbsp;</span></p>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <!-- body start from here -->
               </tbody>
            </table>
         </div>
      </div>
   </body>
</html>