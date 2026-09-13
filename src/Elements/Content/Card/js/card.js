(() => {
    'use strict';

    window.addEventListener('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/eek-card.default', ($scope) => {
            const root = $scope[0]?.querySelector('.eek-card');

            if (! root) {
                return;
            }

            root.dataset.eekReady = 'true';
        });
    });
})();
