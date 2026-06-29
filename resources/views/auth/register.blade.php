<x-guest-layout :authTitle="__('app.auth_register_title')">

    <div class="mb-7">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">{{ __('app.auth_register_title') }}</h2>
        <p class="text-slate-500 mt-1.5 text-sm">{{ __('app.auth_register_subtitle') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" data-ajax data-success-toast="{{ __('app.auth_register_success_toast') }}" class="space-y-4">
        @csrf

        <div class="field">
            <label for="name" class="field-label">{{ __('app.auth_name_label') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="field-input @error('name') has-error @enderror" placeholder="{{ __('app.auth_name_placeholder') }}">
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="email" class="field-label">{{ __('app.auth_email_label') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="field-input @error('email') has-error @enderror" placeholder="{{ __('app.auth_email_placeholder') }}">
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="password" class="field-label">{{ __('app.auth_password_label') }}</label>
            <div class="relative">
                <input id="password" type="password" name="password" required autocomplete="new-password"
                       class="field-input has-toggle @error('password') has-error @enderror" placeholder="{{ __('app.auth_password_register_placeholder') }}">
                <button type="button" class="field-toggle" data-toggle-password="#password" aria-label="{{ __('app.auth_show_password') }}">
                    <i class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="password_confirmation" class="field-label">{{ __('app.auth_password_confirm_label') }}</label>
            <div class="relative">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                       class="field-input has-toggle" placeholder="{{ __('app.auth_password_confirm_placeholder') }}">
                <button type="button" class="field-toggle" data-toggle-password="#password_confirmation" aria-label="{{ __('app.auth_show_password') }}">
                    <i class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary !mt-6">
            {{ __('app.auth_register_submit') }}
            <i class="fa-solid {{ is_rtl() ? 'fa-arrow-left' : 'fa-arrow-right' }} text-xs"></i>
        </button>

        <p class="text-center text-sm text-slate-600 !mt-6">
            {{ __('app.auth_have_account') }}
            <a href="{{ route('login') }}" class="link">{{ __('app.auth_login_link') }}</a>
        </p>
    </form>
</x-guest-layout>
