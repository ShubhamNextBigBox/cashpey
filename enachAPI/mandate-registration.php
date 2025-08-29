<!DOCTYPE html>
<html lang="en">
<head>
  <title>Mandate Registration</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://www.paynimo.com/paynimocheckout/client/lib/jquery.min.js" type="text/javascript"></script>
   <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  

    <style>

        .thank-header{
           
            padding: 5px;
            padding-top: 49px;
        }
        .logo-size{
            /*width:160px;*/
            
        }
        .goodskill_logo{
            width:95px;
        } 
        .thankyou-page{
            max-width: 500px;
            border: 1px solid #eaeaea;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            order-radius: 10px;
            box-shadow: 0 4px 30px rgb(0 0 0 / 0%);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
            
        }
        .goodskill-heading{
            background-color: #528ff0;
            color: #fff;
            padding: 15px;
            margin-bottom: 0px;
            text-align: center;
        }

        .thank-body{
            /* background-color: #effff6; */
            padding: 60px 15px 30px;
            /* background-image: url('./img/bg-image.jpg'); */
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
        }

        .backtohome, .backtohome:hover{
            background-color: #7700d4;
            color: #fff;
            font-size: 18px;
            border-radius: 20px;
            padding: 5px 30px;
            box-shadow: rgb(50 50 93 / 0%) 0px 0px 12px -2px, rgb(0 0 0) 0px 4px 0px 0px;
        }
        .thank-footer{
/*            background-color: #525355;*/
            padding: 10px;



        }

        
        .tasks-list-mark {
        
        position: relative;
        display: inline-block;
        vertical-align: top;
        margin: 5px;
        width: 50px;
        height: 50px;
        border: 4px solid #000;
        border-radius: 50%;
        cursor:pointer;
        }
        .tasks-list-mark:before {
            content: '';
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -4px 0 0 -7px;
            height: 8px;
            width: 16px;
            border: solid #000;
            border-width: 0 0 4px 4px;
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
            transform: rotate(-45deg);
        }
        .checkmark-cover{
            text-align: center;
        }

        .content-para{
            font-size: 35px;
            margin-bottom: 0px;
            /* border-top: 1px solid #c7c7c7; */
/*            border-bottom: 1px solid #c7c7c7;*/
            padding-bottom: 3px;
        }

        .nextbigbox{
            font-size: 14px;
            margin-bottom:0px;
/*            color:#fff;*/
           
        }
        .nextbigbox a{
            font-size: 14px;
            margin-bottom:0px;
            color:black;
            text-decoration: underline;
        }
        .next-btn{
            text-align: center;
            padding-top: 60px;
        }
        @media (max-width:768px) {
            .thankyou-page{
                width:100%;
                position: relative;
                left: unset;
                top: unset;
                transform: unset;
            }
        }
    </style>

</head>

<script>
   $(document).ready(function(){    
    //Check if the current URL contains '# or hash'
    if(document.URL.indexOf("#")==-1){
        // Set the URL to whatever it was plus "#process".
        url = document.URL+"#process";
        location = "#process";
        //Reload the page using reload() method
        location.reload(true);
    }
}); 
</script>


<?php
          $fullurl = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            // Inintialize URL to the variable 

            $url_components = parse_url($fullurl); 
    
            parse_str($url_components['query'], $params); 
          
         // Display result 
            $leadID=$params['leadID'];
             $conn=mysqli_connect("127.0.0.1","Care_Pro","5mfU5&1156778321","Care_Pro");
              if (!$conn) {
                echo "connection failed";
              }
    
              
           
             $leadID=$params['leadID']; 
               $query= 'SELECT L.leadID, C.name, C.email, C.mobile, ROUND(SUM((A.emi * A.tenure)) * 2) AS RepayAmount, A.createdDate AS sanctionDate, A.paymentStartDate FROM lms_leads L INNER JOIN lms_contact C ON L.contactID = C.contactID INNER JOIN lms_approval A ON A.leadID = L.leadID WHERE A.status = "Approved" AND L.leadID='.$leadID.'';
                
                $run=$conn->query($query);
                $result=mysqli_fetch_object($run);
                $consumerID =$leadID."CSPY".mt_rand(100,999);
                $cusName=$result->name;
                $cusEmail=$result->email;
                $cusMobile=$result->mobile;
                $cusRepayAmount=$result->RepayAmount;
                $repayDate=$result->paymentStartDate;
                $SanctionDate=$result->sanctionDate;
                $createDate = new DateTime($SanctionDate);
                $cusSanctionDate = $createDate->format('d-m-Y');
                $expiryDate=date('Y-m-d', strtotime('+4 year'));
                $cusExpiryDate = date("d-m-Y", strtotime($expiryDate));
                $txnId="CSPYTXN".mt_rand(1000000000,9999999999);
               
               $tokenNo='L1076928|'.$txnId.'|1||'.$consumerID.'|'.$cusMobile.'|'.$cusEmail.'|'.$cusSanctionDate.'|'.$cusExpiryDate.'|'.$cusRepayAmount.'|M|MNTH|||||3153522012AHLHUI';
               
               $token= hash('sha512', $tokenNo);
               
              ##############Requrest log #####################
               
                $txnId = $txnId; // Example transaction ID
                $consumerID = $consumerID; // Example consumer ID
                $cusMobile = $cusMobile; // Example customer mobile
                $cusEmail = $cusEmail; // Example customer email
                $cusSanctionDate = $cusSanctionDate; // Example sanction date
                $cusExpiryDate = $cusExpiryDate; // Example expiry date
                
                // Construct the token string
                $tokenNo = 'L1076928|' . $txnId . '|1||' . $consumerID . '|' . $cusMobile . '|' . $cusEmail . '|' . $cusSanctionDate . '|' . $cusExpiryDate . '|'.$cusRepayAmount.'|M|MNTH|||||3153522012AHLHUI';
                
                // Prepare the array to be encoded
                $data = [
                    "tokenNo" => $tokenNo
                ];
                
                // Encode the array to JSON
                $jsonData = json_encode($data);
                
                $today = date("Y-m-d H:i:s"); 
                $log = "\n\n".'Logs - '.$today."===============================================\n";
                $log .= 'Mandate Request- '.$jsonData;
               
                $logfile = "Request_log.txt";
                file_put_contents ($logfile, $log, FILE_APPEND | LOCK_EX);
           
              ############### END log  #######################           
                       
               
      
  ?>

