import CommandInterface from "./interface/commandInterface";

export default class Command {

    name: string;
    command: CommandInterface;

    constructor(name: string, command: CommandInterface) {
        this.name = name;
        this.command = command;
    }
}
