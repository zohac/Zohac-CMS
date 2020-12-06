export default class Window {

    /**
     * @param shell {Shell|Cv}
     */
    move(shell) {
        console.log(document);
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

        /* the header is where you move the DIV from:*/
        shell.header.onmousedown = dragMouseDown;

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
}
