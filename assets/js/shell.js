class Shell {
    constructor(shellForm = "shell-form") {
        this.shellForm = document.getElementById(shellForm);
        this.initEventListener();
    }

    initEventListener() {
        this.shellForm.addEventListener("submit", this.shellFormSubmit);
        document.addEventListener("keydown", this.openShellOnKeyPress);
    }

    openShellOnKeyPress(event) {
        if (event.ctrlKey && event.altKey && event.key === "t") {  // case sensitive
            document.getElementById("shell-simulator").classList.remove("hidden")
        }
    }

    shellFormSubmit(event, shell = this) {
        console.log(shell);
        event.preventDefault();

        let newElement = document.createElement("div");
        const shellForm = document.getElementById("shell-form");
        const shellInput = document.getElementById('shell-input');
        const shellContent = document.getElementById("shell-simulator-content");
        const shellSimulator = document.getElementById('shell-simulator');
        const userName = shellSimulator.dataset.username;
        const httpHost = shellSimulator.dataset.httphost;

        newElement.innerHTML = '<span class="text-shell-green">' + userName + '@' + httpHost + '</span>:<span class="text-shell-blue">~</span>$' + shellInput.value;

        shellContent.insertBefore(newElement, shellForm);
        shellInput.value = null;

        return false;
    }
}

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const shell = new Shell();
}));

