<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Medical Certificate</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <table style="max-width: 600px; margin: 0 auto; padding: 20px; border-collapse: collapse;">
        <tr>
            <td style="background-color: #007bff; text-align: center; padding: 10px;">
                <h2 style="color: #fff; margin: 0;">Medical Certificate Download</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Dear {{ $sickLetter->patient_name }},</p>

                <p>We are pleased to inform you that your medical certificate is now available for download. Please click the link below to download your certificate:</p>

                <p><a href="{{ $sickLetterRouteDownload }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Download Medical Certificate</a></p>

                <p>If you have any questions or need further assistance, please feel free to contact us. Thank you.</p>

                <p>Best regards,<br>Medical Team {{ $sickLetter->hospital->name }}</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0; text-align: center; padding: 10px;">
                <p style="margin: 0;"><strong>{{ $sickLetter->hospital->name }}</strong></p>
                <p style="margin: 0;">{{ $sickLetter->hospital->address }}</p>
                <p style="margin: 0;">Phone: <i>{{ $sickLetter->hospital->phone }}</i></p>
            </td>
        </tr>
    </table>

</body>
</html>
