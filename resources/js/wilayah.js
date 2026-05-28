/**
 * Handler dropdown bertingkat Wilayah Indonesia.
 * Cari elemen .alamat-fields lalu pasang event listener.
 *
 * Dependencies: fetch (browser native).
 */

const ENDPOINT = {
    provinsi:  '/api/wilayah/provinsi',
    kota:      (id) => `/api/wilayah/kota/${id}`,
    kecamatan: (id) => `/api/wilayah/kecamatan/${id}`,
    kelurahan: (id) => `/api/wilayah/kelurahan/${id}`,
};

const CHILDREN = {
    provinsi:  'kota',
    kota:      'kecamatan',
    kecamatan: 'kelurahan',
    kelurahan: null,
};

const ORDER = ['provinsi', 'kota', 'kecamatan', 'kelurahan'];

// Memory cache (per page) supaya tidak fetch ulang saat user balik-balik
const memCache = new Map();

async function fetchJson(url) {
    if (memCache.has(url)) return memCache.get(url);

    // Sederhana: 1x retry kalau koneksi gagal
    let lastErr;
    for (let attempt = 0; attempt < 2; attempt++) {
        try {
            const res = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (!Array.isArray(data)) throw new Error('Format respons tidak valid');
            memCache.set(url, data);
            return data;
        } catch (e) {
            lastErr = e;
            // jeda kecil sebelum retry
            await new Promise(r => setTimeout(r, 250));
        }
    }
    throw lastErr || new Error('Gagal memuat data');
}

function showStatus(root, text) {
    const wrap = root.querySelector('[data-wilayah-status]');
    const textEl = root.querySelector('[data-wilayah-status-text]');
    if (!wrap) return;
    if (text) {
        wrap.classList.remove('hidden');
        wrap.classList.add('flex');
        if (textEl) textEl.textContent = text;
    } else {
        wrap.classList.add('hidden');
        wrap.classList.remove('flex');
    }
}

function showError(root, msg) {
    const err = root.querySelector('[data-wilayah-error]');
    if (!err) return;
    if (msg) {
        err.textContent = msg;
        err.classList.remove('hidden');
    } else {
        err.textContent = '';
        err.classList.add('hidden');
    }
}

function setSelectEnabled(select, enabled) {
    select.disabled = !enabled;
    if (enabled) {
        select.classList.remove('bg-zinc-50');
        select.classList.add('bg-white');
    } else {
        select.classList.add('bg-zinc-50');
        select.classList.remove('bg-white');
    }
}

function resetSelect(root, level) {
    const sel = root.querySelector(`[data-wilayah="${level}"]`);
    if (!sel) return;
    sel.innerHTML = `<option value="">— Pilih ${labelOf(level)} —</option>`;
    setSelectEnabled(sel, false);
    const nama = root.querySelector(`[data-wilayah-nama="${level}"]`);
    if (nama) nama.value = '';
}

function labelOf(level) {
    return {
        provinsi:  'Provinsi',
        kota:      'Kota / Kabupaten',
        kecamatan: 'Kecamatan',
        kelurahan: 'Kelurahan / Desa',
    }[level] || level;
}

