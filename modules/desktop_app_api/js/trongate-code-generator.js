window.TrongateCodeGenerator = {

    // ============================================
    // Configuration
    // ============================================
    minViewportWidth: 1280,
    minViewportHeight: 720,

    // ============================================
    // Initialization
    // ============================================
    _uncloakAndFocus(targetForm) {
        const formWrapperEl = targetForm.closest('div');
        formWrapperEl.classList.remove('cloak');
        this._focusMainInput();
    },

    _focusMainInput() {
        const inputEl = document.querySelector('main input[type=text]');
        if (inputEl) {
            inputEl.focus();
        }
    },

    _clearElement(element) {
        while(element.firstChild) {
            element.firstChild.remove();
        }
    },

    _fetchAndSwapContent(requestType, targetUrl, container, additionalAttributes = {}) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.style.display = 'none';

        // Determine the MX attribute based on request type (e.g., mx-get, mx-post)
        const mxAttr = `mx-${requestType.toLowerCase()}`;
        btn.setAttribute(mxAttr, targetUrl);
        btn.setAttribute('mx-target', 'main');

        // Apply any additional attributes
        Object.entries(additionalAttributes).forEach(([key, value]) => {
            btn.setAttribute(key, value);
        });

        // Append and click
        container.appendChild(btn);
        btn.click();

        return btn;
    },

    // ============================================
    // Form Handling
    // ============================================
    focusOnInput(ev) {
        const targetForm = document.querySelector('form');
        if (!targetForm) {
            return;
        }

        const formId = targetForm.id;

        // Define a lookup table mapping form IDs to specific handlers
        const formHandlers = {
            'enter-mod-name-form': () => {
                const baseUrl = window.parent.document.querySelector('base').getAttribute('href');
                const targetUrl = baseUrl + 'code_generator/list_mods';

                const http = new XMLHttpRequest();
                http.open('get', targetUrl);
                http.setRequestHeader('Content-type', 'application/json');
                http.send();

                http.onload = function() {
                    if (http.status === 200) {
                        const modules = JSON.parse(http.responseText);

                        // Clean existing inputs
                        const existingModInputs = targetForm.querySelectorAll('input[name="existing_modules[]"]');
                        existingModInputs.forEach(input => input.remove());

                        // Add each module as a hidden input
                        modules.forEach(module => {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'existing_modules[]';
                            hiddenInput.value = module;
                            targetForm.appendChild(hiddenInput);
                        });

                        // Uncloak and focus
                        TrongateCodeGenerator._uncloakAndFocus(targetForm);
                    }
                };
            },
            'enter-nav-label-form': () => {
                // Uncloak and focus
                const formWrapperEl = targetForm.closest('div');
                formWrapperEl.classList.remove('cloak');

                // Append the record_name_plural value onto the form input
                const inputEl = document.querySelector('main input[type=text]');
                inputEl.value += localStorage.getItem('record_name_plural');

                if (inputEl) {
                    inputEl.focus();
                }
            }
        };

        // Use the handler if it exists, otherwise fall back to default focusing
        if (formHandlers[formId]) {
            formHandlers[formId]();
        } else {
            this._focusMainInput();
        }
    },

    // ============================================
    // API Communication
    // ============================================
    handleApiResponse() {

        const apiResponseEl = document.querySelector('.api-response');
        if (!apiResponseEl) {
            console.warn('No .api-response element found.');
            return;
        }

        let responseObj;
        try {
            responseObj = JSON.parse(apiResponseEl.innerHTML);
        } catch (error) {
            console.error('Failed to parse API response:', error);
            return;
        }

        // Handle localStorage updates
        if (responseObj.storageItemsToAdd) {
            this._applyStorageItems(responseObj.storageItemsToAdd);
        }

        // Handle next request
        if (responseObj.nextRequest) {
            this.executeNextRequest(responseObj.nextRequest);
        }
    },

    executeNextRequest(requestObj) {
        const { targetUrl, requestType, payload, additionalMXAttributes } = requestObj;

        if (!targetUrl || !requestType) {
            console.warn('Invalid nextRequest configuration.');
            return;
        }

        // Locate the container where the button will live temporarily
        const apiResponseContainer = document.querySelector('.api-response');
        if (!apiResponseContainer) {
            console.error('No .api-response container found.');
            return;
        }

        // Build additional attributes object
        const attrs = { ...additionalMXAttributes };

        // Include payload if applicable
        if (payload && (requestType.toLowerCase() === 'post' || requestType.toLowerCase() === 'put')) {
            attrs['mx-vals'] = payload;
        }

        // Create and click button
        const btn = this._fetchAndSwapContent(requestType, targetUrl, apiResponseContainer, attrs);

        // Clean up afterwards
        setTimeout(() => btn.remove(), 500);
    },

    // ============================================
    // Storage Management
    // ============================================
    _applyStorageItems(itemsObj) {
        Object.entries(itemsObj).forEach(([key, value]) => {
            try {
                localStorage.setItem(key, value);
            } catch (error) {
                console.error(`Failed to store localStorage item "${key}":`, error);
            }
        });
    },

    clearLocalStorage() {
        try {
            localStorage.clear();
        } catch (error) {
            console.error('Error clearing localStorage:', error);

            // Fallback: try to remove items one by one
            try {
                for (let i = localStorage.length - 1; i >= 0; i--) {
                    const key = localStorage.key(i);
                    localStorage.removeItem(key);
                }
            } catch (fallbackError) {
                console.error('Fallback method also failed:', fallbackError);
            }
        }
    },

    // ============================================
    // Viewport Management
    // ============================================
    initQuit() {
        TrongateCodeGenerator.clearLocalStorage();

        // Access the parent document instead of the iframe's document
        const parentDoc = window.parent.document;
        const modal = parentDoc.getElementById("codegen-iframe-modal");

        if (modal) {
            modal.remove();
        }
    },

    initChooseUrlCol() {
        // Step 1: Clear the main area.
        const mainEl = document.querySelector('main');
        TrongateCodeGenerator._clearElement(mainEl);

        // Step 2: Invoke the reusable renderer.
        this._renderChooseLocalStorage('properties', 'propertyName', 'desktop_app_api/submit_url_col', true);
    },

    initChooseOrderBy() {
        // Step 1: Read the properties from localStorage
        const storedValue = localStorage.getItem('properties');
        if (!storedValue) {
            console.warn('No data found in localStorage for key: properties');
            return;
        }

        // Step 2: Parse into an array
        const items = JSON.parse(storedValue);
        if (!Array.isArray(items)) {
            console.error('Expected an array in localStorage for key: properties');
            return;
        }

        // Step 3: Build the orderByOptions array
        const orderByOptions = [];

        // Add 'id' at the top (both ASC and DESC)
        orderByOptions.push({ propertyName: 'id' });
        orderByOptions.push({ propertyName: 'id DESC' });

        // Add all other properties in their original order (both ASC and DESC)
        items.forEach(item => {
            orderByOptions.push({ propertyName: item.propertyName });
            orderByOptions.push({ propertyName: `${item.propertyName} DESC` });
        });

        // Step 4: Store orderByOptions in localStorage
        localStorage.setItem('orderByOptions', JSON.stringify(orderByOptions));

        // Step 5: Clear the main area
        const mainEl = document.querySelector('main');
        TrongateCodeGenerator._clearElement(mainEl);

        // Step 6: Invoke the reusable renderer with the new localStorage item
        this._renderChooseLocalStorage('orderByOptions', 'propertyName', 'desktop_app_api/submit_order_by');
    },

    initChooseIcon() {
        const mainEl = document.querySelector('main');
        TrongateCodeGenerator._clearElement(mainEl);

        const optionsList = document.createElement('ul');
        optionsList.setAttribute('class', 'options-selector');

        const yesOption = document.createElement('li');
        yesOption.innerText = 'Yes';
        yesOption.addEventListener('click', () => {
            this.postMyOption('', targetUrl);
        });
        optionsList.appendChild(yesOption);

        const noOption = document.createElement('li');
        noOption.innerText = 'No';
        noOption.addEventListener('click', () => {
            this._fetchAndSwapContent('get', 'desktop_app_api/lets_add_properties_conf', mainEl);
        });
        optionsList.appendChild(noOption);
        mainEl.appendChild(optionsList);
    },

    _renderChooseLocalStorage(localStorageName, targetProperty, targetUrl, renderEmptyOption = false) {
        // Step 1: Read the localStorage value
        const storedValue = localStorage.getItem(localStorageName);
        if (!storedValue) {
            console.warn(`No data found in localStorage for key: ${localStorageName}`);
            return;
        }

        // Step 2: Parse into an array
        const items = JSON.parse(storedValue);
        if (!Array.isArray(items)) {
            console.error(`Expected an array in localStorage for key: ${localStorageName}`);
            return;
        }

        // Step 3: Extract property names (no sorting)
        const propertyNames = items.map(item => item[targetProperty]);

        // Step 4: Create the list element
        const optionsList = document.createElement('ul');
        optionsList.setAttribute('class', 'options-selector');

        // Optional empty option
        if (renderEmptyOption) {
            const emptyItem = document.createElement('li');
            emptyItem.innerText = '';
            emptyItem.addEventListener('click', () => {
                this.postMyOption('', targetUrl);
            });
            optionsList.appendChild(emptyItem);
        }

        // Step 5: Render all items
        propertyNames.forEach(name => {
            const listItem = document.createElement('li');
            listItem.innerText = name;
            listItem.setAttribute('onclick', `TrongateCodeGenerator.postMyOption("${name.replace(/"/g, '\\"')}", "${targetUrl}")`);
            optionsList.appendChild(listItem);
        });

        // Step 6: Append to the main element
        const mainEl = document.querySelector('main');
        mainEl.appendChild(optionsList);
    },

    postMyOption(selectedValue, targetUrl) {

        // Step 1: Clear the main area.
        const mainEl = document.querySelector('main');
        TrongateCodeGenerator._clearElement(mainEl);

        // Step 2: Create an invisible button for invoking HTTP request
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.style.display = 'none';

        // Determine the MX attribute based on request type (e.g., mx-get, mx-post)
        btn.setAttribute('mx-post', targetUrl);
        btn.setAttribute('mx-target', 'main');
        btn.setAttribute('mx-target-loading', 'cloak');
        btn.setAttribute('mx-after-swap', 'TrongateCodeGenerator.handleApiResponse');

        // Safely insert the selected value into the JSON
        const vals = JSON.stringify({ selected: selectedValue });
        btn.setAttribute('mx-vals', vals);

        // Append and click
        mainEl.appendChild(btn);
        btn.click();

        return;

    }

}















