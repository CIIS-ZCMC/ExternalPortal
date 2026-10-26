@php
    $currentUser = Auth::guard('external')->user() ?? Auth::guard('administrator')->user() ?? auth()->user();
    $is2faEnabled = (bool) ($currentUser?->is_2fa_enabled ?? false);
@endphp

<div id="two-factor-auth-settings-root">
    <!-- 2FA Settings Card -->
    <div class="gauth-card">
        <div class="gauth-card-body">
            <div class="gauth-info-group">
                <div class="gauth-icon-box">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <div class="gauth-title">
                        <span>Google Authenticator (2FA)</span>
                        @if ($is2faEnabled)
                            <span class="gauth-badge gauth-badge-active">
                                <span class="gauth-pulse-dot"></span>
                                Active &amp; Protected
                            </span>
                        @else
                            <span class="gauth-badge gauth-badge-disabled">
                                Disabled
                            </span>
                        @endif
                    </div>
                    <p class="gauth-description">
                        Add an extra layer of security to your personnel account. When enabled, signing in will require a 6-digit TOTP verification code from your Google Authenticator or compatible app.
                    </p>
                    @if ($is2faEnabled && $currentUser->two_factor_confirmed_at)
                        <p class="gauth-timestamp">
                            Activated on {{ \Carbon\Carbon::parse($currentUser->two_factor_confirmed_at)->format('F d, Y \a\t h:i A') }}
                        </p>
                    @endif
                </div>
            </div>

            <div>
                @if ($is2faEnabled)
                    <button
                        type="button"
                        onclick="window.disable2FA()"
                        id="disable-2fa-btn"
                        class="gauth-btn gauth-btn-danger"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Disable 2FA</span>
                    </button>
                @else
                    <button
                        type="button"
                        onclick="window.openSetupModal()"
                        class="gauth-btn gauth-btn-primary"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Set up Google Authenticator</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- REGISTRATION MODAL OVERLAY -->
    <div id="setup-modal" class="gauth-modal-overlay">
        <div class="gauth-modal-dialog">
            <!-- Modal Header -->
            <div class="gauth-modal-header">
                <div class="gauth-modal-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Pair Google Authenticator</span>
                </div>
                <button type="button" onclick="window.closeSetupModal()" class="gauth-close-btn" aria-label="Close modal">&times;</button>
            </div>

            <!-- Modal Loading State -->
            <div id="modal-loading" class="gauth-loading">
                <div class="gauth-spinner"></div>
                <p>Initializing secure QR Code with 2FA server...</p>
            </div>

            <!-- Modal Content -->
            <div id="modal-content" class="gauth-modal-body" style="display: none;">
                <div style="text-align: center;">
                    <p style="font-size: 0.75rem; color: #475569; margin: 0;">
                        Scan this QR code with <strong>Google Authenticator</strong> or any TOTP app:
                    </p>
                    <div class="gauth-qr-frame">
                        <img id="qr-image" src="" alt="Google Authenticator QR Code" class="gauth-qr-image" />
                    </div>
                </div>

                <!-- Manual Setup Key -->
                <div style="text-align: center;">
                    <button type="button" onclick="document.getElementById('manual-key-box').style.display = (document.getElementById('manual-key-box').style.display === 'none' ? 'block' : 'none')" class="gauth-link-btn">
                        Cannot scan? View manual setup key
                    </button>
                    <div id="manual-key-box" class="gauth-manual-key-box" style="display: none;">
                        <span id="manual-key-text"></span>
                    </div>
                </div>

                <!-- Emergency Recovery Codes -->
                <div class="gauth-recovery-box">
                    <div class="gauth-recovery-header">
                        <span class="gauth-recovery-title">Emergency Backup Recovery Codes</span>
                        <button type="button" onclick="window.copyRecoveryCodes()" id="copy-btn" class="gauth-recovery-copy">Copy All</button>
                    </div>
                    <p class="gauth-recovery-desc">
                        Save these codes in a safe place. Each code can be used once if you lose your phone.
                    </p>
                    <div id="recovery-codes-grid" class="gauth-recovery-grid"></div>
                </div>

                <!-- First Code Confirmation -->
                <form id="activate-form" onsubmit="window.submitFirstCode(event)" class="gauth-activate-form">
                    <label class="gauth-label">
                        Enter the 6-digit code from your app to confirm:
                    </label>
                    <input
                        type="text"
                        id="activation-code"
                        inputmode="numeric"
                        maxlength="6"
                        placeholder="123456"
                        autocomplete="one-time-code"
                        required
                        class="gauth-code-input"
                    />
                    <div id="activation-error" class="gauth-alert-error" style="display: none;"></div>

                    <button
                        type="submit"
                        id="activate-btn"
                        class="gauth-btn gauth-btn-primary"
                        style="width: 100%; padding-top: 0.75rem; padding-bottom: 0.75rem;"
                    >
                        <span>Confirm &amp; Activate 2FA</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@script
