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
    .thank-header { padding: 5px; padding-top: 49px; }
    .logo-size { }
    .goodskill_logo { width: 95px; }
    .thankyou-page {
        max-width: 500px;
        border: 1px solid #eaeaea;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%,-50%);
        order-radius: 10px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px;
    }
    .goodskill-heading {
        background-color: #528ff0;
        color: #fff;
        padding: 15px;
        margin-bottom: 0px;
        text-align: center;
    }
    .thank-body {
        padding: 60px 15px 30px;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .backtohome, .backtohome:hover {
        background-color: #7700d4;
        color: #fff;
        font-size: 18px;
        border-radius: 20px;
        padding: 5px 30px;
        box-shadow: rgb(0 0 0) 0px 4px 0px 0px;
    }
    .thank-footer { padding: 10px; }
    .tasks-list-mark {
        position: relative;
        display: inline-block;
        margin: 5px;
        width: 50px;
        height: 50px;
        border: 4px solid #000;
        border-radius: 50%;
        cursor: pointer;
    }
    .tasks-list-mark:before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -4px 0 0 -7px;
        height: 8px;
        width: 16px;
        border: solid #000;
        border-width: 0 0 4px 4px;
        transform: rotate(-45deg);
    }
    .checkmark-cover { text-align: center; }
    .content-para {
        font-size: 35px;
        margin-bottom: 0px;
        padding-bottom: 3px;
    }
    .nextbigbox {
        font-size: 14px;
        margin-bottom: 0px;
    }
    .nextbigbox a {
        font-size: 14px;
        color: black;
        text-decoration: underline;
    }
    .next-btn { text-align: center; padding-top: 60px; }
    @media (max-width:768px) {
        .thankyou-page {
            width: 100%;
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
    if(document.URL.indexOf("#")==-1){
        location = "#process";
        location.reload(true);
    }
});
</script>

<body>
<div class="container">
    <div class="thankyou-page">
        <div class="thank-header">
            <div class="logo">
                <center><img src="https://cashpey.com/assets/images/cashlogo.png" style="width:224px;" class="img-fluid logo-size" alt=""></center>
            </div>
        </div>
        <div class="thank-body">
            <div class="text-center">
                <p class="content-para">Mandate Registration <br> Process</p>
            </div>
            <div class="next-btn">
                <input type="submit" value="Next" id="btnSubmit" class="btn backtohome" style="background:#4234ff;">
            </div>
        </div>
        <div class="thank-footer text-center">
            <p class="nextbigbox">Powered By <a href="https://www.nextbigbox.in/" target="_blank">NextBigBox</a></p>
        </div>
    </div>
</div>

<script src="https://www.paynimo.com/paynimocheckout/server/lib/checkout.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    function handleResponse(res) {
        if (res?.paymentMethod?.paymentTransaction?.statusCode == "0300") {
            // success block
        } else if (res?.paymentMethod?.paymentTransaction?.statusCode == "0398") {
            // initiated block
        } else {
            // error block
        }
    }

    $(document).off("click", "#btnSubmit").on("click", "#btnSubmit", function (e) {
        e.preventDefault();

        var reqJson = {
            features: {
                showPGResponseMsg: true,
                enableAbortResponse: true,
                enableNewWindowFlow: false,
                enableExpressPay: true,
                enableMerTxnDetails: true,
                siDetailsAtMerchantEnd: true,
                enableSI: true
            },
            consumerData: {
                deviceId: "WEBSH2",
                token: "{{ $token }}",
                returnUrl: "https://app.cashpey.in/enach-success",
                responseHandler: handleResponse,
                paymentMode: "netBanking",
                merchantLogoUrl: "https://www.paynimo.com/CompanyDocs/company-logo-vertical.png",
                merchantId: "L1076928",
                currency: "INR",
                consumerId: "{{ $consumerID }}",
                consumerMobileNo: "{{ $cusMobile }}",
                consumerEmailId: "{{ $cusEmail }}",
                txnId: "{{ $txnId }}",
                items: [{
                    itemId: "first",
                    amount: "1",
                    comAmt: "0"
                }],
                customStyle: {
                    PRIMARY_COLOR_CODE: "#45beaa",
                    SECONDARY_COLOR_CODE: "#FFFFFF",
                    BUTTON_COLOR_CODE_1: "#2d8c8c",
                    BUTTON_COLOR_CODE_2: "#FFFFFF"
                },
                cartDescription: "{{ $consumerID }}",
                debitStartDate: "{{ $cusSanctionDate }}",
                debitEndDate: "{{ $cusExpiryDate }}",
                maxAmount: "{{ $cusRepayAmount }}",
                amountType: "M",
                frequency: "MNTH"
            }
        };

        $.pnCheckout(reqJson);
        if (reqJson.features.enableNewWindowFlow) {
            pnCheckoutShared.openNewWindow();
        }
    });
});
</script>
</body>
</html>
