<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\scan_results;
use App\Models\Scans;
use App\Jobs\startScanJob;
use App\Jobs\startTlsScanJob;
use App\Jobs\StartFullScanJob;

class AdminController extends Controller
{
    public function index()
    {

        if(Auth::id())
        {

            $usertype = Auth()->user()->usertype;

            if($usertype=='user')
            {
                return view('home.index');
            }

            else if($usertype=='admin')
            {
                return view('admin.index');
            }

            else 
            {
                return redirect()->back();
            }



        }
    }

    public function home()
    {
        return view('home.index');

    }

    public function create_scan()
    {
        return view('admin.create_scan');
    }

    public function add_scan(Request $request)
    {
        // Accessing the data via $request->query() for GET method
        $data = new Scans();
    
        $data->scan_title = $request->query('title'); // Using query to access GET parameters
        $data->description = $request->query('description');
        $data->scan_type = $request->query('scan_type');
        $data->target = $request->query('target');
        $file=$request->query('upload_target');

        
        // Note: file uploads will not work with GET method, so we are not handling file uploads here.
    
        // Save the data to the database
        $data->save();
    
        // Redirect or return response after saving
        return redirect()->back()->with('success', 'Scan added successfully.');
    }
    
    

    public function display_scan()
    {

        $data = Scans::all();
        return view('admin.display_scan', compact('data'));


    }

    public function scan_delete($id)
    {

       $data = Scans::find($id);
       $data->delete();
       return redirect()->back();


    }

 
    //public function scan_start($id)
    //{
        
        //$scanJob = new startScanJob();
        //$this->dispatch($scanJob);

          // Dispatch the job with the required argument
          //startScanJob::dispatch($id);

          // Return a response indicating the scan has been queued
          //return redirect()->back()->with('success', 'Scan has been queued.');

    //}

    public function scan_start($id)
{
    $scan = Scans::find($id);

    if (!$scan) {
        return redirect()->back()->with('error', 'Scan not found.');
    }

    switch ($scan->scan_type) {
        case 'quick':
            startScanJob::dispatch($id);
            break;

        case 'tls':
            startTlsScanJob::dispatch($id);
            break;

        case 'full':
            StartFullScanJob::dispatch($id);
            break;

        case 'ai':
            // Example: startAiScanJob::dispatch($id);
            break;

        default:
            return redirect()->back()->with('error', 'Invalid scan type.');
    }

    return redirect()->back()->with('success', 'Scan has been queued.');
}
    
    public function scan_detail()
    {
        return view('admin.scan_detail');
    }

    public function brute_force()
    {
        return view('admin.brute_force');
    }

    public function dictionary_attack()
    {
        return view('admin.dictionary_attack');
    }

    public function sql_injection()
    {
        return view('admin.sql_injection');
    }

    public function xss()
    {
        return view('admin.xss');
    }


    public function simulate_xss(Request $request)
{
    $input = $request->input('user_input');

    // WARNING: Do NOT do this in a real application
    // This is for simulation/demo only
    return back()->with('result', $input);
}


public function antivirus_game()
{
    return view('admin.antivirus_game');
}


public function simulateSqlmap(Request $request)
{
    $url = escapeshellarg($request->input('target_url'));
    $options = escapeshellcmd($request->input('options'));

    // Example path – adjust to where sqlmap.py actually is on your system
    $sqlmapPath = 'C:\sqlmap\sqlmap.py';  // Use double backslashes \\ or forward slashes /
    $sqlmapPath = str_replace('\\', '/', $sqlmapPath); // optional: normalize to forward slashes

    // Build command
    $command = "python $sqlmapPath -u $url $options 2>&1";

    // Execute
    $output = shell_exec($command);

    return redirect()->back()->with('output', $output);
}

public function adminShell()
{
    return view('admin.shell');
}

public function executeShellCommand(Request $request)
{
    $command = $request->input('command');

    // !!! CRITICAL SECURITY: STRICT COMMAND WHITELISTING AND ARGUMENT SANITIZATION !!!
    $allowedCommands = [
        'nmap',
        'nikto',
        'ping',
        'traceroute',
        'whois',
        // Add ONLY the commands you absolutely need and understand the risks of.
    ];

    $parts = explode(' ', trim($command));
    $baseCommand = escapeshellcmd($parts[0]); // Escape the base command

    if (!in_array($baseCommand, $allowedCommands)) {
        Log::warning('Admin Shell: Unauthorized command attempted by user ' . Auth::id() . ': ' . $command);
        return response()->json(['output' => 'Command not allowed.'], 400);
    }

    // !!! CRITICAL SECURITY: ARGUMENT SANITIZATION (VERY BASIC EXAMPLE - NEEDS IMPROVEMENT) !!!
    $sanitizedArguments = '';
    for ($i = 1; $i < count($parts); $i++) {
        $sanitizedArguments .= escapeshellarg($parts[$i]) . ' ';
    }

    $fullCommand = $baseCommand . ' ' . trim($sanitizedArguments);

    // !!! SECURITY: LOGGING ALL EXECUTED COMMANDS !!!
    Log::info('Admin Shell: User ' . Auth::id() . ' executed command: ' . $fullCommand);

    $process = Process::fromShellCommandline($fullCommand);

    // !!! SECURITY: TIMEOUT TO PREVENT HANGING !!!
    $process->setTimeout(300);

    try {
        $process->run();

        if (!$process->isSuccessful()) {
            Log::error('Admin Shell: Command failed for user ' . Auth::id() . ': ' . $fullCommand . "\n" . $process->getErrorOutput());
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        return response()->json(['output' => $output]);

    } catch (ProcessFailedException $e) {
        Log::error('Admin Shell: Exception during command execution for user ' . Auth::id() . ': ' . $fullCommand . "\n" . $e->getMessage());
        return response()->json(['output' => $e->getMessage()], 500);
    }
}





}













