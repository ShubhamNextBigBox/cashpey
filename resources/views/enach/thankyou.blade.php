<!DOCTYPE html>
<html lang="en">
<head>
    <title>eNACH Registration Status</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .logo-size {
            width: 215px;
            padding-top: 10px;
        }
        .thankyou-page {
            border: 1px solid #eaeaea;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 600px;
        }
        .thank-body {
            background-color: #7591e91a;
            padding: 15px;
            border-top: 1px solid #444;
        }
        .thank-body-error {
            background-color: #7591e91a;
            padding: 15px;
        }
        .tasks-list-mark {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 4px solid #008000;
            border-radius: 50%;
            margin: 5px;
        }
        .tasks-list-mark:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -4px 0 0 -7px;
            height: 8px;
            width: 16px;
            border: solid #008000;
            border-width: 0 0 4px 4px;
            transform: rotate(-45deg);
        }
        .tasks-list-mark-error {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 4px solid #f05252;
            border-radius: 50%;
            margin: 5px;
        }
        .close-icon {
            color: #f05252;
            font-size: 30px;
            line-height: 42px;
        }
        .checkmark-cover, .checkmark-cover-error {
            text-align: center;
            padding-top: 15px;
        }
        .status-message {
            font-size: 26px;
            text-align: center;
            margin-top: 15px;
        }
        .success-message {
            color: #008000;
        }
        .error-message {
            color: #f05252;
        }
        .click-enach-btn {
            text-align: center;
            background-color: #7591e91a;
            padding: 30px 0 20px;
        }
        .thank-footer {
            padding: 10px;
            text-align: center;
        }
        .btn-back {
            background-color: #4234ff;
            border-color: #4234ff;
            color: white;
            padding: 8px 20px;
        }
        .company-tagline {
            font-size: 15px;
            padding: 19px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="thankyou-page">
            <div class="thank-header">
                <div class="logo text-center">
                    <img src="https://cashpey.com/assets/images/cashlogo.png" class="img-fluid logo-size" alt="Cashpey Logo">
                    <div>
                        <span class="company-tagline">The Wholly Owned Product of <b>Naman Commodities Private Limited.</b></span>
                    </div>
                </div>
            </div>

            @if(isset($statusCode) && $statusCode == '0300')
                <div class="thank-body">
                    <div class="checkmark-cover">
                        <div class="tasks-list-mark"></div>
                    </div>
                    <p class="status-message success-message">REGISTRATION SUCCESSFUL</p>
                </div>
            @else
                <div class="thank-body-error">
                    <div class="checkmark-cover-error">
                        <div class="tasks-list-mark-error">
                            <i class="fa fa-close close-icon"></i>
                        </div>
                    </div>
                    <p class="status-message error-message">{{ $txn_err_msg ?? 'Registration failed' }}</p>
                </div>
            @endif

            <div class="click-enach-btn">
                <a href="https://cashpey.com/" class="btn btn-back">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home
                </a>
            </div>

            <div class="thank-footer">
                <p>Powered By <a href="https://www.nextbigbox.in/" target="_blank">NextBigBox</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>