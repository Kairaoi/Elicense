<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('board.admin.index');
    }
}
