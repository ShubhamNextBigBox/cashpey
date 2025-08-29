<!DOCTYPE html>
<html>

<head>
    <title> Loan Repayment Reminder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body data-new-gr-c-s-loaded="14.990.0" spellcheck="false">
    <div>
        <div style="clear: both; text-align: left;">
            <p style="margin-top:0pt; margin-bottom:0pt; font-size:11pt;"><span style="height:0pt; display:block; position:absolute; z-index:-65538;">&nbsp;</span></p><img src="https://app.creditpey.co.in/storage/logo/creditpey-logo.jpeg" width="354" height="124" alt="" style="margin: 0 auto; display: block; text-align:center;">
        </div>
        <div style="clear: both; text-align: left;"><span style="font-family:Calibri;">&nbsp;</span></div>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>&nbsp;</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Dear {{$mailData->name}}</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>It is the time to repay and renew your loan. &nbsp; &nbsp; &nbsp;&nbsp;</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>We had sanctioned your loan for {{nf($mailData->loanAmtApproved)}} on {{df($mailData->disbursalDate)}} which is due for payment of <span style="font-weight:bold;">{{nf($mailData->repayAmount)}}</span> on <span style="font-weight:bold;">{{df($mailData->repayDate)}}</span>. We had specially set this due date as it is your salary date and it would be easier for you to repay the loan.</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>This is a reminder mail for you so that you can repay on or before the due date and renew your loan if need be.</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Please use the following link to make your payments &ndash; <a href="{{cmp()->domain}}/repay-loan.php">Payment Link</a><span style="color:red;">&nbsp;</span></p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Some benefits of making timely repayments:</p>
        <ul style="list-style-type: undefined;">
            <li><strong>Exciting special offers for further loans</strong></li>
            <li>Healthy record with {{cmp()->companyName}}</li>
            <li>No follow-up calls and reminders for repayments</li>
        </ul>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Yes, we have exciting special offers for you if you repay your amount within time. And this also boosts your record with {{cmp()->companyName}}. &nbsp;</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Our representatives will call and discuss the options with you soon.</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>&nbsp;</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Best Regards</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'>Team {{cmp()->companyName}}</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif; margin-left:40px;margin-right:40px;'><br></p>
         <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><strong><span style="color:#595959;"><b style="color:red;">Note :</b> Paying this loan back on time may entitle you to a higher amount of loan next time. Along with benefits of reduced rates of interest and other charges.</span></strong></p>
    </div>
</body>

</html>