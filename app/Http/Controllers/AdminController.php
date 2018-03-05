<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth as Auth;

class AdminController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function index(Request $request){
      $level = Auth::user()->level;
      switch ($level) {
        case '0':
          //dev
          return $this->dashboardLevel1();
          break;

        case '1':
          //Kepala UPT TIK
          return $this->dashboardLevel1();
          break;

        case '2':
        //Direktur
          return $this->dashboardLevel2();
          break;
      }
    }

    protected function dashboardLevel1()
    {
      return view('admin.dashboard.index.admin1');
    }

    protected function dashboardLevel2()
    {
      return redirect()->route('olah_data');
    }
}
