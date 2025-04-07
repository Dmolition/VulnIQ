<?php

namespace App\Jobs;

use App\Models\Scans;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class startScanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scanId;
    public $tries = 5;  // Set the maximum retry attempts to 5
    public $timeout = 300;  // Set the timeout for the job to 5 minutes (300 seconds)
    
    public function __construct($scanId)
    {
        $this->scanId = $scanId;
    }

    public function handle()
    {
        try {
            // Log the start of the job processing
            Log::info("ScanStartJob processing for Scan ID: " . $this->scanId);

            // Fetch the scan data from the database using the given $scanId
            $scanData = Scans::find($this->scanId);
            if (!$scanData) {
                Log::error("Scan not found with ID: " . $this->scanId);
                return;
            }

            $targetIp = $scanData->target;
            // Log the target IP address before sanitizing
            Log::info("Target IP: " . $targetIp);

            // Build the command to run the scan using nmap
            $command = '"C:\\Program Files (x86)\\Nmap\\nmap.exe" -A ' . $targetIp;
            Log::info("Executing command: $command");

            // Use Laravel's Process class to execute the command
            $process = Process::fromShellCommandline($command);
            $process->run();

            // Check if the command was successful
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            // Log the output of the command
            Log::info("Command output: " . $process->getOutput());

            // Log successful completion of the scan
            Log::info("Scan completed successfully for Target IP: " . $targetIp);

        } catch (\Exception $e) {
            // Log any errors or exceptions that occur
            Log::error("Error in ScanStartJob: " . $e->getMessage());
            Log::error($e->getTraceAsString());  // Log the stack trace for better debugging
        }
    }

    // Optionally set the retry delay between attempts
    public function retryAfter()
    {
        return 60;  // Retry the job after 60 seconds
    }
}

