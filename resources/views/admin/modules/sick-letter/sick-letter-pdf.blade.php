<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Certificate</title>
    <style>
        #halaman {
            font-size: 12pt;
            width: auto;
            height: auto;
            padding-top: 5px;
            padding-left: 30px;
            padding-right: 30px;
            padding-bottom: 30px;
        }

        .custom-table {
            width: 100%;
        }

        /* .custom-table th, .custom-table td {
            border: none;
        } */

        .dotted-underline {
            border-bottom: 1px dotted black;
            padding-left: 5px;
        }

        .header, .footer {
            text-align: center;
        }
        .header img {
            height: auto;
        }
    </style>
</head>
<body>
    <div id="halaman">
        <div class="header">
            <img src="storage/{{ $data->hospital->logo }}" alt="Logo" width="200px">
        </div>
        <br><br>

        <table class="custom-table" style="margin-left:auto; margin-right:auto; border:none;" width="100%" cellspacing="0">
            <tr>
                <td align="center" style="font-size: 20px">
                    <b>MEDICAL CERTIFICATE</b><br>
                    <u><b><i>SURAT KETERANGAN SAKIT</i></b></u>
                </td>
            </tr>
            <tr>
                <td align="center" style="font-size: 16px">No. {{ $data->nomor }}</td>
            </tr>
        </table><br>
        <p>This letter explains that the patient below: <br>
            <i>(Surat ini menjelaskan bahwa pasien di bawah ini)</i></p>
        <table class="custom-table" width="100%" cellspacing="0" style="border: none">
            <tr>
                <td style="width: 25%">
                    Patient Name <br> <i>(Nama Pasien)</i>
                </td>
                <td style="width: 2%">: </td>
                <td class="dotted-underline">{{ $data->patient_name }}</td>
            </tr>
            <tr>
                <td style="width: 25%">
                    Age <br> <i>(Umur)</i>
                </td>
                <td style="width: 2%">: </td>
                <td class="dotted-underline">{{ $data->age }} Year</td>
            </tr>
            <tr>
                <td style="width: 25%">
                    Gender <br> <i>(Jenis Kelamin)</i>
                </td>
                <td style="width: 2%">: </td>
                <td class="dotted-underline">
                    {{ $data->gender == 'M' ? 'Male (Laki-laki)' : 'Female (Perempuan)' }}
                </td>
            </tr>
            <tr>
                <td style="width: 25%">
                    Profession <br> <i>(Pekerjaan)</i>
                </td>
                <td style="width: 2%">: </td>
                <td class="dotted-underline">{{ $data->profession }}</td>
            </tr>
            <tr>
                <td style="width: 25%">
                    Address <br> <i>(Alamat)</i>
                </td>
                <td style="width: 2%">: </td>
                <td class="dotted-underline">{{ $data->address }}</td>
            </tr>
        </table>

        <p>
            is recommended to have sick leave <strong>{{ $dayDifference }} days</strong>, from <strong>{{ \Carbon\Carbon::parse($data->start_date)->format('d M Y') }}</strong> until 
            <strong>{{ \Carbon\Carbon::parse($data->end_date)->format('d M Y') }}</strong> <br>
            <i>( dianjurkan untuk mengambil cuti sakit selama {{ $dayDifference }} hari, dari tanggal {{ \Carbon\Carbon::parse($data->start_date)->format('d M Y') }} hingga 
                {{ \Carbon\Carbon::parse($data->end_date)->format('d M Y') }} )
            </i>
        </p>

        <table class="custom-table" width="100%" cellspacing="0" style="border: none">
            <tr>
                <td style="width: 25%">
                    Diagnosis <br> <i>(Diagnosa)</i>
                </td>
                <td style="width: 2%">: </td>
                <td class="dotted-underline">{{ $data->diagnosis }}</td>
            </tr>
        </table>

        <p>
            This certificate is given to be known and used properly. Thank you for your attention. <br>
            <i>( Surat keterangan ini diberikan untuk diketahui dan digunakan sebagaimana mestinya. Terima kasih atas perhatian anda. )
            </i>
        </p>

        <br><p style="font-size: 11pt">Bali, {{ \Carbon\Carbon::parse($data->date)->format('d M Y') }}</p>
        <a href="{{ $qrcodeRoute }}"><img src="data:image/svg+xml;base64, {!! $qrCode !!}"></a>
        <p style="font-size: 12pt;">{{ $data->user->name }}</p>
    </div>
</body>
</html>
