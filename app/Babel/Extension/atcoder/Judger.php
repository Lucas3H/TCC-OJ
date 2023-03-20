<?php
namespace App\Babel\Extension\atcoder;

use App\Babel\Submit\Curl;
use App\Models\Submission\SubmissionModel;
use App\Models\JudgerModel;
use KubAT\PhpSimple\HtmlDomParser;
use Requests;
use Exception;
use Log;

class Judger extends Curl
{

    public $verdict = [
        'AC' => 'Accepted',
        'WA' => 'Wrong Answer',
        'TLE' => 'Time Limit Exceed',
        'MLE' => 'Memory Limit Exceed',
        'RE' => 'Runtime Error',
        'CE' => 'Compile Error',
        'QLE' => 'Judge Error', // I didn't found its description
        'OLE' => 'Output Limit Exceeded',
        'IE' => 'Judge Error', // Internal Error: There is an internal error within the Judging System.
        // 'WJ' => 'Waiting for Judging',
        // 'WR' => 'Waiting for Re-judging',
        // 'Judging' => 'Judging',
    ];


    public function __construct()
    {
        $this->submissionModel = new SubmissionModel();
    }

    public function judge($row)
    {
        $sub=[];

        $remoteId = explode('/', $row['remote_id']);
        $dom = HtmlDomParser::file_get_html("https://atcoder.jp/contests/$remoteId[0]/submissions/$remoteId[1]?lang=en", false, null, 0, -1, true, true, DEFAULT_TARGET_CHARSET, false);

        foreach ($dom->find('table', 0)->find('th') as $th) {
            switch ($th->innertext) {
                case 'Status':
                    $verdict = $th->next_sibling()->children[0]->innertext;
                    if (!isset($this->verdict[$verdict])) return;
                    $sub['verdict'] = $this->verdict[$verdict];
                    $sub['score'] = $verdict == 'AC' ? 1 : 0;
                    break;
                case 'Exec Time':
                    if (!preg_match('/^(\d+) ms$/', $th->next_sibling()->innertext, $match)) throw new Exception('time format error');
                    $sub['time'] = $match[1];
                    break;
                case 'Memory':
                    if (!preg_match('/^(\d+) KB$/', $th->next_sibling()->innertext, $match)) throw new Exception('memory format error');
                    $sub['memory'] = $match[1];
                    break;
            }
        }

        if (!is_null($compileInfo = $dom->find('pre', 1))) {
            $sub['compile_info'] = html_entity_decode($compileInfo->innertext);
        }

        $this->submissionModel->updateSubmission($row['sid'], $sub);
    }
}
