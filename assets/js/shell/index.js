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

/**
 * @param shell {Shell}
 */
function moveShell(shell) {
    let mousePosition;
    let offset = [0,0];
    let isDown = false;

    shell.simulator.addEventListener('mousedown', function(e) {
        isDown = true;
        offset = [
            shell.simulator.offsetLeft - e.clientX,
            shell.simulator.offsetTop - e.clientY
        ];
    }, true);

    document.addEventListener('mouseup', function() {
        isDown = false;
    }, true);

    document.addEventListener('mousemove', function(event) {
        event.preventDefault();
        if (isDown) {
            mousePosition = {
                x : event.clientX,
                y : event.clientY
            };
            shell.simulator.style.left = (mousePosition.x + offset[0]) + 'px';
            shell.simulator.style.top  = (mousePosition.y + offset[1]) + 'px';
        }
    }, true);
}

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const options = {
      width: 600,
      height: 300,
    };

    const shell = new Shell(options);
    shell.addCommand(new Command(Cmd.COMMAND_NAME, new Cmd(shell)));
    shell.addCommand(new Command(Cv.COMMAND_NAME, new Cv()));

    addEventListenerOnShell(shell);

    moveShell(shell);
}));