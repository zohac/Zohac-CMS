export default class Command {

    /**
     *
     * @type {null|string}
     */
    name = null;

    command = null;

    /**
     * @param name {string}
     * @param command
     */
    constructor(name, command) {
        // if (null === this.command || null === this.functionToCall) {
        //     throw 'command or functionToCall can\'t be null';
        // }

        this.name = name;
        this.command = command;
    }

    get name() {
        return this.name;
    }

    get command() {
        return this.command;
    }
}
