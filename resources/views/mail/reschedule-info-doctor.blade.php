<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Rescheduled Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #007BFF;
        }

        p {
            margin-bottom: 15px;
        }

        strong {
            color: #00080f;
        }

        em {
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table, th, td {
            border: none;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #868686;
        }
    </style>
</head>
<body>
    <div class="container">
        <h4>Reschedule Appointment Number [# {{ $appointment->booking_number }} #] </h4>

        <p>Dear <strong>{{ $appointment->doctor->name }}</strong>,</p>

        <p>We wanted to inform you that your patient's appointment has been rescheduled:</p>

        <table>
            <tr>
                <td><strong>Patient Name:</strong></td>
                <td>{{ $appointment->patient_name }}</td>
            </tr>
            <tr>
                <td><strong>Patient DOB:</strong></td>
                <td>{{ \Carbon\Carbon::parse($appointment->patient_dob)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>New Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>New Time:</strong></td>
                <td>{{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }} Wita</td>
            </tr>
            <tr>
                <td><strong>Clinic:</strong></td>
                <td>{{ $appointment->hospital->name }}</td>
            </tr>
        </table>

        <p>We apologize for any inconvenience caused by this change. If you have any questions or concerns, please feel free to contact us.</p>

        <p><em>Phone: {{ $appointment->hospital->phone }}</em></p>
        <p><em>WhatsApp: {{ $appointment->hospital->whatsapp }}</em></p><br>

        <p>Thank you for your understanding.</p>

        <p><em>Best regards,</em></p>
        <p>{{ $appointment->hospital->name }}</p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
    </div>
</body>
</html>
