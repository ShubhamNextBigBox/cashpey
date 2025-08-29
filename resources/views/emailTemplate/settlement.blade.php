<!DOCTYPE html>
<html>
   <head>
      <title>Settlement</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <style>
         .myp {
         margin-top:0cm;
         margin-right:0cm;
         margin-bottom:8.0pt;
         margin-left:0cm;
         line-height:normal;font-size:15px;
         font-family:"Calibri",sans-serif; }
         .span1{
         font-size:16px;font-family:"Times New Roman",serif;
         }
      </style>
   </head>
   <body>
      <div class="container">
         <div style="clear: both; text-align: center;">
            <p style="margin-top:0pt; margin-bottom:0pt; font-size:11pt;"><span style="height:0pt; display:block; position:absolute; z-index:-65538;">&nbsp;</span></p>
            <img src="https://app.creditpey.co.in/storage/logo/creditpey-logo.jpeg" width="auto" height="64px;" alt="" style="margin: 0 auto; display: block; text-align:center;padding-top:20px;"><span style="font-family:Calibri;">&nbsp;</span>
            <p><br></p>
         </div>
         <p style='margin-top:10px;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:normal;font-size:15px;font-family:"Calibri",sans-serif;text-align:center;text-transform: uppercase;'><strong><u><span class="span1">Settlement Letter</span></u></strong></p>
         <p><br></p>
         <p class="myp" ><strong><span class="span1">Date: {{df(date('Y-m-d'))}}</span></strong></p>
         <p class="myp" ><strong><span class="span1">Loan No. : {{$mailData->loanNo}}</span></strong></p>
         <p class="myp" ><strong><span class="span1">Dear Customer,</span></strong></p>
         <p class="myp" ><span class="span1">This is to certify that <strong>Mr/Ms {{ucwords($mailData->name)}}</strong>, having availed a short-term loan from {{cmp()->companyName}} the amount of <strong>Rs. {{nf($mailData->loanAmtApproved)}} ({{convertNumberToWords($mailData->loanAmtApproved)}})</strong> on  <strong>{{df($mailData->disbursalDate)}}</strong>
            has a repayment amount of </strong> <strong> {{nf($mailData->repayAmount )}} ({{convertNumberToWords($mailData->repayAmount)}})</strong> with repayment date on </span>  <strong>{{df($mailData->repayDate)}}</strong>
         </p>
         <p class="myp" ><span class="span1">The payment covers the partial amount which was due from him/her as per the agreed terms.</span></p>
         <p class="myp" ><span class="span1">But as per request we have settled the loan with a lesser amount and thus with mutual agreement, we are pleased to confirm the settlement of the mentioned loan.  
         <p class="myp" ><span class="span1">&nbsp;</span></p>
         <p class="myp" ><strong><span class="span1">Thanks for your co-operation, <br> Sincerely,</br><br> {{cmp()->companyName}}<br></span></strong></p>
         <p class="myp" ><span class="span1">&nbsp;</span> * This is a computer-generated document and does not require any signature.</p>
         <p><br></p>
      </div>
   </body>
</html>