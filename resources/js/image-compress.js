// Kompres foto (JPG/PNG) di browser sebelum diunggah agar tidak kena batas ukuran server (413).
const MAX_SIDE = 1920;
const QUALITY = 0.8;
const MIN_BYTES = 500 * 1024;

function loadImage(file) {
    return new Promise((resolve, reject) => {
        const url = URL.createObjectURL(file);
        const img = new Image();
        img.onload = () => { URL.revokeObjectURL(url); resolve(img); };
        img.onerror = (e) => { URL.revokeObjectURL(url); reject(e); };
        img.src = url;
    });
}

async function compress(file) {
    if (!/^image\/(jpeg|png)$/.test(file.type) || file.size < MIN_BYTES) return file;
    try {
        const img = await loadImage(file);
        const scale = Math.min(1, MAX_SIDE / Math.max(img.width, img.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(img.width * scale);
        canvas.height = Math.round(img.height * scale);
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#fff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        const blob = await new Promise((r) => canvas.toBlob(r, 'image/jpeg', QUALITY));
        if (!blob || blob.size >= file.size) return file;
        const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
        return new File([blob], name, { type: 'image/jpeg', lastModified: Date.now() });
    } catch (e) {
        return file;
    }
}

document.addEventListener('change', async (ev) => {
    const input = ev.target;
    if (!(input instanceof HTMLInputElement) || input.type !== 'file' || !input.files.length) return;
    if (input.dataset.compressing === '1') return;
    if (![...input.files].some((f) => /^image\/(jpeg|png)$/.test(f.type) && f.size >= MIN_BYTES)) return;

    input.dataset.compressing = '1';
    const form = input.form;
    const buttons = form ? [...form.querySelectorAll('button[type=submit],input[type=submit]')] : [];
    buttons.forEach((b) => { b.disabled = true; });
    try {
        const out = await Promise.all([...input.files].map(compress));
        const dt = new DataTransfer();
        out.forEach((f) => dt.items.add(f));
        input.files = dt.files;
    } finally {
        delete input.dataset.compressing;
        buttons.forEach((b) => { b.disabled = false; });
    }
});
