import Shell from './src/shell';
import Command from './src/command';
import Cmd from './src/cmd';
import Cv from './src/cv';
import shellWindow from "./src/shellWindow";

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const options = {
        width: '800px',
        height: '400px',
        scheme: [
            '&nbsp;&nbsp;&nbsp;&nbsp;/$$&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/$$&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;/$$/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;$$&nbsp;&nbsp;',
            '&nbsp;&nbsp;/$$/&nbsp;&nbsp;&nbsp;/$$$$$$$&nbsp;/$$&nbsp;\\&nbsp;&nbsp;$$&nbsp;',
            '&nbsp;/$$/&nbsp;&nbsp;&nbsp;/$$_____/|__/&nbsp;&nbsp;\\&nbsp;&nbsp;$$',
            '|&nbsp;&nbsp;$$&nbsp;&nbsp;|&nbsp;&nbsp;$$$$$$&nbsp;&nbsp;/$$&nbsp;&nbsp;&nbsp;/$$/',
            '&nbsp;\\&nbsp;&nbsp;$$&nbsp;&nbsp;\\____&nbsp;&nbsp;$$|&nbsp;$$&nbsp;&nbsp;/$$/&nbsp;',
            '&nbsp;&nbsp;\\&nbsp;&nbsp;$$&nbsp;/$$$$$$$/|&nbsp;$$&nbsp;/$$/&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;\\__/|_______/&nbsp;|&nbsp;$$|__/&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/$$&nbsp;&nbsp;|&nbsp;$$&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;$$$$$$/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\\______/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        ],
    };

    const shell = new Shell(options);
    shell.addCommand(new Command(Cmd.COMMAND_NAME, new Cmd(shell)));
    shell.addCommand(new Command(Cv.COMMAND_NAME, new Cv()));
}));