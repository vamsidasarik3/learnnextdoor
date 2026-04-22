<?php
 
namespace App\Controllers\Webhooks;
 
use App\Controllers\BaseController;
use App\Models\WhatsappLogModel;
 
class WhatsApp extends BaseController
{
    /**
     * Webhook endpoint for WhatsApp Business API
     * Handles both GET (verification) and POST (events)
     */
    public function index()
    {
        $method = $this->request->getMethod();
 
        if ($method === 'get') {
            return $this->handleVerification();
        }
 
        return $this->handleEvent();
    }
 
    /**
     * Verify the webhook with Meta (required once)
     */
    private function handleVerification()
    {
        $verifyToken = env('WHATSAPP_WEBHOOK_VERIFY_TOKEN');
        
        $mode      = $this->request->getGet('hub_mode');
        $token     = $this->request->getGet('hub_verify_token');
        $challenge = $this->request->getGet('hub_challenge');
 
        if ($mode === 'subscribe' && $token === $verifyToken) {
            log_message('info', '[WhatsAppWebhook] Verification successful.');
            return $this->response->setStatusCode(200)->setBody($challenge);
        }
 
        log_message('error', '[WhatsAppWebhook] Verification failed. Token mismatch.');
        return $this->response->setStatusCode(403);
    }
 
    /**
     * Handle incoming WhatsApp events (status updates)
     */
    private function handleEvent()
    {
        $payload = $this->request->getJSON(true);
        
        if (empty($payload)) {
            return $this->response->setStatusCode(200);
        }
 
        // Log the raw payload for debugging if needed
        log_message('debug', '[WhatsAppWebhook] Received payload: ' . json_encode($payload));
 
        $logModel = new WhatsappLogModel();
 
        // Iterate through entries and changes
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                
                // We only care about statuses in this context
                if (isset($value['statuses'])) {
                    foreach ($value['statuses'] as $statusUpdate) {
                        $wamid  = $statusUpdate['id'] ?? null;
                        $status = $statusUpdate['status'] ?? null;
                        
                        if (!$wamid || !$status) continue;
 
                        $error = null;
                        if (isset($statusUpdate['errors'])) {
                            $error = json_encode($statusUpdate['errors']);
                        }
 
                        // Update existing log or create new one
                        $existing = $logModel->where('wa_message_id', $wamid)->first();
                        
                        if ($existing) {
                            $logModel->update($existing->id, [
                                'status'        => $status,
                                'error_message' => $error,
                                'raw_payload'   => json_encode($payload)
                            ]);
                        } else {
                            $logModel->insert([
                                'wa_message_id' => $wamid,
                                'recipient_id'  => $statusUpdate['recipient_id'] ?? null,
                                'status'        => $status,
                                'error_message' => $error,
                                'raw_payload'   => json_encode($payload)
                            ]);
                        }
                        
                        log_message('info', "[WhatsAppWebhook] Updated message $wamid to status: $status");
                    }
                }
            }
        }
 
        return $this->response->setStatusCode(200);
    }
}
