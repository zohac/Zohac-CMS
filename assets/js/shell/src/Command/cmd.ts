import Shell from '../shell';

export default class Cmd {

    static COMMAND_NAME: string = 'cmd';

    description: string = 'Affiche les commandes disponible.';
    shell: Shell | null = null;

    constructor(shell: Shell, description: string | null = null) {
        this.shell = shell;

        if (description) {
            this.description = description
        }
    }

    execute(): HTMLDivElement | null {
        let newElement = null;

        for (const command of this.shell.command) {
            newElement = document.createElement('div');
            newElement.className = 'flex w-full';
            newElement.innerHTML = `<div class="w-20 mr-4">${command.name}</div>${command.command.description}`;

            this.shell.content.insertBefore(newElement, this.shell.form);
        }

        return newElement;
    }
}