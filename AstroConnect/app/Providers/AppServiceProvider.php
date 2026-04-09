<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register application service bindings and singletons here.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bootstrap shared app behavior (macros, observers, policies, etc.).
        VerifyEmail::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Verify your AstroConnect account')
                ->view('emails.auth-verify-email', [
                    'actionUrl' => $url,
                ]);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $url): MailMessage {
            return (new MailMessage)
                ->subject('Reset your AstroConnect password')
                ->view('emails.auth-reset-password', [
                    'actionUrl' => $url,
                ]);
        });
    }
}
