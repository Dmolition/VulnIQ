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
    public $timeout = 600; // 10 minutes max

    public function __construct($scanId)
    {
        $this->scanId = $scanId;
    }

    public function handle()
    {
        try {
            $scan = Scans::find($this->scanId);
            if (!$scan) {
                Log::error("Scan not found with ID: {$this->scanId}");
                return;
            }

            $domain = $scan->target;
            Log::info("Starting SSL Labs TLS scan for: {$domain}");

            $startResponse = Http::get("https://api.ssllabs.com/api/v3/analyze", [
                'host' => $domain,
                'publish' => 'off',
                'startNew' => 'on',
                'all' => 'done',
            ]);

            if (!$startResponse->ok()) {
                Log::error("Initial request to SSL Labs API failed: " . $startResponse->body());
                return;
            }

            $startTime = time();
            $status = 'IN_PROGRESS';

            do {
                sleep(15); // Respect SSL Labs' polling interval

                $pollResponse = Http::get("https://api.ssllabs.com/api/v3/analyze", [
                    'host' => $domain,
                ]);

                if (!$pollResponse->ok()) {
                    Log::error("Polling failed for $domain: " . $pollResponse->body());
                    return;
                }

                $json = $pollResponse->json();
                $status = $json['status'] ?? 'UNKNOWN';
                $statusMessage = $json['statusMessage'] ?? 'No statusMessage';

                Log::info("[$domain] Scan status: $status - $statusMessage");

                if (in_array($status, ['ERROR', 'FAILED'])) {
                    Log::error("TLS scan failed for $domain with status: $status - $statusMessage");
                    return;
                }

                if ((time() - $startTime) > $this->timeout) {
                    Log::error("TLS scan for $domain timed out after {$this->timeout} seconds");
                    return;
                }

            } while ($status === 'IN_PROGRESS' || $status === 'DNS');

            // Save results
            $folderPath = "D:\senior_project\public\admin\scans";
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }

            $fileName = "tls_ssllabs_{$this->scanId}_" . time() . ".json";
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

            File::put($filePath, json_encode($json, JSON_PRETTY_PRINT));

            // Update DB
            $scan->file = $fileName;
            $scan->status = 'completed';
            $scan->save();

            Log::info("Scan for $domain completed. Result saved to: $filePath");

        } catch (\Exception $e) {
            Log::error("Exception in startTlsScanJob: " . $e->getMessage());
        }
    }
}
