// Cursor-following "black hole" for the landing page: a canvas-drawn twin of
// the .bg-grid-overlay grid whose lines bend toward a trailing ball. The
// static CSS grid stays in the DOM for no-JS, reduced-motion, and touch
// users — this module hides it only once it actually takes over.

const GRID = 32;
const LINE_COLOR = 'rgba(42, 54, 70, 0.22)';
const LINE_WIDTH = 1;
const DPR_CAP = 2;

const R = 260; // influence radius
const MAX_PULL = 22; // < GRID, so adjacent lines can never cross
const CORE_SOFT = 56; // softening length for the 1/r² core
const STEP = 8; // vertex sampling inside the influence band

const GLOW_R = 70;
const LERP = 0.12; // trailing factor per 60fps frame
const SETTLE = 0.08; // distance (px) considered "at rest"

let instance = null;

export function initBlackhole() {
    const canvas = document.getElementById('blackhole-canvas');

    // wire:navigate swaps the whole body — tear down when our canvas is gone or replaced.
    if (instance && (instance.canvas !== canvas || ! canvas?.isConnected)) {
        instance.destroy();
        instance = null;
    }

    if (! canvas || instance) return;
    if (! window.matchMedia('(pointer: fine)').matches) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    instance = createInstance(canvas);
}

export function destroyBlackhole() {
    instance?.destroy();
    instance = null;
}

