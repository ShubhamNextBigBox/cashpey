<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Welcome Letter</title>
  <!--[if !mso]><!-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!--<![endif]-->
  <style type="text/css">
    /* Client-specific styles */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    
    /* Reset styles */
    body {
      margin: 0 !important;
      padding: 0 !important;
      background-color: #ffffff;
    }
    
    /* Main styles */
    .appleLinks a {
      color: #000000;
      text-decoration: none;
    }
    
    .btn-defult {
      background-color: #EF8B00;
      color: #fff;
      padding: 5px 15px;
      text-decoration: none;
      border-radius: 5px;
      display: inline-block;
    }
    
    .footer .info img {
      height: 18px;
    }
    
    /* Font Awesome fallback for Outlook */
    .fa {
      display: inline-block;
      font-family: FontAwesome;
      font-style: normal;
      font-weight: normal;
      line-height: 1;
    }
    
    /* Responsive styles */
    @media screen and (max-width: 700px) {
      .logo {
        font-size: 6vw !important;
      }
      .company-name {
        font-size: 1rem !important;
        padding: 0.5rem 1rem !important;
        white-space: normal !important;
      }
      .footer .info {
        display: block !important;
      }
      .footer-address {
        display: block !important;
      }
      .bottom-bar1 .right,
      .bottom-bar2 {
        display: none !important;
      }
      .Width1 {
        width: 100% !important;
      }
    }
  </style>
