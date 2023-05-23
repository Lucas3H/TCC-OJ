<?php
namespace App\Babel\Extension\atcoder;

use App\Babel\Crawl\CrawlerBase;
use App\Babel\Submit\Curl;
use App\Models\CompilerModel;
use App\Models\ProblemModel;
use App\Models\JudgerModel;
use App\Models\OJModel;
use KubAT\PhpSimple\HtmlDomParser;
use Requests;
use Exception;

class Crawler extends CrawlerBase
{
    public $oid = null;
    private $availCompilers;
    private $downloaded = []; // Download same img or pdf once in one crawl task
    /**
     * Initial
     *
     * @return Response
     */
    public function start($conf)
    {
        $action = isset($conf["action"]) ? $conf["action"] : 'crawl_problem';
        $con = isset($conf["con"]) ? $conf["con"] : 'all';
        $cached = isset($conf["cached"]) ? $conf["cached"] : false;
        $this->oid = OJModel::oid('atcoder');

        if (is_null($this->oid)) {
            throw new Exception("Online Judge Not Found");
        }

        if ($action == 'judge_level') {
            $this->judge_level();
        } else {
            $judger = new JudgerModel();
            $judger_list = $judger->list($this->oid);
            if (!count($judger_list)) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>Crawler requires at least one judger.</>\n");
                return;
            }
            $this->judger = $judger_list[array_rand($judger_list)];
            $this->curl = new Curl();

            $compiler = new CompilerModel();
            $this->availCompilers = [];
            foreach ($compiler->list($this->oid) as $compiler) {
                $this->availCompilers[$compiler['lcode']] = $compiler['coid'];
            }

            $this->crawl($con, $action == 'update_problem');
        }
    }

    public function judge_level()
    {
        // TODO
    }

    private function mkdirs($path)
    {
        if ($path[strlen($path) - 1] != '/') $path .= '/';
        $pos = strpos($path, '/');
        while ($pos !== false) {
            if (!file_exists(substr($path, 0, $pos))) {
                mkdir(substr($path, 0, $pos), 0755);
            }
            $pos = strpos($path, '/', $pos + 1);
        }
    }

    private function grab_page($url, $retry = 0, $total = 3)
    {
        if ($retry < $total) {
            try {
                return $this->curl->grab_page([
                    'site' => $url,
                    'oj' => 'atcoder',
                    'follow' => true,
                ]);
            } catch (\Throwable $e) {
                ++$retry;
                $this->line("<fg=red>Retrying ($retry/$total)</>");
                return $this->grab_page($url, $retry, $total);
            }
        }
        return $this->curl->grab_page([
            'site' => $url,
            'oj' => 'atcoder',
        ]);
    }

    private function login($csrf)
    {
        $this->curl->login([
            'url' => "https://atcoder.jp/login",
            'data' => http_build_query([
                'username' => $this->judger['handle'],
                'password' => $this->judger['password'],
                'csrf_token' => $csrf,
            ]),
            'oj' => 'atcoder',
        ]);
    }

    private function getUrlByPage($page, $path)
    {
        if (preg_match('/^\w+:/', $path)) return $path;
        $slash = strpos($page, '/', strpos($page, '://') + 3);
        if ($slash === false) $domain = $page;
        else $domain = substr($page, 0, $slash);
        if ($path[0] == '/') {
            if ($path[1] == '/') return substr($page, 0, strpos($page, ':') + 1) . $path;
            return $domain . $path;
        }
        $hash1 = strpos($page, '#');
        $hash2 = strpos($page, '?');
        $hash = min($hash1 === false ? 999 : $hash1, $hash2 === false ? 999 : $hash2);
        if ($hash === 999) $hash = -1;
        else $hash = strlen($page) - $hash - 1;
        return ($slash === false ? $page . '/' : substr($page, 0, $pos = strrpos($page, '/', $hash) + 1)) . $path;
    }

    private function crawlContest($con, $incremental)
    {
        $updstr = $incremental ? 'Updat' : 'Crawl';
        $this->line("<fg=yellow>${updstr}ing contest $con.</>");
        $page = $this->grab_page("https://atcoder.jp/contests/$con/submit");
        if (strpos($page, 'moji') !== false) {
            $pageDom = HtmlDomParser::str_get_html($page, true, true, DEFAULT_TARGET_CHARSET, false);
            $this->login($pageDom->find('input[name=csrf_token]', 0)->value);
            $page = $this->grab_page("https://atcoder.jp/contests/$con/submit");
        }

        $submit = HtmlDomParser::str_get_html($page);
        $innerIDs = [];
        $selector = $submit->getElementById('select-task');
        if (is_null($selector)) {
            $this->line("\n  <bg=red;fg=white> Warning </> : <fg=yellow>No Permission</>\n"); // wtf19
            return;
        }
        foreach ($selector->find('option') as $option) {
            $text = $option->innertext;
            $tempTitle = explode(' - ', $text, 2)[1];
            $innerIDs[$tempTitle] = $option->value;
        }
        $contestTitle = $submit->find('.contest-title', 0)->innertext;
        $problemModel = new ProblemModel();
        foreach ($innerIDs as $title => $innerId) {
            try {
                if ($incremental && !empty($problemModel->basic($problemModel->pid('AC' . $innerId)))) {
                    continue;
                }
                $this->line("<fg=yellow>${updstr}ing:</>   AC$innerId");
                $page = "https://atcoder.jp/contests/$con/tasks/$innerId?lang=en";
                $dom = $this->grab_page($page);
                $dom = preg_replace('/([^$])\$([^$])/', '$1$$$$2', $dom);
                $dom = preg_replace('/([^$])\$([^$])/', '$1$$$$2', $dom); // In case of like $n$, will be replaced to $$$n$ if replaced only once
                // $dom = preg_replace_callback("/< *img([^>]*)src *= *[\"\\']?([^\"\\'>]*)([^>]*)>/si", function ($match) use ($page) {
                //     $href = trim($match[2]);
                //     $_path = null;
                //     if (substr($href, 0, 5) != 'data:') {
                //         try {
                //             $url = $this->getUrlByPage($page, $href);
                //             $pos1 = strpos($url, 'img');
                //             $pos2 = strpos($url, '/', $pos1 + 3);
                //             $path = $pos1 === false || $pos2 === false ? substr($url, strpos($url, '/', strpos($url, '://') + 3)) : substr($url, $pos2);
                //             if (!in_array($url, $this->downloaded)) {
                //                 $fn = "public/external/atcoder$path";
                //                 $dirn = substr($fn, 0, strrpos($fn, '/'));
                //                 if (!file_exists($dirn)) {
                //                     $this->mkdirs($dirn);
                //                 }
                //                 file_put_contents(base_path($fn), $this->grab_page($url));
                //                 array_push($this->downloaded, $url);
                //             }
                //             $_path = $path;
                //         } catch (\Exception $e) {
                //             $this->line("\n  <bg=red;fg=white> Warning </> : <fg=yellow>Failed caching $url: {$e->getMessage()}. Use raw url.</>\n");
                //         }
                //     }
                //     $path = is_null($_path) ? $href : '/external/atcoder' . $_path;
                //     return "<img{$match[1]}src=\"$path\"{$match[3]}>";
                // }, $dom);
                $dom = HtmlDomParser::str_get_html($dom, true, true, DEFAULT_TARGET_CHARSET, false);
                foreach ($dom->find('var') as $var) {
                    $text = $var->innertext;
                    $var->innertext = '$$$' . $text . '$$$';
                }
                $this->pro['pcode'] = 'AC' . $innerId;
                $this->pro['OJ'] = $this->oid;
                $this->pro['contest_id'] = $con;
                $this->pro['index_id'] = $innerId;
                $this->pro['origin'] = "https://atcoder.jp/contests/$con/tasks/$innerId";
                $this->pro['title'] = $title;
                [$timeLimit, $memoryLimit] = sscanf($dom->find(".col-sm-12 p", 0)->plaintext, "Time Limit: %d sec / Memory Limit: %d MB");
                $this->pro['time_limit'] = $timeLimit * 1000;
                $this->pro['memory_limit'] = $memoryLimit * 1024;
                $this->pro['solved_count'] = -1;
                $this->pro['input_type'] = 'standard input';
                $this->pro['output_type'] = 'standard output';
                $this->pro['story'] = '';
                $this->pro['description'] = '';
                $this->pro['constraints'] = '';
                $this->pro['partial'] = '';
                $this->pro['input'] = '';
                $this->pro['output'] = '';
                $this->pro['judge'] = '';
                $this->pro['note'] = '';
                $this->pro['source'] = $contestTitle;
                $this->pro['sample'] = [];
                $this->pro['file'] = 0;
                $this->pro['file_url'] = null;
                foreach ($dom->find('a') as $a) {
                    if (preg_match('/^(.*?)(#.*)?$/', $a->href, $match)) {
                        if (strlen($match[1]) >= 4 && substr($match[1], -4) == '.pdf') {
                            dump($match);
                            $pdf = $match[1];
                            $path = '/external/atcoder';
                            $local = "public$path";
                            $fn = substr($pdf, strrpos($pdf, '/'));
                            if (!in_array($pdf, $this->downloaded)) {
                                if (!file_exists($local)) {
                                    $this->mkdirs($local);
                                }
                                $this->line($this->getUrlByPage("https://atcoder.jp/contests/$con/tasks/$innerId", $pdf));
                                file_put_contents(base_path($local . $fn), $this->grab_page($this->getUrlByPage("https://atcoder.jp/contests/$con/tasks/$innerId", $pdf)));
                                array_push($this->downloaded, $pdf);
                            }
                            $this->pro['file'] = 1;
                            $this->pro['file_url'] = $path . $fn;
                            if (isset($match[2])) $this->pro['file_url'] .= $match[2];
                        }
                    }
                }
                $compilers = [];
                foreach ($submit->getElementById('select-lang')->find('option') as $option) {
                    if (isset($this->availCompilers[$option->value])) {
                        array_push($compilers, $this->availCompilers[$option->value]);
                    } else {
                        $this->line("\n  <bg=red;fg=white> Warning </> : <fg=yellow>Compiler not present in database: {$option->value} {$option->innertext}</>\n");
                    }
                }
                $this->pro['special_compiler'] = $compilers == $this->availCompilers ? '' : join(',', $compilers);
                $directShow = false;
                $statement = $dom->getElementById('contest-statement');
                if (is_null($statement)) $statement = $dom->getElementById('task-statement');
                $sampleNoteFlag = false;
                foreach ($statement->children() as $node) {
                    if ($node->tag == 'p') { // PDF
                        $a = $node->find('a', 0);
                        if ($a && substr($a->href, -4) == '.pdf') {
                            // maybe won't occur any more?
                            $pdf = $a->href;
                            $path = '/external/atcoder';
                            $local = "public$path";
                            $fn = substr($pdf, strrpos($pdf, '/'));
                            if (!in_array($pdf, $this->downloaded)) {
                                if (!file_exists($local)) {
                                    $this->mkdirs($local);
                                }
                                $this->line($this->getUrlByPage("https://atcoder.jp/contests/$con/tasks/$innerId", $pdf));
                                file_put_contents(base_path($local . $fn), $this->grab_page($this->getUrlByPage("https://atcoder.jp/contests/$con/tasks/$innerId", $pdf)));
                                array_push($this->downloaded, $pdf);
                            }
                            $this->pro['file'] = 1;
                            $this->pro['file_url'] = $path . $fn;
                            break;
                        } else {
                            $directShow = true;
                            break;
                        }
                    } else if (substr($node->getAttribute('class'), 0, 4) == 'lang' || $node->id == 'task-statement') { // ???
                        if (count($node->find('.part .part'))) { // like AC814(arc018_4)
                            $directShow = true;
                            break;
                        }
                        foreach ($node->find('.part') as $part) {
                            $sections = $part->find('section');
                            if (!count($sections)) $sections = [$part];
                            foreach ($sections as $section) {
                                $h3 = $section->find('h3', 0);
                                $split = true;
                                if (is_null($h3)) {
                                    $h3 = $part->find('h3', 0);
                                    $split = false;
                                }
                                if (is_null($h3)) {
                                    if ($sampleNoteFlag !== false) {
                                        if ($this->pro['sample'][$sampleNoteFlag]['sample_note'] != '') {
                                            $this->pro['sample'][$sampleNoteFlag]['sample_note'] = trim($section->innertext);
                                        }
                                        $sampleNoteFlag = false;
                                        continue;
                                    } else {
                                        $directShow = true;
                                        break;
                                    }
                                }
                                $sampleNoteFlag = false;
                                $title = $h3->innertext;
                                if ($title == '') {
                                    $directShow = true;
                                    break;
                                }
                                if ($title[0] == "\xc2") $title = substr($title, 2); // starts with U+008F or U+0090 // but what is this???
                                // $header->remove(); // not supported
                                if ($split) {
                                    $parts = explode('</h3>', $section->innertext);
                                    array_shift($parts);
                                    $section->innertext = implode('</h3>', $parts);
                                }
                                $map = [
                                    'あらすじ' => 'story',
                                    'Story' => 'story',
                                    '問題' => 'description',
                                    '問題文' => 'description',
                                    'Problem Statement' => 'description',
                                    'Problem statement' => 'description',
                                    '制約' => 'constraints',
                                    'Constraints' => 'constraints',
                                    '部分点' => 'partial',
                                    '得点' => 'partial',
                                    '小課題' => 'partial',
                                    'Partial Points' => 'partial',
                                    'Partial Score' => 'partial',
                                    'Partial Scores' => 'partial',
                                    'Scoring' => 'partial',
                                    'Subtasks' => 'partial',
                                    'Subscore' => 'partial',
                                    '入力' => 'input',
                                    'Input' => 'input',
                                    'Input Format' => 'input',
                                    'Inputs' => 'input',
                                    '出力' => 'output',
                                    'Output' => 'output',
                                    'Outputs' => 'output',
                                    'ヒント' => 'note',
                                    'ノート' => 'note',
                                    '注意' => 'note',
                                    '注釈' => 'note',
                                    '備考' => 'note',
                                    '注記' => 'note',
                                    'Hint' => 'note',
                                    'Notes' => 'note',
                                    'Note' => 'note',
                                    '判定' => 'judge',
                                    'Judging' => 'judge',
                                ];
                                $unknown = false;
                                if (isset($map[$title])) {
                                    if (ctype_alpha($title[0]) || $this->pro[$map[$title]] == '') {
                                        $this->pro[$map[$title]] = trim(preg_replace('/<pre>\s*/', '<pre class="tex2jax_process">', $section->innertext));
                                    }
                                } else if (preg_match('/(?:入力例|Sample Input) *(\d+)/', $title, $match)) {
                                    if ($title[0] == 'S' || !isset($this->pro['sample'][$match[1] - 1])) {
                                        $this->pro['sample'][$match[1] - 1] = ['sample_input' => trim($section->find('pre', 0)->innertext)];
                                    }
                                    if (preg_match('/<h3>((?:出力例|Sample Output) *\d+)<\/h3>/', $section->innertext, $match)) {
                                        $title = $match[1];
                                        // $section->innertext = substr($section->innertext, strpos($section->innertext, '</h3>') + 5);
                                        $section = HtmlDomParser::str_get_html(substr($section->innertext, strpos($section->innertext, '</h3>') + 5),
                                            true, true, DEFAULT_TARGET_CHARSET, false); // Fuck DomParser full of bugs
                                    }
                                } else $unknown = true;
                                if (preg_match('/(?:出力例|Sample Output) *(\d+)/', $title, $match)) {
                                    if (!isset($this->pro['sample'][$match[1] - 1])) $this->pro['sample'][$match[1] - 1] = ['sample_input' => null];
                                    if ($title[0] == 'S' || !isset($this->pro['sample'][$match[1] - 1]['sample_output']) || $this->pro['sample'][$match[1] - 1]['sample_output'] != '') {
                                        $pre = $section->find('pre', 0);
                                        if (!is_null($pre)) {
                                            $this->pro['sample'][$match[1] - 1]['sample_output'] = trim($pre->innertext);
                                            // $pre->remove();
                                            $section->innertext = substr($section->innertext, strpos($section->innertext, '</pre>') + 6);
                                        } else $this->pro['sample'][$match[1] - 1]['sample_output'] = null;
                                        $this->pro['sample'][$match[1] - 1]['sample_note'] = trim($section->innertext);
                                    }
                                    $sampleNoteFlag = $match[1] - 1;
                                } else if ($unknown) {
                                    if ($debug = false) { // It's not typo
                                        $this->line("\n  <bg=red;fg=white> Warning </> : <fg=yellow>Unknown section: $title</>\n");
                                    } else {
                                        $directShow = true;
                                        break;
                                    }
                                }
                            }
                            if ($directShow) break;
                        }
                    } else if ($node->tag == 'meta') { // ?????
                    } else {
                        $directShow = true;
                        break;
                    }
                }

                $this->pro['description'] = $this->pro['story'] . $this->pro['description'];
                $this->pro['input'] .= $this->pro['constraints'] . $this->pro['partial'];
                $this->pro['output'] .= $this->pro['judge'];

                if ($directShow) {
                    $this->pro['description'] = trim(preg_replace('/<pre>\s*/', '<pre class="tex2jax_process">', $statement->innertext));
                    $this->pro['input'] = $this->pro['output'] = $this->pro['note'] = '';
                    $this->pro['sample'] = [];
                    $this->line("\n  <bg=red;fg=white> Warning </> : Unknown page format\n"); // no highlight for it's a common warning
                }

                $problem = $problemModel->pid($this->pro['pcode']);

                if ($problem) {
                    $problemModel->clearTags($problem);
                    $new_pid = $this->updateProblem($this->oid);
                } else {
                    $new_pid = $this->insertProblem($this->oid);
                }

                // $problemModel->addTags($new_pid, $tag); // not present

                $this->line("<fg=green>${updstr}ed:</>   </>AC$innerId");
            } catch (\Throwable $e) {
                $this->line("\n  <bg=red;fg=white> Exception </> : <fg=yellow>{$e->getMessage()}</>\n");
                $this->line($e);
            }
        }
        $this->line("<fg=green>${updstr}ed contest $con.</>");
    }

    public function crawl($con, $incremental)
    {
        if ($con == 'all' || $con == 'en') {
            $last = 1;
            $list = [];
            $lang = $con == 'en' ? 'en' : 'ja';
            $this->line('<fg=yellow>Crawling contest list.</>');
            for ($page = 1; $page <= $last; ++$page) {
                $dom = HtmlDomParser::str_get_html($this->grab_page("https://atcoder.jp/contests/archive?lang=$lang&page=$page"));
                $last = $dom->find('ul.pagination li', -1)->find('a')[0]->innertext;
                $this->line("<fg=yellow>Crawled $page/$last</>");
                foreach ($dom->find('tr') as $tr) {
                    $a = $tr->children(1)->find('a');
                    if (count($a)) {
                        $href = $a[0]->href;
                        if (substr($href, 0, 10) === '/contests/') array_unshift($list, substr($href, 10));
                    }
                }
            }
            $this->line('<fg=green>Crawled contest list.</>');
            for($i = 0; $i < sizeof($list); $i++) { 
                $this->crawlContest($list[$i], $incremental);
            }
        } else {
            $this->crawlContest($con, $incremental);
        }
    }
}
