@if(config('livewire-swal.auto_include_sweetalert', true))
<script src="{{ config('livewire-swal.cdn_url', 'https://cdn.jsdelivr.net/npm/sweetalert2@11') }}"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Determine Livewire version and initialize accordingly
    if (typeof Livewire !== 'undefined') {
        // Check for Livewire 3
        if (typeof Livewire.hook === 'function') {
            // Livewire 3
            document.addEventListener('livewire:init', () => {
                initSwalListenersV3();
            });
        } else {
            // Livewire 2
            document.addEventListener('livewire:load', function () {
                initSwalListenersV2();
            });
        }
    }
});

// Livewire 3 event listeners
function initSwalListenersV3() {
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded. Please include SweetAlert2.');
        return;
    }

    const defaultOptions = @json(config('livewire-swal.default_options', []));
    const buttonColors = @json(config('livewire-swal.button_colors', []));

    Livewire.on('swal:fire', (event) => {
        const [options] = event;
        handleSwalFire(options, defaultOptions, buttonColors);
    });

    Livewire.on('swal:confirm', (event) => {
        const [options] = event;
        handleSwalConfirm(options, defaultOptions, buttonColors, 3);
    });

    Livewire.on('swal:close', () => {
        Swal.close();
    });

    handleSessionAlerts(defaultOptions, buttonColors);
}

// Livewire 2 event listeners
function initSwalListenersV2() {
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded. Please include SweetAlert2.');
        return;
    }

    const defaultOptions = @json(config('livewire-swal.default_options', []));
    const buttonColors = @json(config('livewire-swal.button_colors', []));

    window.addEventListener('swal:fire', event => {
        handleSwalFire(event.detail, defaultOptions, buttonColors);
    });

    window.addEventListener('swal:confirm', event => {
        handleSwalConfirm(event.detail, defaultOptions, buttonColors, 2);
    });

    window.addEventListener('swal:close', () => {
        Swal.close();
    });

    handleSessionAlerts(defaultOptions, buttonColors);
}

function handleSwalFire(options, defaultOptions, buttonColors) {
    const mergedOptions = { ...defaultOptions, ...options };
    
    if (mergedOptions.confirmButtonColor === undefined && buttonColors.confirm) {
        mergedOptions.confirmButtonColor = buttonColors.confirm;
    }
    if (mergedOptions.cancelButtonColor === undefined && buttonColors.cancel) {
        mergedOptions.cancelButtonColor = buttonColors.cancel;
    }

    Swal.fire(mergedOptions);
}

function handleSwalConfirm(options, defaultOptions, buttonColors, livewireVersion) {
    const confirmOptions = {
        ...defaultOptions,
        ...options,
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Yes, do it!',
        cancelButtonText: options.cancelButtonText || 'Cancel',
        confirmButtonColor: options.confirmButtonColor || buttonColors.confirm,
        cancelButtonColor: options.cancelButtonColor || buttonColors.cancel,
    };

    Swal.fire(confirmOptions).then((result) => {
        if (result.isConfirmed && options.confirmCallback) {
            if (livewireVersion === 3) {
                // Livewire 3
                @this.call(options.confirmCallback, options.callbackData || {});
            } else {
                // Livewire 2 - try different approaches
                if (typeof @this !== 'undefined') {
                    @this.call(options.confirmCallback, options.callbackData || {});
                } else if (typeof window.livewire !== 'undefined' && options.componentId) {
                    window.livewire.find(options.componentId).call(options.confirmCallback, options.callbackData || {});
                }
            }
        } else if (result.isDismissed && options.cancelCallback) {
            if (livewireVersion === 3) {
                // Livewire 3
                @this.call(options.cancelCallback, options.callbackData || {});
            } else {
                // Livewire 2
                if (typeof @this !== 'undefined') {
                    @this.call(options.cancelCallback, options.callbackData || {});
                } else if (typeof window.livewire !== 'undefined' && options.componentId) {
                    window.livewire.find(options.componentId).call(options.cancelCallback, options.callbackData || {});
                }
            }
        }
    });
}

function handleSessionAlerts(defaultOptions, buttonColors) {
    @if(session('swal_alert'))
        @php $alert = session('swal_alert'); @endphp
        
        @if($alert['event'] === 'swal:fire')
            const flashOptions = { ...defaultOptions, ...@json($alert['data']) };
            Swal.fire(flashOptions);
        @elseif($alert['event'] === 'swal:confirm')
            const confirmFlashOptions = {
                ...defaultOptions,
                ...@json($alert['data']),
                showCancelButton: true,
                confirmButtonColor: buttonColors.confirm,
                cancelButtonColor: buttonColors.cancel,
            };
            Swal.fire(confirmFlashOptions);
        @endif
    @endif
}
</script>
