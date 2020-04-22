import {eventBus} from "../app";

export const SubmittingMixin = {
    data() {
        return {
            submitting: false,
        }
    },
    methods: {
        toggleSubmitter() {
            this.submitting = !this.submitting;
            eventBus.$emit('toggleLoadingAppDialogForm');
        },
        stopSubmitter() {
            this.submitting = false;
            eventBus.$emit('stopLoadingAppDialogForm');
        },
        submitSuccess() {
            this.toggleSubmitter();
            eventBus.$emit('closeAppDialogForm');
            eventBus.$emit('closeAppDialogAlert');
        },
        submitFailure(err) {
            if (err.response) {
                if (err.response.status === 422) {
                    this.populateErrors(err);
                } else if (err.response.data.message === 'create_limit_exceeded') {
                    eventBus.$emit('somethingBadHappened', 'create_limit_exceeded');
                } else {
                    eventBus.$emit('somethingBadHappened');
                }
            } else {
                eventBus.$emit('somethingBadHappened');
            }

            this.stopSubmitter();
        },
    }
};
