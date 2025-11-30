const TGUI_ADMIN = (() => {
    const UI_CONSTANTS = {
        SLIDE_NAV: {
            WIDTH: "250px",
            WIDTH_CLOSED: "0",
            TRANSITION_DELAY: 500,
            Z_INDEX: 2,
            Z_INDEX_HIDDEN: -1
        },
        MODAL: {
            Z_INDEX: 4,
            Z_INDEX_HIDDEN: -4,
            Z_INDEX_CONTAINER: 9999,
            DEFAULT_MARGIN_TOP: "12vh",
            OPENING_DELAY: 100
        },
        IFRAME_MODAL: {
            Z_INDEX_CONTAINER: 10000,
            HEADER_HEIGHT: 60
        },
        OVERLAY: {
            Z_INDEX: 2
        }
    };

    const body = document.querySelector("body");
    const slideNav = document.getElementById("slide-nav");
    const main = document.querySelector("main");
    let slideNavOpen = false;
    let openingModal = false;
    let mousedownEl;
    let mouseupEl;

    // Private functions
    function handleSlideNavClick(event) {
        if (slideNavOpen && event.target.id !== "open-btn" && !slideNav.contains(event.target)) {

            const mousedownInsideSlideNav = slideNav.contains(mousedownEl);
            const mouseupInsideSlideNav = slideNav.contains(mouseupEl);

            if ((!mousedownInsideSlideNav) && (!mouseupInsideSlideNav)) {
                _adminCloseSlideNav();
            }

        }
    }

    function handleEscapeKey(event) {
        if (event.key === "Escape") {
            // Check for iframe modal first (higher priority)
            const iframeModalContainer = document.getElementById("trongate-iframe-modal");
            if (iframeModalContainer) {
                _adminCloseModal();
                return;
            }

            // Then check for regular modal
            const modalContainer = document.getElementById("modal-container");
            if (modalContainer) {
                _adminCloseModal();
            }
        }
    }

    function handleModalClick(event) {

        if (openingModal) {
            return;
        }

        // Handle iframe modal clicks
        const iframeModalContainer = document.getElementById("trongate-iframe-modal");
        if (iframeModalContainer) {
            const iframeModal = iframeModalContainer.querySelector(".trongate-iframe-modal-content");
            const clickedOutside = iframeModal && !iframeModal.contains(event.target);
            const mousedownInsideModal = iframeModal && iframeModal.contains(mousedownEl);
            const mouseupInsideModal = iframeModal && iframeModal.contains(mouseupEl);

            if ((clickedOutside) && (!mousedownInsideModal) && (!mouseupInsideModal)) {
                _adminCloseModal();
                return;
            }
        }

        // Handle regular modal clicks
        const modalContainer = document.getElementById("modal-container");
        if (modalContainer) {
            const modal = modalContainer.querySelector(".modal");
            const clickedOutside = modal && !modal.contains(event.target);
            const mousedownInsideModal = modal && modal.contains(mousedownEl);
            const mouseupInsideModal = modal && modal.contains(mouseupEl);

            if ((clickedOutside) && (!mousedownInsideModal) && (!mouseupInsideModal)) {
                _adminCloseModal();
            }

        }
    }

    function autoPopulateSlideNav() {
        const slideNavLinks = document.querySelector("#slide-nav ul");
        if (slideNavLinks && slideNavLinks.getAttribute("auto-populate") === "true") {
            const navLinks = document.querySelector("#top-nav");
            if (navLinks) {
                slideNavLinks.innerHTML = navLinks.innerHTML;
            }
        }
    }

    // Initialize
    autoPopulateSlideNav();

    // Add event listeners
    body.addEventListener("click", (event) => {
        handleSlideNavClick(event);
        handleModalClick(event);
    });

    body.addEventListener("mousedown", (event) => {
        mousedownEl = event.target;
    });

    body.addEventListener("mouseup", (event) => {
        mouseupEl = event.target;
    });

    document.addEventListener("keydown", handleEscapeKey);

    return {
        body,
        slideNav,
        main,
        getSlideNavOpen: () => slideNavOpen,
        setSlideNavOpen: (value) => { slideNavOpen = value; },
        getOpeningModal: () => openingModal,
        setOpeningModal: (value) => { openingModal = value; },
        UI_CONSTANTS
    };
})();

