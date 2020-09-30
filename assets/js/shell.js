class Shell {
    form = null;
    input = null;
    content = null;
    simulator = null;
    userName = null;
    httpHost = null;

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
     * @returns {boolean}
     */
    shellFormSubmit(event, shell) {
        console.log(event, shell);
        event.preventDefault();

        let newElement = document.createElement("div");
        newElement.innerHTML = `<span class="text-shell-green">${this.userName}@${this.httpHost}</span>:<span class="text-shell-blue">~</span>$ ${this.shellInput.value}`;

        this.shellContent.insertBefore(newElement, this.shellForm);
        this.shellInput.value = null;

        return false;
    }
}

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const shell = new Shell();

    shell.form.addEventListener("submit", function (event, shell) {
        shell.shellFormSubmit(event, shell);
    });
    document.addEventListener("keydown", function (event, shell) {
        console.log(shell);
        // shell.openShellOnKeyPress(event, shell);
    });
}));

