import axios from "axios";
import route from "../route/route";

export const authModule = {
    state: {
        status: {
            loggedIn: !!localStorage.getItem('token'),
            verified: !!localStorage.getItem('verification'),
        },
        currentJwt: localStorage.getItem('token') || '',
        me: null,
    },
    getters: {
        isLoggedIn: state => state.status.loggedIn,
        isVerified: state => state.status.verified,
        jwt: state => state.currentJwt,
        jwtData: state => state.status.loggedIn ? JSON.parse(atob(state.currentJwt.split('.')[1])) : null,
        myId: (state, getters) => state.status.loggedIn ? getters.jwtData.sub : null,
        tokenRefreshTime: (state, getters) => state.status.loggedIn ? getters.jwtData.exp * 1000 - ((30 * 60) * 1000) : null,

        /*=== Roles ===*/
        isSuperAdmin: state => state.status.loggedIn ? state.me.details.role === 'super_admin' : false,
        isAdmin: state => state.status.loggedIn ? state.me.details.role === 'admin' : false,
        isNetworkManager: state => state.status.loggedIn ? state.me.details.role === 'network_manager' : false,
        isJobManager: state => state.status.loggedIn ? state.me.details.role === 'job_manager' : false,
        isContactManager: state => state.status.loggedIn ? state.me.details.role === 'contact_manager' : false,
        isGold: state => state.status.loggedIn ? state.me.details.role === 'gold' : false,
        isSilver: state => state.status.loggedIn ? state.me.details.role === 'silver' : false,
        isNormal: state => state.status.loggedIn ? state.me.details.role === 'normal' : false,

        /*=== Abilities ===*/
        canManageContacts: (state, getters) => getters.isSuperAdmin || getters.isAdmin || getters.isContactManager,
        canAccessChat: (state, getters) => !getters.isNormal,
        canHaveCompany: (state, getters) => !getters.isNormal && !getters.isSilver,
        canManageJobs: (state, getters) => getters.isSuperAdmin || getters.isAdmin || getters.isJobManager,
        canFilterJobs: (state, getters) => !getters.isNormal,


        hasResume: state => state.status.loggedIn ? state.me.details.resume_id !== 0 : false,
        hasCompany: state => state.status.loggedIn ? state.me.details.company_id !== 0 : false,
        myTimelineId: state => state.status.loggedIn ? state.me.details.timeline_id : '',
    },
    actions: {
        register({state, commit, rootState}, payload) {
            const $url = route('auth.register');
            return axios.post($url, payload)
                .then(res => {
                    commit('SET_TOKEN', res.data.access_token);
                    commit('SAVE_USER_DATA', res.data.me);
                    commit('LOGIN_SUCCESS');
                })
        },
        submitVerifyLink({state, commit, rootState}, payload) {
            return axios.post(payload)
                .then(res => {
                    commit('VERIFY_USER', res.data.data);
                })
        },
        login({state, commit, rootState, dispatch}, payload) {
            const $url = route('auth.login');
            return axios.post($url, payload)
                .then(res => {
                    commit('SET_TOKEN', res.data.access_token);
                    commit('SAVE_USER_DATA', res.data.me);
                    commit('LOGIN_SUCCESS');
                    commit('VERIFY_USER', res.data.me);
                })
        },
        getMe({commit}) {
            const $url = route('auth.me');
            return axios.post($url)
                .then(res => {
                    commit('SAVE_USER_DATA', res.data.data);
                    commit('LOGIN_SUCCESS');
                })
        },
        refresh({commit}) {
            const $url = route('auth.refresh');
            return axios.post($url).then(res => {
                commit('SET_TOKEN', res.data.access_token);
            })
        },
        logout({commit}) {
            const $url = route('auth.logout');
            return axios.post($url)
                .then(() => {
                    commit('DO_LOGOUT');
                })
                .catch(err => {
                    commit('DO_LOGOUT');
                })
        },
    },
    mutations: {
        LOGIN_SUCCESS(state) {
            state.status.loggedIn = true;
        },
        VERIFY_USER(state, payload) {
            if (payload.details.email_verified_at) {
                state.status.verified = true;
                localStorage.setItem('verification', 'the user is verified');
            } else {
                state.status.verified = false;
            }
        },
        SET_TOKEN(state, payload) {
            localStorage.setItem('token', payload);
            axios.defaults.headers.common['Authorization'] = `Bearer ${payload}`;
            Echo.connector.pusher.config.auth.headers['Authorization'] = `Bearer ${payload}`;
            state.currentJwt = payload;
        },
        SAVE_USER_DATA(state, payload) {
            state.me = payload;
        },
        DO_LOGOUT(state) {
            localStorage.removeItem('token');
            localStorage.removeItem('verification');
            delete axios.defaults.headers.common['Authorization'];
            state.status.loggedIn = false;
            state.status.verified = false;
            state.currentJwt = null;
        },
        SET_RESUME_ID(state, payload) {
            state.me.resume_id = payload.data.data.id;
        }
    }
};
