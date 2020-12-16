export default class Option {
    add(key: string, value): Option {
        this[key] = value;

        return this;
    }
}