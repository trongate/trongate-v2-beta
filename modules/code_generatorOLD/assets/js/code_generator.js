function renderLoader() {
    const main = document.querySelector('main');
    Array.from(main.children).forEach(childEl => {
        if (childEl.classList.contains('loader')) {
            childEl.classList.remove('cloak');
        } else {
            childEl.classList.add('cloak');
        }
    });

	setTimeout(() => {
		hideLoader();
	}, 1000);

}

function hideLoader() {
    const main = document.querySelector('main');
    Array.from(main.children).forEach(childEl => {
        if (childEl.classList.contains('loader')) {
            childEl.classList.add('cloak');
        } else {
            childEl.classList.remove('cloak');
        }
    });
}

function revealOptions() {
    const codegenSelectContainer = document.querySelector('#codegen-select-container');
    codegenSelectContainer.remove();
    const optionsList = document.querySelector('.options-list');
    optionsList.removeAttribute('style');
}

function formOnInput() {
    const valueInputEl = document.querySelector('.value-input');
    valueInputEl.focus();
}

function redrawIframe(targetUrl, width, height) {
    // Log to confirm function is running
    console.log('redrawIframe called with URL:', targetUrl, 'width:', width, 'height:', height);

    // Reference the iframe element
    const iframe = window.frameElement;
    if (!iframe) {
        console.error('Cannot access iframe element. Ensure this script runs inside an iframe.');
        return;
    }

    // Access parent document
    const parentDoc = window.parent.document;
    if (!parentDoc) {
        console.error('Cannot access parent document.');
        return;
    }

    // Find the modal container
    const modalContent = parentDoc.querySelector('.trongate-iframe-modal-content');
    if (!modalContent) {
        console.error('Modal content container (.trongate-iframe-modal-content) not found in parent document.');
        return;
    }

    // Ensure modal is visible
    const modal = parentDoc.getElementById('trongate-iframe-modal');
    if (!modal) {
        console.error('Modal (#trongate-iframe-modal) not found in parent document.');
        return;
    }
    modal.style.display = 'block';
    console.log('Modal display set to block');

    // Set the iframe source URL
    iframe.src = targetUrl;
    console.log('Iframe src set to:', targetUrl);

    // Resize the modal container
    modalContent.style.maxWidth = `${width}px`;
    modalContent.style.maxHeight = `${height}px`;
    modalContent.style.width = `${width}px`;
    modalContent.style.height = `${height}px`;
    console.log(`Modal resized to ${width}x${height}`);
}

window.addEventListener('load', (ev) => {
	const loaderEl = document.querySelector('.loader');
	loaderEl.removeAttribute('style');
});