import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    initCountdown();
    initResultsRefresh();
    initQrDownload();
});

function initCountdown() {
    const el = document.getElementById('vote-countdown');
    if (!el) return;

    const end = el.dataset.end;
    if (!end) return;

    const target = new Date(end).getTime();

    const tick = () => {
        const diff = target - Date.now();
        if (diff <= 0) {
            el.textContent = 'Votes terminés';
            return;
        }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        el.textContent = `${h}h ${m}m ${s}s`;
        requestAnimationFrame(() => setTimeout(tick, 1000));
    };

    tick();
}

function initResultsRefresh() {
    const container = document.getElementById('results-container');
    if (!container) return;

    const talent = container.dataset.talent;

    setInterval(async () => {
        try {
            const res = await fetch(`/api/resultats?talent=${talent}`);
            const data = await res.json();
            if (data.empty) return;
            window.dispatchEvent(new CustomEvent('results-updated', { detail: data }));
        } catch (_) {}
    }, 30000);
}

function initQrDownload() {
    const btn = document.getElementById('download-qr');
    const svg = document.querySelector('#qr-svg-wrapper svg');
    if (!btn || !svg) return;

    btn.addEventListener('click', () => {
        const serializer = new XMLSerializer();
        const svgStr = serializer.serializeToString(svg);
        const img = new Image();
        const svgBlob = new Blob([svgStr], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);

        img.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 512;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#0a0a0a';
            ctx.fillRect(0, 0, 512, 512);
            ctx.drawImage(img, 0, 0, 512, 512);
            const a = document.createElement('a');
            a.download = 'akwaba-qrcode.png';
            a.href = canvas.toDataURL('image/png');
            a.click();
            URL.revokeObjectURL(url);
        };

        img.src = url;
    });
}
