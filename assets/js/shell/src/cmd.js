export default class Cmd {
    static COMMAND_NAME = 'cmd';

    /**
     *
     * @type {string}
     */
    description = 'Affiche les commandes disponible.';

    /**
     * @type {null|Shell}
     */
    shell = null;

    /**
     * @param shell {Shell}
     * @param description {null|string}
     */
    constructor(shell, description = null) {
        this.shell = shell;

        if (description) {
            this.description = description
        }
    }

    /**
     * @returns {null|string}
     */
    get description() {
        return this.description;
    }

    /**
     * @param description {string}
     */
    set description(description) {
        this.description = description;
    }

    /**
     * @returns {null|HTMLDivElement}
     */
    execute() {
        let newElement = null;
        for (const command of this.shell.command) {
            newElement = document.createElement("div");
            newElement.className = "flex w-full";
            newElement.innerHTML = `<div class="w-20 mr-4">${command.name}</div>${command.command.description}`;

            this.shell.content.insertBefore(newElement, this.shell.form);
        }

        return newElement;
    }
}