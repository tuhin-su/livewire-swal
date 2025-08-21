@if(config('livewire-swal.auto_include_sweetalert', true))
<script src="{{ config('livewire-swal.cdn_url', 'https://cdn.jsdelivr.net/npm/sweetalert2@11') }}"></script>
@endif

<script>
document.addEventListener('livewire:init', () => {
    // Check if SweetAlert2 is loaded
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 is not loaded. Please include SweetAlert2 or set auto_include_sweetalert to true in config.');
        return;
    }

    const defaultOptions = @json(config('livewire-swal.default_options', []));
    const buttonColors = @json(config('livewire-swal.button_colors', []));

    // Handle basic swal:fire events
    Livewire.on('swal:fire', (event) => {
        const [options] = event;
        const mergedOptions = { ...defaultOptions, ...options };
        
        // Apply button colors if not specified
        if (mergedOptions.confirmButtonColor === undefined && buttonColors.confirm) {
            mergedOptions.confirmButtonColor = buttonColors.confirm;
        }
        if (mergedOptions.cancelButtonColor === undefined && buttonColors.cancel) {
            mergedOptions.cancelButtonColor = buttonColors.cancel;
        }

        Swal.fire(mergedOptions);
    });

    // Handle confirmation dialogs with enhanced features
    Livewire.on('swal:confirm', (event) => {
        const [options] = event;
        
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
            if (result.isConfirmed) {
                // Call the confirmation callback if provided
                if (options.confirmCallback) {
                    @this.call(options.confirmCallback, options.callbackData || {});
                }
            } else if (result.isDismissed && options.cancelCallback) {
                // Call the cancel callback if provided
                @this.call(options.cancelCallback, options.callbackData || {});
            }
        });
    });

    // Handle close events
    Livewire.on('swal:close', () => {
        Swal.close();
    });

    // Handle session flash messages for non-Livewire contexts
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
});
</script>
