import ShellWindow from './Service/shellWindow';
import Command from './Service/command';
import Option from './Service/option';

function addEventListenerOnShell(shell: Shell) {
    shell.form.addEventListener('submit', function (event) {
        shell.shellFormSubmit(event, shell);
    });
    document.addEventListener('keydown', function (event) {
        shell.openShellOnKeyPress(event, shell);
    });
    document.getElementById('shell-simulator-close').addEventListener('click', function (event) {
        shell.closeShell(shell);
    });
    shell.header.addEventListener('click', function (event) {
        shell.simulator.style.zIndex = shell.displayFront();
    });
}

export default class Shell extends ShellWindow {

    shellSimulatorId: string = 'shell-simulator';

    form: HTMLElement | HTMLFormElement;
    input: HTMLElement | HTMLInputElement;
    content: HTMLElement;

    userName: string | null = null;
    httpHost: string | null = null;

    command: Command[] = [];

    historic: string[] = [];
    scheme: string[] = [];

    constructor(options: Option | null) {
        super();

        this.loadOptionsIfNotNull(options);

        this.init();
    }

    init(): Shell {
        this.simulator = document.getElementById(this.shellSimulatorId);
        this.content = document.getElementById(`${this.shellSimulatorId}-content`);
        this.input = document.getElementById(`${this.shellSimulatorId}-input`);
        this.form = document.getElementById(`${this.shellSimulatorId}-form`);
        this.header = document.getElementById(`${this.shellSimulatorId}-header`);
        this.userName = this.simulator.dataset.username;
        this.httpHost = this.simulator.dataset.httpHost;

        this.move(this);
        addEventListenerOnShell(this);

        this.simulator.style.width = this.width;
        this.simulator.style.height = this.height;
        this.content.style.maxHeight = this.height;

        this.drawScheme();

        return this;
    }

    drawScheme() {
        let newElement = null;
        const content = this.content;
        const form = this.form;

        this.scheme.forEach(function (item) {
            newElement = document.createElement('div');
            newElement.className = 'flex w-full text-shell-green font-mono';
            newElement.innerHTML = item;

            content.insertBefore(newElement, form);
        });

        newElement = document.createElement('div');
        newElement.className = 'flex w-full';
        newElement.innerHTML = 'Bienvenue, pour voir les commandes disponible commencez par taper \'cmd\'';

        content.insertBefore(newElement, form);
    }

    openShellOnKeyPress(event: KeyboardEvent, shell: Shell): Shell {
        if (event.ctrlKey && event.altKey && (event.key === 't' || event.key === 'r')) {  // case sensitive
            shell.simulator.classList.remove('hidden');
            shell.input.focus();
        }

        return this;
    }

    closeShell(shell: Shell): Shell {
        shell.simulator.classList.add('hidden');

        return this;
    }

    addCommand(command: Command): Shell {
        this.command.push(command);

        return this;
    }

    executeCommand(command: string): HTMLDivElement | null {
        let response = null;

        for (const obCmd of this.command) {
            if (obCmd.name === command) {
                response = obCmd.command.execute();
            }
        }

        return response;
    }

    addCommandToHistoric(command: string): Shell {
        this.historic.push(command);

        return this;
    }

    /**
     * @param event
     * @param shell {Shell}
     * @returns {Shell}
     */
    shellFormSubmit(event: Event, shell: Shell): Shell {
        event.preventDefault();

        const newElement = document.createElement('div');
        let inputValue: string | null = null;

        if ('value' in this.input) {
            inputValue = this.input.value;
            this.addCommandToHistoric(inputValue);
            this.input.value = null;
        }

        newElement.innerHTML = `<span class="text-shell-green">${this.userName}@${this.httpHost}</span>:
<span class="text-shell-blue">~</span>$ ${inputValue}`;

        this.content.insertBefore(newElement, this.form);

        const response = this.executeCommand(inputValue);
        if (response) {
            this.content.insertBefore(response, this.form);
        }

        this.content.scrollTop = this.content.scrollHeight;

        return this;
    }
}
