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
use Illuminate\Support\Facades\File;


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
            Log::info("ScanStartJob processing for Scan ID: " . $this->scanId);
    
            $scanData = Scans::find($this->scanId);
            if (!$scanData) {
                Log::error("Scan not found with ID: " . $this->scanId);
                return;
            }
    
            $targetIp = $scanData->target;
            Log::info("Target IP: " . $targetIp);
    
            // Define a custom folder and file path
            $folderPath = "D:\senior_project\public\admin\scans"; // double backslashes for Windows path
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0755, true);
            }
    
            $fileName = "scan_{$this->scanId}_" . time() . ".txt";
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;
    
            // Build command
            $command = "\"C:\\Program Files (x86)\\Nmap\\nmap.exe\" -sT {$targetIp} -oN \"$filePath\"";
            Log::info("Executing command: $command");
    
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(300);
            $process->run();
    
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
    
            Log::info("Command output: " . $process->getOutput());
    
            // Save result info to DB
            $scanData->file = $fileName;
            $scanData->status = 'completed';
            $scanData->save();
    
            Log::info("Scan saved to: $filePath");
            Log::info("Scan completed successfully for Target IP: " . $targetIp);
    
        } catch (\Exception $e) {
            Log::error("Error in ScanStartJob: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
    


    // Optionally set the retry delay between attempts
    public function retryAfter()
    {
        return 60;  // Retry the job after 60 seconds
    }
}

