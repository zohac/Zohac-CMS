export default interface CommandInterface {
    description: string;

    execute(): HTMLDivElement | null;
}