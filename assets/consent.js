(function () {
    'use strict';

    const CONSENT_KEY = 'ccfw_consent';
    const banner = document.getElementById('ccfw-banner');

    if (!banner) {
        return;
    }

    function getConsent() {
        return localStorage.getItem(CONSENT_KEY);
    }

    function setConsent(value) {
        localStorage.setItem(CONSENT_KEY, value);
    }

    function hideBanner() {
        banner.hidden = true;
    }

    function showBanner() {
        banner.hidden = false;
    }

    function loadConsentedScripts() {
        const scripts = document.querySelectorAll('script[type="text/plain"][data-ccfw-category]');

        scripts.forEach(function (blockedScript) {
            const newScript = document.createElement('script');

            Array.from(blockedScript.attributes).forEach(function (attr) {
                if (attr.name !== 'type' && attr.name !== 'data-ccfw-category') {
                    newScript.setAttribute(attr.name, attr.value);
                }
            });

            newScript.text = blockedScript.text || blockedScript.textContent || '';

            blockedScript.parentNode.replaceChild(newScript, blockedScript);
        });
    }

    if (getConsent() === 'accepted') {
        loadConsentedScripts();
        hideBanner();
        return;
    }

    if (getConsent() === 'rejected') {
        hideBanner();
        return;
    }

    showBanner();

    banner.addEventListener('click', function (event) {
        const button = event.target.closest('[data-ccfw-choice]');

        if (!button) {
            return;
        }

        const choice = button.getAttribute('data-ccfw-choice');

        if (choice === 'accept') {
            setConsent('accepted');
            loadConsentedScripts();
            hideBanner();
        }

        if (choice === 'reject') {
            setConsent('rejected');
            hideBanner();
        }
    });
})();
