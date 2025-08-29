<!DOCTYPE html>
<html lang="en">
<head>
  <title>Thank You</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  

    <style>

         .logo-size{
            width:215px;
            padding-top: 10px;
            /* padding-bottom: 10px; */
            /* border-bottom: 1px solid #6e6e6e; */
            
        }

        .goodskill_logo{
            width:95px;
        }   
        .thankyou-page{
            border: 1px solid #eaeaea;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%,-50%);
            
        }
        .goodskill-heading{
            background-color: #528ff0;
            color: #fff;
            padding: 15px;
            margin-bottom: 0px;
            text-align: center;
        }

        .thank-body{
            background-color: #7591e91a;
            padding: 15px 15px 15px 15px;
            border-top: 1px solid #444;
        }

        .backtohome, .backtohome:hover{
            background-color: #ca0c33;
            color: #ffffff;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 18px;
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
        border: 4px solid #008000;
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
            border: solid #008000;
            border-width: 0 0 4px 4px;
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
            transform: rotate(-45deg);
        }
        .checkmark-cover{
            text-align: center;
            padding-top: 15px;
        }


        .tasks-list-mark-error {
        position: relative;
        display: inline-block;
        vertical-align: top;
        margin: 5px;
        width: 50px;
        height: 50px;
        border: 4px solid #f05252;
        border-radius: 50%;
        cursor:pointer;
        }
        .tasks-list-mark-error:before {
            content: '';
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -4px 0 0 -7px;
            height: 8px;
            width: 16px;
            border: solid #f05252;
            border-width: 0 0 4px 4px;
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            -o-transform: rotate(-45deg);
            transform: rotate(-45deg);
            display: none;
        }

        .thank-body-error{
            background-color: #7591e91a;
            padding: 15px 15px 15px 15px;
        }

        .checkmark-cover-error{
            text-align: center;
            padding-top: 15px;
        }
        .close-icon{
            color: #f05252;
            font-size: 30px;
            line-height: 42px;
        }
        .nextbigbox{
            font-size: 14px;
            margin-bottom:0px;
            color:black;
            margin-top: 8px;
        }
        .nextbigbox a{
            font-size: 14px;
            margin-bottom:0px;
            color:black;
            text-decoration: underline;
        }

        .click-enach-btn{
           
            text-align: center;
            background-color: #7591e91a;
            padding-top: 30px;
            padding-bottom: 20px;
        }
    </style>

