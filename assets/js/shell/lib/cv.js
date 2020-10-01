export default class Cv {

    /**
     * @type {string}
     */
    static COMMAND_NAME = 'cv';

    /**
     *
     * @type {string}
     */
    description = 'Un CV de développeur.';

    /**
     * @param description {null|string}
     */
    constructor(description = null) {
        if (description) {
            this.description = description
        }
    }

    /**
     * @param description {string}
     */
    set description(description) {
        this.description = description;
    }

    /**
     * @returns {null|string}
     */
    get description() {
        return this.description;
    }

    /**
     * @returns {null|HTMLDivElement}
     */
    execute() {
        let newElement = document.createElement("div");
        newElement.className = "flex w-full";
        newElement.innerHTML = `<div class="w-20 mr-4">${Cv.COMMAND_NAME}</div>${this.description}`;

        return newElement;
    }
}