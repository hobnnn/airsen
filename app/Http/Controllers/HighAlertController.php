<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HighAlertController extends Controller
{
    public function index()
    {
        // You can pass alert data to the view here later
        return view('highalert');
    }
}