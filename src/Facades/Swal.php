<?php

namespace LivewireSwal\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void fire(array $options = [])
 * @method static void success(string $title = 'Success!', string $text = '', array $options = [])
 * @method static void error(string $title = 'Error!', string $text = '', array $options = [])
 * @method static void warning(string $title = 'Warning!', string $text = '', array $options = [])
 * @method static void info(string $title = 'Info', string $text = '', array $options = [])
 * @method static void confirm(string $title = 'Are you sure?', string $text = "You won't be able to revert this!", string $confirmCallback = null, array $options = [])
 * @method static void toast(string $title, string $icon = 'success', string $position = 'top-end', array $options = [])
 * @method static void loading(string $title = 'Loading...', string $text = 'Please wait')
 * @method static void close()
 * 
 * @see \LivewireSwal\SwalService
 */
class Swal extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'livewire-swal';
    }
}
