export default interface ShellInterface {
    height: string;
    width: string;
    zIndex: number;

    simulator: HTMLElement | null;
    header: HTMLElement | null;
}
