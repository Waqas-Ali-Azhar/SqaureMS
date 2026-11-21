// document.addEventListener('DOMContentLoaded', function () {
//     const editableCells = document.querySelectorAll('.editable-cell');

//     editableCells.forEach(cell => {
//         const displayDiv = cell.querySelector('.cell-display');
//         const editor = cell.querySelector('.cell-editor');

//         if (!editor) return;

//         cell.addEventListener('click', function (e) {
//             if (['select', 'input', 'textarea'].includes(e.target.tagName.toLowerCase())) return;

//             displayDiv.style.display = 'none';
//             editor.style.display = 'inline-block';
//             editor.focus();

//             if (editor.tagName.toLowerCase() === 'input') {
//                 editor.select();
//             }
//         });

//         editor.addEventListener('blur', function () {
//             let newValue;

//             if (editor.tagName.toLowerCase() === 'select' && editor.multiple) {
//                 const selectedOptions = Array.from(editor.selectedOptions).map(opt => opt.value);
//                 newValue = selectedOptions.join(', ');
//             } else {
//                 newValue = editor.value;
//             }

//             displayDiv.textContent = newValue;
//             displayDiv.style.display = 'block';
//             editor.style.display = 'none';

//             const rowId = cell.dataset.rowId;
//             const column = cell.dataset.column;

//             fetch('update-cell.php', {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json'
//                 },
//                 body: JSON.stringify({
//                     row_id: rowId,
//                     column: column,
//                     value: newValue
//                 })
//             })
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error(`HTTP error! Status: ${response.status}`);
//                 }
//                 return response.json();
//             })
//             .then(data => {
//                 if (!data.success) {
//                     console.log('Update failed: ' + data.message);
//                 } else {
//                     console.log('Update success!');
//                 }
//             })
//             .catch(err => {
//                 console.error('AJAX error:', err);
//                 // alert('AJAX request failed.');
//             });
//         });

//         if (editor.tagName.toLowerCase() === 'input') {
//             editor.addEventListener('keydown', function (e) {
//                 if (e.key === 'Enter') {
//                     e.preventDefault();
//                     editor.blur();
//                 }
//             });
//         }

