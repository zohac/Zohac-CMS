import UtilityClass from "./utilityClass";

export default class ShellWindow extends UtilityClass {

    /**
     * @type {ShellWindow[]}
     */
    static instances = [];

    /**
     * @type {number}
     */
    _zIndex = 1000;

    /**
     * @type {string}
     */
    _width = '300px';

    /**
     * @type {string}
     */
    _height = '200px';

    /**
     * @param {Object} options
     */
    constructor(options = {}) {
        super();

        if (!this.isEmpty(options)) {
            this.loadOptions(options);
        }

        this.calculateZ_index();

        ShellWindow.instances.push(this);
    }

    /**
     * @param value
     */
    set zIndex(value) {
        console.log(value);
        this._zIndex = this.convertToInt(value);
    }

    /**
     * @returns {number}
     */
    get zIndex() {
        return this._zIndex;
    }

    /**
     * @param value
     */
    set width(value) {
        console.log(value);
        this._width = this.convertToString(value);
    }

    /**
     * @returns {string}
     */
    get width() {
        return this._width;
    }

    /**
     * @param value
     */
    set height(value) {
        console.log(value);
        this._height = this.convertToString(value);
    }

    /**
     * @returns {string}
     */
    get height() {
        return this._height;
    }

    /**
     * @returns {ShellWindow}
     */
    calculateZ_index() {
        if (0 < ShellWindow.instances.length) {
            let zIndexes = [];
            ShellWindow.instances.forEach(element => zIndexes.push(element.z_index));
            let zIndexMax = Math.max(...zIndexes);
            this.zIndex = zIndexMax + 1;
        }

        return this;
    }

    /**
     * @returns {string}
     */
    displayFront() {
        this.calculateZ_index();

        return this.zIndex.toString();
    }

    /**
     * @param shell {Shell|Cv}
     */
    move(shell) {
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
