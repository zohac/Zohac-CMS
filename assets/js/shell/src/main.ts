import Shell from './shell';
import Option from "./Service/option";
import Command from './Service/command';
import Cmd from './Command/cmd';
import Cv from './Command/cv';

// IIFE - Immediately Invoked Function Expression
(function (shell) {
    shell(window, document);
}(function (window, document) {
    const options = new Option();
    options.add('width', '800px')
        .add('height', '400px')
        .add('scheme', [
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
        ]);

    const shell = new Shell(options);
    shell.addCommand(new Command(Cmd.COMMAND_NAME, new Cmd(shell)))
        .addCommand(new Command(Cv.COMMAND_NAME, new Cv()));
}));