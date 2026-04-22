# WhatsApp OTP & Tracking: Live Site Migration Guide

Follow these steps to ensure the WhatsApp OTP and message tracking features work correctly on your live production server.

## 1. Database Setup
You must create the `whatsapp_logs` table in your live database. You can do this by running the CodeIgniter migration or manually executing this SQL:

```sql
CREATE TABLE IF NOT EXISTS `whatsapp_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wa_message_id` VARCHAR(255) NOT NULL,
  `recipient_id` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'sent',
  `error_message` TEXT DEFAULT NULL,
  `raw_payload` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX (`wa_message_id`),
  INDEX (`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 2. Environment Configuration (`.env`)
Update your `.env` file on the live server. Since you are moving to a **Real Account**, you must replace the Test IDs with Production IDs:

```ini
# Meta WhatsApp Production Credentials
WHATSAPP_TOKEN=YOUR_PERMANENT_SYSTEM_USER_TOKEN
WHATSAPP_BUSINESS_ID=YOUR_REAL_BUSINESS_ID
WHATSAPP_PHONE_NUMBER_ID=YOUR_REAL_PHONE_NUMBER_ID

# Template Name must match the one approved in Meta Business Suite
WHATSAPP_TEMPLATE_OTP=learnnextdoorv1 

# Webhook Verification (Use a strong secret string)
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_custom_secret_token
```

> [!IMPORTANT]
> To avoid OTP failures on the live site, ensure you generate a **Permanent Access Token** in your Meta Business Settings (System Users section). The tokens generated in the "Getting Started" page expire after 24 hours.

## 3. Webhook Endpoint Configuration
1. Go to **Meta for Developers** → **Your App** → **WhatsApp** → **Configuration**.
2. Set the **Callback URL** to: `https://yourdomain.com/webhooks/whatsapp`
3. Set the **Verify Token** to match the value in your `.env` (e.g., `your_custom_secret_token`).
4. Under **Webhook Fields**, click `Manage` and subscribe to `messages`.

## 4. File Permissions
The live site needs to write logs and process tracking updates. Run these commands via SSH on your server:

```bash
# Give write access to the logs directory
chmod -R 775 writable/logs
chmod -R 777 writable/session # Important for OTP sessions
```

## 5. Security & Redirect Bypass
Ensure your `app/Filters/AuthFilter.php` allows the webhook to receive data without being redirected to the login page:

```php
// app/Filters/AuthFilter.php
public function before(RequestInterface $request, $arguments = null)
{
    $path = $request->uri->getPath();
    
    // Bypass for WhatsApp Webhooks
    if (str_contains($path, '/webhooks/whatsapp')) {
        return;
    }

    // Existing auth logic...
}
```

## 6. Verification
Once deployed, you can monitor message statuses in real-time by visiting:
`https://yourdomain.com/admin/whatsapp-logs`