</head>
<body style="">


    <div class="container">
        <div class="thankyou-page">
            <div class="thank-header" style="">
                <div class="logo">
                    <center><img src="https://cashpey.com/assets/images/cashlogo.png" class="img-fluid logo-size" alt=""></center>
                    
                   <div class="text-center">
                        <span style="font-size: 15px;color: ;padding: 19px;">The Wholly Owned Product of <b> Naman Commodities Private Limited.</b> </span> 
                    </div> 
        
                 </div>
            </div>
           
                
                <?php
                     date_default_timezone_set("Asia/Kolkata");
                    $conn=mysqli_connect("127.0.0.1","Care_Pro","5mfU5&1156778321","Care_Pro");
                      if (!$conn) {
                        echo "connection failed";
                      }
                    
                      $dataResponse=$_POST['msg'];
                     
                if($dataResponse!=null && $dataResponse!="")
                  {
                
                        $str_arr = explode ("|", $dataResponse);
                      
                        $statusCode=$str_arr['0'];
                        $status=$str_arr['1'];
                        $txn_err_msg=$str_arr['2'];
                        $txnID=$str_arr['3'];
                        $clnt_txn_ref=$str_arr['5'];
                        $tpsl_txn_time=$str_arr['8'];
                        $mandate_reg_no=$str_arr['13'];
                        $value=$str_arr['7'];
                       ######### Index 7 Value start here ############
                        $cusDetails = explode ("~", $value);
                        $cID=$cusDetails['0'];
                        $mandateData=$cusDetails['1'];
                        $IFSCCode=$cusDetails['2'];
                        $amount_type=$cusDetails['3'];
                        $expiry_date=$cusDetails['6'];
                        $account_number=$cusDetails['5'];
                        $amount=$cusDetails['8'];
                        $schedule_date=$cusDetails['10'];
                        $accountHolderName=$cusDetails['14'];
                        $pan=$cusDetails['21'];
                        $phoneNumber=$cusDetails['22'];
                        $cusProfile=$cusDetails['23'];
                        $accountType=$cusDetails['15'];
                        $consumerID = str_replace( array( 'itc', ':', '{', ), '', $cID);
                         $leadID =substr($consumerID, 0, -7);
                    
                        $UMRNNumber = str_replace( array( 'mandateData', ':', '{','UMRNNumber' , ), '', $mandateData);
                        $ifscCode = str_replace( array( 'IFSCCode', ':',), '', $IFSCCode);
                        $accountNo = str_replace( array( 'account_number', ':',), '', $account_number);
                        $amountType = str_replace( array( 'amount_type', ':',), '', $amount_type);
                        $enachAmount = str_replace( array( 'amount', ':',), '', $amount);
                        $cusName = str_replace( array( 'accountHolderName', ':',), '', $accountHolderName);
                        $cusPan = str_replace( array( 'pan', ':',), '', $pan);
                        $cusAccountType = str_replace( array( 'accountType', ':',), '', $accountType);
                        $expiryDate = str_replace( array( 'expiry_date', ':',), '', $expiry_date);
                        $scheduleDate = str_replace( array( 'schedule_date', ':',), '', $schedule_date);
                        $cusPhoneNumber = str_replace( array( 'phoneNumber', ':',), '', $phoneNumber);
                            
                        $cusp = explode ("{", $cusProfile);
                        $email=$cusp[1];
                        $mobile=$cusp[2];
                        $cusEmail = str_replace( array( 'email', ':', '}' , ), '', $email);
                        $cusMobile = str_replace( array( 'mob', ':', '}' , ), '', $mobile);
                        
                        ######### Index 7 Value end here ############
                     
                      if($statusCode=='0300')
                       {
                       
                       $query="insert into lms_enach_register (status,leadID,statusCode,txn_err_msg,txnID,consumerID,clnt_txn_ref,tpsl_txn_time,UMRNNumber,mandate_reg_no,ifscCode,accountNo,amountType,enachAmount,cusName,cusPan,cusAccountType,expiryDate,scheduleDate,cusPhoneNumber,cusEmail,cusMobile)
                       values('$status','$leadID','$statusCode','$txn_err_msg','$txnID','$consumerID','$clnt_txn_ref','$tpsl_txn_time','$UMRNNumber','$mandate_reg_no','$ifscCode','$accountNo','$amountType','$enachAmount','$cusName','$cusPan','$cusAccountType','$expiryDate','$scheduleDate','$cusPhoneNumber','$cusEmail','$cusMobile')";
                       $run=$conn->query($query);
                    
                       ?>
                        <div class="thank-body">
                        <div class="checkmark-cover">
                        <div class="tasks-list-mark"></div>
                      </div>

                     <div class="text-center ">
                    <p style="font-size: 26px;">REGISTRATION  SUCCESSFULLY </p>
                    </div>
                    </div>
                       
                     <?php  }
                       else
                       {?>
                           
                           
                           <div class="thank-body-error">
                            <div class="checkmark-cover-error">
                                    <div class="tasks-list-mark-error"><i class="fa fa-close close-icon"></i></div>
                            </div>
            
                            <div class="text-center ">
                                <p style="font-size: 26px;"><?=$txn_err_msg?> </p>
                            </div>
                        </div>
                     <?php  }
                            
                            
                     ############### Genrating log here #######################
                        $fullResponse=json_encode($str_arr);
                        $today = date("Y-m-d H:i:s"); 
                        $log = "\n\n".'eNach Registration Logs - '.$today."===============================================\n";
                        $log .= 'Response- '.$fullResponse;
                       
                    $logfile = "MandateRegister_Response.txt";
                    file_put_contents ($logfile, $log, FILE_APPEND | LOCK_EX);
                   
                      ############### END log  #######################
                
                     
                }
                
     
     ?>
        
               <div class="click-enach-btn">
                    <a href="https://cashpey.com/" class="btn btn-success1" style="background-color: #4234ff; border-color: #4234ff;color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to Home</a>
                </div>
          
            <div class="thank-footer text-center">
                
        
        <p class="nextbigbox">Powered By <a  href="https://www.nextbigbox.in/" target="_blank">NextBigBox</a></p> 
            </div>
        </div>
    </div>
</body>
</html>