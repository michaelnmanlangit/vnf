<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f6fb; font-family: Arial, sans-serif; color: #333; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #1e3ba8 0%, #4169E1 100%); padding: 28px 32px; text-align: center; }
        .header img { height: 40px; margin-bottom: 10px; }
        .header h1 { margin: 0; color: #fff; font-size: 1.25rem; font-weight: 700; letter-spacing: .3px; }
        .body { padding: 32px; }
        .status-badge { display: inline-block; background: #eef1fc; color: #1e3ba8; font-weight: 700; font-size: .85rem; padding: 4px 14px; border-radius: 20px; margin-bottom: 20px; }
        .message { font-size: 1rem; line-height: 1.7; color: #444; margin: 0 0 28px; }
        .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
        .footer { padding: 20px 32px; background: #f8f9fd; text-align: center; font-size: .8rem; color: #888; border-top: 1px solid #eee; }
        .footer strong { color: #444; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>V&amp;F Ice Plant &amp; Cold Storage</h1>
        </div>
        <div class="body">
            <span class="status-badge">Order Update</span>
            <p class="message">
                <strong>{{ $title }}</strong><br><br>
                {{ $body }}
            </p>
            <hr class="divider">
            <p style="font-size:.85rem;color:#888;margin:0;">
                If you have any questions about your order, please contact us directly.
            </p>
        </div>
        <div class="footer">
            <strong>V&amp;F Ice Plant &amp; Cold Storage</strong><br>
            San Roque, City of Santo Tomas, Batangas<br>
            +63 912 345 6789
        </div>
    </div>
</body>
</html>
