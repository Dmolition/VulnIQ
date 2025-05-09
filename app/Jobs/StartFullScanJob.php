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
use Symfony\Component\Process\Process;
use App\Services\ScanResultsService;

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

        // Retrieve scan information from the database
        $scan = Scans::find($this->scanId);
        if (!$scan) {
            Log::error("Scan with ID {$this->scanId} not found.");
            return;
        }

        $target = $scan->target;
        Log::info("Target resolved: $target");

        // Run Nmap scan using Docker
        $nmapOutput = shell_exec("docker exec scanner-container nmap -v $target");
        if ($nmapOutput) {
            Log::info("Nmap scan completed.");
        } else {
            Log::error("Nmap scan failed or returned no output.");
        }

        // Run Nikto scan using Docker
        $niktoCommand = "docker exec scanner-container timeout 30s nikto -h $target -p 8080";
        Log::info("Running Nikto command: $niktoCommand");
        $niktoOutput = shell_exec($niktoCommand);
        if ($niktoOutput) {
            Log::info("Nikto scan completed.");
        } else {
            Log::error("Nikto scan failed or returned no output.");
        }

// // Run Nessus scan
// $process = new Process(['bash', base_path('run-nessus-scan.sh'), $target]);
// $process->run();

// if (!$process->isSuccessful()) {
//     throw new \RuntimeException($process->getErrorOutput());
// }

// // Get the scan ID from the output
// $scanId = $process->getOutput(); // The scan ID returned from the script
// $scanId = trim($scanId); // Remove any surrounding whitespace or newline characters

// if (empty($scanId)) {
//     Log::error("Failed to get a valid Nessus scan ID.");
//     return;
// }

// Log::info("Started Nessus scan with ID: $scanId");

// // Check Nessus scan status and wait for completion
// $scanStatus = $this->checkScanStatus($scanId);
// while ($scanStatus !== 'completed') {
//     sleep(30); // Sleep for 30 seconds before checking again
//     $scanStatus = $this->checkScanStatus($scanId);
// }

// Log::info("Nessus scan completed!");

// // Download the scan results
// $scanResultsPath = $this->downloadScanResults($scanId);
// if ($scanResultsPath) {
//     $scan->nessus_results = file_get_contents($scanResultsPath);
//     Log::info("Nessus results downloaded.");
// } else {
//     Log::error("Failed to download Nessus scan results.");
// }


// Inject the service or resolve it manually
$service = app(ScanResultsService::class);

// Read and format Nessus CSV
$csvPath = '/home/taif/Downloads/weak_ip_scan_c9nmnh.csv';
if (!empty($csvPath) && file_exists($csvPath)) {
Log::info("CSV file exists at: {$csvPath}");

try {

$nessusData = $service->readNessusCSV($csvPath);
if (empty($nessusData)) {
    Log::error("Nessus data is empty!");
} else {
    Log::debug("Nessus data loaded successfully.");
}

Log::debug("Raw Nessus Data: ", ['data' => $nessusData]);


// Log the raw Nessus data after reading the CSV
Log::debug("Raw Nessus Data:", $nessusData);

$formattedNessus = '';
foreach ($nessusData as $entry) {
    // Log individual entry details
    if (empty($entry)) {
        Log::warning("Empty entry detected!");
    } else {
        Log::debug("Processing entry: ", $entry);

        // Check if the required keys exist before using them
        $host = isset($entry['Host']) ? $entry['Host'] : 'N/A';
        $port = isset($entry['Port']) ? $entry['Port'] : 'N/A';
        $protocol = isset($entry['Protocol']) ? $entry['Protocol'] : 'N/A';
        $severity = isset($entry['Severity']) ? $entry['Severity'] : 'N/A';
        $pluginName = isset($entry['Plugin Name']) ? $entry['Plugin Name'] : 'N/A';
        $description = isset($entry['Description']) ? $entry['Description'] : 'N/A';

        $formattedNessus .= "Host: {$host}\n";
        $formattedNessus .= "Port: {$port}\n";
        $formattedNessus .= "Protocol: {$protocol}\n";
        $formattedNessus .= "Severity: {$severity}\n";
        $formattedNessus .= "Plugin Name: {$pluginName}\n";
        $formattedNessus .= "Description: {$description}\n";
        $formattedNessus .= "-----------------------------\n";
    }
}

// Log the formatted Nessus data to check if it's properly built
Log::debug("Formatted Nessus Results: \n{$formattedNessus}");



} catch (\Throwable $e) {
    Log::error("Exception while processing Nessus CSV: " . $e->getMessage());
}
} else {
    Log::info("No valid Nessus CSV path provided. Skipping Nessus scan processing.");
}

