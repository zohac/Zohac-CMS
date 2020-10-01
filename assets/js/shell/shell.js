import Command from './lib/command';
import Cmd from './lib/cmd';
import Cv from './lib/cv';

class Shell {

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
     * @param shellForm {string}
     */
    constructor(shellForm = "shell-form") {
        this.form = document.getElementById(shellForm);
        this.input = document.getElementById('shell-input');
        this.content = document.getElementById("shell-simulator-content");
        this.simulator = document.getElementById('shell-simulator');
        this.userName = this.simulator.dataset.username;
        this.httpHost = this.simulator.dataset.httpHost;

        console.log(this.input);
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
     * @param event
     * @param shell {Shell}
     * @returns {Shell}
     */
    shellFormSubmit(event, shell) {
        event.preventDefault();

        let newElement = document.createElement("div");
        let inputValue = this.input.value;

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

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const shell = new Shell();
    shell.addCommand(new Command(Cmd.COMMAND_NAME, new Cmd(shell)));
    shell.addCommand(new Command(Cv.COMMAND_NAME, new Cv()));

    shell.form.addEventListener("submit", function (event) {
        shell.shellFormSubmit(event, shell);
    });
    document.addEventListener("keydown", function (event) {
        shell.openShellOnKeyPress(event, shell);
    });
}));
