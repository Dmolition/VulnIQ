<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\scan_results;
use App\Models\Scans;

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


    public function scan_start($id)
    {
        // Fetch the scan data from the database using the given $id
        $data = Scans::find($id);
    
        // Check if scan data exists
        if (!$data) {
            return redirect()->back()->with('error', 'Scan not found.');
        }
    
        // Retrieve the target IP address from the database (column 'target')
        $targetIp = $data->target; // Using the 'target' column instead of 'target_ip'
    
        // Sanitize the target IP to avoid security issues
        $targetIp = escapeshellarg($targetIp);
    
        // Nmap command to run the scan (you can customize the scan options as needed)
        $command = '"C:\\Program Files (x86)\\Nmap\\nmap.exe" -sT ' . $targetIp . ' 2>&1'; // Capture stderr as well


        // Execute the Nmap scan
        $output = null;
        $resultCode = null;
    
        // Execute the command and ensure it waits for completion
        exec($command, $output, $resultCode);
    
        // Debugging: Log the result code and output
        \Log::info("Nmap Result Code: $resultCode");
        \Log::info("Nmap Output: " . implode("\n", $output));
    
        // Check if the Nmap command was successful
        if ($resultCode === 0) {
            // Process the Nmap output if necessary
            return view('admin.scan_detail', compact('output'));
        } else {
            // If the scan failed, return an error message
            return redirect()->back()->with('error', 'Failed to start Nmap scan.');
        }
 

    }



    

    public function scan_detail()
    {
        return view('admin.scan_detail');
    }

}

