// =============================================
// WARUNGKU - Main JavaScript
// =============================================

// Category filter for menu
document.addEventListener('DOMContentLoaded', function () {
    // Category filter
    const catBtns = document.querySelectorAll('.cat-btn');
    const menuCards = document.querySelectorAll('.menu-card[data-kategori]');

    catBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            catBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const kat = this.dataset.kategori;
            menuCards.forEach(card => {
                if (kat === 'semua' || card.dataset.kategori === kat) {
                    card.style.display = '';
                    card.style.animation = 'fadeIn .3s ease';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Payment option highlight
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(opt => {
        opt.addEventListener('click', function () {
            paymentOptions.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Quantity controls
    document.querySelectorAll('.qty-control').forEach(ctrl => {
        const btn_plus = ctrl.querySelector('.qty-plus');
        const btn_minus = ctrl.querySelector('.qty-minus');
        const input = ctrl.querySelector('input');

        if (btn_plus) {
            btn_plus.addEventListener('click', () => {
                input.value = parseInt(input.value) + 1;
                input.dispatchEvent(new Event('change'));
            });
        }

        if (btn_minus) {
            btn_minus.addEventListener('click', () => {
                const min = parseInt(input.min) || 1;
                if (parseInt(input.value) > min) {
                    input.value = parseInt(input.value) - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
    });

    // Auto-submit cart qty on change (with debounce)
    let debounceTimer;
    document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.addEventListener('change', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                this.closest('form').submit();
            }, 600);
        });
    });
});

// Fade-in animation
const style = document.createElement('style');
style.textContent = `@keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }`;
document.head.appendChild(style);

// Confirm delete
function confirmDelete(msg) {
    return confirm(msg || 'Yakin ingin menghapus?');
}

// Print order
function printOrder() {
    window.print();
}
