<?php

namespace App\Services;

/**
 * WebPushService
 * ─────────────────────────────────────────────────────────────────────
 * Pure PHP implementation of the Web Push Protocol (RFC 8291 / 8292).
 * Uses VAPID authentication (ES256 JWT over P-256 ECDH) and
 * message encryption (ECDH-ES + AES-128-GCM).
 *
 * Requirements: PHP 8.0+, ext-openssl, ext-curl
 *
 * Env keys used:
 *   VAPID_SUBJECT     — mailto: or URL identifying your service
 *   VAPID_PUBLIC_KEY  — Base64url-encoded uncompressed P-256 public key (65 bytes → 87 chars)
 *   VAPID_PRIVATE_KEY — Base64url-encoded P-256 private key (32 bytes → 43 chars)
 *
 * Generate keys once with:  php spark push:generate-vapid
 */
class WebPushService
{
    private string $subject;
    private string $publicKeyB64;
    private string $privateKeyB64;

    public function __construct()
    {
        $this->subject      = env('VAPID_SUBJECT', 'mailto:admin@classnextdoor.in');
        $this->publicKeyB64 = env('VAPID_PUBLIC_KEY', '');
        $this->privateKeyB64 = env('VAPID_PRIVATE_KEY', '');
    }

    /**
     * True when VAPID keys are configured.
     */
    public function isConfigured(): bool
    {
        return $this->publicKeyB64 !== '' && $this->privateKeyB64 !== '';
    }

    // ── Public API ────────────────────────────────────────────────────

    /**
     * Send a Web Push notification to one subscription.
     *
     * @param  array  $sub     Subscription row: endpoint, p256dh, auth
     * @param  array  $payload Notification payload (title, body, icon, url)
     * @return bool
     */
    public function send(array $sub, array $payload): bool
    {
        if (!$this->isConfigured()) {
            log_message('info', '[WebPushService] VAPID keys not configured — push skipped.');
            return false;
        }

        $endpoint    = $sub['endpoint'];
        $clientPub   = $sub['p256dh'];  // base64url
        $clientAuth  = $sub['auth'];    // base64url

        // Build JSON payload
        $json = json_encode([
            'title'   => $payload['title']   ?? 'Class Next Door',
            'body'    => $payload['body']    ?? '',
            'icon'    => $payload['icon']    ?? '/assets/frontend/img/icon-192.png',
            'badge'   => $payload['badge']   ?? '/assets/frontend/img/icon-72.png',
            'url'     => $payload['url']     ?? '/',
            'tag'     => $payload['tag']     ?? 'cnd-reminder',
            'vibrate' => [200, 100, 200],
            'requireInteraction' => true,
        ]);

        // Encrypt payload
        try {
            [$ciphertext, $salt, $serverPubKey] = $this->encrypt($json, $clientPub, $clientAuth);
        } catch (\Throwable $e) {
            log_message('error', '[WebPushService] Encryption error: ' . $e->getMessage());
            return false;
        }

        // Build VAPID Authorization header
        try {
            $vapidAuth = $this->buildVapidHeader($endpoint);
        } catch (\Throwable $e) {
            log_message('error', '[WebPushService] VAPID header error: ' . $e->getMessage());
            return false;
        }

        // Send via cURL
        return $this->postPush($endpoint, $ciphertext, $salt, $serverPubKey, $vapidAuth);
    }

    // ── Encryption (RFC 8291) ─────────────────────────────────────────

