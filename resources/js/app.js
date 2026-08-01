document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-pw-toggle]');
    if (!btn) return;

    const input = document.getElementById(btn.dataset.pwToggle);
    if (!input) return;

    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';

    btn.setAttribute('aria-pressed', String(show));
    btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');

    btn.querySelectorAll('[data-pw-icon]').forEach((icon) => {
        icon.classList.toggle('hidden', icon.dataset.pwIcon !== (show ? 'eye-off' : 'eye'));
    });
});

const formatRupiah = (digits) => digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

document.addEventListener('input', (e) => {
    const input = e.target.closest('[data-rupiah]');
    if (!input) return;

    const caret = input.selectionStart ?? input.value.length;
    const digitsBefore = input.value.slice(0, caret).replace(/\D/g, '');
    const formatted = formatRupiah(input.value.replace(/\D/g, ''));

    if (input.value === formatted) return;

    input.value = formatted;
    const newCaret = formatRupiah(digitsBefore).length;
    input.setSelectionRange(newCaret, newCaret);
});

const initDescToggles = () => {
    document.querySelectorAll('[data-desc]').forEach((wrap) => {
        const text = wrap.querySelector('[data-desc-text]');
        const btn = wrap.querySelector('[data-desc-toggle]');
        if (!btn || !text) return;
        if (text.scrollHeight <= text.clientHeight + 1) btn.classList.add('hidden');
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDescToggles);
} else {
    initDescToggles();
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-desc-toggle]');
    if (!btn) return;

    const text = btn.closest('[data-desc]').querySelector('[data-desc-text]');
    const expanded = text.classList.toggle('line-clamp-2') === false;
    btn.querySelector('[data-desc-arrow]')?.classList.toggle('rotate-180', expanded);
    btn.setAttribute('aria-expanded', String(expanded));
});


