<?php

namespace LaravelSwal\Tests\Feature\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Livewire;
use LaravelSwal\Tests\TestCase;
use LaravelSwal\Traits\Swal;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DummyComponent extends Component
{
    use Swal;

    public function triggerToastSuccess()
    {
        $this->swalToastSuccess('Test Title', 'Test Text');
    }

    public function triggerModalError()
    {
        $this->swalFireError('Error Title', 'Error Text', ['timer' => 3000]);
    }

    public function triggerConfirm()
    {
        $this->swalConfirm('Confirm?', 'Are you sure?', ['confirmButtonText' => 'Yes'], 'confirmedEvent');
    }

    public function render()
    {
        return '<div>Dummy</div>';
    }
}

class SwalTraitTest extends TestCase
{
    public function test_it_dispatches_toast_success()
    {
        Livewire::test(DummyComponent::class)
            ->call('triggerToastSuccess')
            ->assertDispatched('swal:toast', function ($name, $payload) {
                return $payload['icon'] === 'success' && 
                       $payload['title'] === 'Test Title' && 
                       $payload['text'] === 'Test Text';
            });
    }

    public function test_it_dispatches_modal_error_with_options()
    {
        Livewire::test(DummyComponent::class)
            ->call('triggerModalError')
            ->assertDispatched('swal:modal', function ($name, $payload) {
                return $payload['icon'] === 'error' && 
                       $payload['title'] === 'Error Title' && 
                       $payload['opts']['timer'] === 3000;
            });
    }

    public function test_it_dispatches_confirm()
    {
        Livewire::test(DummyComponent::class)
            ->call('triggerConfirm')
            ->assertDispatched('swal:confirm', function ($name, $payload) {
                return $payload['title'] === 'Confirm?' && 
                       $payload['thenEvent'] === 'confirmedEvent' &&
                       $payload['opts']['confirmButtonText'] === 'Yes';
            });
    }
}