function createInstance(canvas) {
    const ctx = canvas.getContext('2d');
    const ac = new AbortController();
    const staticGrid = document.querySelector('[data-grid-static]');

    let w = 0;
    let h = 0;
    let raf = 0;
    let running = false;
    let lastTs = 0;

    const ball = { x: 0, y: 0 };
    const target = { x: 0, y: 0 };

    const restAnchor = () => ({ x: w * 0.5, y: h * 0.35 });

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, DPR_CAP);
        // clientWidth/Height exclude scrollbars — they match the canvas box exactly.
        w = document.documentElement.clientWidth;
        h = document.documentElement.clientHeight;
        if (w === 0 || h === 0) return;
        canvas.width = Math.round(w * dpr);
        canvas.height = Math.round(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    // Softened inverse-square pull, smoothstep-windowed to exactly 0 at r = R.
    function pull(r) {
        if (r >= R) return 0;
        const s = 1 - r / R;
        const win = s * s * (3 - 2 * s);
        const core = (CORE_SOFT * CORE_SOFT) / (r * r + CORE_SOFT * CORE_SOFT);
        return MAX_PULL * core * win;
    }

    function warpX(x, y) {
        const dx = x - ball.x;
        const dy = y - ball.y;
        const r = Math.max(Math.hypot(dx, dy), 1e-4);
        const p = pull(r);
        return p === 0 ? [x, y] : [x - (dx / r) * p, y - (dy / r) * p];
    }

    function draw() {
        ctx.clearRect(0, 0, w, h);

        ctx.strokeStyle = LINE_COLOR;
        ctx.lineWidth = LINE_WIDTH;
        ctx.beginPath();

        for (let x = 0.5; x <= w; x += GRID) {
            if (Math.abs(x - ball.x) >= R) {
                ctx.moveTo(x, 0);
                ctx.lineTo(x, h);
                continue;
            }
            const y0 = Math.max(0, ball.y - R);
            const y1 = Math.min(h, ball.y + R);
            ctx.moveTo(x, 0);
            if (y0 > 0) ctx.lineTo(x, y0);
            for (let y = y0; y <= y1; y += STEP) {
                const [vx, vy] = warpX(x, y);
                ctx.lineTo(vx, vy);
            }
            ctx.lineTo(x, y1);
            if (y1 < h) ctx.lineTo(x, h);
        }

        for (let y = 0.5; y <= h; y += GRID) {
            if (Math.abs(y - ball.y) >= R) {
                ctx.moveTo(0, y);
                ctx.lineTo(w, y);
                continue;
            }
            const x0 = Math.max(0, ball.x - R);
            const x1 = Math.min(w, ball.x + R);
            ctx.moveTo(0, y);
            if (x0 > 0) ctx.lineTo(x0, y);
            for (let x = x0; x <= x1; x += STEP) {
                const [vx, vy] = warpX(x, y);
                ctx.lineTo(vx, vy);
            }
            ctx.lineTo(x1, y);
            if (x1 < w) ctx.lineTo(w, y);
        }

        ctx.stroke();

        // Fade the grid toward the bottom exactly like .bg-grid-overlay's CSS mask,
        // but in-canvas so it applies to the GRID ONLY — the ball stays visible.
        const rx = 0.9 * w;
        const ry = 0.65 * h;
        ctx.save();
        ctx.globalCompositeOperation = 'destination-out';
        ctx.translate(w / 2, 0);
        ctx.scale(1, ry / rx);
        const fade = ctx.createRadialGradient(0, 0, 0, 0, 0, rx);
        fade.addColorStop(0.25, 'rgba(0, 0, 0, 0)');
        fade.addColorStop(0.75, 'rgba(0, 0, 0, 1)');
        ctx.fillStyle = fade;
        ctx.fillRect(-w / 2, 0, w, (h * rx) / ry);
        ctx.restore();

        // Event horizon: black core → penumbra into the page bg → emerald rim → glow.
        const g = ctx.createRadialGradient(ball.x, ball.y, 0, ball.x, ball.y, GLOW_R);
        g.addColorStop(0, 'rgba(0, 0, 0, 1)');
        g.addColorStop(0.2, 'rgba(0, 0, 0, 1)');
        g.addColorStop(0.245, 'rgba(10, 13, 18, 1)');
        g.addColorStop(0.27, 'rgba(52, 211, 153, 0.5)');
        g.addColorStop(0.36, 'rgba(52, 211, 153, 0.1)');
        g.addColorStop(1, 'rgba(52, 211, 153, 0)');
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(ball.x, ball.y, GLOW_R, 0, Math.PI * 2);
        ctx.fill();
    }

    function step(ts) {
        // Cancel any queued frame so manual step() calls can't stack loops;
        // clamp dt to [0, 50] so out-of-order timestamps can't reverse the lerp.
        cancelAnimationFrame(raf);
        const dt = Math.min(Math.max(lastTs ? ts - lastTs : 16.667, 0), 50);
        lastTs = ts;

        const k = 1 - Math.pow(1 - LERP, dt / 16.667);
        ball.x += (target.x - ball.x) * k;
        ball.y += (target.y - ball.y) * k;

        if (Math.hypot(target.x - ball.x, target.y - ball.y) < SETTLE) {
            ball.x = target.x;
            ball.y = target.y;
            draw();
            running = false;
            return;
        }

        draw();
        raf = requestAnimationFrame(step);
    }

    function wake() {
        if (running) return;
        running = true;
        lastTs = 0;
        raf = requestAnimationFrame(step);
    }

    window.addEventListener('pointermove', (e) => {
        target.x = e.clientX;
        target.y = e.clientY;
        wake();
    }, { passive: true, signal: ac.signal });

    document.documentElement.addEventListener('mouseleave', () => {
        const rest = restAnchor();
        target.x = rest.x;
        target.y = rest.y;
        wake();
    }, { signal: ac.signal });

    window.addEventListener('resize', () => {
        resize();
        wake();
    }, { signal: ac.signal });

    document.addEventListener('visibilitychange', () => {
        lastTs = 0;
    }, { signal: ac.signal });

    resize();
    const rest = restAnchor();
    ball.x = target.x = rest.x;
    ball.y = target.y = rest.y;
    staticGrid?.style.setProperty('visibility', 'hidden');
    draw();

    return {
        canvas,
        step,
        destroy() {
            ac.abort();
            cancelAnimationFrame(raf);
            staticGrid?.style.removeProperty('visibility');
        },
        get state() {
            return { ball: { ...ball }, target: { ...target }, running };
        },
    };
}

// Always-present debug/verification hook — harmless no-ops when inactive.
window.__blackhole = {
    init: initBlackhole,
    destroy: destroyBlackhole,
    step: (ts) => instance?.step(ts ?? performance.now()),
    get state() {
        return instance?.state ?? null;
    },
};
