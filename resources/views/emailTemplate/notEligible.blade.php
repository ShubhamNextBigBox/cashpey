@php error_reporting(0) @endphp
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rejection of Application Mail</title>
  <style type="text/css">
    * {
      line-height: 100%;
    }

    .appleLinks a {
      color: #000000;
      text-decoration: none;
    }

    .ReadMsgBody,
    .ExternalClass,
    body {
      width: 100%;
      background-color: #ffffff;
    }

    body {
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
      font-family: Georgia, Times, serif;
    }

    html {
      -webkit-text-size-adjust: none !important;
      -ms-text-size-adjust: none !important;
    }

    table {
      border-collapse: collapse;
    }

    img {
      display: block;
    }

    img.g-img+div {
      display: none;
    }

    .disp {
      display: none !important;
    }

    .btn-defult {
      background-color: #EF8B00;
      color: #fff;
      padding: 5px 15px;
      text-decoration: none;
      border-radius: 5px;
    }

    .btn-defult:hover {
      background-color: #57003d;
      color: #fff;
    }

    @media only screen and (max-width: 700px),
    only screen and (max-width: 480px) {
      .Width,
      .devicewidth3 {
        width: 100% !important;
      }

      .Width1 {
        width: 320px !important;
      }

      .Width2 {
        width: 90% !important;
      }

      .width15 {
        width: 100% !important;
        height: 15px !important;
      }

      .width30 {
        width: 100% !important;
        height: 30px !important;
      }

      .m14 {
        margin-top: 14px !important;
      }

      .width40,
      .w40 {
        width: 40% !important;
      }

      .Width89 {
        width: 89% !important;
      }

      .font14,
      .fnt13 {
        font-size: 12px !important;
      }

      .hide {
        display: none !important;
      }

      .CToWUd {
        width: 80px !important;
      }
    }

    .myul li {
      line-height: 1.6;
    }
  </style>
</head>

<body>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="Width" bgcolor="#ffffff">
    <tbody>
      <tr>
        <td style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#f4f4f4;line-height:1px;
        max-height:0px;max-width:0px;opacity:0;overflow:hidden;" align="center"></td>
      </tr>

      <tr>
        <td align="center" class="hide" valign="top">
          <span style="font-family: Arial, Helvetica, sans-serif; font-size: 2px; color: #ffffff; font-weight: normal; visibility:hidden;">
            &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
          </span>
        </td>
      </tr>

      <tr>
        <td align="center" valign="top">
          <table border="0" cellspacing="0" cellpadding="0" class="Width1" bgcolor="#ffffff">
            <tbody>
              <tr>
                <td align="center" valign="top">
                  <a href="https://www.cashpey.com" target="_blank">
                    <img src="https://app.cashpey.in/storage/logo/CP1.png" width="125px"
                      style="display: block; border: none; margin-top: 40px; margin-bottom: 10px;" title="REQUEST A DEMO"
                      class="g-img" />
                  </a>
                </td>
              </tr>

              <tr>
                <td align="center" bgcolor="#e6f0ef">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#e6f0ef">
                    <tbody>
                      <tr>
                        <td align="left"
                          style="font-size: 20px; font-family: Montserrat, Trebuchet MS, sans-serif; color: #000; line-height: 1.8; padding: 30px; background: #fff; font-weight: 400;"
                          class="fnt13">
                          <b>Dear {{$mailData->name}},</b><br><br>

                          <p style="line-height: 1.4; font-size: 17px;">
                           Thank you for your interest in our loan services.
                          </p>

                          <p style="line-height: 1.4; font-size: 17px;">
                            We regret to inform you that your loan application is currently not eligible for processing as per our internal policy.
                          </p>

                           <p style="line-height: 1.4; font-size: 17px;">
                           You are welcome to reapply in the future once the eligibility criteria are met or if there are any changes in your financial profile.

                          </p>

                           <p style="line-height: 1.4; font-size: 17px;">
                           Thank you for your understanding.

                          </p>

                          <p style="line-height: 1.4; font-size: 17px;">
                            Warm Regards,<br>
                            Team {{ cmp()->companyName }}
                          </p>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>

              <tr>
                <td align="center">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#fff" align="center">
                    <tbody>
                      <tr>
                        <td align="left">
                          <table width="325" border="0" cellspacing="0" cellpadding="0" align="left" class="Width"
                            style="width: 325px;">
                            <tbody>
                              <tr>
                                <td align="center">
                                  <table width="325" border="0" cellspacing="0" cellpadding="0" align="center"
                                    class="Width" style="width: 325px;">
                                    <!-- Optional footer content -->
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>

            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
  <br>
</body>

</html>
