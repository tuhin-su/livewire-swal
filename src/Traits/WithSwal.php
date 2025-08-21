<?php

namespace LivewireSwal\Traits;

trait WithSwal
{
    /**
     * Fire a SweetAlert success notification
     */
    public function swalSuccess(string $title = 'Success!', string $text = '', array $options = []): void
    {
        app('livewire-swal')->success($title, $text, $options);
    }

    /**
     * Fire a SweetAlert error notification
     */
    public function swalError(string $title = 'Error!', string $text = '', array $options = []): void
    {
        app('livewire-swal')->error($title, $text, $options);
    }

    /**
     * Fire a SweetAlert warning notification
     */
    public function swalWarning(string $title = 'Warning!', string $text = '', array $options = []): void
    {
        app('livewire-swal')->warning($title, $text, $options);
    }

    /**
     * Fire a SweetAlert info notification
     */
    public function swalInfo(string $title = 'Info', string $text = '', array $options = []): void
    {
        app('livewire-swal')->info($title, $text, $options);
    }

    /**
     * Fire a SweetAlert confirmation dialog
     */
    public function swalConfirm(
        string $title = 'Are you sure?',
        string $text = "You won't be able to revert this!",
        string $confirmCallback = null,
        array $options = []
    ): void {
        app('livewire-swal')->confirm($title, $text, $confirmCallback, $options);
    }

    /**
     * Fire a SweetAlert toast notification
     */
    public function swalToast(
        string $title,
        string $icon = 'success',
        string $position = 'top-end',
        array $options = []
    ): void {
        app('livewire-swal')->toast($title, $icon, $position, $options);
    }

    /**
     * Fire a SweetAlert loading dialog
     */
    public function swalLoading(string $title = 'Loading...', string $text = 'Please wait'): void
    {
        app('livewire-swal')->loading($title, $text);
    }

    /**
     * Close any open SweetAlert
     */
    public function swalClose(): void
    {
        app('livewire-swal')->close();
    }

    /**
     * Fire a custom SweetAlert with full options
     */
    public function swalFire(array $options = []): void
    {
        app('livewire-swal')->fire($options);
    }
}
