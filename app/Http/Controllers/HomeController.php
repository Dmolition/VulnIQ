<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
 
    public function index()
    {
        return view('home.index'); // This loads resources/views/home/index.blade.php
    }


}
