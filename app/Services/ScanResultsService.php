<?php 
namespace App\Services;

use Illuminate\Support\Facades\Log;

class ScanResultsService
{
    public function readNessusCSV($filePath)
    {
        Log::info("readNessusCSV called with path: {$filePath}");

        $results = [];
        $fullFilePath = $filePath;

        if (($handle = fopen($fullFilePath, "r")) !== false) {
            $header = fgetcsv($handle);
            Log::debug("CSV Header: ", $header); // Log the header to check the field names

            while (($row = fgetcsv($handle)) !== false) {
                $rowData = array_combine($header, $row);

                // Check if "Severity" key exists before using it
                if (!isset($rowData['Severity'])) {
                    Log::warning("Severity key is missing in this row. Data: " . json_encode($rowData));
                    // Optionally, you can set a default value like "Unknown" or "None"
                    $rowData['Severity'] = 'Unknown';
                }

                $results[] = $rowData;
                Log::debug("Row Data: ", $rowData); // Log each row to verify the content
            }

            fclose($handle);
        } else {
            Log::error("Unable to open the file: {$filePath}"); // Log error if the file can't be opened
        }

        return $results;
    }

    public function convertNessusToJson(array $nessusData): string
    {
        // Log the raw data before processing it
        Log::debug("Original Nessus Data: ", $nessusData);

        // Process empty or "n/a" fields to null if desired
        foreach ($nessusData as &$row) {
            foreach ($row as $key => $value) {
                if ($value === "n/a" || $value === "") {
                    $row[$key] = null; // or you could unset($row[$key]) to remove it entirely
                }
            }
        }

        // Log the processed data
        Log::debug("Processed Nessus Data: ", $nessusData);

        return json_encode($nessusData, JSON_PRETTY_PRINT);
    }
}
