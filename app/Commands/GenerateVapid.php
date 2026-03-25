<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\WebPushService;

/**
 * GenerateVapid
 * ─────────────────────────────────────────────────────────────────────
 * One-time command to generate VAPID key pair for Web Push.
 *
 * Usage:
 *   php spark push:generate-vapid
 *
 * Output: two base64url strings to paste into .env
 */
class GenerateVapid extends BaseCommand
{
    protected $group       = 'Push';
    protected $name        = 'push:generate-vapid';
    protected $description = 'Generate a VAPID key pair (P-256) for Web Push notifications.';

    public function run(array $params): void
    {
        CLI::write('Generating P-256 VAPID key pair…', 'yellow');

        $key = openssl_pkey_new([
            'curve_name'       => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);

        if (!$key) {
            CLI::error('openssl_pkey_new failed: ' . openssl_error_string());
            return;
        }

        $details = openssl_pkey_get_details($key);

        // Public key: uncompressed point \x04 || X || Y  (65 bytes)
        $pubRaw = "\x04"
                . str_pad($details['ec']['x'], 32, "\x00", STR_PAD_LEFT)
                . str_pad($details['ec']['y'], 32, "\x00", STR_PAD_LEFT);

        // Private key: raw 32-byte scalar
        $privRaw = $details['ec']['d'];

        $pub  = WebPushService::base64urlEncode($pubRaw);
        $priv = WebPushService::base64urlEncode(str_pad($privRaw, 32, "\x00", STR_PAD_LEFT));

        CLI::newLine();
        CLI::write('✅  Add these lines to your .env file:', 'green');
        CLI::newLine();
        CLI::write('VAPID_SUBJECT=mailto:admin@classnextdoor.in');
        CLI::write("VAPID_PUBLIC_KEY={$pub}");
        CLI::write("VAPID_PRIVATE_KEY={$priv}");
        CLI::newLine();
        CLI::write('ℹ  Public key length : ' . strlen($pub)  . ' chars (should be 87)', 'cyan');
        CLI::write('ℹ  Private key length: ' . strlen($priv) . ' chars (should be 43)', 'cyan');
        CLI::newLine();
        CLI::write('Also expose the public key to JavaScript (paste in base.php or as a meta tag):', 'yellow');
        CLI::write("  <meta name=\"vapid-public-key\" content=\"{$pub}\">");
    }
}
