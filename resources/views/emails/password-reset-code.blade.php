<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 480px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .email-header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            padding: 32px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .email-header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 32px;
            text-align: center;
        }
        .greeting {
            font-size: 16px;
            color: #333;
            margin-bottom: 16px;
        }
        .code-box {
            background: #fef5f5;
            border: 2px dashed #e74c3c;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .code {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #e74c3c;
            font-family: 'Courier New', monospace;
        }
        .note {
            font-size: 13px;
            color: #888;
            margin-top: 24px;
            line-height: 1.5;
        }
        .warning {
            font-size: 13px;
            color: #e74c3c;
            margin-top: 12px;
            font-weight: 500;
        }
        .email-footer {
            padding: 20px 32px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>🔑 Bookverse</h1>
            <p>Yêu cầu đặt lại mật khẩu</p>
        </div>

        <div class="email-body">
            <p class="greeting">Xin chào <strong>{{ $userName }}</strong>,</p>
            <p style="color: #555; font-size: 14px;">
                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Vui lòng nhập mã sau:
            </p>

            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>

            <p class="note">
                ⏱ Mã này có hiệu lực trong <strong>10 phút</strong>.<br>
                Bạn chỉ được nhập sai tối đa <strong>5 lần</strong>.
            </p>
            <p class="warning">
                🔒 Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.
            </p>
        </div>

        <div class="email-footer">
            © {{ date('Y') }} Bookverse. Email này được gửi tự động, vui lòng không trả lời.
        </div>
    </div>
</body>
</html>
