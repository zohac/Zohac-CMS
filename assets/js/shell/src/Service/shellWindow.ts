import UtilityClass from './utilityClass';
import ShellInterface from '../interface/shellInterface'

export default class ShellWindow extends UtilityClass {

    static instances: ShellWindow[] = [];

    height: string = 'auto';
    width: string = '50vw';
    zIndex: number = 1000;

    simulator: HTMLElement | null = null;
    header: HTMLElement | null = null;

    constructor() {
        super();

        this.calculateZ_index();

        ShellWindow.instances.push(this);
    }

    calculateZ_index(): ShellInterface {
        if (0 < ShellWindow.instances.length) {
            const zIndexes = [];
            ShellWindow.instances.forEach(element => zIndexes.push(element.zIndex));
            const zIndexMax = Math.max(...zIndexes);
            this.zIndex = zIndexMax + 1;
        }

        return this;
    }

    displayFront(): string {
        this.calculateZ_index();

        return this.zIndex.toString();
    }

    move(shell: ShellInterface): ShellInterface {
        let pos1 = 0;
        let pos2 = 0;
        let pos3 = 0;
        let pos4 = 0;

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
            shell.simulator.style.top = (shell.simulator.offsetTop - pos2) + 'px';
            shell.simulator.style.left = (shell.simulator.offsetLeft - pos1) + 'px';
        }

        function closeDragElement() {
            /* stop moving when mouse button is released:*/
            document.onmouseup = null;
            document.onmousemove = null;
        }

        return this;
    }
}
