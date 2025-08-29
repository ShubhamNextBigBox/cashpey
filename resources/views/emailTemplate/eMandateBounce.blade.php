<!DOCTYPE html>
<html>

<head>
    <title> E-Mandate Mail Bounce</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body data-new-gr-c-s-loaded="14.991.0" spellcheck="false">
    <div>
        <div style="clear: both; text-align: left;">
            <p style="margin-top:0pt; margin-bottom:0pt; font-size:11pt;"><span style="height:0pt; display:block; position:absolute; z-index:-65538;">&nbsp;</span></p><img src="https://app.creditpey.co.in/storage/logo/creditpey-logo.jpeg" width="354" height="124" alt="" style="margin: 0 auto; display: block; text-align:center;">
        </div>
        <div style="clear: both; text-align: left;"><span style="font-family:Calibri;">&nbsp;</span></div>
        <p style="margin-right: 30px;margin-left: 30px;">Dear {{$mailData->name}},</p>
        <p style="margin-right: 30px;margin-left: 30px;"><br></p>

        <p style="margin-right: 30px;margin-left: 30px;">At the time of taking a loan, and while agreeing to all the terms and conditions vide formal agreement, you had also approved an e-mandate which would enable us to activate it on your payment due date in order to recover the due amount.</p>

        <p style="margin-right: 30px;margin-left: 30px;">Your due date for repaying of our loan for {{nf($mailData->repayAmount)}} was on {{df($mailData->repayDate)}}.   We had sent you a reminder mail saying that the e-mandate would be activated on {{df($mailData->repayDate)}} and requested you to maintain sufficient balance. Subsequently also we sent you text messages saying the same.</p>
        <p style="margin-right: 30px;margin-left: 30px;">But when we had duly activated the e-mandate on {{df($mailData->repayDate)}} it was dishonored saying you didn’t have sufficient balance in your account.</p>
        <p style="margin-right: 30px;margin-left: 30px;"><br></p>
        <p style="margin-right: 30px;margin-left: 30px;">Since we gave you sufficient advance notice about the e-mandate,  it appears that your action was deliberate, with the aim of avoiding payment. </p>
        <p style="margin-right: 30px;margin-left: 30px;"><br></p>
        <p style="margin-right: 30px;margin-left: 30px;">Hence, we have no alternative but to initiate legal proceedings against you for recovery of our dues and also the penalty for bouncing of e-mandates.</p>
        <p style="margin-right: 30px;margin-left: 30px;"><br></p>
        <p style="margin-right: 30px;margin-left: 30px;">However, we will activate the same e-mandate a few times more before proceeding with the legal course.</p>
        <p style="margin-right: 30px;margin-left: 30px;"><br></p>
        <p style="margin-right: 30px;margin-left: 30px;">Best regards</p>
        <p style="margin-right: 30px;margin-left: 30px;">{{cmp()->companyName}}</p>
        <p style='margin-right: 30px;margin-left: 30px;'><strong><span style="color:#595959;"><b style="color:red;">Note :</b> Paying this loan back on time may entitle you to a higher amount of loan next time. Along with benefits of reduced rates of interest and other charges.</span></strong></p>
    </div>
</body>

</html>