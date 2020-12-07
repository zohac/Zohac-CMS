import ShellWindow from './shellWindow';

/**
 * @param cv {Cv}
 */
function addEventListenerOnCV(cv) {
    document
        .getElementById("shell-simulator-cv-close")
        .addEventListener("click", function (event) {
            cv.closeCv();
        });
    cv.header.addEventListener("click", function (event) {
        cv.simulator.style.zIndex = cv.displayFront();
    });
}

export default class Cv extends ShellWindow {

    /**
     * @type {string}
     */
    static COMMAND_NAME = 'cv';

    /**
     * @type {string}
     */
    description = 'Un CV de développeur.';

    /**
     * @type {string}
     */
    shellSimulatorCvId = 'shell-simulator-cv';

    /**
     * @type {HTMLElement}
     */
    simulator = null;

    /**
     * @type {HTMLElement}
     */
    header = null

    /**
     * @type {string}
     */
    width = '50vw';

    /**
     * @type {string}
     */
    height = 'auto';

    /**
     * @param description {null|string}
     */
    constructor(description = null) {
        super();

        if (description) {
            this.description = description
        }

        this.init();
    }

    /**
     * @returns {null|string}
     */
    get description() {
        return this.description;
    }

    /**
     * @param description {string}
     */
    set description(description) {
        this.description = description;
    }

    init() {
        this.simulator = document.getElementById(this.shellSimulatorCvId);
        this.header = document.getElementById(this.shellSimulatorCvId + '-header');
        this.simulator.style.width = this.width;
        this.simulator.style.height = this.height;

        this.move(this);
        addEventListenerOnCV(this);

        return this;
    }

    openCv() {
        this.simulator.classList.remove("hidden");
    }

    closeCv() {
        this.simulator.classList.add("hidden");
    }

    /**
     * @returns {null|HTMLDivElement}
     */
    execute() {
        this.openCv();

        let newElement = document.createElement("div");
        newElement.className = "flex w-full";
        newElement.innerHTML = `<div class="w-20 mr-4">${Cv.COMMAND_NAME}</div>${this.description}`;

        return newElement;
    }
}