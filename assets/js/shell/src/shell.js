import ShellWindow from './shellWindow';

/**
 * @param shell {Shell}
 */
function addEventListenerOnShell(shell) {
    shell.form.addEventListener("submit", function (event) {
        shell.shellFormSubmit(event, shell);
    });
    document.addEventListener("keydown", function (event) {
        shell.openShellOnKeyPress(event, shell);
    });
    document.getElementById("shell-simulator-close").addEventListener("click", function (event) {
        shell.closeShell(event, shell);
    });
    shell.header.addEventListener("click", function (event) {
        shell.simulator.style.zIndex = shell.displayFront();
    });
}

export default class Shell extends ShellWindow {

    /**
     * @type {string}
     */
    shellSimulatorId = 'shell-simulator';

    /**
     * @type {HTMLElement}
     */
    form = null;

    /**
     * @type {HTMLElement}
     */
    header = null

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
     * @type {[]}
     */
    scheme = [];

    /**
     * @param {Object} options
     */
    constructor(options = {}) {
        super();

        if (!this.isEmpty(options)) {
            this.loadOptions(options);
        }

        this.init();
    }

    /**
     * @returns {Shell}
     */
    init() {
        this.simulator = document.getElementById(this.shellSimulatorId);
        this.content = document.getElementById(this.shellSimulatorId + '-content');
        this.input = document.getElementById(this.shellSimulatorId + '-input');
        this.form = document.getElementById(this.shellSimulatorId + '-form');
        this.header = document.getElementById(this.shellSimulatorId + '-header');
        this.userName = this.simulator.dataset.username;
        this.httpHost = this.simulator.dataset.httpHost;

        this.move(this);
        addEventListenerOnShell(this);

        this.simulator.style.width = this.width;
        this.simulator.style.height = this.height;
        this.content.style.maxHeight = this.height;

        let newElement = null;
        const content = this.content;
        const form = this.form;

        this.scheme.forEach(function (item) {
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

        this.content.scrollTop = this.content.scrollHeight;

        return this;
    }
}
