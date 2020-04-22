<template>
    <app-layout v-if="loadingFinished">
        <template #content>
            <router-view></router-view>
            <the-snackbar-catch-all></the-snackbar-catch-all>
            <the-snackbar-message></the-snackbar-message>
        </template>
    </app-layout>
</template>

<script>
    import axios from "axios";
    import AppLayout from "./components/Layouts/AppLayout";
    import {mapActions, mapMutations} from "vuex";
    import TheSnackbarCatchAll from "./components/Layouts/TheSnackbarCatchAll";
    import {LoadingMixin} from "./Mixins/LoadingMixin";
    import {AuthMixin} from "./Mixins/AuthMixin";
    import TheSnackbarMessage from "./components/Layouts/TheSnackbarMessage";

    export default {
        name: "App",
        mixins: [LoadingMixin, AuthMixin],
        data() {
            return {
                loadingFinished: false,
            }
        },
        components: {
            TheSnackbarMessage,
            TheSnackbarCatchAll,
            AppLayout,
        },
        methods: {
            ...mapMutations(
                [
                    'SET_LOADING',
                    'DO_LOGOUT'
                ]
            ),
            ...mapActions(
                [
                    'getMe',
                    'logout',
                ]
            )
        },
        computed: {},
        beforeCreate() {
            /*=== Setting axios logic for all requests when token is not a valid ===*/
            axios.interceptors.response.use(undefined, err => {
                if (err.response) {
                    if (err.response.status === 401) {
                        this.DO_LOGOUT();
                        this.$router.push('login');
                    }
                }
                return Promise.reject(err)
            });
        },
        created() {
            /*=== Check local storage token and if exist set user data with a ajax request. ===*/
            if (!!localStorage.getItem('token')) {
                this.getMe()
                    .then(() => {
                        this.loadingFinished = true;
                    })
                    .catch(() => {
                        this.DO_LOGOUT();
                        this.loadingFinished = true;
                    })
            } else {
                this.loadingFinished = true;
            }
        }
    }
</script>
