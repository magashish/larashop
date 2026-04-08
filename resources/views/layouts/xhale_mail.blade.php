<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Xhale Email')</title>
    <style>
        /* Basic Reset & Global Styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f2f2f2; /* Light grey background for the page around the email */
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        /* Styles for the main black content area */
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #000000; /* Black background */
            border-radius: 8px;
            overflow: hidden; /* Ensures rounded corners clip content */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow */
        }
        .content-padding {
            padding: 20px;
        }
        /* Text colors for the dark background */
        .email-container p,
        .email-container span,
        .email-container strong,
        .email-container a {
            color: #ffffff; /* Default white text */
        }
        /* Specific brand highlight */
        .highlight-brand {
            background-color: #FFD700; /* Yellow/orange */
            padding: 2px 5px;
            border-radius: 3px;
            color: #000000; /* Black text on highlight */
            font-weight: bold;
            white-space: nowrap; /* Prevent breaking "Xhale" */
        }
        /* Links within the dark background */
        .email-container a {
            color: #3366CC; /* Standard link blue */
            text-decoration: underline;
        }
        /* Button styling */
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #3366CC; /* Blue button */
            color: #ffffff !important; /* Ensure white text on button */
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }
        /* Footer styling */
        .footer {
            background-color: #1a1a1a;
            padding: 15px 20px;
            font-size: 12px;
            color: #aaaaaa;
            text-align: center;
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f2f2f2;">
    <center>
        <table border="0" cellpadding="0" cellspacing="0" class="email-container">
            <tr>
                <td class="content-padding" style="text-align: center; color: #ffffff; font-size: 24px; font-weight: bold;">
                    <span class="highlight-brand">Xhale</span> @yield('header_title')
                </td>
            </tr>
            <tr>
                <td height="20" style="font-size:1px; line-height:1px;">&nbsp;</td>
            </tr>

            <tr>
                <td class="content-padding">
                    @yield('content')
                </td>
            </tr>

            <tr>
                <td class="footer">
                    <p style="margin: 0;">&copy; {{ date('Y') }} <span class="highlight-brand" style="padding: 1px 3px; border-radius: 2px;">Xhale</span>. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>