// IIFE - Immediately Invoked Function Expression
(function(addAnotherCollectionWidget) {
    addAnotherCollectionWidget(window, document);
}(function(window, document) {
    const element = document.getElementById("add-another-collection-widget");

    element.onclick = function() {
        const list = document.getElementById(this.getAttribute("data-list-selector"));
        // Try to find the counter of the list or use the length of the list
        let counter = list.dataset.widgetCounter;

        // grab the prototype template
        let newWidget = list.dataset.prototype;

        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        newWidget += '' +
            '<button type="button" class="remove" onclick="remove()">\n' +
            '   <span aria-hidden="true">&times;</span>\n' +
            '</button>';

        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.dataset.widgetCounter = counter;

        // create a new list element and add it to the list
        let newElem = document.createElement(list.dataset.widgetTags);
        newElem.insertAdjacentHTML('afterbegin', newWidget);

        list.appendChild(newElem);
    };

    function remove() {
        const removeBtn = document.getElementsByClassName("remove");
        console.log(removeBtn);
    }

}));
