<?php
namespace App\Jobs;

use App\Models\Scans;

use Illuminate\Bus\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;

class StartFullScanJob implements ShouldQueue

{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $scanId;

    public function __construct($scanId)

    {

        $this->scanId = $scanId;

    }

    public function handle()

    {

        Log::info("StartFullScanJob triggered for scan ID: {$this->scanId}");

        $scan = Scans::find($this->scanId);

        if (!$scan) {

            Log::error("Scan with ID {$this->scanId} not found.");

            return;

        }

        $target = $scan->target;

        Log::info("Target resolved: $target");

        // Run Nmap (Command execution using docker)

        $nmapOutput = shell_exec("docker exec scanner-container nmap -v $target");

        if ($nmapOutput) {

            Log::info("Nmap scan completed.");

        } else {

            Log::error("Nmap scan failed or returned no output.");

        }

        // Run Nikto (Command execution using docker)

        $niktoCommand = escapeshellcmd("docker exec scanner-container nikto -h $target -p 8080");

        Log::info("Running Nikto command: $niktoCommand");

        $niktoOutput = shell_exec($niktoCommand);

        if ($niktoOutput) {

            Log::info("Nikto scan completed.");

        } else {

            Log::error("Nikto scan failed or returned no output.");

        }

        // Save raw results to database

        $scan->nmap_results = $nmapOutput;

        $scan->nikto_results = $niktoOutput;

        $scan->status = 'completed'; // Mark scan as completed

        $scan->save();

        Log::info("Scan results saved to database.");

        // Combine results for the final report

        $combinedResults = "Nmap Results:\n" . $nmapOutput . "\n\nNikto Results:\n" . $niktoOutput;

        // Define the folder path for saving the report

        $folderPath = public_path('admin/scans');  // Correct directory for public access

        File::ensureDirectoryExists($folderPath);   // Ensure directory exists

        // Define a unique file name for the scan report

    // Define the unique part of the file name
    $uniqueSuffix = Str::uuid(); // Generate a new unique ID every time

    // Define the file name using the same scanId, but with a unique suffix
    $fileName = 'scan_' . $this->scanId . '_' . $uniqueSuffix . '_report.txt';

        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        Log::info("Final scan report path: $filePath");

        Log::info("Combined scan content: " . substr($combinedResults, 0, 500)); // Show just first 500 chars for preview

        try {

            // Save the combined results to the file

            File::put($filePath, $combinedResults);

            Log::info("Scan report file created: $fileName");

            // Update the scan record in the database with the file name

            $scan->file = $fileName;  // Save file name to database

            $scan->save();

            Log::info("File name saved to database.");

        } catch (\Exception $e) {

            Log::error("Error saving scan report file: " . $e->getMessage());

        }

        Log::info("End of handle function, before returning.");

    }

}


