<!DOCTYPE html>
<html lang="en">
<head>
    <title>ENach Registration</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Roboto', sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06); overflow: hidden;">

                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding: 30px 20px 20px;">
                            <img src="https://app.cashpey.in/storage/logo/CP1.png" width="180" alt="CashPey Logo" style="display: block;">
                        </td>
                    </tr>

                    <!-- Sub Branding -->
                    <tr>
                        <td align="center" style="background-color: #fafafa; padding: 10px 0; border-top: 1px solid #e6e6e6; border-bottom: 1px solid #e6e6e6;">
                            <span style="font-size: 14px; color: #555;">
                                A Wholly Owned Product of
                                <img src="https://app.cashpey.in/storage/logo/nbfc.png" alt="NBFC Logo" width="90" style="vertical-align: middle;">
                            </span>
                        </td>
                    </tr>

                    <!-- Message -->
                    <tr>
                        <td style="padding: 35px 40px 10px; color: #333;">
                            <p style="font-size: 16px; margin: 0 0 20px;"><strong>Dear Mr/Ms. {{ ucwords($mailData->name) }},</strong></p>
                            <p style="font-size: 15px; line-height: 1.7; margin: 0 0 10px;">
                                <strong>Naman Commodities Pvt. Ltd.</strong> has requested authorization for your bank account via eNACH.
                                To proceed securely, please click the button below to authorize your mandate.
                            </p>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td align="center" style="padding: 10px;">
                            <a href="{{ route('enachRegister', ['leadID' => $mailData->leadID]) }}"
                               style="background-color: #4603FF; color: #ffffff; text-decoration: none; padding: 12px 25px; font-size: 15px; font-weight: bold; border-radius: 28px; display: inline-block;">
                                Click here
                            </a>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f1f1f; padding: 18px; text-align: center;">
                            <span style="color: #ccc; font-size: 13px;">
                                Powered by
                                <a href="https://www.nextbigbox.in/" target="_blank" style="color: #fcf80c; text-decoration: underline;">NextBigBox</a>
                            </span>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
