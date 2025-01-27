<?php


namespace App\Http\Controllers\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class AgentBoardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index()
    {
        
        return view('board.agent.index');
    }
}
