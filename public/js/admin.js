const TG_APP = {
    MODAL_CONSTANTS: {
        Z_INDEX: 1000,
        Z_INDEX_HIDDEN: -4,
        Z_INDEX_CONTAINER: 999,
        Z_INDEX_OVERLAY: 998,
        DEFAULT_MARGIN_TOP: "12vh",
        OPENING_DELAY: 100
    },

    openingModal: false,
    mousedownEl: null,
    mouseupEl: null,

    init() {
        // Track mouse events for proper modal closing behavior
        document.addEventListener("mousedown", (event) => {
            TG_APP.mousedownEl = event.target;
        });

        document.addEventListener("mouseup", (event) => {
            TG_APP.mouseupEl = event.target;
        });

        // Handle clicks to close modal when clicking outside
        document.addEventListener("click", (event) => {
            if (TG_APP.openingModal) {
                return;
            }

            const modalContainer = document.getElementById("modal-container");
            if (modalContainer) {
                const modal = modalContainer.querySelector(".modal");
                const clickedOutside = modal && !modal.contains(event.target);
                const mousedownInsideModal = modal && modal.contains(TG_APP.mousedownEl);
                const mouseupInsideModal = modal && modal.contains(TG_APP.mouseupEl);

                if (clickedOutside && !mousedownInsideModal && !mouseupInsideModal) {
                    TG_APP._closeModal();
                }
            }
        });

        // Handle Escape key
        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                const modalContainer = document.getElementById("modal-container");
                if (modalContainer) {
                    TG_APP._closeModal();
                }
            }
        });
    },

    _openModal(modalId) {
        TG_APP.openingModal = true;
        setTimeout(() => {
            TG_APP.openingModal = false;
        }, TG_APP.MODAL_CONSTANTS.OPENING_DELAY);

        // Don't create if already exists
        let pageOverlay = document.getElementById("overlay");
        if (pageOverlay) {
            return;
        }

        // Create modal container
        const modalContainer = document.createElement("div");
        modalContainer.setAttribute("id", "modal-container");
        modalContainer.style.zIndex = TG_APP.MODAL_CONSTANTS.Z_INDEX_CONTAINER;
        document.body.appendChild(modalContainer);

        // Create overlay
        pageOverlay = document.createElement("div");
        pageOverlay.setAttribute("id", "overlay");
        pageOverlay.style.zIndex = TG_APP.MODAL_CONSTANTS.Z_INDEX_OVERLAY;
        document.body.appendChild(pageOverlay);

        // Get the target modal
        const targetModal = document.getElementById(modalId);
        if (!targetModal) return;

        // Store the original modal content and remove it from DOM
        const targetModalContent = targetModal.innerHTML;
        targetModal.remove();

        // Create new modal in the container
        const newModal = document.createElement("div");
        newModal.className = "modal";
        newModal.id = modalId;
        newModal.style.zIndex = TG_APP.MODAL_CONSTANTS.Z_INDEX;
        newModal.innerHTML = targetModalContent;
        modalContainer.appendChild(newModal);

        // Animate the modal in
        requestAnimationFrame(() => {
            newModal.style.display = "block";
            newModal.style.opacity = 1;
            
            // Use CSS variable for margin-top if available, otherwise use default
            const marginTop = getComputedStyle(document.documentElement)
                .getPropertyValue("--modal-margin-top").trim() || TG_APP.MODAL_CONSTANTS.DEFAULT_MARGIN_TOP;
            newModal.style.marginTop = marginTop;
        });
    },

    _closeModal() {
        const modalContainer = document.getElementById("modal-container");
        if (!modalContainer) return;

        const openModal = modalContainer.querySelector(".modal");
        if (openModal) {
            // Reset modal styles and move it back to body
            openModal.style.zIndex = TG_APP.MODAL_CONSTANTS.Z_INDEX_HIDDEN;
            openModal.style.opacity = 0;
            openModal.style.marginTop = "-160px"; // Trongate's default hidden position
            openModal.style.display = "none";
            document.body.appendChild(openModal);
        }

        // Remove modal container and overlay
        modalContainer.remove();
        
        const overlay = document.getElementById("overlay");
        if (overlay) {
            overlay.remove();
        }

        // Dispatch custom event (like Trongate does)
        const event = new Event("modalClosed", { bubbles: true, cancelable: true });
        document.dispatchEvent(event);
    }
};

// Initialize the app
TG_APP.init();

// Make functions globally available
window.openModal = window.openModal || TG_APP._openModal.bind(TG_APP);
window.closeModal = window.closeModal || TG_APP._closeModal.bind(TG_APP);