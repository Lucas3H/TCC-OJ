<?php
namespace App\Http\Controllers;

use App\Models\Eloquent\Dojo\DojoPhase;
use App\Models\Eloquent\Dojo\Dojo;
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
            foreach($p->dojos->sortBy('order') as $dojo) {
                $dojo->transformMarkdown();
            }
        }

        return view('dojo.index', [
            'page_title' => "Cursos",
            'site_title' => config("app.name"),
            'navigation' => "Cursos",
            'phases' => $phases,
            'status' => $status,
        ]);
    }
}
