import axios from "axios";
import route from "../route/route";

export const selectablesModule = {
    namespaced: true,
    state: {
        provinces: undefined,
        jobFilterOptions: {},
    },
    actions: {
        getProvinces({commit}) {
            const url = route('selectable.provinces');
            return axios.get(url)
                .then(res => {
                    commit('SET_PROVINCES', res.data.data);
                })
        },
        getJobOptions({commit}) {
            const url = route('jobs.job.filter.options');
            return axios.get(url)
                .then(res => {
                    commit('SET_JOB_OPTIONS', res.data.data);
                })
        },
    },
    mutations: {
        SET_PROVINCES(state, payload) {
            state.provinces = payload;
        },
        SET_JOB_OPTIONS(state, payload) {
            state.jobFilterOptions = payload;
        },
    }
};
