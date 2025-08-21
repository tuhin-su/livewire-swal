<?php

namespace LivewireSwal;

class SwalService
{
    /**
     * Fire a basic SweetAlert
     */
    public function fire(array $options = []): void
    {
        $this->dispatch('swal:fire', $options);
    }

    /**
     * Fire a success alert
     */
    public function success(string $title = 'Success!', string $text = '', array $options = []): void
    {
        $defaultOptions = [
            'title' => $title,
            'text' => $text,
            'icon' => 'success',
            'timer' => config('livewire-swal.default_timer', 3000),
            'showConfirmButton' => false,
        ];

        $this->fire(array_merge($defaultOptions, $options));
    }

    /**
     * Fire an error alert
     */
    public function error(string $title = 'Error!', string $text = '', array $options = []): void
    {
        $defaultOptions = [
            'title' => $title,
            'text' => $text,
            'icon' => 'error',
            'showConfirmButton' => true,
        ];

        $this->fire(array_merge($defaultOptions, $options));
    }

    /**
     * Fire a warning alert
     */
    public function warning(string $title = 'Warning!', string $text = '', array $options = []): void
    {
        $defaultOptions = [
            'title' => $title,
            'text' => $text,
            'icon' => 'warning',
            'showConfirmButton' => true,
        ];

        $this->fire(array_merge($defaultOptions, $options));
    }

    /**
     * Fire an info alert
     */
    public function info(string $title = 'Info', string $text = '', array $options = []): void
    {
        $defaultOptions = [
            'title' => $title,
            'text' => $text,
            'icon' => 'info',
            'timer' => config('livewire-swal.default_timer', 3000),
            'showConfirmButton' => false,
        ];

        $this->fire(array_merge($defaultOptions, $options));
    }

    /**
     * Fire a confirmation alert
     */
    public function confirm(
        string $title = 'Are you sure?',
        string $text = "You won't be able to revert this!",
        string $confirmCallback = null,
        array $options = []
    ): void {
        $defaultOptions = [
            'title' => $title,
            'text' => $text,
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => 'Yes, do it!',
            'cancelButtonText' => 'Cancel',
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor' => '#d33',
        ];

        if ($confirmCallback) {
            $defaultOptions['confirmCallback'] = $confirmCallback;
        }

        $this->dispatch('swal:confirm', array_merge($defaultOptions, $options));
    }

    /**
     * Fire a toast notification
     */
    public function toast(
        string $title,
        string $icon = 'success',
        string $position = 'top-end',
        array $options = []
    ): void {
        $defaultOptions = [
            'toast' => true,
            'position' => $position,
            'title' => $title,
            'icon' => $icon,
            'showConfirmButton' => false,
            'timer' => config('livewire-swal.toast_timer', 3000),
            'timerProgressBar' => true,
        ];

        $this->fire(array_merge($defaultOptions, $options));
    }

    /**
     * Fire a loading alert
     */
    public function loading(string $title = 'Loading...', string $text = 'Please wait'): void
    {
        $this->fire([
            'title' => $title,
            'text' => $text,
            'allowOutsideClick' => false,
            'allowEscapeKey' => false,
            'showConfirmButton' => false,
            'didOpen' => 'Swal.showLoading()',
        ]);
    }

    /**
     * Close any open SweetAlert
     */
    public function close(): void
    {
        $this->dispatch('swal:close');
    }

    /**
     * Dispatch browser event for Livewire
     */
    protected function dispatch(string $event, array $data = []): void
    {
        // Check if we're in a Livewire component context
        if (app()->bound('livewire') && app('livewire')->current()) {
            app('livewire')->current()->dispatch($event, $data);
        } else {
            // Fallback: store in session for next request
            session()->flash('swal_alert', ['event' => $event, 'data' => $data]);
        }
    }
}