<script>
    let recoveryCodesList = [];

    window.openSetupModal = async function() {
        const modal = document.getElementById('setup-modal');
        const loading = document.getElementById('modal-loading');
        const content = document.getElementById('modal-content');
        const errBox = document.getElementById('activation-error');
        const codeInput = document.getElementById('activation-code');

        modal.classList.add('is-active');
        loading.style.display = 'flex';
        content.style.display = 'none';
        errBox.style.display = 'none';
        codeInput.value = '';

        try {
            const res = await fetch('{{ route('settings.2fa.enroll') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const result = await res.json();

            if (!result.success) {
                alert(result.message || 'Error generating QR code.');
                window.closeSetupModal();
                return;
            }

            const data = result.data;
            document.getElementById('qr-image').src = data.qr_code_data_uri;
            document.getElementById('manual-key-text').innerText = data.manual_entry_key;

            recoveryCodesList = data.recovery_codes || [];
            const grid = document.getElementById('recovery-codes-grid');
            grid.innerHTML = '';
            recoveryCodesList.forEach(code => {
                const item = document.createElement('div');
                item.className = 'gauth-recovery-item';
                item.textContent = code;
                grid.appendChild(item);
            });

            loading.style.display = 'none';
            content.style.display = 'flex';
            setTimeout(() => {
                codeInput.focus();
            }, 100);
        } catch (err) {
            alert('Cannot connect to 2FA service. Please ensure the authentication server is running.');
            window.closeSetupModal();
        }
    };

    window.closeSetupModal = function() {
        document.getElementById('setup-modal').classList.remove('is-active');
    };

    window.copyRecoveryCodes = function() {
        if (!recoveryCodesList.length) return;
        navigator.clipboard.writeText(recoveryCodesList.join('\n')).then(() => {
            const btn = document.getElementById('copy-btn');
            btn.innerText = 'Copied!';
            setTimeout(() => { btn.innerText = 'Copy All'; }, 2000);
        });
    };

    window.submitFirstCode = async function(e) {
        e.preventDefault();
        const code = document.getElementById('activation-code').value.trim();
        const errBox = document.getElementById('activation-error');
        const btn = document.getElementById('activate-btn');

        errBox.style.display = 'none';
        btn.disabled = true;
        btn.querySelector('span').innerText = 'Activating...';

        try {
            const res = await fetch('{{ route('settings.2fa.activate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ code })
            });
            const result = await res.json();

            if (result.success) {
                alert('Google Authenticator 2FA has been successfully activated for your account!');
                window.location.href = window.location.pathname;
            } else {
                errBox.innerText = result.message || 'Invalid code entered. Check your phone time and try again.';
                errBox.style.display = 'block';
                btn.disabled = false;
                btn.querySelector('span').innerText = 'Confirm & Activate 2FA';
            }
        } catch (err) {
            errBox.innerText = 'Network error while validating activation code.';
            errBox.style.display = 'block';
            btn.disabled = false;
            btn.querySelector('span').innerText = 'Confirm & Activate 2FA';
        }
    };

    window.disable2FA = async function() {
        if (!confirm('Are you sure you want to disable Google Authenticator 2FA for your account? Your account will only be protected by your password.')) {
            return;
        }

        const btn = document.getElementById('disable-2fa-btn');
        btn.disabled = true;
        btn.querySelector('span').innerText = 'Disabling...';

        try {
            const res = await fetch('{{ route('settings.2fa.disable') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const result = await res.json();

            if (result.success) {
                alert('Two-factor authentication has been disabled.');
                window.location.href = window.location.pathname;
            } else {
                alert(result.message || 'Error disabling 2FA.');
                btn.disabled = false;
                btn.querySelector('span').innerText = 'Disable 2FA';
            }
        } catch (err) {
            alert('Network error while disabling 2FA.');
            btn.disabled = false;
            btn.querySelector('span').innerText = 'Disable 2FA';
        }
    };
</script>
@endscript
