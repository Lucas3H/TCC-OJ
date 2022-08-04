@extends('auth-captcha::login_base')
@section('content')
    <div id="captchaError" class="form-group has-feedback {!! !$errors->has('captcha') ?: 'has-error' !!}"
         style="margin-bottom: 0;">
        @if($errors->has('captcha'))
            @foreach($errors->get('captcha') as $message)
                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{ $message }}
                </label><br>
            @endforeach
        @endif
    </div>
    <div id="dingxiangContainer"></div>
    <div class="row">
        <div class="col-xs-8">
            @if(config('admin.auth.remember'))
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" name="remember"
                               value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
                        {{ trans('admin.remember_me') }}
                    </label>
                </div>
            @endif
        </div>
        <div class="col-xs-4">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="token" name="token" value="">
            <button type="button" class="btn btn-primary btn-block btn-flat" id="loginButton">
                {{ trans('admin.login') }}
            </button>
        </div>
    </div>
@endsection
@section('js')
    <script src="https://cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js"></script>
    <script>
        let captcha = _dx.Captcha($('#dingxiangContainer'),
            Object.assign({
                    appId: '{{ $captchaAppid }}',
                    style: 'popup',
                    success: function (token) {
                        $('#token').attr('value', token);
                        $('#auth-login').submit();
                    }
                }, @json(config('admin.extensions.auth-captcha.ext_config', []))
            ));

        $('#loginButton').on('click', function (event) {
            captcha.show();
        });

        $('#auth-login').on('keyup', function (event) {
            if (event.keyCode === 13) {
                $('#loginButton').click();
            }
        });
    </script>
@endsection