</head>
<body style="margin: 0; padding: 0;">
  <!--[if (gte mso 9)|(IE)]>
  <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td>
  <![endif]-->
  
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
    <!-- Letterhead Header -->
    <tr>
      <td style="padding: 40px 0 0 40px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
          <tr>
            <td width="100%" style="text-align: left;">
              <div class="logo" style="font-size: 70px; font-weight: bolder; color: #1e4b91;">NCPL</div>
            </td>
            <td width="100%" style="text-align: right;">
              <div class="company-name" style="background-color: #1e4b91; color: white; padding: 12px 30px; font-size: 22px; font-weight: 600; border-radius: 50px 0 0 50px; display: inline-block; white-space: nowrap;">
                Naman Commodities Pvt Ltd
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    
    <!-- Logo -->
    <tr>
      <td style="text-align: center; padding: 20px 0;">
        <a href="https://www.cashpey.com" target="_blank">
          <img src="https://app.cashpey.in/storage/logo/CP1.png" width="125" style="display: block; margin: 0 auto;" title="REQUEST A DEMO" />
        </a>
      </td>
    </tr>
    
    <!-- Content -->
    <tr>
      <td style="padding: 30px; font-size: 17px; line-height: 1.6;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
          <tr>
            <td style="padding-bottom: 15px;">
              <b>Subject: Welcome Letter</b>
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              <b>Dear 
              @if($mailData->gender=='Male')
                {{ 'Mr.' }}
              @elseif($mailData->gender=='Female')
                {{ 'Ms.' }}
              @else
              @endif
              {{$mailData->name}},</b>
              <span style="float: right;">Date: {{date('d-m-Y')}}</span>
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              Greetings from {{ cmp()->companyName }}!
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              We are absolutely delighted to Welcome you to our Cashpey family.
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              Congratulations on the successful approval of your loan. We truly value the trust you have placed in us, and we are honoured to be your chosen financial partner in this important step of your journey.
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              At {{ cmp()->companyName }}, we believe that finance should be simple, accessible and empowering. To simplify your needs- We're here to help you every step of the way.
            </td>
          </tr>
          
          <!-- Loan Details Table -->
          <tr>
            <td style="padding-bottom: 15px;">
              <h4>Please find your loan details mentioned here under: --</h4>
              <table border="1" cellspacing="0" cellpadding="8" width="100%" style="border-collapse: collapse; margin: 15px 0;">
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>S. no.</b></th>
                  <td style="border: 1px solid #000;">Description</td>
                  <td style="border: 1px solid #000;">Details</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>1.</b></th>
                  <td style="border: 1px solid #000;">Product</td>
                  <td style="border: 1px solid #000;">Personal Loan</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>2.</b></th>
                  <td style="border: 1px solid #000;">Loan Account Number</td>
                  <td style="border: 1px solid #000;">{{ $mailData->loanNo }}</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>3.</b></th>
                  <td style="border: 1px solid #000;">Loan Agreement Date</td>
                  <td style="border: 1px solid #000;">{{ df($mailData->addedOn) }}</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>4.</b></th>
                  <td style="border: 1px solid #000;">Loan Amount (INR)</td>
                  <td style="border: 1px solid #000;">{{ $mailData->loanAmtApproved }}</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>5.</b></th>
                  <td style="border: 1px solid #000;">Tenure (Months)</td>
                  <td style="border: 1px solid #000;">{{ $mailData->tenure }}</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>6.</b></th>
                  <td style="border: 1px solid #000;">EMI Amount (INR)</td>
                  <td style="border: 1px solid #000;">{{ $mailData->emi }}</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>7.</b></th>
                  <td style="border: 1px solid #000;">ROI (p.a.)</td>
                  <td style="border: 1px solid #000;">{{ $mailData->roi }} %</td>
                </tr>
                <tr>
                  <th style="border: 1px solid #000; text-align: left;"><b>8.</b></th>
                  <td style="border: 1px solid #000;">First EMI Due on</td>
                  <td style="border: 1px solid #000;">{{ df($mailData->paymentStartDate) }}</td>
                </tr>
              </table>
            </td>
          </tr>
          
          <!-- Additional content -->
          <tr>
            <td style="padding-bottom: 15px;">
              Please find your Loan agreement, E-stamp Agreement, Repayment schedule, KFS attached with this mail for your reference and safekeeping.
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              Our dedicated support team is always just a call away to answer any questions or assist you throughout the lifecycle of your loan. Please feel free to contact us at <b>+91-7003270034</b> or <b>customerservice@cashpey.com.</b>
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              We are sincerely grateful for the opportunity to serve you. Your success in our priority, and we look forward to building a lasting relationship based on trust, transparency and mutual growth.
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              <b>Note: - Please remember to mention your loan account number in all future communications with us.</b>
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              Once again, welcome aboard- and thank you for choosing {{ cmp()->companyName }}.
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              <h4>Enclosures: --</h4>
              1. Loan Agreement Copy<br />
              2. E-stamp Agreement<br />
              3. Repayment Schedule<br />
              4. KFS
            </td>
          </tr>
          <tr>
            <td style="padding-bottom: 15px;">
              Best regards,<br />
              Team {{ cmp()->companyName }}<br />
              (This is a system generated letter and does not require any signature)
            </td>
          </tr>
        </table>
      </td>
    </tr>
    
    <!-- Footer -->
    <tr>
      <td style="padding: 0px 0 0 00px; background-color: white; font-size: 14px;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
          <!--<tr>-->
          <!--  <td style="padding-bottom: 15px;">-->
          <!--    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">-->
          <!--      <tr>-->
          <!--        <td width="50%" style="vertical-align: middle;">-->
          <!--          <span style="width: 25px; height: 25px; border-radius: 100%; display: inline-block; background: #1e4b91; text-align: center; line-height: 25px;">-->
          <!--            <i class="fa fa-envelope" style="color: white; font-size: 12px;"></i>-->
          <!--          </span>-->
          <!--          <a href="mailto:info@ncplnbfc.com" style="text-decoration: none; color: black; font-size: 18px; font-weight: 100; margin-left: 8px;">info@ncplnbfc.com</a>-->
          <!--        </td>-->
          <!--        <td width="50%" style="vertical-align: middle;">-->
          <!--          <span style="width: 25px; height: 25px; border-radius: 100%; display: inline-block; background: #1e4b91; text-align: center; line-height: 25px;">-->
          <!--            <i class="fa fa-globe" style="color: white; font-size: 12px;"></i>-->
          <!--          </span>-->
          <!--          <a href="https://www.ncplnbfc.com" style="text-decoration: none; color: black; font-size: 18px; font-weight: 100; margin-left: 8px;">www.ncplnbfc.com</a>-->
          <!--        </td>-->
          <!--      </tr>-->
          <!--    </table>-->
          <!--  </td>-->
          <!--</tr>-->
          <tr>
            <td>
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                <tr>
                 
                     <img src="https://app.cashpey.in/storage/logo/bottom.png" style='width:1080px;height:100%;'>
                  
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
  <!--[if (gte mso 9)|(IE)]>
      </td>
    </tr>
  </table>
  <![endif]-->
</body>
</html>