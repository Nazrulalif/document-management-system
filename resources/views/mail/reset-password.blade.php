<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
</head>

<body id="kt_body" class="app-blank">
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Body-->
            <div class="scroll-y flex-column-fluid px-10 py-10" data-kt-scroll="true" data-kt-scroll-activate="true"
                data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_header_nav"
                data-kt-scroll-offset="5px" data-kt-scroll-save-state="true"
                style="background-color:#D5D9E2; --kt-scrollbar-color: #d9d0cc; --kt-scrollbar-hover-color: #d9d0cc">
                <!--begin::Email template-->
                <style>
                    html,
                    body {
                        padding: 0;
                        margin: 0;
                        font-family: Inter, Helvetica, "sans-serif";
                    }

                    a:hover {
                        color: #009ef7;
                    }

                </style>
                <div id="#kt_app_body_content"
                    style="background-color:#D5D9E2; font-family:Arial,Helvetica,sans-serif; line-height: 1.5; min-height: 100%; font-weight: normal; font-size: 15px; color: #2F3044; margin:0; padding:0; width:100%;">
                    <div
                        style="background-color:#ffffff; padding: 45px 0 34px 0; border-radius: 24px; margin:40px auto; max-width: 600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" height="auto"
                            style="border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td align="center" valign="center" style="text-align:center; padding-bottom: 10px">
                                        <!--begin:Email content-->
                                        <div style="text-align:center; margin:0 60px 34px 60px">
                                            <!--begin:Logo-->
                                            <div style="margin-bottom: 10px">
                                                <img alt="Logo"
                                                    src="{{ $message->embed(asset('assets/media/logos/docms-light.svg')) }}"
                                                    style="height: 35px" />
                                            </div>
                                            <!--end:Logo-->
                                            <!--begin:Media-->
                                            <div style="margin-bottom: 15px">
                                                <img alt="Logo"
                                                    src="{{ $message->embed(asset('assets/media/email/icon-positive-vote-1.svg')) }}" />
                                            </div>
                                            <!--end:Media-->
                                            <!--begin:Text-->
                                            <div
                                                style="font-size: 14px; font-weight: 500; margin-bottom: 27px; font-family:Arial,Helvetica,sans-serif;">
                                                <p
                                                    style="margin-bottom:9px; color:#181C32; font-size: 22px; font-weight:700">
                                                    It’s almost set!</p>
                                                <p style="margin-bottom:2px; color:#7E8299">You are receiving this
                                                    email because we received a password reset
                                                </p>
                                                <p style="margin-bottom:2px; color:#7E8299">request for your account. To
                                                    proceed with the password reset

                                                </p>
                                                <p style="margin-bottom:2px; color:#7E8299">please click on the button
                                                    below:
                                                </p>
                                            </div>
                                            <!--end:Text-->
                                            <!--begin:Action-->
                                            <a href="{{ route('reset.password', $token) }}" target="_blank" style="background-color:#50cd89; border-radius:6px;display:inline-block;
                                            padding:11px 19px; color: #FFFFFF; font-size: 14px; font-weight:500;
                                            font-family:Arial,Helvetica,sans-serif;text-decoration:none">Reset
                                                Password</a>

                                            <div style="margin-bottom:2px; color:#7E8299; margin-top:25px">This password
                                                reset link will
                                                expire in 60 minutes.</div>
                                            <div style="margin-bottom:2px; color:#7E8299"> If you did
                                                not request a password reset, no further action is required.</div>
                                            <div style="border-bottom: 1px solid #eeeeee; margin: 15px 0"></div>
                                            <div style="margin-bottom:2px; color:#7E8299 word-wrap: break-all;">
                                                <p style="margin-bottom:2px; color:#7E8299">Button not working? Try
                                                    pasting this URL into your browser:
                                                </p>
                                                <a href="{{ route('reset.password', $token) }}" rel="noopener"
                                                    target="_blank"
                                                    style="text-decoration:none;color: #009ef7;word-wrap:break-word;word-break:break-all;">
                                                    {{ route('reset.password', $token) }}</a>
                                            </div>
                                        </div>
                                        <!--end:Email content-->
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" valign="center"
                                        style="font-size: 13px; padding:0 15px; text-align:center; font-weight: 500; color: #A1A5B7;font-family:Arial,Helvetica,sans-serif">
                                        <p>&copy; Copyright DMS.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--end::Email template-->
            </div>
            <!--end::Body-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";

    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="{{ asset("assets/plugins/global/plugins.bundle.js") }}"></script>
    <script src="{{ asset("assets/js/scripts.bundle.js") }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
</body>

</html>
