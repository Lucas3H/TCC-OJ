<?php

namespace Encore\Simplemde;

use Encore\Admin\Form\Field;

class Editor extends Field
{
    /**
     * @var string
     */
    protected $view = 'laravel-admin-simplemde::simplemde';

    /**
     * @var array
     */
    protected static $css = [
    ];

    /**
     * @var array
     */
    protected static $js = [
        'static/library/mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML'
    ];

    /**
     * @var int
     */
    protected $height = 300;

    /**
     * @param int $height
     * @return $this
     */
    public function height($height = 300)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->addVariables([
            'height'     => $this->height,
            'scopeClass' => 'simplemde-'.uniqid()
        ]);

        $name = $this->formatName($this->column);

        $config = (array) Simplemde::config('config');

        $config = json_encode($config);

        $varName = 'simplemde_'.uniqid();

        $this->script = <<<EOT

var options = {element: $("#{$this->id}")[0]};
var customSimpleMDE = {
    drawInlineFormula: (editor) => {
        var cm = editor.codemirror;
        var output = '';
        var selectedText = cm.getSelection();
        var text = selectedText || 'x = (-b \\pm \\sqrt{b^2-4ac})/(2a)';
        output = '$$$' + text + '$$$';
        cm.replaceSelection(output);
    },
    drawBlockFormula: (editor) => {
        var cm = editor.codemirror;
        var output = '';
        var selectedText = cm.getSelection();
        var text = selectedText || 'x = \\frac{-b \\pm \\sqrt{b^2-4ac}}{2a}';
        output = '$$$$$$' + text + '$$$$$$';
        cm.replaceSelection(output);
    }
};
options.hideIcons = options.hideIcons || ["guide", "heading","side-by-side","fullscreen"];
options.spellChecker = options.spellChecker || false;
options.tabSize = options.tabSize || 4;
options.status = options.status || false;
options.renderingoptions = options.renderingoptions || {
    codeSyntaxHighlighting: true
};
options.previewRender = options.previewRender || function (plainText) {
    document.getElementById("noj-markdown-editor-preview").innerHTML=DOMPurify.sanitize(marked(plainText, {
        highlight: function (code, lang) {
            var language = hljs.getLanguage(code);
            if (!language) {
                return hljs.highlightAuto(code).value;
            }
            return hljs.highlight(lang, code).value;
        }
    }));
    MathJax.Hub.Queue(["Typeset",MathJax.Hub,"noj-markdown-editor-preview"]);
    return document.getElementById("noj-markdown-editor-preview").innerHTML;
};
options.toolbar = options.toolbar || [{
    name: "bold",
    action: SimpleMDE.toggleBold,
    className: "MDI format-bold",
    title: "Bold",
},
{
    name: "italic",
    action: SimpleMDE.toggleItalic,
    className: "MDI format-italic",
    title: "Italic",
},
{
    name: "strikethrough",
    action: SimpleMDE.toggleStrikethrough,
    className: "MDI format-strikethrough",
    title: "Strikethrough",
},
"|",
{
    name: "quote",
    action: SimpleMDE.toggleBlockquote,
    className: "MDI format-quote",
    title: "Quote",
},
{
    name: "unordered-list",
    action: SimpleMDE.toggleUnorderedList,
    className: "MDI format-list-bulleted",
    title: "Generic List",
},
{
    name: "ordered-list",
    action: SimpleMDE.toggleOrderedList,
    className: "MDI format-list-numbers",
    title: "Numbered List",
},
"|",
{
    name: "code",
    action: SimpleMDE.toggleCodeBlock,
    className: "MDI code-tags",
    title: "Create Code",
},
{
    name: "link",
    action: SimpleMDE.drawLink,
    className: "MDI link-variant",
    title: "Insert Link",
},
{
    name: "image",
    action: SimpleMDE.drawImage,
    className: "MDI image-area",
    title: "Insert Image",
},
{
    name: "inline-formula",
    action: customSimpleMDE.drawInlineFormula,
    className: "MDI alpha",
    title: "Inline Formula",
},
{
    name: "block-formula",
    action: customSimpleMDE.drawBlockFormula,
    className: "MDI beta",
    title: "Block Formula",
},
"|",
{
    name: "preview",
    action: SimpleMDE.togglePreview,
    className: "MDI eye no-disable",
    title: "Toggle Preview",
}];

var $varName = new SimpleMDE(options);

$varName.codemirror.on("change", function(){
	var html = $varName.value();
    $('input[name=$name]').val(html);
});

EOT;
        return parent::render();
    }
}
