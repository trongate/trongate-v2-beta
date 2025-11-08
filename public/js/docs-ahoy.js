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

function revealCodeBlocks(targetEls = false, applyPrism = true) {
    if (targetEls === false) {
        targetEls = document.querySelectorAll('.code-block-pending');
    }

    targetEls.forEach(targetEl => {
        targetEl.style.display = 'block';

        let targetElInnerHTML = targetEl.innerHTML;
        targetEl.innerHTML = targetElInnerHTML.replace(/&lt;br&gt;/g, '');

        const targetElParent = targetEl.parentNode;
        const codeBlockPre = document.createElement('pre');
        const codeBlockEl = document.createElement('code');

        let codeContent = decodeCodeSample(targetEl.textContent);
        codeBlockEl.textContent = codeContent;

        const language = targetEl.getAttribute('data-language') || 'text';
        codeBlockPre.setAttribute('class', 'language-' + language);
        codeBlockPre.appendChild(codeBlockEl);
        targetElParent.insertBefore(codeBlockPre, targetEl);

        const codeBlockHeader = document.createElement('div');
        codeBlockHeader.classList.add('code-block-header');

        let displayLanguage = language.toLowerCase();

        if (displayLanguage === 'vf') {
            displayLanguage = 'View File';
        } else if (displayLanguage.includes('j')) {
            displayLanguage = 'JavaScript';
        } else if (displayLanguage === 'text') {
            displayLanguage = 'Plain Text';
        } else {
            displayLanguage = displayLanguage.toUpperCase();
        }

        if (displayLanguage === 'View File') {
            codeBlockPre.classList.remove('language-vf');
            codeBlockPre.classList.add('language-php');
        }

        const innerCodeEl = codeBlockPre.querySelector('code');
        trimElementInnerContent(innerCodeEl);

        const divLhs = document.createElement('div');
        divLhs.innerText = displayLanguage + ':';
        codeBlockHeader.appendChild(divLhs);

        const copyIconDiv = document.createElement('div');
        const copyIcon = document.createElement('i');
        copyIcon.classList.add('fa', 'fa-copy');
        copyIconDiv.classList.add('copy-icon-container');
        copyIconDiv.appendChild(copyIcon);

        codeBlockHeader.appendChild(copyIconDiv);

        const codeSampleDiv = document.createElement('div');
        codeSampleDiv.classList.add('code-sample');
        codeSampleDiv.appendChild(codeBlockHeader);
        codeSampleDiv.appendChild(codeBlockPre);

        targetElParent.insertBefore(codeSampleDiv, targetEl);
        targetEl.remove();

        copyIcon.addEventListener('click', () => {
            const range = document.createRange();
            range.selectNode(codeBlockEl);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            try {
                document.execCommand('copy');
                alert('Code copied to clipboard!');
            } catch (err) {
                console.error('Unable to copy', err);
            }
            window.getSelection().removeAllRanges();
        });

        targetEl.remove();
    });

    if (applyPrism === true) {
        Prism.highlightAll();
    }
}

function decodeCodeSample(encodedContent) {
    const safeEntities = {
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&apos;': "'",
        '&amp;': '&'
    };

    return encodedContent.replace(/&(lt|gt|quot|apos|amp);/g,
        match => safeEntities[match] || match
    );
}

function trimElementInnerContent(element) {
    if (element && element.nodeType === Node.ELEMENT_NODE) {
        let textContent = element.textContent.trim();
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
        if (textContent) {
            element.appendChild(document.createTextNode(textContent));
        }
    } else {
        console.error("The provided element is not a valid DOM element.");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    window.makeAlertsFantasticola();
    const codeBlocks = document.querySelectorAll('.code-block-pending');
    revealCodeBlocks(codeBlocks, true);
    // window.makeIframesFantasticola();
    // window.stylePageNavBtns();
    // updateScrollToTopButton();
});