// Global Functions
const _adminOpenSlideNav = function () {
    TGUI_ADMIN.slideNav.style.opacity = 1;
    TGUI_ADMIN.slideNav.style.width = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.WIDTH;
    TGUI_ADMIN.slideNav.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.Z_INDEX;
    setTimeout(() => {
        TGUI_ADMIN.setSlideNavOpen(true);
    }, TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.TRANSITION_DELAY);
};

const _adminCloseSlideNav = function () {
    TGUI_ADMIN.slideNav.style.opacity = 0;
    TGUI_ADMIN.slideNav.style.width = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.WIDTH_CLOSED;
    TGUI_ADMIN.slideNav.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.Z_INDEX_HIDDEN;
    TGUI_ADMIN.setSlideNavOpen(false);
};

const _adminOpenModal = function (modalIdOrUrl, width = null, height = null) {
    // If width and height are provided, treat as iframe modal
    if (width !== null && height !== null) {
        _adminOpenIframeModal(modalIdOrUrl, width, height);
        return;
    }

    // Original modal functionality for DOM elements
    TGUI_ADMIN.setOpeningModal(true);
    setTimeout(() => {
        TGUI_ADMIN.setOpeningModal(false);
    }, TGUI_ADMIN.UI_CONSTANTS.MODAL.OPENING_DELAY);

    let pageOverlay = document.getElementById("overlay");
    if (!pageOverlay) {
        const modalContainer = document.createElement("div");
        modalContainer.setAttribute("id", "modal-container");
        modalContainer.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.MODAL.Z_INDEX_CONTAINER;
        TGUI_ADMIN.body.append(modalContainer);

        pageOverlay = document.createElement("div");
        pageOverlay.setAttribute("id", "overlay");
        pageOverlay.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.OVERLAY.Z_INDEX;
        TGUI_ADMIN.body.append(pageOverlay);

        const targetModal = modalIdOrUrl.startsWith(".")
            ? document.querySelector(modalIdOrUrl)
            : document.getElementById(modalIdOrUrl);

        if (!targetModal) return;
        const targetModalContent = targetModal.innerHTML;
        targetModal.remove();

        const newModal = document.createElement("div");
        newModal.className = "modal";
        newModal.id = modalIdOrUrl;
        newModal.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.MODAL.Z_INDEX;
        newModal.innerHTML = targetModalContent;
        modalContainer.appendChild(newModal);

        requestAnimationFrame(() => {
            newModal.style.display = "block";
            newModal.style.opacity = 1;

            const marginTop = getComputedStyle(newModal).getPropertyValue("--modal-margin-top").trim() || TGUI_ADMIN.UI_CONSTANTS.MODAL.DEFAULT_MARGIN_TOP;
            newModal.style.marginTop = marginTop;
        });
    }
};

