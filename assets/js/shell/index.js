import Shell from './lib/shell';
import Command from "./lib/command";
import Cmd from "./lib/cmd";
import Cv from "./lib/cv";

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
    document.getElementById("shell-close").addEventListener("click", function (event){
        shell.closeShell(event, shell);
    });
}

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const shell = new Shell();
    shell.addCommand(new Command(Cmd.COMMAND_NAME, new Cmd(shell)));
    shell.addCommand(new Command(Cv.COMMAND_NAME, new Cv()));

    addEventListenerOnShell(shell);
}));