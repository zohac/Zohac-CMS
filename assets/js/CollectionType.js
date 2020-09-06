// IIFE - Immediately Invoked Function Expression
(function (addAnotherCollectionWidget) {
    addAnotherCollectionWidget(window, document);
}(function (window, document) {
    // Add listener on the remove buttons
    const removeButtons = document.getElementsByClassName("remove");
    addListenerOnRemoveBtn(removeButtons);

    // Add listener on the "add-another-collection-widget" button
    const element = document.getElementById("add-another-collection-widget");
    element.addEventListener("click", addAnotherWidget);
}));

// Remove a parent node
function removeParent(removeBtn) {
    const newElem = removeBtn.parentNode;
    newElem.remove();
}

// Add listener on remove buttons
function addListenerOnRemoveBtn(removeButtons) {
    for (const removeBtn of removeButtons) {
        removeBtn.addEventListener("click", function () {
            removeParent(removeBtn);
        });
    }
}

// Add another collection widget
function addAnotherWidget() {
    const list = document.getElementById(this.getAttribute("data-list-selector"));
    // Try to find the counter of the list or use the length of the list
    let counter = list.dataset.widgetCounter;

    // grab the prototype template
    let newWidget = list.dataset.prototype;

    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, counter);
    // Add a remove btn
    newWidget += '<button type="button" class="remove">\n' +
        '   <span aria-hidden="true">&times;</span>\n' +
        '</button>';

    // Increase the counter
    counter++;
    // And store it, the length cannot be used if deleting widgets is allowed
    list.dataset.widgetCounter = counter;

    // create a new list element and add it to the list
    let newElem = document.createElement(list.dataset.widgetTags);
    newElem.insertAdjacentHTML("afterbegin", newWidget);

    // Add listener on the remove btn
    const removeButtons = newElem.getElementsByClassName('remove');
    addListenerOnRemoveBtn(removeButtons);

    // Add the newElem in the list
    list.appendChild(newElem);
}
