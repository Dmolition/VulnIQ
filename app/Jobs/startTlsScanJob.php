<?php

namespace App\Jobs;

use App\Models\Scans;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;

class startTlsScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scanId;

    public $timeout = 600; // Allow up to 10 minutes if needed

    public function __construct($scanId)
    {
        $this->scanId = $scanId;
    }

    public function handle()
    {
        try {
            $scan = Scans::find($this->scanId);
            if (!$scan) {
                Log::error("Scan not found with ID: " . $this->scanId);
                return;
            }

            $domain = $scan->target;
            Log::info("Starting SSL Labs TLS scan for: $domain");

            // Start the scan
            $response = Http::get("https://api.ssllabs.com/api/v3/analyze", [
                'host' => $domain,
                'publish' => 'off',
                'startNew' => 'on',
                'all' => 'done'
            ]);

            if (!$response->ok()) {
                Log::error("SSL Labs API error for $domain: " . $response->body());
                return;
            }

            // Poll until scan is ready
            do {
                sleep(15);
                $response = Http::get("https://api.ssllabs.com/api/v3/analyze", [
                    'host' => $domain,
                ]);
                $status = $response->json()['status'] ?? 'ERROR';
                Log::info("SSL Labs scan status: $status");
            } while (in_array($status, ['IN_PROGRESS', 'DNS']));

            // Save result to file
            $folderPath = "D:\\senior_project\\app\\scans";
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $fileName = "tls_ssllabs_{$this->scanId}_" . time() . ".json";
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

            File::put($filePath, $response->body());

            // Update DB
            $scan->file = $fileName;
            $scan->status = 'completed';
            $scan->save();

            Log::info("SSL Labs scan saved to: $filePath");

        } catch (\Exception $e) {
            Log::error("Error in startTlsScanJob: " . $e->getMessage());
        }
    }
}
