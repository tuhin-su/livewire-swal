<?php

namespace LaravelGenericSwal\Traits;

use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

trait Swal
{
    // Toast (small)
    protected function swalToastSuccess(string $title = 'Success', string $text = '', array $opts = []): void
    {
        $this->dispatch('swal:toast', icon: 'success', title: $title, text: $text, opts: $opts);
    }

    protected function swalToastWarning(string $title = 'Warning', string $text = '', array $opts = []): void
    {
        $this->dispatch('swal:toast', icon: 'warning', title: $title, text: $text, opts: $opts);
    }

    protected function swalToastError(string $title = 'Error', string $text = '', array $opts = []): void
    {
        $this->dispatch('swal:toast', icon: 'error', title: $title, text: $text, opts: $opts);
    }

    // Modal (larger)
    protected function swalFireSuccess(string $title = 'Success', string $text = '', array $opts = []): void
    {
        $this->dispatch('swal:modal', icon: 'success', title: $title, text: $text, opts: $opts);
    }

    protected function swalFireWarning(string $title = 'Warning', string $text = '', array $opts = []): void
    {
        $this->dispatch('swal:modal', icon: 'warning', title: $title, text: $text, opts: $opts);
    }

    protected function swalFireError(string $title = 'Error', string $text = '', array $opts = []): void
    {
        $this->dispatch('swal:modal', icon: 'error', title: $title, text: $text, opts: $opts);
    }

    // Confirm (modal-only)
    protected function swalConfirm(
        string $title = 'Are you sure?',
        string $text = '',
        array $opts = [],
        ?string $thenEvent = null,
        array $thenParams = [],
        ?string $thenEventTo = null
    ): void {
        $this->dispatch(
            'swal:confirm',
            title: $title,
            text: $text,
            thenEvent: $thenEvent,
            thenParams: $thenParams,
            thenEventTo: $thenEventTo,
            opts: $opts
        );
    }

    // Generic input → returns to PHP via thenEvent with payload['value']
    protected function swalTakeInput(
        string $title = 'Enter a value',
        string $text = '',
        string $input = 'text',
        array $opts = [],
        ?string $thenEvent = null,
        array $thenParams = [],
        ?string $thenEventTo = null
    ): void {
        $this->dispatch(
            'swal:input',
            title: $title,
            text: $text,
            input: $input,
            thenEvent: $thenEvent,
            thenParams: $thenParams,
            thenEventTo: $thenEventTo,
            opts: $opts
        );
    }

    // Password prompt preset
    protected function swalPromptPassword(
        string $title = 'Enter your password',
        string $text = '',
        array $opts = [],
        ?string $thenEvent = null,
        array $thenParams = [],
        ?string $thenEventTo = null
    ): void {
        $this->dispatch(
            'swal:password',
            title: $title,
            text: $text,
            thenEvent: $thenEvent,
            thenParams: $thenParams,
            thenEventTo: $thenEventTo,
            opts: array_merge([
                'input' => 'password',
                'inputLabel' => $opts['inputLabel'] ?? 'Password',
                'inputPlaceholder' => $opts['inputPlaceholder'] ?? 'Enter your password',
                'inputAttributes' => array_merge(['autocapitalize' => 'off', 'autocorrect' => 'off'], $opts['inputAttributes'] ?? []),
                'confirmButtonText' => $opts['confirmButtonText'] ?? 'Verify',
                'showCancelButton' => $opts['showCancelButton'] ?? true,
            ], $opts)
        );
    }

    // One-call verify flow: prompt for password, verify against current user, and optionally dispatch events for true/false
    protected function swalVerifyCurrentUserPassword(
        string $title = 'Verify password',
        string $text = 'Please confirm your password to continue',
        array $opts = [],
        ?string $thenEventTrue = null,
        array $thenParamsTrue = [],
        ?string $thenEventFalse = null,
        array $thenParamsFalse = [],
        ?string $thenEventTo = null
    ): void {
        $this->dispatch(
            'swal:verify-current-password',
            title: $title,
            text: $text,
            thenEventTrue: $thenEventTrue,
            thenParamsTrue: $thenParamsTrue,
            thenEventFalse: $thenEventFalse,
            thenParamsFalse: $thenParamsFalse,
            thenEventTo: $thenEventTo,
            opts: $opts
        );
    }

