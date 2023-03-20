<?php
namespace App\Babel\Extension\atcoder;

use App\Babel\Submit\Curl;
use App\Models\CompilerModel;
use App\Models\ProblemModel;
use App\Models\JudgerModel;
use App\Models\OJModel;
use KubAT\PhpSimple\HtmlDomParser;
use Illuminate\Support\Facades\Validator;
use Exception;
use Requests;

class Submitter extends Curl
{
    protected $sub;
    public $post_data=[];
    protected $oid;
    protected $selectedJudger;

    public function __construct(& $sub, $all_data)
    {
        $this->sub=& $sub;
        $this->post_data=$all_data;
        $judger=new JudgerModel();
        $this->oid=OJModel::oid('atcoder');
        if(is_null($this->oid)) {
            throw new Exception("Online Judge Not Found");
        }
        $judger_list=$judger->list($this->oid);
        $this->selectedJudger=$judger_list[array_rand($judger_list)];
    }

    private function _login()
    {
        /* // Version 1
        $cookieFile = babel_path('Cookies/atcoder_' . $this->selectedJudger['handle'] . '.cookie');
        if (!file_exists($cookieFile) || !preg_match('/(\d+)\t_user_name\t(.*)/', file_get_contents($cookieFile), $match) || $match[1] < time() - 60 || $match[2] == 'deleted') {
            $this->login([
                'url' => 'https://' . $this->problem['contest_id'] . '.contest.atcoder.jp/login',
                'data' => http_build_query([
                    'name' => $this->selectedJudger["handle"],
                    'password' => $this->selectedJudger["password"],
                ]),
                'oj' => 'atcoder',
                'handle' => $this->selectedJudger['handle'],
            ]);
        }
        // */
        $response = $this->grab_page([
            'site' => 'https://atcoder.jp/login',
            'oj' => 'atcoder',
            'handle' => $this->selectedJudger['handle'],
        ]);
        preg_match('/name="csrf_token" value="(.*?)"/', $response, $match);
        $this->csrfToken = str_replace('&#43;', '+', $match[1]);
        if (preg_match('/userScreenName = ""/', $response)) {
            $this->login([
                'url' => 'https://atcoder.jp/login',
                'data' => http_build_query([
                    'username' => $this->selectedJudger['handle'],
                    'password' => $this->selectedJudger['password'],
                    'csrf_token' => $this->csrfToken,
                ]),
                'oj' => 'atcoder',
                'handle' => $this->selectedJudger['handle'],
                'ret' => true,
            ]);
        }
    }

    private function formatSolution($solution)
    {
        return trim(str_replace("\r", "\n", str_replace("\r\n", "\n", $solution)));
    }

    private function _submit()
    {
        $solution = $this->formatSolution($this->post_data["solution"]);
        $problem = $this->problem;
        $compiler = new CompilerModel();
        $response = $this->post_data([
            'site' => "https://atcoder.jp/contests/$problem[contest_id]/submit?lang=en",
            'data' => http_build_query([
                'data.TaskScreenName' => $problem['index_id'],
                'data.LanguageId' => $compiler->detail($this->post_data['coid'])['lcode'],
                'sourceCode' => $solution,
                'csrf_token' => $this->csrfToken,
            ]),
            'oj' => 'atcoder',
            'ret' => true,
            'follow' => true,
            'handle' => $this->selectedJudger['handle'],
        ]);

        if (strpos($response, '<title>My Submissions') !== false) {
            $sha1 = sha1($solution);
            preg_match_all('/submissions\/(\d+)/', $response, $matches);
            foreach ($matches[1] as $remoteId) {
                $dom = HtmlDomParser::str_get_html($this->grab_page([
                    'site' => "https://atcoder.jp/contests/$problem[contest_id]/submissions/$remoteId",
                    'oj' => 'atcoder',
                    'handle' => $this->selectedJudger['handle'],
                ]), true, true, DEFAULT_TARGET_CHARSET, false);
                if (sha1($this->formatSolution(html_entity_decode($dom->getElementById('submission-code')->innertext))) === $sha1) {
                    $this->sub['remote_id'] = $problem['contest_id'] . '/' . $remoteId;
                    // Longest contest id is 'Recruit-Programing-contest-practice', $contest_id/submissions/$remoteId may longer than 50
                    return;
                }
            }
        }
        sleep(1);
        throw new Exception('Submission Error');
    }

    public function submit()
    {
        $validator=Validator::make($this->post_data, [
            'pid' => 'required|integer',
            'coid' => 'required|integer',
            'solution' => 'required',
        ]);

        if ($validator->fails()) {
            $this->sub['verdict']="System Error";
            return;
        }

        $problem = new ProblemModel();
        $this->problem = $problem->basic($this->post_data['pid']);

        $this->_login();
        $this->_submit();
    }
}
