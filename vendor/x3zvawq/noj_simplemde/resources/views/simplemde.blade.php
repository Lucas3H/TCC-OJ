<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}} {{ $scopeClass }}">

        @include('admin::form.error')

        <textarea id="{{$id}}">{{ old($column, $value) }}</textarea>

        <input type="hidden" name="{{$name}}" value="{{ old($column, $value) }}"/>

        @include('admin::form.help-block')

    </div>
</div>
<div id="noj-markdown-editor-preview"></div>
<script type="text/x-mathjax-config">
    MathJax.Hub.Config({
        tex2jax: {
            inlineMath: [ ['$$$','$$$'], ["\\(","\\)"] ],
            displayMath: [ ["$$$$$$","$$$$$$"], ['$$','$$'], ['\\[', '\\]'] ],
            processEscapes: true
        },
        showMathMenu: false
    });
</script>
<style>
.{{ $scopeClass }} .editor-toolbar.fullscreen, .{{ $scopeClass }} .CodeMirror-fullscreen {
    z-index: 10000 !important;
}

.{{ $scopeClass }} .CodeMirror {
    height: {{ $height ?: 300 }}px;
}
#noj-markdown-editor-preview{
    display: none;
}
</style>