const _adminOpenIframeModal = function (targetUrl, width, height) {
    TGUI_ADMIN.setOpeningModal(true);
    setTimeout(() => {
        TGUI_ADMIN.setOpeningModal(false);
    }, TGUI_ADMIN.UI_CONSTANTS.MODAL.OPENING_DELAY);

    // Create iframe modal overlay
    const iframeModalOverlay = document.createElement("div");
    iframeModalOverlay.setAttribute("id", "trongate-iframe-modal");
    iframeModalOverlay.style.cssText = `
        display: block;
        position: fixed;
        z-index: ${TGUI_ADMIN.UI_CONSTANTS.IFRAME_MODAL.Z_INDEX_CONTAINER};
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    `;

    // Create modal content container with responsive width and height
    const modalContent = document.createElement("div");
    modalContent.className = "trongate-iframe-modal-content";
    modalContent.style.cssText = `
        background-color: transparent;
        margin: 0;
        padding: 0;
        border: none;
        border-radius: 12px;
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

    // Create iframe (fills entire modal)
    const modalIframe = document.createElement("iframe");
    modalIframe.style.cssText = `
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 12px;
        display: block;
    `;
    modalIframe.src = targetUrl;
    modalIframe.title = "Modal Content";

    // Handle iframe errors
    modalIframe.onerror = function() {
        modalIframe.srcdoc = `
            <div style="padding: 20px; text-align: center; color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; font-family: Arial, sans-serif; font-size: 14px;">
                <h3>Unable to load content</h3>
                <p>The URL "${targetUrl}" could not be loaded.</p>
                <p>This might be due to CORS restrictions or the site not allowing embedding.</p>
            </div>
        `;
    };

    // Assemble the modal (just iframe, no header)
    modalContent.appendChild(modalIframe);
    iframeModalOverlay.appendChild(modalContent);

    // Add to page
    TGUI_ADMIN.body.appendChild(iframeModalOverlay);
};

const _adminCloseModal = function () {
    // Check for iframe modal first (higher priority)
    const iframeModalContainer = document.getElementById("trongate-iframe-modal");
    if (iframeModalContainer) {
        iframeModalContainer.remove();
        const event = new Event("modalClosed", { bubbles: true, cancelable: true });
        document.dispatchEvent(event);
        return;
    }

    // Handle regular modal
    const modalContainer = document.getElementById("modal-container");
    if (modalContainer) {
        const openModal = modalContainer.firstChild;
        openModal.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.MODAL.Z_INDEX_HIDDEN;
        openModal.style.opacity = 0;
        openModal.style.marginTop = TGUI_ADMIN.UI_CONSTANTS.MODAL.DEFAULT_MARGIN_TOP;
        openModal.style.display = "none";
        TGUI_ADMIN.body.appendChild(openModal);
        modalContainer.remove();

        const overlay = document.getElementById("overlay");
        if (overlay) {
            overlay.remove();
        }

        const event = new Event("modalClosed", { bubbles: true, cancelable: true });
        document.dispatchEvent(event);
    }
};

// Admin-specific functions
const _setPerPage = function() {
    const perPageSelector = document.querySelector("#results-tbl select");
    if (!perPageSelector) return;
    
    const selectedIndex = perPageSelector.value;
    let targetUrl = `${window.location.protocol}//${window.location.hostname}${window.location.pathname}`;
    targetUrl = targetUrl.replace("/manage/", `/set_per_page/${selectedIndex}/`);
    targetUrl = targetUrl.replace("/manage", `/set_per_page/${selectedIndex}/`);
    window.location.href = targetUrl;
};

