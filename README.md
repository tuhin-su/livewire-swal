# Laravel & Livewire 3+ SweetAlert2 Helpers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tuhin-su/laravel-generic-swal.svg?style=flat-square)](https://packagist.org/packages/tuhin-su/laravel-generic-swal)
[![Total Downloads](https://img.shields.io/packagist/dt/tuhin-su/laravel-generic-swal.svg?style=flat-square)](https://packagist.org/packages/tuhin-su/laravel-generic-swal)
[![License](https://img.shields.io/packagist/l/tuhin-su/laravel-generic-swal.svg?style=flat-square)](https://packagist.org/packages/tuhin-su/laravel-generic-swal)

A simple, reusable global SweetAlert2 integration for Laravel and Livewire 3 with clean, secure, one-line PHP wrappers.

---

## Features

- **Zero-Configuration Installation**: SweetAlert2 CDN and the JavaScript event wrapper are automatically injected into your application's HTML pages, with no NPM or Vite compilation needed out-of-the-box.
- **Small Top-Corner Toasts**: `swalToastSuccess`, `swalToastWarning`, `swalToastError`
- **Larger Modal Dialogs**: `swalFireSuccess`, `swalFireWarning`, `swalFireError`
- **Confirm Modals**: `swalConfirm`
- **Generic Input Prompts**: `swalTakeInput` (captures user input and sends it back to PHP)
- **Password Prompts**: `swalPromptPassword`
- **One-Call Password Verification**: `swalVerifyCurrentUserPassword` (prompts for the password, verifies it securely on the server via `Hash::check`, and automatically dispatches success/failure events)
- **Secure Cryptographically Signed Actions**: `swalSecureAction` (forces multi-step confirmations & password prompts on the client and executes target methods internally on the server using cryptographically signed payloads, completely preventing client-side bypasses)

---

## Requirements

- Laravel 10 or 11+
- Livewire 3+

---

## Installation & Setup

Choose between the two installation methods depending on your project architecture:

### Option A: Zero-Configuration (Recommended & Default)
Use this option if you want the package to work instantly without running build tools, installing NPM packages, or modifying layout files.

1. **Install via Composer**:
   ```bash
   composer require tuhin-su/laravel-generic-swal
   ```
   **That's it!** The package automatically injects the SweetAlert2 CDN and the event wrapper into the closing `</body>` tag of all HTML pages.

   > [!TIP]
   > **Self-Hosting / Customizing SWAL CDN**: By default, SweetAlert2 is loaded via jsDelivr CDN. If you want to host it yourself locally or use a custom CDN URL, publish the config file using `php artisan vendor:publish --tag=laravel-generic-swal-config` and edit the `swal_cdn` key in `config/laravel-generic-swal.php`. Set it to `false` or `null` if you already load SweetAlert2 separately in your layouts.

2. **Use the Trait in your Livewire Component**:
   ```php
   namespace App\Livewire;

   use Livewire\Component;
   use LaravelGenericSwal\Traits\Swal;

   class MyComponent extends Component
   {
       use Swal;

       public function save()
       {
           $this->swalToastSuccess('Saved', 'Record updated successfully.');
       }
   }
   ```

---

### Option B: Manual Asset Compilation (NPM + Vite)
Use this option if you prefer to compile the assets locally, bundle SweetAlert2 inside your custom built assets, avoid CDNs, or customize the frontend JavaScript logic.

1. **Install via Composer**:
   ```bash
   composer require tuhin-su/laravel-generic-swal
   ```

2. **Publish Configuration & Assets**:
   Run the publish command to copy files into your application's resources directory:
   ```bash
   php artisan vendor:publish --tag=laravel-generic-swal-config
   php artisan vendor:publish --tag=laravel-generic-swal-assets
   ```
   This will create:
   - Config file at `config/laravel-generic-swal.php`
   - JS wrapper file at `resources/js/vendor/laravel-generic-swal/swal.js`

3. **Disable Auto-Injection**:
   Open `config/laravel-generic-swal.php` and set `auto_inject` to `false` to disable the middleware CDN injection:
   ```php
   'auto_inject' => false,
   ```

4. **Install SweetAlert2 via NPM**:
   ```bash
   npm install sweetalert2
   ```

5. **Register Wrapper in JavaScript**:
   Import SweetAlert2 and register the helper script in your `resources/js/app.js` (or Vite entrypoint):
   ```javascript
   import Swal from 'sweetalert2'
   window.Swal = Swal
   import './vendor/laravel-generic-swal/swal'
   ```
   *Make sure your layout contains your Vite directive: `@vite(['resources/css/app.css', 'resources/js/app.js'])`.*

6. **Use the Trait in your Livewire Component**:
   ```php
   namespace App\Livewire;

   use Livewire\Component;
   use LaravelGenericSwal\Traits\Swal;

   class MyComponent extends Component
   {
       use Swal;

       public function save()
       {
           $this->swalToastSuccess('Saved', 'Record updated successfully.');
       }
   }
   ```

---

## Helper Methods

### 1. Toasts (Small, Top-End)

```php
$this->swalToastSuccess(string $title = 'Success', string $text = '', array $opts = []): void
$this->swalToastWarning(string $title = 'Warning', string $text = '', array $opts = []): void
$this->swalToastError(string $title = 'Error', string $text = '', array $opts = []): void
```

#### Example
```php
$this->swalToastSuccess('Saved', 'Record updated successfully.');
```

You can pass extra [SweetAlert2 options](https://sweetalert2.github.io/#configuration) to override defaults:
```php
$this->swalToastSuccess('Saved', 'Record updated successfully.', [
    'timer' => 5000,
    'position' => 'bottom-end'
]);
```

---

### 2. Modals (Larger Dialogs)

```php
$this->swalFireSuccess(string $title = 'Success', string $text = '', array $opts = []): void
$this->swalFireWarning(string $title = 'Warning', string $text = '', array $opts = []): void
$this->swalFireError(string $title = 'Error', string $text = '', array $opts = []): void
```

#### Example
```php
$this->swalFireWarning('Check your inputs!', 'Please review form fields before submitting.');
```

---

### 3. Confirm Modal

Triggers a confirmation prompt and dispatches a Livewire event upon acceptance.

```php
$this->swalConfirm(
    string $title = 'Are you sure?',
    string $text = '',
    array $opts = [],
    ?string $thenEvent = null,
    array $thenParams = [],
    ?string $thenEventTo = null
): void
```

#### Example
```php
// Dispatch "users.delete" with user ID if confirmed
$this->swalConfirm(
    title: 'Delete user?',
    text: 'This action cannot be undone.',
    opts: ['confirmButtonText' => 'Delete', 'cancelButtonText' => 'Cancel'],
    thenEvent: 'users.delete',
    thenParams: ['id' => $id]
);
```

Listen for it in your component using the `#[On]` attribute:
```php
use Livewire\Attributes\On;

#[On('users.delete')]
public function delete(array $payload)
{
    $id = (int)($payload['id'] ?? 0);
    // Perform delete...
    $this->swalToastSuccess('Deleted', "User {$id} has been removed.");
}
```

---

### 4. Generic Input Prompts

Prompt the user for a value and return it to a Livewire event listener.

```php
$this->swalTakeInput(
    string $title = 'Enter a value',
    string $text = '',
    string $input = 'text',
    array $opts = [],
    ?string $thenEvent = null,
    array $thenParams = [],
    ?string $thenEventTo = null
): void
```

#### Example
```php
$this->swalTakeInput(
    title: 'Add Note',
    text: 'Type your note below:',
    input: 'textarea',
    opts: ['inputPlaceholder' => 'Write something...'],
    thenEvent: 'notes.saved'
);
```

In your listener, access the value via `$payload['value']`:
```php
use Livewire\Attributes\On;

#[On('notes.saved')]
public function saveNote(array $payload)
{
    $note = (string)($payload['value'] ?? '');
    // Save note logic...
    $this->swalToastSuccess('Saved', 'Your note was recorded.');
}
```

---

### 5. Password Prompt Preset

A wrapper around `swalTakeInput` preset for secure password fields.

```php
$this->swalPromptPassword(
    string $title = 'Enter your password',
    string $text = '',
    array $opts = [],
    ?string $thenEvent = null,
    array $thenParams = [],
    ?string $thenEventTo = null
): void
```

#### Example
```php
$this->swalPromptPassword(
    title: 'Re-enter Password',
    text: 'Confirm password to continue',
    thenEvent: 'security.verified'
);
```

---

### 6. Verify Current User Password Flow

Prompts for the password, verifies it against the authenticated user's current password (using `Hash::check`), shows an automated feedback toast, and dispatches true/false events.

```php
$this->swalVerifyCurrentUserPassword(
    string $title = 'Verify password',
    string $text = 'Please confirm your password to continue',
    array $opts = [],
    ?string $thenEventTrue = null,
    array $thenParamsTrue = [],
    ?string $thenEventFalse = null,
    array $thenParamsFalse = [],
    ?string $thenEventTo = null
): void
```

---

## Secure Actions (Bypass Prevention & Multi-Step Verification)

Standard event-driven flows (like `swalConfirm`) are dispatched to the frontend and rely on the client to send a follow-up event back to the server to trigger the final action. A malicious user can open the browser console and manually dispatch the target event (e.g. `Livewire.dispatch('users.delete', { id: 5 })`), completely bypassing any frontend SweetAlert confirmation.

To solve this, the library provides a **secure cryptographically signed action system**:
1. **Server-side Signature**: The server-side trait encrypts the target method name and its parameters into a secure payload signed using your application key (`APP_KEY`). This payload includes safety constraints like expiration timestamp, user session ID, and component class validation.
2. **Multi-Step Client Prompts**: The client receives this encrypted payload and sequentially prompts the user (e.g. first confirmation, then password verification) depending on your options.
3. **Internal Server-side Execution**: When confirmed, the client dispatches the encrypted payload back to the server. The server decrypts it, validates all constraints (e.g. session matching and time validity), checks the password, and calls the target method **directly** on the component instance.

**Important Security Rule**: Define your target execution methods as `protected` or `private` (e.g., `protected function deleteUser($id)`). Since Livewire only exposes `public` methods to the frontend, clients cannot invoke the method directly under any circumstances. The only entry point is the secure decryption handler in the trait.

### Usage

Call `$this->swalSecureAction` from your component:

```php
$this->swalSecureAction(
    string $method,             // Target protected/private method to execute
    array $params = [],         // Parameters to pass to the method
    bool $requirePassword = false, // Whether to require password verification
    string $confirmTitle = 'Are you sure?',
    string $confirmText = '',
    array $confirmOpts = [],
    string $passwordTitle = 'Enter your password',
    string $passwordText = 'Please confirm your password to continue',
    array $passwordOpts = []
);
```

### Full Multi-Step Example

First, trigger the secure action flow when a user clicks a button:

```php
use LaravelGenericSwal\Traits\Swal;
use Livewire\Component;

class UserManagement extends Component
{
    use Swal;

    public function initiateDelete($userId)
    {
        // 1. Asks user to confirm deletion (Yes/No)
        // 2. Prompts user for their password
        // 3. Verifies password on the server and executes the protected `deleteUser` method
        $this->swalSecureAction(
            method: 'deleteUser',
            params: ['id' => $userId],
            requirePassword: true,
            confirmTitle: 'Confirm User Deletion?',
            confirmText: 'This action is irreversible.',
            passwordTitle: 'Password Required',
            passwordText: 'Enter your password to verify authorization.'
        );
    }

    /**
     * Target method must be protected or private for maximum security.
     * Hackers cannot invoke this method from the console.
     */
    protected function deleteUser($id)
    {
        // Execute deletion safely
        $user = User::findOrFail($id);
        $user->delete();

        $this->swalToastSuccess('Deleted', 'User removed successfully.');
    }
}
```

---

## JavaScript-only Usage

All SweetAlert2 helpers are bound to the `window` object and can be used directly in custom scripts:

- `swalToastSuccess(title, text, opts)`
- `swalToastWarning(title, text, opts)`
- `swalToastError(title, text, opts)`
- `swalFireSuccess(title, text, opts)`
- `swalFireWarning(title, text, opts)`
- `swalFireError(title, text, opts)`
- `swalFireAsk(cfg)` (returns `Promise<boolean>`)
- `swalTakeInput(cfg)` (returns `Promise<string|null>`)
- `swalPromptPassword(cfg)`

---

## Component-Targeted Events (`thenEventTo`)

Use `thenEventTo` to target specific Livewire components by their component name instead of broadcasting the event globally:

```php
$this->swalConfirm(
    title: 'Confirm deletion',
    thenEvent: 'delete-record',
    thenParams: ['id' => $recordId],
    thenEventTo: 'admin.dashboard' // Target Component name
);
```

---

## Security Notes

1. **Password Prompts**: Plaintext passwords are sent back to the Livewire component for verification via Livewire event payloads. Always ensure your application is running over **HTTPS** to secure this transmission, and prevent logging of sensitive request/event bodies.
2. **Centralization**: Keep SweetAlert2 logic centralized to prevent inline JS script execution issues and maintain a strong Content Security Policy (CSP).
