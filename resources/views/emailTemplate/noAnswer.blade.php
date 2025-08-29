<!DOCTYPE html>
<html>
    <head>
        <title> No Answer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <style type="text/css">
        ul#socials li {
        display:inline;
        }
        </style>
    </head>
    <body>
        <div>
            <div style="clear: both; text-align: center;">
                <p style="margin-top:0pt; margin-bottom:0pt; font-size:11pt;"><span style="height:0pt; display:block; position:absolute; z-index:-65538;">&nbsp;</span></p><img src="{{Storage::url(cmp()->logo)}}" width="auto" height="64px;" alt="" style="margin: 0 auto; display: block; text-align:center;"><span style="font-family:Calibri;">&nbsp;</span>
            </div>
        </div>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>Dear {{ucwords($mailData->name)}},</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>&nbsp;</p>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>You had taken a loan from us for {{nf($mailData->loanAmtApproved)}} and your repayment date was {{df($mailData->repayDate)}}.</p><br>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>You have not only failed to repay till date but for the last few days you are not answering our calls or reverting to our mails.</p> <br>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>Since we are not able to talk to you regarding to your pay back, we would like to visit you to discuss your problems so we can offer some solutions.</p><br>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>Please let us know when we can come to you.</p> <br>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>Looking forward to your confirmation, failing for which we may have to make an unscheduled visit.</p> <br>
        <p style='margin-top:0cm;margin-right:0cm;margin-bottom:8.0pt;margin-left:0cm;line-height:107%;font-size:15px;font-family:"Calibri",sans-serif;'>Please confirm</p> <br>
        <p style="margin-right: 30px;margin-left: 30px;"><br></p>
        <p class="my" style="color: ;color: #;
            font-family: Calibri;
            font-size: 14px;"><span style="
            margin-top: 40px;"> Team <br> {{cmp()->companyName}}</p>
            <br>
            <hr>
            <p style='margin-top:0cm;margin-right:0cm;margin-bottom:0cm;margin-left:0cm;line-height:normal;font-size:15px;font-family:"Calibri",sans-serif;text-align:center;background:white;'><span style="color:black;">{{cmp()->address}}</span></p>
        </div>
    </body>
</html>