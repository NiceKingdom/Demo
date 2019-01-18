<!DOCTYPE html>
<html lang="ch">
<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <title></title> <!-- the <title> tag shows on email notifications on Android 4.4. -->
    <style type="text/css">
        /* ensure that clients don't add any padding or spaces around the email design and allow us to style emails for the entire width of the preview pane */
        body,
        #bodyTable {
            height:100% !important;
            width:100% !important;
            margin:0;
            padding:0;
        }

        /* Ensures Webkit- and Windows-based clients don't automatically resize the email text. */
        body,
        table,
        td,
        p,
        a,
        li,
        blockquote {
            -ms-text-size-adjust:100%;
            -webkit-text-size-adjust:100%;
        }
        /* Forces Yahoo! to display emails at full width */
        .thread-item.expanded .thread-body .body,
        .msg-body {
            width: 100% !important;
            display: block !important;
        }
        /* Forces Hotmail to display emails at full width */
        .ReadMsgBody,
        .ExternalClass {
            width: 100%;
            background-color: #f4f4f4;
        }
        /* Forces Hotmail to display normal line spacing. */
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height:100%;
        }
        /* Resolves webkit padding issue. */
        table {
            border-spacing:0;
        }
        /* Resolves the Outlook 2007, 2010, and Gmail td padding issue, and removes spacing around tables that Outlook adds. */
        table,
        td {
            border-collapse:collapse;
            mso-table-lspace:0pt;
            mso-table-rspace:0pt;
        }

        /* Corrects the way Internet Explorer renders resized images in emails. */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Ensures images don't have borders or text-decorations applied to them by default. */
        img,
        a img {
            border:0;
            outline:none;
            text-decoration:none;
        }
        /* Styles Yahoo's auto-sensing link color and border */
        .yshortcuts a {
            border-bottom: none !important;
        }

        /* Styles the tel URL scheme */
        a[href^=tel],
        .mobile_link,
        .mobile_link a {
            color:#222222 !important;
            text-decoration: underline !Important;
        }


        /* Apple Mail doesn't support max-width, so we use media queries to constrain the email container width. */
        @media only screen and (min-width: 601px) {
            .email-container {
                width: 600px !important;
            }
        }

    </style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#f4f4f4" style="margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none;">
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="#f4f4f4" id="bodyTable" style="border-collapse: collapse;table-layout: fixed;margin:0 auto;"><tr><td>

            <!-- Hidden Preheader Text : BEGIN -->
            <div style="display:none; visibility:hidden; opacity:0; color:transparent; height:0; width:0;line-height:0; overflow:hidden;mso-hide: all;">
                Visually hidden preheader text.
            </div>
            <!-- Hidden Preheader Text : END -->

            <!-- Beginning of Outlook-specific wrapper : BEGIN -->
            <!--[if (gte mso 9)|(IE)]>
            <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>
            <![endif]-->
            <!-- Beginning of Outlook-specific wrapper : END -->

            <table border="0" width="100%" cellpadding="0" cellspacing="0" align="center" style="max-width: 600px;margin: auto;" class="email-container">
                <tr>
                    <td>
                        <!-- Logo Left, Nav Right : BEGIN -->
                        <!-- <table border="0" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td height="40" style="font-size: 0; line-height: 0;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td valign="middle" style="padding-left: 30px;text-align: left;">
                                    <img src="http://placehold.it/200x40/888888/7777777" alt="alt text" height="40" width="200" border="0" style="display: block;">
                                </td>
                                <td valign="middle" style="padding-right: 40px;text-align: right;">
                                    <a href="" style="color: #888888;font-family: sans-serif;white-space: nowrap;">Link 1</a>&nbsp;&nbsp;
                                    <a href="" style="color: #888888;font-family: sans-serif;white-space: nowrap;">Link 2</a>
                                </td>
                            </tr>
                            <tr>
                                <td height="40" style="font-size: 0; line-height: 0;">&nbsp;</td>
                            </tr>
                        </table> -->
                        <!-- Logo Left, Nav Right : END -->

                        <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
                            <tr>
                                <td valign="middle" align="center" style="padding: 40px 0;text-align: center;">
                                    {{ $userInfo['prefix'] }}{{ $userInfo['nickname'] }}，今天的空气质量不太好，<b>{{ $userInfo['city'] }}</b>的 PM 2.5 达到了 <span style="color: #f3a710;">{{ $userInfo['pm2.5'] }}</span>
                                    <br><br><span style="color: #38aace">{{ $userInfo['blessed'] ?: '为了关心你的人和自己，出门请带好口罩，么么扎。' }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center;padding: 40px 0;font-family: sans-serif; font-size: 12px; line-height: 18px;color: #888888;">
<!--                        如果你不喜欢这个邮件，请点击 <unsubscribe style="color: #444444; padding: 0;text-decoration: underline">退订</unsubscribe>.<br>-->
                         制作者：UioSun &bull; <span class="mobile_link">(+86) 17630120043</span> &bull; Template by <a href="https://github.com/TedGoas/Cerberus">TedGoas</a><br>
                    </td>
                </tr>
            </table>

            <!-- End of Outlook-specific wrapper : BEGIN -->
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
            <!-- End of Outlook-specific wrapper : END -->

        </td></tr></table>
</body>
</html>
