<?php

namespace ZsgsDesign\CodeMirror;

use Encore\Admin\Extension;

class CodeMirror extends Extension
{
    const ASSETS_PATH = 'static/library/codemirror/';

    public $name = 'code-mirror';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';
}