async function loadOptions(root, level, parentId, selectedId = null, selectedName = null) {
    const sel = root.querySelector(`[data-wilayah="${level}"]`);
    if (!sel) return;

    showError(root, '');
    setSelectEnabled(sel, false);
    sel.innerHTML = `<option value="">Memuat ${labelOf(level)}…</option>`;
    showStatus(root, `Memuat ${labelOf(level)}…`);

    try {
        let url;
        if (level === 'provinsi') {
            url = ENDPOINT.provinsi;
        } else {
            if (!parentId) {
                // tidak ada parent id, biarkan kosong
                sel.innerHTML = `<option value="">— Pilih ${labelOf(level)} —</option>`;
                setSelectEnabled(sel, false);
                return;
            }
            url = ENDPOINT[level](parentId);
        }

        const data = await fetchJson(url);
        sel.innerHTML = `<option value="">— Pilih ${labelOf(level)} —</option>`;

        for (const item of data) {
            const opt = document.createElement('option');
            opt.value = String(item.id);
            opt.textContent = item.name;
            opt.dataset.name = item.name;
            sel.appendChild(opt);
        }

        setSelectEnabled(sel, true);

        // Auto pilih kalau ada nilai sebelumnya
        if (selectedId) {
            sel.value = String(selectedId);
            if (sel.value === String(selectedId)) {
                const nama = root.querySelector(`[data-wilayah-nama="${level}"]`);
                if (nama) nama.value = selectedName || sel.options[sel.selectedIndex]?.dataset?.name || '';

                const child = CHILDREN[level];
                if (child) {
                    const childInitId   = root.dataset[`current${cap(child)}Id`];
                    const childInitName = root.dataset[`current${cap(child)}Nama`];
                    await loadOptions(root, child, selectedId, childInitId, childInitName);
                }
            }
        }
    } catch (e) {
        sel.innerHTML = `<option value="">— Pilih ${labelOf(level)} —</option>`;
        setSelectEnabled(sel, false);
        console.error('[wilayah]', level, e);
        showError(root, `Gagal memuat data ${labelOf(level)}. Periksa koneksi internet Anda lalu coba lagi.`);
    } finally {
        showStatus(root, '');
    }
}

function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

function bind(root) {
    if (!root || root.dataset.wilayahBound === '1') return;
    root.dataset.wilayahBound = '1';

    ORDER.forEach((level) => {
        const sel = root.querySelector(`[data-wilayah="${level}"]`);
        if (!sel) return;
        sel.addEventListener('change', () => {
            const selectedId = sel.value;
            const selectedName = sel.options[sel.selectedIndex]?.dataset?.name || '';
            const nama = root.querySelector(`[data-wilayah-nama="${level}"]`);
            if (nama) nama.value = selectedName;

            const child = CHILDREN[level];
            if (!child) return;

            // Reset semua child di bawahnya
            const idx = ORDER.indexOf(level);
            for (let i = idx + 1; i < ORDER.length; i++) {
                resetSelect(root, ORDER[i]);
            }

            if (selectedId) {
                loadOptions(root, child, selectedId);
            }
        });
    });

    // Inisialisasi pertama: load provinsi (dengan nilai awal kalau ada)
    const initProvId   = root.dataset.currentProvinsiId || null;
    const initProvNama = root.dataset.currentProvinsiNama || null;
    loadOptions(root, 'provinsi', null, initProvId, initProvNama);
}

function init(scope = document) {
    const nodes = (scope.querySelectorAll ? scope.querySelectorAll('.alamat-fields') : []);
    nodes.forEach(bind);
}

// 1) Init saat DOM siap (atau langsung kalau sudah siap)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => init());
} else {
    init();
}

// 2) Re-bind ketika modal profile dibuka (kontennya sudah ada di DOM tapi awalnya tersembunyi)
document.addEventListener('open-profile-modal', () => {
    setTimeout(() => init(), 50);
});

// 3) Observasi DOM untuk handle konten yang disisipkan secara dinamis (Livewire / modal)
if (typeof MutationObserver !== 'undefined') {
    const mo = new MutationObserver((mutations) => {
        for (const m of mutations) {
            m.addedNodes.forEach((node) => {
                if (!(node instanceof HTMLElement)) return;
                if (node.classList && node.classList.contains('alamat-fields')) {
                    bind(node);
                } else {
                    node.querySelectorAll && node.querySelectorAll('.alamat-fields').forEach(bind);
                }
            });
        }
    });
    mo.observe(document.body, { childList: true, subtree: true });
}

// Ekspor untuk kebutuhan re-init manual
window.PILAR = window.PILAR || {};
window.PILAR.initWilayah = init;
