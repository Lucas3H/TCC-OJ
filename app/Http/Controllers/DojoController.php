<?php
namespace App\Http\Controllers;

use App\Models\Eloquent\Dojo\DojoPhase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class DojoController extends Controller
{
    /**
     * Show the Rank Page.
     *
     * @param Request $request your web request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $status = Auth::check() ? Auth::user()->getDojoStatistics() : false;
        $phases = DojoPhase::all()->sortBy('order');
        foreach($phases as $p) {
            $p->transformMarkdown();
        }
        return view('dojo.index', [
            'page_title' => "Dojo",
            'site_title' => config("app.name"),
            'navigation' => "Dojo",
            'phases' => $phases,
            'status' => $status,
        ]);
    }
}
