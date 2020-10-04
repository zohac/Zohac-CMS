export default class Shell {

    /**
     * @type {HTMLElement}
     */
    form = null;

    /**
     * @type {HTMLElement}
     */
    input = null;

    /**
     * @type {HTMLElement}
     */
    content = null;

    /**
     * @type {HTMLElement}
     */
    simulator = null;

    /**
     * @type {null|string}
     */
    userName = null;

    /**
     * @type {null|string}
     */
    httpHost = null;

    /**
     * @type {Command[]}
     */
    command = [];

    /**
     * @type {[]}
     */
    historic = [];

    /**
     * @param shellForm {string}
     */
    constructor(shellForm = "shell-form") {
        this.form = document.getElementById(shellForm);
        this.input = document.getElementById('shell-input');
        this.content = document.getElementById("shell-simulator-content");
        this.simulator = document.getElementById('shell-simulator');
        this.userName = this.simulator.dataset.username;
        this.httpHost = this.simulator.dataset.httpHost;

        this.init();
    }

    init() {
        const scheme = [
            '&nbsp;&nbsp;&nbsp;&nbsp;/$$&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/$$&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;/$$/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;$$&nbsp;&nbsp;',
            '&nbsp;&nbsp;/$$/&nbsp;&nbsp;&nbsp;/$$$$$$$&nbsp;/$$&nbsp;\\&nbsp;&nbsp;$$&nbsp;',
            '&nbsp;/$$/&nbsp;&nbsp;&nbsp;/$$_____/|__/&nbsp;&nbsp;\\&nbsp;&nbsp;$$',
            '|&nbsp;&nbsp;$$&nbsp;&nbsp;|&nbsp;&nbsp;$$$$$$&nbsp;&nbsp;/$$&nbsp;&nbsp;&nbsp;/$$/',
            '&nbsp;\\&nbsp;&nbsp;$$&nbsp;&nbsp;\\____&nbsp;&nbsp;$$|&nbsp;$$&nbsp;&nbsp;/$$/&nbsp;',
            '&nbsp;&nbsp;\\&nbsp;&nbsp;$$&nbsp;/$$$$$$$/|&nbsp;$$&nbsp;/$$/&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;\\__/|_______/&nbsp;|&nbsp;$$|__/&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/$$&nbsp;&nbsp;|&nbsp;$$&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;$$$$$$/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\\______/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        ];

        let newElement = null;
        const content = this.content;
        const form = this.form;

        scheme.forEach(function(item) {
            newElement = document.createElement("div");
            newElement.className = "flex w-full text-shell-green font-mono";
            newElement.innerHTML = item;

            content.insertBefore(newElement, form);
        });

        newElement = document.createElement("div");
        newElement.className = "flex w-full";
        newElement.innerHTML = 'Bienvenue, pour voir les commandes disponible commencez par taper \'cmd\'';

        content.insertBefore(newElement, form);

        return this;
    }

    /**
     * @param event
     * @param shell {Shell}
     */
    openShellOnKeyPress(event, shell) {
        if (event.ctrlKey && event.altKey && (event.key === "t" || event.key === "r")) {  // case sensitive
            shell.simulator.classList.remove("hidden");
            shell.input.focus();
        }
    }

    /**
     * @param event
     * @param shell {Shell}
     */
    closeShell(event, shell) {
        shell.simulator.classList.add("hidden");
    }

    /**
     *
     * @param command {Command}
     * @returns {Shell}
     */
    addCommand(command) {
        this.command.push(command);

        return this;
    }

    /**
     * @param command {string}
     * @returns {null|HTMLDivElement}
     */
    executeCommand(command) {
        let response = null;

        /**
         * @param obCmd {Command}
         */
        for (const obCmd of this.command) {
            if (obCmd.name === command) {
                response = obCmd.command.execute();
            }
        }
        return response;
    }

    /**
     * @param command {string}
     * @returns {Shell}
     */
    addCommandToHistoric(command) {
        this.historic.push(command);

        return this;
    }

    /**
     * @param event
     * @param shell {Shell}
     * @returns {Shell}
     */
    shellFormSubmit(event, shell) {
        event.preventDefault();

        let newElement = document.createElement("div");
        let inputValue = this.input.value;
        this.addCommandToHistoric(inputValue);

        newElement.innerHTML = `<span class="text-shell-green">${this.userName}@${this.httpHost}</span>:
<span class="text-shell-blue">~</span>$ ${inputValue}`;

        this.content.insertBefore(newElement, this.form);
        this.input.value = null;

        const response = this.executeCommand(inputValue);
        if (response) {
            this.content.insertBefore(response, this.form);
        }

        return this;
    }
}
