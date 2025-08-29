<!DOCTYPE html>
<html>
    <head>
        <title> E-Mandate Mail Activate</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
    </head>
    <body data-new-gr-c-s-loaded="14.991.0" spellcheck="false">
        <div>
            <div style="clear: both; text-align: left;">
                <p style="margin-top:0pt; margin-bottom:0pt; font-size:11pt;"><span style="height:0pt; display:block; position:absolute; z-index:-65538;">&nbsp;</span></p><img src="{{Storage::url(cmp()->logo)}}" width="160" height="90" alt="" style="margin: 0 auto; display: block; text-align:center;">
            </div>
            <div style="clear: both; text-align: left;"><span style="font-family:Calibri;">&nbsp;</span></div>
            <p style="margin-right: 40px;margin-left: 40px;">Dear {{$eMandateActiveData->name}},</p>
            <p style="margin-right: 40px;margin-left: 40px;"><br></p>
            <p style="margin-right: 40px;margin-left: 40px;">We had notified you that we would be activating your e-mandate for {{nf($eMandateActiveData->loanAmtApproved)}} on your due date {{df($eMandateActiveData->repayDate)}} and requested you to maintain sufficient balance so it gets cleared.</p>
            <p style="margin-right: 40px;margin-left: 40px;">You never reverted and the e-mandate bounced. Subsequently we sent another email informing you of the bouncing but you never reverted to that also.</p>
            <p style="margin-right: 40px;margin-left: 40px;">We fail to understand your intentions. In case you had any issues you could have got in touch with us instead to avoiding us and going into hiding.</p>
            <p style="margin-right: 40px;margin-left: 40px;"><br></p>
            <p style="margin-right: 40px;margin-left: 40px;">Since you continue to avoid us we are again going to activate your e-mandate for the next two days and request you to make all efforts to have it cleared. </p>
            <p style="margin-right: 40px;margin-left: 40px;"><br></p>
            <p style="margin-right: 40px;margin-left: 40px;">You may be aware that each bouncing of e-mandate attracts an additional charge of Rs 1000.</p>
            <p style="margin-right: 40px;margin-left: 40px;"><br></p>
            <p style="margin-right: 40px;margin-left: 40px;">Moreover, we will also be forced to start legal proceedings against you, apart form reporting you to the Credit Bureaus.</p>
            <p style="margin-right: 40px;margin-left: 40px;">Please do maintain sufficient balance in your account and get this cleared.</p>
            <p style="margin-right: 40px;margin-left: 40px;"><br></p>
            <p style="margin-right: 40px;margin-left: 40px;">Best regards</p>
            <p style="margin-right: 40px;margin-left: 40px;">{{cmp()->companyName}}</p>
            <p style='margin-right: 40px;margin-left: 40px;'><strong><span style="color:#595959;"><b style="color:red;">Note :</b> Paying this loan back on time may entitle you to a higher amount of loan next time. Along with benefits of reduced rates of interest and other charges.</span></strong></p>
        </div>
    </body>
</html>