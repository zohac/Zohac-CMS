export default class UtilityClass {

    /**
     * @param {Object} obj
     * @returns {boolean}
     */
    isEmpty(obj) {
        for (const key in obj) {
            if (obj.hasOwnProperty(key))
                return false;
        }

        return true;
    }

    /**
     * @param {Object} options
     */
    loadOptions(options) {
        for (const key in options) {
            if (this.hasOwnProperty(key)) {
                this[key] = options[key];
            }
        }
    }

    /**
     * @param value
     * @returns {number}
     */
    convertToInt(value) {
        const parsed = parseInt(value, 10);

        if (isNaN(parsed)) {
            throw new Error(`Parameter is not a number : {value: ${value}, parsed: ${parsed}}`);
        }

        return parsed;
    }

    /**
     * @param value
     * @returns {string}
     */
    convertToString(value) {
        const converted = String(value);

        if ('string' === converted) {
            return converted;
        }

        throw new Error(`Parameter is not a string : {value: ${value}, converted: ${converted}}`);
    }
}