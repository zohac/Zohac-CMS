import Option from './option';

export default class UtilityClass {

    isObjectEmpty(obj: Object): boolean {
        for (const key in obj) {
            if (obj.hasOwnProperty(key))
                return false;
        }

        return true;
    }

    loadOptionsIfNotNull(options: Option | null) {
        if (null !== options && !this.isObjectEmpty(options)) {
            this.loadOptions(options);
        }
    }

    loadOptions(options: Option | null) {
        for (const key in options) {
            if (this.hasOwnProperty(key)) {
                this[key] = options[key];
            }
        }
    }
}