    /**
     * Encrypt a plaintext string using ECDH-ES + AES-128-GCM.
     * Returns [$ciphertext, $salt, $serverPublicKeyUncompressed].
     */
    private function encrypt(string $plaintext, string $clientPubB64, string $clientAuthB64): array
    {
        // Decode client keys
        $clientPubRaw  = self::base64urlDecode($clientPubB64);   // 65-byte uncompressed P-256
        $clientAuthRaw = self::base64urlDecode($clientAuthB64);   // 16-byte auth secret

        // Generate ephemeral server ECDH key pair (P-256)
        $serverKey = openssl_pkey_new([
            'curve_name'       => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
        if (!$serverKey) {
            throw new \RuntimeException('Failed to generate ephemeral EC key.');
        }
        $serverKeyDetails = openssl_pkey_get_details($serverKey);
        $serverPubX = $serverKeyDetails['ec']['x'];
        $serverPubY = $serverKeyDetails['ec']['y'];

        // Pad X and Y to 32 bytes (big-endian)
        $serverPubRaw = "\x04" . str_pad($serverPubX, 32, "\x00", STR_PAD_LEFT)
                                . str_pad($serverPubY, 32, "\x00", STR_PAD_LEFT);

        // Build client public key as PEM for ECDH
        $clientPem = self::uncompressedPointToPem($clientPubRaw);
        $clientPub = openssl_pkey_get_public($clientPem);
        if (!$clientPub) {
            throw new \RuntimeException('Failed to parse client public key.');
        }

        // Compute ECDH shared secret
        $sharedSecret = '';
        if (!openssl_pkey_derive($sharedSecret, $clientPub, $serverKey)) {
            throw new \RuntimeException('ECDH derive failed: ' . openssl_error_string());
        }

        // Random 16-byte salt
        $salt = random_bytes(16);

        // HKDF-SHA256 (PRK then two expand steps)
        // Step 1: Extract PRK = HMAC-SHA256(auth_info + "\x01", client_auth)
        // Following Section 3.3 of RFC 8291
        $authInfo = "WebPush: info\x00" . $clientPubRaw . $serverPubRaw;
        $prk      = hash_hmac('sha256', $sharedSecret . "\x01", $clientAuthRaw . $authInfo, true);

        // Step 2: Derive HKDF PRK from salt
        $hkdfSalt    = hash_hmac('sha256', $salt, $prk, true);

        // Step 3: Derive content-encryption key (16 bytes) and nonce (12 bytes)
        $cekInfo  = "Content-Encoding: aes128gcm\x00";
        $nonceInfo = "Content-Encoding: nonce\x00";
        $cek   = substr(hash_hmac('sha256', $cekInfo   . "\x01", $hkdfSalt, true), 0, 16);
        $nonce = substr(hash_hmac('sha256', $nonceInfo . "\x01", $hkdfSalt, true), 0, 12);

        // Pad plaintext: append \x02, pad to next block boundary (not strictly required
        // but good practice; use a single \x02 delimiter as per RFC 8291 §2)
        $paddedPlaintext = $plaintext . "\x02";

        // AES-128-GCM encrypt
        $tag        = '';
        $ciphertext = openssl_encrypt(
            $paddedPlaintext,
            'aes-128-gcm',
            $cek,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            '',
            16
        );
        if ($ciphertext === false) {
            throw new \RuntimeException('AES-GCM encrypt failed: ' . openssl_error_string());
        }
        $ciphertext .= $tag;

        // Build aes128gcm content-coding header (54 bytes)
        // salt (16) + rs (uint32 BE, 4) + idlen (uint8, 1) + keyid (65)
        $rs     = pack('N', strlen($ciphertext) + 16 + 1); // record size
        $header = $salt . $rs . chr(65) . $serverPubRaw;

        return [$header . $ciphertext, $salt, $serverPubRaw];
    }

    /**
     * Convert an uncompressed P-256 point (65 bytes, \x04 || X || Y)
     * to a PEM public key for use with openssl_pkey_get_public().
     */
    private static function uncompressedPointToPem(string $point): string
    {
        // DER prefix for P-256 uncompressed public key (SubjectPublicKeyInfo)
        // ecPublicKey OID (1.2.840.10045.2.1) + prime256v1 OID (1.2.840.10045.3.1.7)
        $der = "\x30\x59"             // SEQUENCE (89 bytes)
             . "\x30\x13"             //   SEQUENCE (19 bytes)
             . "\x06\x07\x2a\x86\x48\xce\x3d\x02\x01"   // OID ecPublicKey
             . "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07" // OID prime256v1
             . "\x03\x42\x00"         //   BIT STRING (66 bytes, 0 unused)
             . $point;                //   uncompressed key (65 bytes)

        return "-----BEGIN PUBLIC KEY-----\n"
             . chunk_split(base64_encode($der), 64, "\n")
             . "-----END PUBLIC KEY-----\n";
    }

    // ── VAPID JWT (RFC 8292) ──────────────────────────────────────────

    /**
     * Generate a VAPID Authorization header value.
     * Format: vapid t=<jwt>,k=<publicKey>
     */
    private function buildVapidHeader(string $endpoint): string
    {
        // Extract audience (origin) from endpoint URL
        $parsed   = parse_url($endpoint);
        $audience = $parsed['scheme'] . '://' . $parsed['host'];
        if (!empty($parsed['port'])) {
            $audience .= ':' . $parsed['port'];
        }

        $now = time();
        $header  = self::base64urlEncode(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $claims  = self::base64urlEncode(json_encode([
            'aud' => $audience,
            'exp' => $now + 43200, // 12 hours
            'sub' => $this->subject,
        ]));

        $sigInput = $header . '.' . $claims;

        // Load private key
        $privRaw = self::base64urlDecode($this->privateKeyB64);
        $privPem = $this->privateKeyToPem($privRaw);
        $privKey = openssl_pkey_get_private($privPem);
        if (!$privKey) {
            throw new \RuntimeException('Failed to load VAPID private key: ' . openssl_error_string());
        }

        // Sign with ES256 (ECDSA-SHA256) — yields DER signature
        $derSig = '';
        if (!openssl_sign($sigInput, $derSig, $privKey, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('ECDSA sign failed: ' . openssl_error_string());
        }

        // Convert DER to raw r||s (64 bytes) for JWT
        $sig = $this->derToJwtSignature($derSig);

        $jwt = $sigInput . '.' . self::base64urlEncode($sig);

        return 'vapid t=' . $jwt . ',k=' . $this->publicKeyB64;
    }

    /**
     * Convert a raw 32-byte P-256 private key to PEM.
     */
    private function privateKeyToPem(string $privRaw): string
    {
        // ECPrivateKey DER structure for P-256
        // SEQUENCE { version=1, privateKey (32 bytes), OID prime256v1, publicKey (placeholder) }
        // We use the minimal form (version + private key + parameters OID)
        $der = "\x30\x77"                    // SEQUENCE (119 bytes)
             . "\x02\x01\x01"                //   INTEGER 1 (version)
             . "\x04\x20" . str_pad($privRaw, 32, "\x00", STR_PAD_LEFT) // OCTET STRING (32 bytes)
             . "\xa0\x0a"                    //   [0] EXPLICIT parameters
             . "\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07"; // OID prime256v1

        return "-----BEGIN EC PRIVATE KEY-----\n"
             . chunk_split(base64_encode($der), 64, "\n")
             . "-----END EC PRIVATE KEY-----\n";
    }

    /**
     * Convert DER-encoded ECDSA signature to raw r||s (64 bytes).
     */
    private function derToJwtSignature(string $der): string
    {
        // DER: SEQUENCE { INTEGER r, INTEGER s }
        $offset = 2; // skip SEQUENCE tag + length
        // r
        $offset++;   // skip INTEGER tag
        $rLen    = ord($der[$offset++]);
        $r       = substr($der, $offset, $rLen);
        $offset += $rLen;
        // s
        $offset++;   // skip INTEGER tag
        $sLen    = ord($der[$offset++]);
        $s       = substr($der, $offset, $sLen);

        // Trim or pad to 32 bytes
        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");
        return str_pad($r, 32, "\x00", STR_PAD_LEFT)
             . str_pad($s, 32, "\x00", STR_PAD_LEFT);
    }

    // ── HTTP delivery ─────────────────────────────────────────────────

    private function postPush(
        string $endpoint,
        string $body,
        string $salt,
        string $serverPubKey,
        string $vapidAuth
    ): bool {
        $response = cnd_http_request('POST', $endpoint, $body, [
            'Authorization: '   . $vapidAuth,
            'Content-Type: application/octet-stream',
            'Content-Encoding: aes128gcm',
            'TTL: 86400',
        ]);

        if ($response->code === 410) {
            log_message('info', '[WebPushService] Subscription expired (410): ' . $endpoint);
            return false;
        }

        if ($response->code >= 200 && $response->code < 300) {
            return true;
        }

        log_message('warning', '[WebPushService] Push HTTP ' . $response->code . ': ' . substr($response->body, 0, 200));
        return false;
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public static function base64urlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64urlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
}