    // Auto-wired in v3 via attribute: receives the plain password from JS and verifies
    #[On('swal.__verify_current_user_password')]
    public function __swalVerifyCurrentUserPassword(array $payload): void
    {
        $plain = (string) ($payload['value'] ?? '');
        $thenEventTo = $payload['thenEventTo'] ?? null;
        $thenEventTrue = $payload['thenEventTrue'] ?? null;
        $thenParamsTrue = $payload['thenParamsTrue'] ?? [];
        $thenEventFalse = $payload['thenEventFalse'] ?? null;
        $thenParamsFalse = $payload['thenParamsFalse'] ?? [];

        $ok = false;
        if (Auth::check()) {
            $ok = Hash::check($plain, Auth::user()->getAuthPassword());
        }

        if ($ok) {
            $this->swalToastSuccess('Verified', 'Password confirmed');
            if ($thenEventTrue) {
                if ($thenEventTo) {
                    $this->dispatch($thenEventTrue, ...$thenParamsTrue)->to($thenEventTo);
                } else {
                    $this->dispatch($thenEventTrue, ...$thenParamsTrue);
                }
            }
        } else {
            $this->swalToastError('Invalid', 'Incorrect password');
            if ($thenEventFalse) {
                if ($thenEventTo) {
                    $this->dispatch($thenEventFalse, ...$thenParamsFalse)->to($thenEventTo);
                } else {
                    $this->dispatch($thenEventFalse, ...$thenParamsFalse);
                }
            }
        }
    }

    /**
     * Generates a cryptographically signed/encrypted payload for a secure action.
     * Use this if you want to render the payload in your Blade template and trigger the action directly from JavaScript.
     */
    protected function swalGenerateSecureActionPayload(string $method, array $params = [], bool $requirePassword = false): string
    {
        return encrypt([
            'method' => $method,
            'params' => $params,
            'requirePassword' => $requirePassword,
            'user_id' => Auth::id(),
            'component' => get_class($this),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);
    }

    /**
     * Secure one-call action flow with multi-step support.
     * Generates a cryptographically signed/encrypted payload on the server.
     * Invokes confirmation (Yes/No) and/or password prompts on the client.
     * Executes the target component method internally (on the server) after verifying the payload and password.
     * Note: The target method can (and should) be defined as protected/private to prevent direct invocation by clients.
     */
    protected function swalSecureAction(
        string $method,
        array $params = [],
        bool $requirePassword = false,
        string $confirmTitle = 'Are you sure?',
        string $confirmText = '',
        array $confirmOpts = [],
        string $passwordTitle = 'Enter your password',
        string $passwordText = 'Please confirm your password to continue',
        array $passwordOpts = []
    ): void {
        $encryptedPayload = $this->swalGenerateSecureActionPayload($method, $params, $requirePassword);

        $this->dispatch(
            'swal:secure-action',
            encryptedPayload: $encryptedPayload,
            requirePassword: $requirePassword,
            confirmTitle: $confirmTitle,
            confirmText: $confirmText,
            confirmOpts: $confirmOpts,
            passwordTitle: $passwordTitle,
            passwordText: $passwordText,
            passwordOpts: $passwordOpts
        );
    }

    #[On('swal.__execute_secure_action')]
    public function __swalExecuteSecureAction(string $encryptedPayload, ?string $password = null): void
    {
        try {
            $payload = decrypt($encryptedPayload);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            $this->swalToastError('Security Error', 'Invalid or tampered action payload.');
            return;
        }

        // Validate expiration
        if (!isset($payload['expires_at']) || now()->timestamp > $payload['expires_at']) {
            $this->swalToastError('Expired', 'This action has expired. Please try again.');
            return;
        }

        // Validate user session match
        if (($payload['user_id'] ?? null) !== Auth::id()) {
            $this->swalToastError('Security Error', 'Session mismatch. Action unauthorized.');
            return;
        }

        // Validate component match
        if (($payload['component'] ?? null) !== get_class($this)) {
            $this->swalToastError('Security Error', 'Component mismatch.');
            return;
        }

        // Verify password if required
        if ($payload['requirePassword'] ?? false) {
            $plain = (string) $password;
            $ok = false;
            if (Auth::check()) {
                $ok = Hash::check($plain, Auth::user()->getAuthPassword());
            }
            if (!$ok) {
                $this->swalToastError('Invalid Password', 'Incorrect password. Action aborted.');
                return;
            }
        }

        $method = $payload['method'];
        $params = $payload['params'] ?? [];

        if (!method_exists($this, $method)) {
            $this->swalToastError('Error', 'The action handler could not be found.');
            return;
        }

        // Execute the target method dynamically on this component
        $this->{$method}(...$params);
    }
}
