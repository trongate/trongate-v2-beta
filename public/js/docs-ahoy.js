window.makeAlertsFantasticola = window.makeAlertsFantasticola || ((alertEls = document.querySelectorAll('.alert')) => {
    const alertTypes = {
        'alert-info': { headline: 'Just To Let You Know...', icon: 'fa-info-circle' },
        'alert-warning': { headline: 'Warning!', icon: 'fa-exclamation-circle' },
        'alert-success': { headline: 'Best Practices', icon: 'fa-star' },
        'alert-danger': { headline: 'Danger!', icon: 'fa-warning' }
    };

    alertEls.forEach(alertEl => {
        const alertBodyInner = alertEl.innerHTML.trim();
        const alertType = Object.keys(alertTypes).find(type => alertEl.classList.contains(type));
        const { headline = 'Alert', icon = 'fa-star' } = alertTypes[alertType] || {};

        alertEl.innerHTML = `
            <div class="alert-heading">
                <i class="fa ${icon}"></i> ${headline}
            </div>
            <div class="alert-body">${alertBodyInner}</div>
        `;
    });
});

document.addEventListener("DOMContentLoaded", () => {
    window.makeAlertsFantasticola();
    // window.makeIframesFantasticola();
    // window.stylePageNavBtns();
    // updateScrollToTopButton();
});