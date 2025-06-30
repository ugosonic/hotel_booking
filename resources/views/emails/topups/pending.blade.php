<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Top-Up Approval</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
        }
        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h2 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #4299e1;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-item {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #4a5568;
            font-weight: 600;
            width: 100px;
            display: inline-block;
        }
        .info-value {
            color: #2d3748;
            font-weight: 500;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4299e1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 600;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h2>Top-Up Approval Required</h2>
        </div>

        <div class="content">
            <p style="font-size: 16px; color: #4a5568;">Dear Administrator,</p>
            
            <p style="font-size: 16px; color: #4a5568;">A new bank transfer top-up request has been submitted and requires your immediate attention for approval.</p>
            
            <div class="info-box">
                <div class="info-item">
                    <span class="info-label">User ID:</span>
                    <span class="info-value">{{ $topup->user_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Amount:</span>
                    <span class="info-value">₦{{ number_format($topup->amount, 2) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Method:</span>
                    <span class="info-value">{{ ucfirst($topup->method) }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="#" class="button">Review Request</a>
            </div>

            <div class="divider"></div>

            <p style="font-size: 14px; color: #718096;">For security reasons, this request will expire in 24 hours. Please ensure to review it before the expiration time.</p>
        </div>

        <div class="footer">
            <p>Best regards,<br><strong>Admin Team</strong></p>
            <div style="margin-top: 20px; font-size: 12px;">
                This is an automated message. Please do not reply directly to this email.
            </div>
        </div>
    </div>
</body>
</html>