<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\scan_results;
use App\Models\Scans;
use App\Jobs\startScanJob; 
use App\Jobs\startTlsScanJob;

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
            startScanJob::dispatch($id);
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

}