<body style="">


    <div class="container">
        <div class="thankyou-page">
            <div class="thank-header">
                 <div class="logo">
                   <center><img src="https://cashpey.com/assets/images/cashlogo.png" style="width:224px;" class="img-fluid logo-size" alt=""></center>

                  
                 </div>
            </div>
            <div class="thank-body">
                
                <div class="text-center ">
                    <p class="content-para">Mandate Registration <br> Process</p>
                </div>
                <div class="next-btn">
                    
     <input type="submit" value="Next" id="btnSubmit" name="submit" class="btn backtohome" style="background:#4234ff;">
                </div>
            </div>
            <div class="thank-footer text-center">
                <p class="nextbigbox">Powered By <a  href="https://www.nextbigbox.in/" target="_blank">NextBigBox</a></p> 
            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="https://www.paynimo.com/paynimocheckout/server/lib/checkout.js"></script>
  <script type="text/javascript">
$(document).ready(function() {
    function handleResponse(res) {
        if (typeof res != "undefined" && typeof res.paymentMethod != "undefined" && typeof res.paymentMethod.paymentTransaction != "undefined" && typeof res.paymentMethod.paymentTransaction.statusCode != "undefined" && res.paymentMethod.paymentTransaction.statusCode == "0300") {
            // success block
        } else if (typeof res != "undefined" && typeof res.paymentMethod != "undefined" && typeof res.paymentMethod.paymentTransaction != "undefined" && typeof res.paymentMethod.paymentTransaction.statusCode != "undefined" && res.paymentMethod.paymentTransaction.statusCode == "0398") {
            // initiated block
        } else {
            // error block
        }
    };

    $(document).off("click", "#btnSubmit").on("click", "#btnSubmit", function(e) {
        
        e.preventDefault();

        var reqJson = {
            "features": {
                "showPGResponseMsg": true,
                "enableAbortResponse": true,
                "enableNewWindowFlow": false,    //for hybrid applications please disable this by passing false
                "enableExpressPay":true,
                "enableMerTxnDetails": true,
                "siDetailsAtMerchantEnd":true,
                "enableSI":true
            },
            "consumerData": {
                "deviceId": "WEBSH2",    //possible values "WEBSH1" or "WEBSH2"
                "token":"<?=$token?>",
                          
                "returnUrl":"https://app.cashpey.in/enach-success",    //merchant response page URL
                "responseHandler": handleResponse,
                "paymentMode": "netBanking",
                "merchantLogoUrl": "https://www.paynimo.com/CompanyDocs/company-logo-vertical.png",  //provided merchant logo will be displayed
                "merchantId": "L1076928",
                "currency": "INR",
                "consumerId": "<?=$consumerID?>",  //Your unique consumer identifier to register a eMandate/eNACH
                "consumerMobileNo": "<?=$cusMobile?>",
                "consumerEmailId": "<?=$cusEmail?>",
                "txnId": "<?=$txnId?>",   //Unique merchant transaction ID
                "items": [{
                    "itemId": "first",
                    "amount": "1",
                    "comAmt": "0"
                }],
                "customStyle": {
                    "PRIMARY_COLOR_CODE": "#45beaa",   //merchant primary color code
                    "SECONDARY_COLOR_CODE": "#FFFFFF",   //provide merchant"s suitable color code
                    "BUTTON_COLOR_CODE_1": "#2d8c8c",   //merchant"s button background color code
                    "BUTTON_COLOR_CODE_2": "#FFFFFF"   //provide merchant"s suitable color code for button text
                },
                //"accountNo": "1234567890",    //Pass this if accountNo is captured at merchant side for eMandate/eNACH
                //"accountHolderName": "Name",  //Pass this if accountHolderName is captured at merchant side for ICICI eMandate & eNACH registration this is mandatory field, if not passed from merchant Customer need to enter in Checkout UI.
                //"ifscCode": "ICIC0000001",        //Pass this if ifscCode is captured at merchant side.
                //"accountType": "Saving",  //Required for eNACH registration this is mandatory field
                "cartDescription":"<?=$consumerID?>",
                
                "debitStartDate": "<?=$cusSanctionDate?>",
                "debitEndDate": "<?=$cusExpiryDate?>", // expiry date
                "maxAmount": "<?=$cusRepayAmount?>",
                //"maxAmount": "10",
                "amountType": "M",
                "frequency": "MNTH"    //  Available options DAIL, WEEK, MNTH, QURT, MIAN, YEAR, BIMN and ADHO
            }
        };

        $.pnCheckout(reqJson);
        if(reqJson.features.enableNewWindowFlow){
            pnCheckoutShared.openNewWindow();
        }
    });
});
</script>

</body>
</html>