import ShellWindow from '../shellWindow';

function addEventListenerOnCV(cv: Cv) {
    document
        .getElementById("shell-simulator-cv-close")
        .addEventListener("click", function () {
            cv.closeCv();
        });
    cv.header.addEventListener("click", function () {
        cv.simulator.style.zIndex = cv.displayFront();
    });
}

export default class Cv extends ShellWindow {

    static COMMAND_NAME: string = 'cv';

    description: string = 'Un CV de développeur.';
    shellSimulatorCvId: string = 'shell-simulator-cv';

    constructor(description: string | null = null) {
        super();

        if (description) {
            this.description = description
        }

        this.init();
    }

    init(): Cv {
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

    execute(): HTMLDivElement | null {
        this.openCv();

        let newElement = document.createElement("div");
        newElement.className = "flex w-full";
        newElement.innerHTML = `<div class="w-20 mr-4">${Cv.COMMAND_NAME}</div>${this.description}`;

        return newElement;
    }
}