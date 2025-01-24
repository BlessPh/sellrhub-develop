<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; color: #333;">
<div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div style="background-color: #4CAF50; color: white; padding: 16px; text-align: center;">
        <h1 style="font-size: 24px; font-weight: bold; margin: 0;">🎉 The <strong>{{ $shop->name }}</strong> store offers you an exceptional deal !</h1>
    </div>
    <div style="padding: 20px;">
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">Hello,</p>
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
            The <strong>{{ $shop->name }}</strong> store offers you an exceptional deal :
            <br>
            💸 <strong>{{ $promotion->discount_percentage }}%</strong> off selected items!
        </p>
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
            📅 <strong>Promotion valid from</strong>
            {{ $promotion->starts_at->format('d/m/Y') }}
            <strong>au</strong>
            {{ $promotion->ends_at->format('d/m/Y') }}.
        </p>
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
            Don't miss this opportunity!<br>
            Use the promo code: <span style="background-color: #4CAF50; color: white; padding: 4px 8px; border-radius: 4px; font-weight: bold;">{{ $promotion->promo_code }}</span>
        </p>
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px; text-align: center;">
            👉 <a href="{{ url('/shop/' . $shop->id) }}" style="color: #ffffff; background-color: #4CAF50; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold;">See you now</a>
        </p>
        <p style="font-size: 16px; line-height: 1.6; margin-bottom: 20px;">
            Kind regards,<br>
            The <strong>{{ $shop->name }}</strong> team
        </p>
    </div>
    <div style="background-color: #f1f1f1; padding: 16px; text-align: center; font-size: 14px; color: #777;">
        Thank you for trusting us, see you soon! 🌟
    </div>
</div>
</body>
</html>
