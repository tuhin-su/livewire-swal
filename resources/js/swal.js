// resources/js/swal.js

(function () {
  const Swal = window.Swal;
  
  if (!Swal) {
    console.error('SweetAlert2 is not loaded. Make sure SweetAlert2 is loaded on window.Swal before loading swal.js');
    return;
  }

  // Toast mixin (small, top corner)
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    timer: 3000,
    timerProgressBar: true,
    showConfirmButton: false,
  })

  // Small toasts
  window.swalToastSuccess = (title = 'Success', text = '', opts = {}) =>
    Toast.fire({ icon: 'success', title, text, ...opts })

  window.swalToastWarning = (title = 'Warning', text = '', opts = {}) =>
    Toast.fire({ icon: 'warning', title, text, ...opts })

  window.swalToastError = (title = 'Error', text = '', opts = {}) =>
    Toast.fire({ icon: 'error', title, text, ...opts })

  // Larger modals
  window.swalFireSuccess = (title = 'Success', text = '', opts = {}) =>
    Swal.fire({ icon: 'success', title, text, ...opts })

  window.swalFireWarning = (title = 'Warning', text = '', opts = {}) =>
    Swal.fire({ icon: 'warning', title, text, ...opts })

  window.swalFireError = (title = 'Error', text = '', opts = {}) =>
    Swal.fire({ icon: 'error', title, text, ...opts })

  // Generic input prompt: returns the entered value or null if cancelled
  window.swalTakeInput = async (cfg = {}) => {
    const {
      title = 'Enter a value',
      text = '',
      input = 'text', // e.g., 'text' | 'email' | 'password' | 'number' | 'textarea'
      inputLabel = '',
      inputPlaceholder = '',
      inputAttributes = {},
      confirmButtonText = 'OK',
      cancelButtonText = 'Cancel',
      showCancelButton = true,
      ...opts
    } = cfg

    const res = await Swal.fire({
      icon: 'question',
      title,
      text,
      input,
      inputLabel,
      inputPlaceholder,
      inputAttributes,
      showCancelButton,
      confirmButtonText,
      cancelButtonText,
      reverseButtons: true,
      ...opts,
    })

    if (res.isConfirmed) return res.value ?? '' // SweetAlert2 returns value on confirm
    return null;
  }

  // Password prompt preset
  window.swalPromptPassword = (cfg = {}) =>
    window.swalTakeInput({
      title: 'Enter your password',
      input: 'password',
      inputLabel: 'Password',
      inputPlaceholder: 'Enter your password',
      inputAttributes: { autocapitalize: 'off', autocorrect: 'off', ...cfg.inputAttributes },
      confirmButtonText: 'Verify',
      ...cfg,
    })

  // Confirm (modal-only): returns boolean
  window.swalFireAsk = async (cfg = {}) => {
    const {
      title = 'Are you sure?',
      text = '',
      confirmButtonText = 'Yes',
      cancelButtonText = 'No',
      ...opts
    } = cfg
    const res = await Swal.fire({
      icon: 'question',
      title,
      text,
      showCancelButton: true,
      confirmButtonText,
      cancelButtonText,
      reverseButtons: true,
      ...opts,
    })
    return res.isConfirmed
  }

  // Execute a secure cryptographically signed action (multi-step) from client-side JS
  window.swalExecuteSecureAction = async (encryptedPayload, requirePassword = false, opts = {}) => {
    const {
      confirmTitle = 'Are you sure?',
      confirmText = '',
      confirmOpts = {},
      passwordTitle = 'Enter your password',
      passwordText = 'Please confirm your password to continue',
      passwordOpts = {}
    } = opts

    // 1. First step: Confirm dialog (if confirmTitle is specified)
    if (confirmTitle) {
      const ok = await window.swalFireAsk({
        title: confirmTitle,
        text: confirmText,
        ...confirmOpts
      })
      if (!ok) return false // User cancelled
    }

    // 2. Second step: Password prompt (if requirePassword is true)
    let password = null
    if (requirePassword) {
      password = await window.swalPromptPassword({
        title: passwordTitle,
        text: passwordText,
        ...passwordOpts
      })
      if (password === null) return false // User cancelled password prompt
    }

    // 3. Final step: Dispatch secure action payload back to server
    Livewire.dispatch('swal.__execute_secure_action', {
      encryptedPayload,
      password
    })
    return true
  }

  // Livewire v3 integration
  document.addEventListener('livewire:init', () => {
    if (window.__swalLivewireBound) return
    window.__swalLivewireBound = true

    // Toast from PHP
    Livewire.on('swal:toast', (cfg = {}) => {
      const { icon, title, text, opts = {} } = cfg || {}
      if (icon === 'success') return window.swalToastSuccess(title ?? 'Success', text ?? '', opts)
      if (icon === 'warning') return window.swalToastWarning(title ?? 'Warning', text ?? '', opts)
      if (icon === 'error')   return window.swalToastError(title ?? 'Error', text ?? '', opts)
      return Toast.fire({ title, text, ...opts })
    })

    // Modal from PHP
    Livewire.on('swal:modal', (cfg = {}) => {
      const { icon, title, text, opts = {} } = cfg || {}
      if (icon === 'success') return window.swalFireSuccess(title ?? 'Success', text ?? '', opts)
      if (icon === 'warning') return window.swalFireWarning(title ?? 'Warning', text ?? '', opts)
      if (icon === 'error')   return window.swalFireError(title ?? 'Error', text ?? '', opts)
      return Swal.fire({ title, text, ...opts })
    })

    // Confirm from PHP
    Livewire.on('swal:confirm', async (cfg = {}) => {
      const { opts = {}, thenEvent, thenParams = {}, thenEventTo } = cfg || {}
      const askCfg = {
        title: cfg.title,
        text: cfg.text,
        ...opts
      }
      const ok = await window.swalFireAsk(askCfg)
      if (ok && thenEvent) {
        if (thenEventTo) Livewire.dispatchTo(thenEventTo, thenEvent, thenParams)
        else Livewire.dispatch(thenEvent, thenParams)
      }
    })

    // Generic input from PHP → send value back via thenEvent
    Livewire.on('swal:input', async (cfg = {}) => {
      const { opts = {}, thenEvent, thenParams = {}, thenEventTo } = cfg || {}
      const inputCfg = {
        title: cfg.title,
        text: cfg.text,
        input: cfg.input,
        ...opts
      }
      const value = await window.swalTakeInput(inputCfg)
      if (value === null || !thenEvent) return
      const payload = { ...thenParams, value }
      if (thenEventTo) Livewire.dispatchTo(thenEventTo, thenEvent, payload)
      else Livewire.dispatch(thenEvent, payload)
    })

    // Password prompt from PHP → send value back via thenEvent
    Livewire.on('swal:password', async (cfg = {}) => {
      const { opts = {}, thenEvent, thenParams = {}, thenEventTo } = cfg || {}
      const passwordCfg = {
        title: cfg.title,
        text: cfg.text,
        ...opts
      }
      const value = await window.swalPromptPassword(passwordCfg)
      if (value === null || !thenEvent) return
      const payload = { ...thenParams, value }
      if (thenEventTo) Livewire.dispatchTo(thenEventTo, thenEvent, payload)
      else Livewire.dispatch(thenEvent, payload)
    })

    // Verify current user password flow
    Livewire.on('swal:verify-current-password', async (cfg = {}) => {
      const {
        opts = {},
        thenEventTrue = null,
        thenParamsTrue = {},
        thenEventFalse = null,
        thenParamsFalse = {},
        thenEventTo = null,
      } = cfg || {}
      const passwordCfg = {
        title: cfg.title,
        text: cfg.text,
        ...opts
      }
      const value = await window.swalPromptPassword(passwordCfg)
      if (value === null) return
      const payload = { value, thenEventTrue, thenParamsTrue, thenEventFalse, thenParamsFalse, thenEventTo }
      Livewire.dispatch('swal.__verify_current_user_password', { payload })
    })

    // Secure cryptographically signed action (prevents client-side bypass)
    Livewire.on('swal:secure-action', async (cfg = {}) => {
      const {
        encryptedPayload,
        requirePassword = false,
        confirmTitle,
        confirmText = '',
        confirmOpts = {},
        passwordTitle,
        passwordText,
        passwordOpts
      } = cfg || {}

      await window.swalExecuteSecureAction(encryptedPayload, requirePassword, {
        confirmTitle,
        confirmText,
        confirmOpts,
        passwordTitle,
        passwordText,
        passwordOpts
      })
    })
  })
})();
