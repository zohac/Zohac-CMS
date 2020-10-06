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
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

    if (shell.header) {
        /* if present, the header is where you move the DIV from:*/
        shell.header.onmousedown = dragMouseDown;
    } else {
        /* otherwise, move the DIV from anywhere inside the DIV:*/
        shell.simulator.onmousedown = dragMouseDown;
    }

    function dragMouseDown(event) {
        event.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = event.clientX;
        pos4 = event.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
    }

    function elementDrag(event) {
        event.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - event.clientX;
        pos2 = pos4 - event.clientY;
        pos3 = event.clientX;
        pos4 = event.clientY;
        // set the element's new position:
        shell.simulator.style.top = (shell.simulator.offsetTop - pos2) + "px";
        shell.simulator.style.left = (shell.simulator.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        /* stop moving when mouse button is released:*/
        document.onmouseup = null;
        document.onmousemove = null;
    }
}

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const options = {
        width: 800,
        height: 400,
        scheme: [
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
        ],
    };

    const shell = new Shell(options);
    shell.addCommand(new Command(Cmd.COMMAND_NAME, new Cmd(shell)));
    // shell.addCommand(new Command(Cv.COMMAND_NAME, new Cv()));

    addEventListenerOnShell(shell);

    moveShell(shell);
}));