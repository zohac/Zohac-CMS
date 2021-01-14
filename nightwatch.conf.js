const geckodriver = require('geckodriver');
const seleniumServer = require('selenium-server');

module.exports = {
    // An array of folders (excluding subfolders) where your tests are located;
    // if this is not specified, the test source must be passed as the second argument to the test runner.
    src_folders: ['tests/e2e'],

    webdriver: {
        start_process: true,
        port: 4444,
        server_path: geckodriver.path,
        cli_args: [
            // very verbose geckodriver logs
            // '-vv'
        ]
    },

    selenium: {
        // Selenium Server is running locally and is managed by Nightwatch
        selenium: {
            start_process: true,
            port: 4444,
            server_path: seleniumServer.path,
            cli_args: {
                'webdriver.gecko.driver': geckodriver.path,
            }
        },
        webdriver: {
            start_process: false
        }
    },

    'selenium.firefox': {
        extends: 'selenium',
        desiredCapabilities: {
            browserName: 'firefox'
        }
    },

    test_settings: {
        default: {
            launch_url: 'https://nightwatchjs.org',
            desiredCapabilities : {
                browserName : 'firefox',
                alwaysMatch: {
                    // Enable this if you encounter unexpected SSL certificate errors in Firefox
                    // acceptInsecureCerts: true,
                    'moz:firefoxOptions': {
                        args: [
                            // '-headless',
                            // '-verbose'
                        ],
                    }
                }
            }
        }
    }
};