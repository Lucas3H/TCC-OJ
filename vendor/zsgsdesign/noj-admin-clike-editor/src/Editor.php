<?php

namespace ZsgsDesign\ClikeEditor;

use Encore\Admin\Form\Field;
use ZsgsDesign\CodeMirror\CodeMirror;

abstract class Editor extends Field
{
    protected $mode = '';

    /**
     * {@inheritdoc}
     */
    protected $view = 'laravel-admin-code-mirror::editor';

    /**
     * {@inheritdoc}
     */
    protected static $css = [
    ];

    /**
     * {@inheritdoc}
     */
    protected static $js = [
    ];

    /**
     * Set editor height.
     *
     * @param int $height
     * @return $this
     */
    public function height($height = 10)
    {
        return $this->addVariables(compact('height'));
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $options = array_merge(
            [
                'mode' => $this->mode,
                'lineNumbers' => true,
                'matchBrackets' => true,
            ],
            ClikeEditor::config('config', [])
        );

        $options = json_encode($options);

        $this->script = <<<EOT
CodeMirror.fromTextArea(document.getElementById("{$this->id}"), $options);
EOT;

        return parent::render();
    }
}