//         if (editor.tagName.toLowerCase() === 'select' && editor.multiple) {
//             editor.addEventListener('change', function () {
//                 editor.blur();
//             });
//         }
//     });
// });
document.addEventListener('DOMContentLoaded', function () {
    // console.log("view_sheet.js: DOM content loaded. Initializing all functionalities.");

    // --- EXISTING EDITABLE CELL LOGIC ---
    const editableCells = document.querySelectorAll('.editable-cell');

    editableCells.forEach(cell => {
        const displayDiv = cell.querySelector('.cell-display');
        const editor = cell.querySelector('.cell-editor');

        // Only apply standard editable cell logic if it's NOT a custom multiselect
        if (cell.classList.contains('multiselect-cell')) {
            // We'll handle custom multiselect clicks separately
            return;
        }

        if (!editor) return;

        cell.addEventListener('click', function (e) {
            // Prevent opening editor if click is on an interactive element within the cell
            if (['select', 'input', 'textarea'].includes(e.target.tagName.toLowerCase())) return;

            displayDiv.style.display = 'none';
            editor.style.display = 'inline-block';
            editor.focus();

            if (editor.tagName.toLowerCase() === 'input') {
                editor.select();
            }
        });

        editor.addEventListener('blur', function () {
            let newValue;

            if (editor.tagName.toLowerCase() === 'select' && editor.multiple) {
                const selectedOptions = Array.from(editor.selectedOptions).map(opt => opt.value);
                newValue = selectedOptions.join(', ');
            } else {
                newValue = editor.value;
            }

            displayDiv.textContent = newValue;
            displayDiv.style.display = 'block';
            editor.style.display = 'none';

            const rowId = cell.dataset.rowId;
            const column = cell.dataset.column;

            // AJAX call for standard editable cells
            fetch('update-cell.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    row_id: rowId,
                    column: column,
                    value: newValue
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        console.log('Update failed: ' + data.message);
                    } else {
                        console.log('Update success!');
                    }
                })
                .catch(err => {
                    console.error('AJAX error:', err);
                });
        });

        if (editor.tagName.toLowerCase() === 'input') {
            editor.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    editor.blur();
                }
            });
        }

        if (editor.tagName.toLowerCase() === 'select' && editor.multiple) {
            // This is for native multiselects that are not part of the custom UI.
            // For custom UI, the blur/change on the custom elements will trigger the native select's change event.
            editor.addEventListener('change', function () {
                // We don't blur immediately on change for native multiselects as users might want to select multiple.
                // The blur will happen when they click away.
                // If you want immediate blur/save on any change, uncomment editor.blur();
                // editor.blur();
            });
        }
    });

    // --- CUSTOM MULTI-SELECT UI LOGIC ---

    // Function to update the display div and checkboxes based on native select
    function updateCustomMultiselectDisplay(container) {
        const targetSelectId = container.dataset.targetSelect;
        const currentNativeSelect = document.getElementById(targetSelectId);
        const currentDisplayDiv = container.querySelector('.selected-items-display');
        const currentOptionsDiv = container.querySelector('.custom-multiselect-options');

        if (!currentNativeSelect || !currentDisplayDiv || !currentOptionsDiv) {
            console.error("Missing elements for custom multiselect display update. Container:", container);
            return;
        }

        // Clear existing tags in the display
        currentDisplayDiv.innerHTML = '';
        Array.from(currentNativeSelect.selectedOptions).forEach(option => {
            const tag = document.createElement('span');
            tag.className = 'selected-item-tag';
            tag.innerHTML = `${option.value} <span class="remove-item" data-value="${option.value}">&times;</span>`;
            currentDisplayDiv.appendChild(tag);
        });

        // Update checkbox states in the dropdown
        Array.from(currentOptionsDiv.querySelectorAll('input[type="checkbox"]')).forEach(checkbox => {
            const optionValue = checkbox.value;
            const nativeOption = Array.from(currentNativeSelect.options).find(opt => opt.value === optionValue);
            if (nativeOption) {
                checkbox.checked = nativeOption.selected;
            }
        });
    }

    // Function to initialize a single custom multiselect
    function initializeCustomMultiselect(container) {
        // console.log("Initializing custom multiselect for container:", container);
        const targetSelectId = container.dataset.targetSelect;
        const nativeSelect = document.getElementById(targetSelectId);
        const displayDiv = container.querySelector('.selected-items-display');
        const optionsDiv = container.querySelector('.custom-multiselect-options');

        if (!nativeSelect || !displayDiv || !optionsDiv) {
            console.error("Missing core elements for custom multiselect setup. Native Select:", nativeSelect, "Display Div:", displayDiv, "Options Div:", optionsDiv, "Container:", container);
            return;
        }

        // Clear previous content to prevent duplicates on re-initialization (if applicable)
        displayDiv.innerHTML = '';
        optionsDiv.innerHTML = '';

        // Populate options dropdown with checkboxes and attach listeners
        Array.from(nativeSelect.options).forEach(option => {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'custom-multiselect-option';
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = option.value;
            checkbox.checked = option.selected; // Set checked based on native select's initial selected state

            // Add a label for better usability
            const label = document.createElement('label');
            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(option.text));
            optionDiv.appendChild(label);

            optionsDiv.appendChild(optionDiv);

            // Add event listener for checkbox change within the custom dropdown
            checkbox.addEventListener('change', function () {
                option.selected = this.checked; // Update the hidden native select's state
                updateCustomMultiselectDisplay(container); // Update the visual display and other checkboxes

                // Trigger a 'change' event on the native select.
                // This will then be caught by the general 'change' listener for '.multiselect-editor'
                // which handles the AJAX update.

                const event = new Event('change', { bubbles: true });
                nativeSelect.dispatchEvent(event);
            });
        });

        // Toggle dropdown visibility when the custom container is clicked
        container.addEventListener('click', function (e) {
            // Prevent opening/closing if clicking directly on a selected tag's 'x' button
            if (e.target.classList.contains('remove-item')) {
                return;
            }
            optionsDiv.classList.toggle('active');
        });

        // Close dropdown when clicking outside of the custom multiselect container
        document.addEventListener('click', function (e) {
            // if (!container.contains(e.target) && optionsDiv.classList.contains('active')) {
            //     optionsDiv.classList.remove('active');
            // }
            document.querySelectorAll('.custom-multiselect-options.active').forEach(activeDropdown => {
                const activeContainer = activeDropdown.closest('.custom-multiselect-container');
                if (!activeContainer.contains(e.target)) {
                    activeDropdown.classList.remove('active');
                }
            });
        });
        optionsDiv.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        // Handle removing tags from the display (via 'x' button)
        displayDiv.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const valueToRemove = e.target.dataset.value;
                const optionToRemove = Array.from(nativeSelect.options).find(opt => opt.value === valueToRemove);
                if (optionToRemove) {
                    optionToRemove.selected = false; // Deselect in native select
                    updateCustomMultiselectDisplay(container); // Update custom display

                    // Trigger change on native select to save the new state
                    const event = new Event('change', { bubbles: true });
                    nativeSelect.dispatchEvent(event);
                }
            }
        });

        // Initial update of the custom display based on the native select's current state
        updateCustomMultiselectDisplay(container);
    }

    // Find and initialize all custom multiselects on page load
    const allCustomContainers = document.querySelectorAll('.custom-multiselect-container');
    // console.log("Found", allCustomContainers.length, "custom multiselect containers to initialize.");
    allCustomContainers.forEach(initializeCustomMultiselect);

    // --- GLOBAL EVENT LISTENER FOR NATIVE MULTISELECT EDITOR (FOR AJAX) ---
    // This listener will catch changes from both the standard multiselects and the custom ones
    // because the custom UI updates the hidden native select, which then dispatches a 'change' event.
    document.body.addEventListener('change', function (e) {
        if (e.target.classList.contains('multiselect-editor')) {
            const nativeSelect = e.target;
            const cell = nativeSelect.closest('.editable-cell');
            const column = cell.dataset.column;
            const rowId = cell.dataset.rowId;

            const selectedValues = Array.from(nativeSelect.selectedOptions).map(option => option.value);
            const newValue = selectedValues.join(','); // Join with comma for saving

            console.log(`AJAX: Saving multiselect data for ${column} (Article No: ${rowId}): ${newValue}`);

            // This is your existing AJAX call (modified slightly for clarity)
            fetch('update-cell.php', { // Ensure this path is correct for your server
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    row_id: rowId, // Use row_id as per your PHP update script
                    column: column,
                    value: newValue // Use value as per your PHP update script
                }),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('Update successful:', data.message);
                    } else {
                        console.error('Update failed:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error during fetch for multiselect update:', error);
                });
        }
    });

    // --- OTHER EXISTING SCROLL AND COLUMN LIST LOGIC (Keep these as they were) ---
    const scrollWrapper = document.querySelector('.table-scroll-wrapper');
    const tbody = scrollWrapper ? scrollWrapper.querySelector('tbody') : null;
    const tableWrapper = document.querySelector('.table-wrapper');

    if (scrollWrapper && tbody) {
        tbody.addEventListener('wheel', function (e) {
            const canScroll = scrollWrapper.scrollWidth > scrollWrapper.clientWidth;
            if (canScroll) {
                e.preventDefault();
                scrollWrapper.scrollLeft += e.deltaY;
                e.stopPropagation();
            }
        }, {
            passive: false
        });
    }

    if (tableWrapper && scrollWrapper) { // Check scrollWrapper existence
        tableWrapper.addEventListener('wheel', function (e) {
            const isInsideSideList = e.target.closest('.side-list');
            if (scrollWrapper.scrollHeight > scrollWrapper.clientHeight && !(e.target.closest('.ulList')) && !isInsideSideList) {
                e.preventDefault();
                scrollWrapper.scrollTop += e.deltaY;
            }
        }, {
            passive: false
        });
    }
    //----------------------
    const toggleLink = document.querySelector('.side-list-toggle');
    const sideList = document.querySelector('.side-list');

    if (toggleLink && sideList) { // Check toggleLink and sideList existence
        toggleLink.addEventListener('click', function (e) {
            e.preventDefault();
            sideList.style.display = (sideList.style.display === 'block') ? 'none' : 'block';
        });

        document.addEventListener('click', function (e) {
            if (!toggleLink.contains(e.target) && !sideList.contains(e.target)) {
                sideList.style.display = 'none';
            }
        });
    }

    const columnLinks = document.querySelectorAll('.column-link');
    const columnSearchInput = document.getElementById('columnSearch'); // Get the search input

    columnLinks.forEach(link => {
        link.addEventListener('click', function () {
            const columnName = this.dataset.column;
            const targetHeader = document.querySelector(`th[data-column-name="${columnName}"]`);

            if (targetHeader && scrollWrapper) {
                const scrollLeft = targetHeader.offsetLeft;
                scrollWrapper.scrollTo({
                    left: scrollLeft - 20,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Search functionality for column list
    if (columnSearchInput) {
        columnSearchInput.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const items = document.querySelectorAll('#columnList .column-link');

            items.forEach(function (item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }
});