<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<!--[if gte mso 9]><xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml><![endif]-->
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MYRE Email - Reset Password</title>

<link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">

<style type="text/css">
body,table {
  font-family: 'Roboto', sans-serif;
  font-weight: 400;
  font-size: 14px;
  line-height: 20px;
  color: #0d1627;
}
h1, h2, h3, h4, h5, h6,
b, strong {
  font-weight: 500;
}
.cta-button {
  width: 260px;
}
/* Client-specific Styles & Reset */
#outlook a {
  padding: 0;
}
/* .ExternalClass applies to Outlook.com (the artist formerly known as Hotmail) */
.ExternalClass {
  width: 100%;
}
.ExternalClass,
.ExternalClass p,
.ExternalClass span,
.ExternalClass font,
.ExternalClass td,
.ExternalClass div {
  line-height: 100%;
}
a[x-apple-data-detectors]{
  color:inherit !important;
  text-decoration:none !important;
  font-size:inherit !important;
  font-family:inherit !important;
  font-weight:inherit !important;
  line-height:inherit !important;
}
#backgroundTable {
  margin: 0;
  padding: 0;
  width: 100% !important;
  line-height: 100% !important;
}
@media only screen and (max-width: 640px) {
  table[class=inner] {
    width:100% !important;
  }
  .cta-button {
    width:100% !important;
    padding: 0 !important;
  }
}
</style>
</head>

<body marginwidth="0" marginheight="0" topmargin="0" leftmargin="0" offset="0" style="width:100%; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%!important; margin:0; padding:0; background-color:#f5f5f5">

<table class="outer" style="border-spacing:0; border-collapse:collapse; height:100%; width: 100%;" bgcolor="#f5f5f5" cellpadding="0" cellspacing="0" border="0" width="100%">
  <tr>
    <td style="padding:16px; font-family:'Roboto',sans-serif; font-weight:400; font-size:14px;" align="center">

      <table class="inner" style="border-spacing:0; border-collapse:collapse;" width="600" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>

            <table class="content" style="border-spacing:0; border-collapse:collapse; border:1px solid #e6e6e6" width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td>
                  <table class="header" style="border-spacing:0; border-collapse:collapse; width:100%!important" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                      <td height="80" width="50%" align="left" valign="middle" style="padding-left:24px; padding-right:24px;">
                        <a href="https://www.myre.fr">
                          <img style="outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; clear:both; display:block; border:0; max-width: 80px" border="0" src="{{asset('assets/image/myre_logo_email.png')}}" alt="MYRE Logo" height="32">
                        </a>
                      </td>
                      <td height="80" width="50%" align="right" valign="middle" style="font-size:12px; line-height:18px; padding-left:24px; padding-right:24px;">
                        <a href="https://www.myre.fr" style="text-decoration:none; color:#bebebf;">www.myre.fr</a>
                      </td>
                    </tr>
                  </table><!-- .header -->

                  <!-- BODY -->
                  <table class="body" style="border-spacing:0; border-collapse:collapse;" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                      <td style="padding-left:24px; padding-right:24px; padding-bottom:24px;">

                        <p><b>Hello {!! $firstName !!},</b></p>
                        <p>Your password has been reset by admin.</p>
                        
                        <div style="text-align:center; margin-top:24px; margin-bottom:24px;">
                        	<p>Your Password : {!! $password !!}</p>
                        </div>
                        
                        <p style="margin-top:24px; margin-bottom:0; color:#8c95aa;">See you soon,<br />The MYRE team</p>

                      </td>
                    </tr>
                  </table><!-- .body -->
                </td>
              </tr>
            </table><!-- .content -->

            <!-- FOOTER -->
            <table class="footer" style="border-spacing:0; border-collapse:collapse;" width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="padding-top:16px; padding-right:24px; padding-bottom:16px; padding-left:24px; font-size:12px; line-height:18px; color:#bebebf;" align="left" valign="top">
                  <p style="margin-top:0; margin-bottom:0;">This message is sent automatically, please do not reply directly to this email. If you have any questions or would like help, write to us at <a href="mailto:support@myre.fr" style="text-decoration:none; color:#bebebf;"><b>support@myre.fr</b></a></p>
                  <p style="margin-top:10px; margin-bottom:0;">©{{ date('Y') }} MYRE. All rights reserved.</p>
                </td>
              </tr>
            </table><!-- .footer -->

          </td>
        </tr>
      </table><!-- .inner -->

    </td>
  </tr>
</table><!-- .outer -->

</body>
</html>