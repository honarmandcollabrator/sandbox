import {mapMutations, mapState} from "vuex";
import {eventBus} from "../app";

export const LoadingMixin = {
    data() {
        return {
            loading: false,
        }
    },
    computed: {
        ...mapState(
            {
                appLoading: state => state.appLoading,
            }
        )
    },
    methods: {
        ...mapMutations(
            [
                'SET_LOADING'
            ]
        ),
        loadingStart() {
            this.SET_LOADING(true)
        },
        loadingFinish() {
            this.SET_LOADING(false)
        },
        loadingSuccess() {
            this.loadingFinish();
        },
        appError(err) {
            eventBus.$emit('somethingBadHappened');
        },
        loadingFailure(err) {
            this.appError(err);
            this.loadingFinish();
        },
        toggleLoading() {
            this.loading = !this.loading;
        }
    }
};
