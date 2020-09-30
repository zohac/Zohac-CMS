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
     * @type {[]}
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
     * @param event
     * @param shell {Shell}
     * @returns {Shell}
     */
    shellFormSubmit(event, shell) {
        console.log(shell);
        event.preventDefault();

        let newElement = document.createElement("div");
        newElement.innerHTML = `<span class="text-shell-green">${this.userName}@${this.httpHost}</span>:<span class="text-shell-blue">~</span>$ ${this.input.value}`;

        this.content.insertBefore(newElement, this.form);
        this.input.value = null;

        return this;
    }
}

class Command {

    /**
     *
     * @type {null|string}
     */
    command = null;

    /**
     *
     * @type {null|string}
     */
    functionToCall = null;

    /**
     * @param command
     * @param functionToCall
     */
    constructor(command, functionToCall) {
        // if (null === this.command || null === this.functionToCall) {
        //     throw 'command or funtionToCall can\'t be null';
        // }

        this.command = command;
        this.functionToCall = functionToCall
    }

    get command() {
        return this.command;
    }

    get functionToCall() {
        return this.functionToCall;
    }
}

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const shell = new Shell();

    shell.addCommand(new Command('cmd', 'uneFonction'));

    shell.form.addEventListener("submit", function (event) {
        shell.shellFormSubmit(event, shell);
    });
    document.addEventListener("keydown", function (event) {
        shell.openShellOnKeyPress(event, shell);
    });
}));