// Save the scan results to the database
$scan->nmap_results = $nmapOutput;
$scan->nikto_results = $niktoOutput;
$scan->status = 'completed'; // Mark scan as completed
$scan->save();

      
Log::info("Scan results saved to database.");

     // Combine results for the final report
$combinedResults = "Nmap Results:\n" . $nmapOutput .
                   "\n\nNikto Results:\n" . $niktoOutput;

if (!empty($formattedNessus)) {
    $combinedResults .= "\n\nNessus Results:\n" . $formattedNessus;
}

Log::debug("Combined Results for Report: \n{$combinedResults}");

        // Define the folder path for saving the report
        $folderPath = public_path('admin/scans');
        File::ensureDirectoryExists($folderPath);

        // Define a unique file name for the scan report
        $uniqueSuffix = Str::uuid();
        $fileName = 'scan_' . $this->scanId . '_' . $uniqueSuffix . '_report.txt';
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $fileName;

        Log::info("Final scan report path: $filePath");
        Log::info("Combined scan content: " . substr($combinedResults, 0, 500)); // Show just first 500 chars for preview

        try {
            // Save the combined results to the file
            File::put($filePath, $combinedResults);
            Log::info("Scan report file created: $fileName");

            // Update the scan record in the database with the file name
            $scan->file = $fileName;
            $scan->save();
            Log::info("File name saved to database.");
        } catch (\Exception $e) {
            Log::error("Error saving scan report file: " . $e->getMessage());
        }

        Log::info("End of handle function, before returning.");
    }

     // // Check the status of the Nessus scan
// private function checkScanStatus($scanId)
// {
//     $accessKey = env('NESSUS_ACCESS_KEY');
//     $secretKey = env('NESSUS_SECRET_KEY');
//     $nessusUrl = env('NESSUS_URL');

//     $process = new Process([
//         'curl', 
//         '-k', 
//         '-H', "X-ApiKeys: accessKey=$accessKey; secretKey=$secretKey", 
//         "$nessusUrl/scans/$scanId"
//     ]);
//     $process->run();

//     if (!$process->isSuccessful()) {
//         throw new \RuntimeException($process->getErrorOutput());
//     }

//     $scanData = json_decode($process->getOutput(), true);
//     return $scanData['info']['status'] ?? 'unknown';
// }

// // Download the results of the Nessus scan
// private function downloadScanResults($scanId)
// {
//     $accessKey = env('NESSUS_ACCESS_KEY');
//     $secretKey = env('NESSUS_SECRET_KEY');
//     $nessusUrl = env('NESSUS_URL');

//     $process = new Process([
//         'curl',
//         '-k',
//         '-H', "X-ApiKeys: accessKey=$accessKey; secretKey=$secretKey",
//         "$nessusUrl/scans/$scanId/export"
//     ]);
//     $process->run();

//     if (!$process->isSuccessful()) {
//         throw new \RuntimeException($process->getErrorOutput());
//     }

//     $exportData = json_decode($process->getOutput(), true);
//     $fileId = $exportData['file'] ?? null;

//     if ($fileId) {
//         $downloadProcess = new Process([
//             'curl',
//             '-k',
//             '-o', base_path('storage/scan_results.nessus'),
//             '-H', "X-ApiKeys: accessKey=$accessKey; secretKey=$secretKey",
//             "$nessusUrl/scans/$scanId/export/$fileId/download"
//         ]);
//         $downloadProcess->run();
//         if (!$downloadProcess->isSuccessful()) {
//             throw new \RuntimeException($downloadProcess->getErrorOutput());
//         }

//         return base_path('storage/scan_results.nessus');
//     }

//     return null;
// }

//    }
}
