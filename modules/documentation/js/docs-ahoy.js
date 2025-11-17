const IFRAME_MODAL_ID = 'iframe-modal';

function jumpToUrl(url) {
    window.location.href = url;
}

function openIframeModal(targetUrl, width, height) {

    const { overlay, iframe, spinnerContainer } = createModal(width, height);

    // Remove spinner when iframe loads
    iframe.addEventListener('load', () => {
        spinnerContainer.remove();
    });

    iframe.src = targetUrl;

    attachModalEventListeners(overlay);
    document.body.appendChild(overlay);
    adjustIframeSize(iframe);
}

function adjustIframeSize(iframe) {
    const targetEl = document.querySelector('.iframe-modal-content');
    targetEl.style.maxHeight = '900px';
}

function createModal(width=800, height=600) {

    const iframeModalOverlay = document.createElement("div");
    iframeModalOverlay.setAttribute("id", IFRAME_MODAL_ID);
    
    iframeModalOverlay.style.cssText = `
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-end;
        position: fixed;
        z-index: 1001;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.733);
    `;

    const modalContent = document.createElement("div");
    modalContent.className = "iframe-modal-content";
    modalContent.style.cssText = `
        background-color: transparent;
        margin: 0;
        padding: 0;
        border: none;
        border-radius: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        width: 94%;
        max-width: ${width}px;
        height: 94vh;
        max-height: ${height}px;
    `;

    // Create Trongate CSS spinner container
    const spinnerContainer = document.createElement("div");
    spinnerContainer.className = "codegen-spinner-container";
    spinnerContainer.style.cssText = `
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
    `;

    const spinner = document.createElement("div");
    spinner.className = "spinner";
    spinnerContainer.appendChild(spinner);

    const modalIframe = document.createElement("iframe");
    modalIframe.style.cssText = `
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 0;
        display: block;
        background-color: #000;
    `;
    modalIframe.title = "Code Generator";

    modalContent.appendChild(spinnerContainer);
    modalContent.appendChild(modalIframe);
    iframeModalOverlay.appendChild(modalContent);

    return { overlay: iframeModalOverlay, iframe: modalIframe, spinnerContainer: spinnerContainer };
}

function attachModalEventListeners(overlay) {
    const modalContent = overlay.querySelector('.iframe-modal-content');

    overlay.addEventListener("click", (event) => {
        if (!modalContent.contains(event.target)) {
            this.close();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            this.close();
        }
    }, { once: true });
}

function close() {
    const modal = document.getElementById(IFRAME_MODAL_ID);
    if (modal) {
        modal.remove();
    }
}

// ============================================
// Modal Operations
// ============================================
function reloadIframe(targetUrl, width = null, height = null, templateName = null) {
    this.close();
    this._openIframeModal(targetUrl, width, height, templateName);
}

function reset() {
    this.close();
    CodeGenerator.openCodeGenerator();
}

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
            displayLanguage = '';
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
        divLhs.innerText = displayLanguage;
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

function activateRefs() {
    const allRefs = document.querySelectorAll('.feature-ref');

    allRefs.forEach(thisRef => {
        const fullFunctionName = thisRef.textContent.trim();

        // Remove parentheses for reference operations
        const cleanedName = fullFunctionName.replace('()', '');

        // Extract last segment using the same logic as PHP get_last_part()
        const label = cleanedName.includes('-')
            ? cleanedName.split('-').pop()
            : cleanedName;

        // Create the button
        const button = document.createElement('button');
        button.className = 'feature-ref-btn';
        button.innerHTML = `${label} <i class="fa fa-info-circle"></i>`;

        // Add click handler
        button.addEventListener('click', () => {
            const targetUrl = `documentation-ref/display_info/${cleanedName}`;
            openIframeModal(targetUrl, 1200, 600);
        });

        // Replace span
        thisRef.replaceWith(button);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    window.makeAlertsFantasticola();
    const codeBlocks = document.querySelectorAll('.code-block-pending');
    revealCodeBlocks(codeBlocks, true);
    activateRefs();
    // window.makeIframesFantasticola();
    // window.stylePageNavBtns();
    // updateScrollToTopButton();
});