const _fetchAssociatedRecords = function(relationName, updateId) {
    const params = {
        relationName,
        updateId,
        callingModule: segment1
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/fetch_associated_records`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function() {
        _drawAssociatedRecords(params.relationName, JSON.parse(http.responseText));
    };
};

const _drawAssociatedRecords = function(relationName, results) {
    const targetTbl = document.getElementById(`${relationName}-records`);
    if (!targetTbl) return;
    
    targetTbl.innerHTML = '';
    
    results.forEach(result => {
        const tr = document.createElement("tr");
        
        const tdValue = document.createElement("td");
        tdValue.textContent = result.value;
        
        const tdButton = document.createElement("td");
        const disBtn = document.createElement("button");
        disBtn.innerHTML = '<i class="fa fa-ban"></i> disassociate';
        disBtn.onclick = () => _openDisassociateModal(relationName, result.id);
        disBtn.className = "danger";
        
        tdButton.appendChild(disBtn);
        tr.append(tdValue, tdButton);
        targetTbl.appendChild(tr);
    });

    _populatePotentialAssociations(relationName, results);
};

const _populatePotentialAssociations = function(relationName, results) {
    const params = {
        updateId,
        relationName,
        results,
        callingModule: segment1
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/fetch_available_options`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function() {
        const options = JSON.parse(http.responseText);
        const associateBtn = document.getElementById(`${relationName}-create`);
        const dropdown = document.getElementById(`${relationName}-dropdown`);
        
        if (!associateBtn || !dropdown) return;

        if (options.length > 0) {
            associateBtn.style.display = "block";
            dropdown.innerHTML = '';
            
            options.forEach(option => {
                const newOption = document.createElement("option");
                newOption.value = option.key;
                newOption.textContent = option.value;
                dropdown.appendChild(newOption);
            });
        } else {
            associateBtn.style.display = "none";
        }
    };
};

const _openDisassociateModal = function(relationName, recordId) {
    setTimeout(() => {
        const elId = `${relationName}-record-to-go`;
        const element = document.getElementById(elId);
        if (element) element.value = recordId;
    }, 100);

    const targetModalId = `${relationName}-disassociate-modal`;
    _adminOpenModal(targetModalId);
};

const _disassociate = function(relationName) {
    _adminCloseModal();

    const elId = `${relationName}-record-to-go`;
    const element = document.getElementById(elId);
    if (!element) return;

    const params = {
        updateId: element.value,
        relationName
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/disassociate`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function() {
        _fetchAssociatedRecords(params.relationName, updateId);
    };
};

const _submitCreateAssociation = function(relationName) {
    const dropdown = document.getElementById(`${relationName}-dropdown`);
    if (!dropdown) return;
    
    const params = {
        updateId,
        relationName,
        callingModule: segment1,
        value: dropdown.value
    };

    _adminCloseModal();
    
    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/submit`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function() {
        _fetchAssociatedRecords(params.relationName, params.updateId);
    };
};

const _submitComment = function() {
    const textarea = document.querySelector("#comment-modal > div.modal-body > p:nth-child(1) > textarea");
    if (!textarea) return;
    
    const comment = textarea.value.trim();
    if (comment === "") return;
    
    textarea.value = "";
    _adminCloseModal();

    const params = {
        comment,
        target_table: segment1,
        update_id: updateId
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}api/create/trongate_comments`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function() {
        if (http.status === 401) {
            window.location.href = `${baseUrl}trongate_administrators/login`;
        } else if (http.status === 200) {
            _fetchComments();
        }
    };
};

const _fetchComments = function() {
    const commentsTbl = document.querySelector("#comments-block > table");
    if (!commentsTbl) return;

    const params = {
        target_table: segment1,
        update_id: updateId,
        orderBy: "date_created"
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}api/get/trongate_comments`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function() {
        if (http.status === 401) {
            window.location.href = `${baseUrl}trongate_administrators/login`;
        } else if (http.status === 200) {
            const comments = JSON.parse(http.responseText);
            
            commentsTbl.innerHTML = '';
            comments.forEach(comment => {
                const tr = document.createElement("tr");
                const td = document.createElement("td");
                
                const pDate = document.createElement("p");
                pDate.textContent = comment.date_created;
                
                const pComment = document.createElement("p");
                pComment.innerHTML = comment.comment;

                td.append(pDate, pComment);
                tr.appendChild(td);
                commentsTbl.appendChild(tr);
            });
        }
    };
};

// Initialize comments if enabled
if (typeof drawComments === "boolean") {
    _fetchComments();
}

// Expose shared functions (these are identical to app.js versions)
window.openSlideNav = window.openSlideNav || _adminOpenSlideNav;
window.closeSlideNav = window.closeSlideNav || _adminCloseSlideNav;
window.openModal = window.openModal || _adminOpenModal;
window.closeModal = window.closeModal || _adminCloseModal;

// Expose admin-specific functions
window.setPerPage = window.setPerPage || _setPerPage;
window.fetchAssociatedRecords = window.fetchAssociatedRecords || _fetchAssociatedRecords;
window.disassociate = window.disassociate || _disassociate;
window.submitCreateAssociation = window.submitCreateAssociation || _submitCreateAssociation;
window.submitComment = window.submitComment || _submitComment;