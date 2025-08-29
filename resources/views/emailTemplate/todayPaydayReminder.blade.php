<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="x-apple-disable-message-reformatting" />
      <meta name="apple-mobile-web-app-capable" content="yes" />
      <meta name="apple-mobile-web-app-status-bar-style" content="black" />
      <meta name="format-detection" content="telephone=no" />
      <title>Today Repayment loan</title>
   </head>
   <body>
      Dear {{$todayPaydayReminderData->name}}<br/><br/>
      Today is the day you can pay and close your loan and have the option to renew it immediately at a reduced rate of interest.
      <br/><br/>
      Your Loan is {{$todayPaydayReminderData->loanNo}}.
      <br/><br/>
      
      Please ignore this message if you have already paid meanwhile. <br><br>
      You may make payment by visiting this link:
      <br/><br/>
      <a href="{{cmp()->domain}}/repay-loan.php" alt="Pay Now"  target="_blank">Payment Link</a>
      <br/>
      <br/><br/>
      Thanks & Regards,<br/>
      Team {{cmp()->companyName}}
      <br/><br/><br/>
      <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;font-size:16px;font-family:"Calibri",sans-serif;'><strong><span style="color:#595959;"><b style="color:red;">Note :</b> Paying this loan back on time may entitle you to a higher amount of loan next time. Along with benefits of reduced rates of interest and other charges.</span></strong></p>
   </body>
</html>


