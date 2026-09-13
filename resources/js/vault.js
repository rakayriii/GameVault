window.Vault = {
    copy(text, elementId = null, label = 'Disalin!') {
        const done = () => {
            if (elementId) {
                const el = document.getElementById(elementId);
                if (el) {
                    const original = el.textContent;
                    el.textContent = label;
                    el.classList.add('text-tertiary');
                    window.setTimeout(() => {
                        el.textContent = original;
                        el.classList.remove('text-tertiary');
                    }, 1800);
                }
            }
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done).catch(() => { done(); });
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            done();
        }
    },

    formatAmount(value) {
        const n = Number(String(value).replace(/[^\d]/g, ''));
        return Number.isFinite(n) ? n.toLocaleString('id-ID') : '';
    },

    rupiah(value) {
        const n = Number(String(value).replace(/[^\d]/g, ''));
        return Number.isFinite(n) ? `Rp ${n.toLocaleString('id-ID')}` : 'Rp 0';
    },

    showToast(icon, text) {
        const toast = document.getElementById('toastNotification');
        if (!toast) return;
        document.getElementById('toastIcon').textContent = icon;
        document.getElementById('toastText').textContent = text;
        toast.classList.remove('pointer-events-none');
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
        window.setTimeout(() => {
            toast.style.transform = 'translateY(80px)';
            toast.style.opacity = '0';
            toast.classList.add('pointer-events-none');
        }, 5000);
    },

    startCountdown(hours, minutes, seconds, elementId = 'escrow-countdown') {
        const el = document.getElementById(elementId);
        if (!el) return;
        let total = (hours * 3600) + (minutes * 60) + seconds;

        const render = () => {
            const h = Math.floor(total / 3600);
            const m = Math.floor((total % 3600) / 60);
            const s = total % 60;
            el.innerHTML = '';
            [h, m, s].forEach((part, index) => {
                if (index > 0) {
                    const sep = document.createElement('span');
                    sep.className = 'text-secondary';
                    sep.textContent = ':';
                    el.appendChild(sep);
                }
                const box = document.createElement('span');
                box.className = 'bg-surface-container px-2 py-0.5 rounded text-on-surface';
                box.textContent = String(part).padStart(2, '0');
                el.appendChild(box);
            });
        };

        render();
        const timer = window.setInterval(() => {
            total -= 1;
            if (total < 0) {
                window.clearInterval(timer);
                el.className = el.className.replace(/text-secondary/g, 'text-error');
                el.textContent = 'EXPIRED';
                return;
            }
            render();
        }, 1000);
    },
};

window.gallery = (images) => ({
    images: (images || []).map((img) => ({
        url: img.url,
        caption: img.caption || 'Foto',
    })),
    active: 0,
    zoomed: false,
});

document.addEventListener('DOMContentLoaded', () => {
    const isGuest = document.body.dataset.auth !== 'true';

    document.querySelectorAll('.wishlist-btn').forEach((btn) => {
        btn.addEventListener('click', async (e) => {
            if (isGuest) return;

            e.preventDefault();
            e.stopPropagation();

            const id = btn.dataset.account;
            const icon = btn.querySelector('.material-symbols-outlined');
            if (!id) return;

            btn.disabled = true;

            try {
                const res = await fetch(`/wishlist/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await res.json();

                if (data.ok) {
                    if (data.added) {
                        btn.classList.remove('bg-surface-container-highest', 'text-on-surface-variant', 'hover:bg-surface-container-high', 'hover:text-on-surface');
                        btn.classList.add('bg-secondary-container/30', 'text-secondary');
                        if (icon) icon.textContent = 'favorite';
                        window.Vault.showToast('favorite', 'Ditambahkan ke wishlist');
                    } else {
                        btn.classList.remove('bg-secondary-container/30', 'text-secondary');
                        btn.classList.add('bg-surface-container-highest', 'text-on-surface-variant', 'hover:bg-surface-container-high', 'hover:text-on-surface');
                        if (icon) icon.textContent = 'favorite_border';
                        window.Vault.showToast('favorite_border', 'Dihapus dari wishlist');
                    }
                }
            } catch (err) {
                window.Vault.showToast('error', 'Gagal memperbarui wishlist');
            } finally {
                btn.disabled = false;
            }
        });
    });

    document.querySelectorAll('[data-countdown]').forEach((el) => {
        const seconds = Number(el.dataset.countdown) || 0;
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        const hEl = document.getElementById('hoursVal');
        const mEl = document.getElementById('minsVal');
        const sEl = document.getElementById('secsVal');
        if (hEl && mEl && sEl) {
            window.Vault.startCountdown(h, m, s, el.id);
        } else {
            window.Vault.startCountdown(h, m, s);
        }
    });

    document.querySelectorAll('[data-otp-input]').forEach((input) => {
        input.addEventListener('input', () => {
            const val = input.value.replace(/[^\d]/g, '');
            input.value = val;
            if (val && input.nextElementSibling && input.nextElementSibling.matches('[data-otp-input]')) {
                input.nextElementSibling.focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && input.previousElementSibling && input.previousElementSibling.matches('[data-otp-input]')) {
                input.previousElementSibling.focus();
            }
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.passwordToggle);
            const icon = btn.querySelector('.material-symbols-outlined');
            if (target && icon) {
                const show = target.type === 'password';
                target.type = show ? 'text' : 'password';
                icon.textContent = show ? 'visibility_off' : 'visibility';
            }
        });
    });
});