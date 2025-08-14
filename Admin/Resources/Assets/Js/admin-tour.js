// Admin/Resources/Assets/Js/admin-tour.js
(function () {
    const TOUR_KEY = 'cmw_admin_tour_step';
    const TOUR_DISMISSED = 'cmw_admin_tour_dismissed';
    const TOUR_NAV_FLAG = 'cmw_tour_nav'; // <- flag de nav pour ton menu

    const BASE = (window.PATH_SUBFOLDER || '/').replace(/\/+$/,'/');
    const ADMIN_ROOT = (BASE + 'cmw-admin/').replace(/\/+/g,'/');

    function pagePath(p){
        if (/^https?:\/\//i.test(p)) return p;
        if (p?.startsWith('/')) return (BASE + p.replace(/^\//,'')).replace(/\/+/g,'/');
        return (ADMIN_ROOT + String(p || '').replace(/^\/+/,'')).replace(/\/+/g,'/');
    }
    const samePath = (a,b)=>a.replace(/\/+$/,'')===b.replace(/\/+$/,'');

    function waitForEl(selector, timeout=8000){
        return new Promise((resolve, reject)=>{
            const hit = selector ? document.querySelector(selector) : null;
            if (hit) return resolve(hit);
            if (!selector) return resolve(null);
            const obs = new MutationObserver(()=>{
                const el = document.querySelector(selector);
                if (el){ obs.disconnect(); resolve(el); }
            });
            obs.observe(document.documentElement, { childList:true, subtree:true });
            setTimeout(()=>{ obs.disconnect(); reject(new Error('Element not found: '+selector)); }, timeout);
        });
    }

    function libsReady(){
        const ok = !!window.Popper && !!window.Shepherd;
        if(!window.Popper) console.error('[Tour] Popper.js manquant');
        if(!window.Shepherd) console.error('[Tour] Shepherd manquant');
        return ok;
    }

    // ====== Étapes ======
    const stepsConfig = [
        {
            page: null, element: null, welcome: true,
            title: 'Bienvenue 👋',
            text: 'Passons en revue les fonctionnalités de votre centre d’administration CraftMyWebsite.',
        },

        // Dashboard
        { page: 'dashboard', element: '#shepherd-notification', title: 'Notification', text: 'Suivez les notifications importantes. Configurez un WebHook Discord…', placement: 'bottom' },
        { page: 'dashboard', element: '#shepherd-website',      title: 'Votre site',   text: 'Retour rapide vers le site public.', placement: 'bottom' },
        { page: 'dashboard', element: '#shepherd-dashboard',    title: 'Tableau de bord', text: '“Accueil” de l’admin.', placement: 'right' },
        { page: 'dashboard', element: '#shepherd-menu-0',       title: 'Paramètres',   text: 'Langue, mails, CGU, sécurité, maintenance…', placement: 'right' },
        { page: 'dashboard', element: '#shepherd-menu-6',       title: 'Menus',        text: 'Liens de la barre de navigation.', placement: 'right' },
        { page: 'dashboard', element: '#shepherd-menu-7',       title: 'Mises à jour', text: 'Mettez à jour tôt pour sécurité & features.', placement: 'right' },
        { page: 'dashboard', element: '#shepherd-menu-8',       title: 'Thèmes',       text: 'Modifier, gérer et installer des thèmes.', placement: 'right' },
        { page: 'theme/theme',  element: '#shepherd-sub-menu-10',   title: 'Thèmes installés', text: 'Gérer vos thèmes installés.', placement: 'right' },
        { page: 'theme/market', element: '#shepherd-sub-menu-11',   title: 'Thèmes du market', text: 'Installer de nouveaux thèmes.', placement: 'right' },

        // Users
        { page: 'users', element: '#menu-users', title: 'Utilisateurs', text: 'Gestion des comptes.', placement: 'right' },
    ];

    function buildTour(startIndex = 0, {ignoreDismiss=false} = {}){
        if(!libsReady()) return;

        const tour = new Shepherd.Tour({
            useModalOverlay: true,
            defaultStepOptions: {
                scrollTo: { behavior: 'smooth', block: 'center' },
                canClickTarget: false,
                cancelIcon: { enabled: true }
            }
        });

        stepsConfig.forEach((s, idx)=>{
            const def = {
                id: 'step-'+idx,
                title: s.title,
                text: s.text,
                buttons: []
            };

            // Boutons
            if (s.welcome) {
                def.buttons = [
                    { text: 'Ne plus proposer', action: () => { localStorage.setItem(TOUR_DISMISSED,'1'); localStorage.removeItem(TOUR_KEY); tour.cancel(); } },
                    { text: 'Commencer', action: () => tour.next() },
                ];
            } else {
                if (idx > 0) def.buttons.push({ text: 'Précédent', action: tour.back });
                def.buttons.push(
                    idx < stepsConfig.length - 1
                        ? { text: 'Suivant', action: tour.next }
                        : { text: 'Terminer', action: tour.complete }
                );
            }

            // AttachTo seulement si cible définie
            if (s.element) def.attachTo = { element: s.element, on: s.placement || 'bottom' };

            // Redirection + attente DOM (sauf welcome)
            def.beforeShowPromise = () => {
                if (s.welcome) return Promise.resolve();

                const targetPath = pagePath(s.page);
                const targetUrlPath = new URL(targetPath, location.origin).pathname;

                if (!samePath(location.pathname, targetUrlPath)) {
                    // >>> Flag pour empêcher l’ouverture auto du menu après redirection
                    sessionStorage.setItem(TOUR_NAV_FLAG, '1');
                    // Sauvegarde progression
                    localStorage.setItem(TOUR_KEY, String(idx));
                    // Redirection
                    location.href = targetPath;
                    return new Promise(()=>{});
                }

                // Attendre l’élément si nécessaire
                return waitForEl(s.element).catch(()=>{});
            };

            tour.addStep(def);
        });

        // Sauvegarde progression
        tour.on('show', ()=>{
            const i = tour.steps.indexOf(tour.currentStep);
            localStorage.setItem(TOUR_KEY, String(i));
        });

        const clear = ()=> localStorage.removeItem(TOUR_KEY);
        tour.on('complete', clear);
        tour.on('cancel', clear);

        if (ignoreDismiss) localStorage.removeItem(TOUR_DISMISSED);

        tour.start();
        if (startIndex > 0) tour.show(`step-${startIndex}`);
        return tour;
    }

    function init(){
        if(!libsReady()) return;

        const saved = localStorage.getItem(TOUR_KEY);
        const dismissed = localStorage.getItem(TOUR_DISMISSED) === '1';

        if (saved !== null) {
            buildTour(parseInt(saved,10));
        } else if (!dismissed) {
            buildTour(0); // première visite → welcome auto
        }

        // Boutons globaux
        window.CMW_startAdminTour = () => buildTour(0, { ignoreDismiss:true });
        window.CMW_resetAdminTour = () => {
            localStorage.removeItem(TOUR_DISMISSED);
            localStorage.removeItem(TOUR_KEY);
            buildTour(0, { ignoreDismiss:true });
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