function skipAhead() {
    localStorage.setItem('record_name_singular', 'Student');
    localStorage.setItem('record_name_plural', 'Students');
    localStorage.setItem('module_folder_name', 'students');
    localStorage.setItem('nav_label', '');
    localStorage.setItem('icon_code', '');
    localStorage.setItem('icon_id', '');
    //localStorage.setItem('properties', '[{"propertyName":"First Name","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[2]","max length[255]"]},{"propertyName":"Last Name","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[2]","max length[255]"]},{"propertyName":"E-mail Address","propertyType":"email","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[7]","max length[255]"]}]');
    localStorage.setItem('properties', '[{"propertyName":"First Name","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[2]","max length[255]"]},{"propertyName":"Middle Name","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[2]","max length[255]"]},{"propertyName":"Last Name","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[2]","max length[255]"]},{"propertyName":"E-mail Address","propertyType":"email","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","min length[7]","max length[255]"]},{"propertyName":"Address Line 1","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["required","max length[255]"]},{"propertyName":"Address Line 2","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["max length[255]"]},{"propertyName":"Address Line 3","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["max length[255]"]},{"propertyName":"Town","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["min length[2]","max length[85]"]},{"propertyName":"City","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["min length[2]","max length[45]"]},{"propertyName":"Postcode","propertyType":"varchar","onForm":"yes","isSearchable":"yes","onSummaryTable":"yes","validationRules":["min length[6]","max length[7]","required"]}]');
    localStorage.setItem('tempParams', '{"previousAction":"submitProperties"}');
    parent.CodeGenerator.reloadIframe('http://localhost/trongate_live5/desktop_app_api/choose_url_col', 800, 600, 'c64');
}
