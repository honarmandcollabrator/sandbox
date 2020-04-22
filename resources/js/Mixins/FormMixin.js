import {eventBus} from "../app";

export const FormMixin = {
    data() {
        return {
            valid: false,
            errors: '',
        }
    },
    methods: {
        populateErrors(err) {
            this.errors = err.response.data.errors;
        },
        clearServerErrors(string) {
            this.errors[string] ? this.errors[string] = null : ''
        },
    }
};
