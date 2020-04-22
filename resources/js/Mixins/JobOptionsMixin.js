import {mapActions, mapState} from "vuex";

export const JobOptionsMixin = {
    data() {
        return {
            jobOptionsLoading: false,
        }
    },
    methods: {
        ...mapState(
            {
                jobFilterOptions: state => state.selectablesModule.jobFilterOptions,
            }
        ),
        ...mapActions(
            'selectablesModule',
            [
                'getJobOptions',
            ]
        ),
        fillOptionsIfEmpty() {
            if (this.objectIsEmpty(this.jobFilterOptions)) {
                this.jobOptionsLoading = true;
                return this.getJobOptions()
                    .then(() => {
                        this.jobOptionsLoading = false;
                    })
                    .catch(err => {
                        this.appError(err);
                    })
            }
        }
    },
};
