<?php
use CMW\Manager\Notice\WarningManager;

$notices = WarningManager::pullAll();
if (!empty($notices['info']) || !empty($notices['warning']) || !empty($notices['error'])):
    ?>
    <style>
        .cmw-notices{position:fixed;top:16px;right:16px;z-index:9999;max-width:420px}
        .cmw-card{margin:8px 0;padding:12px 14px 22px;border-radius:10px;border:1px solid;position:relative;overflow:hidden;cursor:pointer}
        .cmw-card--error{background:#fee2e2;color:#7f1d1d;border-color:#fecaca}
        .cmw-card--warning{background:#fef3c7;color:#78350f;border-color:#fde68a}
        .cmw-card--info{background:#e0f2fe;color:#0c4a6e;border-color:#bae6fd}
        .cmw-progress{position:absolute;left:0;bottom:0;height:4px;width:100%;opacity:.9;transition:width linear}

        .cmw-card--error .cmw-progress{background:#fca5a5}
        .cmw-card--warning .cmw-progress{background:#fcd34d}
        .cmw-card--info .cmw-progress{background:#93c5fd}

        .cmw-close{
            position:absolute;top:8px;right:8px;
            width:24px;height:24px;display:flex;align-items:center;justify-content:center;
            border-radius:6px;border:1px solid rgba(0,0,0,.08); background:rgba(255,255,255,.6);
            font-size:14px;line-height:1;color:inherit;cursor:pointer;
        }
        .cmw-card--error .cmw-close{background:rgba(255,255,255,.7)}
        .cmw-card--warning .cmw-close{background:rgba(255,255,255,.7)}
        .cmw-card--info   .cmw-close{background:rgba(255,255,255,.7)}
        .cmw-close:hover{filter:brightness(.95)}
        .cmw-close:focus{outline:2px solid rgba(0,0,0,.25);outline-offset:2px}
    </style>

    <div class="cmw-notices" id="cmw-notices">
        <?php foreach ($notices['error'] as $msg): ?>
            <div class="cmw-card cmw-card--error" data-duration="12000" role="status" aria-live="polite">
                <button type="button" class="cmw-close" aria-label="Fermer" title="Fermer">✕</button>
                ⚠️ <strong>Erreur :</strong> <?= $msg ?>
                <div class="cmw-progress"></div>
            </div>
        <?php endforeach; ?>

        <?php foreach ($notices['warning'] as $msg): ?>
            <div class="cmw-card cmw-card--warning" data-duration="12000" role="status" aria-live="polite">
                <button type="button" class="cmw-close" aria-label="Fermer" title="Fermer">✕</button>
                🚧 <strong>Attention :</strong> <?= $msg ?>
                <div class="cmw-progress"></div>
            </div>
        <?php endforeach; ?>

        <?php foreach ($notices['info'] as $msg): ?>
            <div class="cmw-card cmw-card--info" data-duration="12000" role="status" aria-live="polite">
                <button type="button" class="cmw-close" aria-label="Fermer" title="Fermer">✕</button>
                ℹ️ <strong>Info :</strong> <?= $msg ?>
                <div class="cmw-progress"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        (function(){
            const wrap = document.getElementById('cmw-notices');
            const cards = document.querySelectorAll('.cmw-card');

            cards.forEach(card => {
                const dur = parseInt(card.dataset.duration || '12000', 10);
                const bar = card.querySelector('.cmw-progress');
                const closeBtn = card.querySelector('.cmw-close');

                let start = Date.now();
                let remaining = dur;
                let timeout;

                function removeCard() {
                    card.remove();
                    if (!document.querySelector('.cmw-card') && wrap) wrap.remove();
                }

                function startAnim() {
                    bar.style.transition = `width ${remaining}ms linear`;
                    // width initiale = 100% par défaut (CSS implicite)
                    requestAnimationFrame(() => { bar.style.width = '0%'; });
                    timeout = setTimeout(removeCard, remaining);
                    start = Date.now();
                }

                function pauseAnim() {
                    clearTimeout(timeout);
                    const elapsed = Date.now() - start;
                    remaining = Math.max(0, remaining - elapsed);
                    const pct = (remaining / dur) * 100;
                    bar.style.transition = 'none';
                    bar.style.width = pct + '%';
                }

                card.addEventListener('mouseenter', pauseAnim);
                card.addEventListener('mouseleave', () => {
                    if (remaining > 0) startAnim();
                });

                card.addEventListener('click', removeCard);

                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeCard();
                });

                closeBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        removeCard();
                    }
                });

                // Init
                startAnim();
            });
        })();
    </script>
<?php endif; ?>
