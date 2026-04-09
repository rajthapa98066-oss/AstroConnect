<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password</title>
    <style>
        body { margin:0; padding:0; background:#020617; font-family:Outfit, Arial, Helvetica, sans-serif; color:#e2e8f0; }
        .wrap { width:100%; background:#020617; padding:20px 12px; }
        .container { max-width:640px; margin:0 auto; background:#0f172a; border:1px solid #243247; border-radius:10px; overflow:hidden; box-shadow:0 18px 40px rgba(2,6,23,0.45); }
        .header { background:linear-gradient(135deg, #f59e0b 0%, #f97316 40%, #111827 100%); padding:22px 24px; text-align:center; }
        .brand { font-family:'Cormorant Garamond', Georgia, serif; font-size:28px; line-height:32px; font-weight:700; color:#fff; }
        .brand-sub { margin-top:8px; font-size:13px; line-height:18px; color:#fff7ed; }
        .content { padding:26px 24px 14px; background:#0f172a; }
        .eyebrow { margin:0 0 10px; font-size:12px; line-height:16px; letter-spacing:.22em; text-transform:uppercase; color:#f59e0b; font-weight:700; }
        h2 { margin:0; font-family:'Cormorant Garamond', Georgia, serif; font-size:26px; line-height:32px; color:#f8fafc; font-weight:700; }
        p { margin:14px 0 0; font-size:15px; line-height:24px; color:#cbd5e1; }
        .box { margin-top:18px; background:#111c34; border:1px solid #243247; border-radius:8px; padding:16px 18px; }
        .box p { margin:0; padding:10px 0; border-bottom:1px solid #243247; color:#dbeafe; }
        .box p:last-child { border-bottom:none; }
        .cta { margin-top:22px; }
        .btn { display:inline-block; padding:12px 22px; background:#f59e0b; color:#0f172a; text-decoration:none; border-radius:9999px; font-size:14px; line-height:20px; font-weight:700; }
        .footer { padding:16px 24px 22px; background:#0b1220; border-top:1px solid #23324a; text-align:center; }
        .footer p { margin:0; font-size:12px; line-height:18px; color:#64748b; }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="wrap">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="container">
                    <tr>
                        <td class="header">
                            <div class="brand">AstroConnect</div>
                            <div class="brand-sub">Cosmic guidance with trusted astrologers.</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <p class="eyebrow">Reset password</p>
                            <h2>A secure reset link is ready.</h2>
                            <p>You are receiving this email because we received a password reset request for your account.</p>
                            <div class="cta">
                                <a href="{{ $actionUrl }}" class="btn">Reset Password</a>
                            </div>
                            <p style="margin-top:18px;color:#94a3b8;">If you did not request a password reset, you can safely ignore this email.</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>This is an automated message from AstroConnect. If you did not expect this email, you can safely ignore it.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
