import axios from "axios";

export const resumeModule = {
    namespaced: true,
    state: {
        resume: '',
    },
    getters: {},
    actions: {
        // index({state}) {
        //     return axios.get('job')
        //         .then(res => {
        //             state.jobs = res.data.data
        //         })
        // },
        show({state, commit}, id) {
            return axios.get(`resume/${id}`)
                .then(res => {
                    commit('SET_RESUME', res);
                })
        },
        store({state, commit, rootState}, request) {
            return axios.post(`resume`, request)
                .then(res => {
                    commit('SET_RESUME', res);
                    commit('SET_RESUME_ID', res, {root: true})
                })
        },
        update({state, commit}, request) {
            return axios.put(`resume/${state.resume.id}`, request)
                .then(res => {
                    commit('SET_RESUME', res)
                })
        },
        // destroy({commit}, id) {
        //     return axios.delete(`question/${id}`)
        //         .then(res => {
        //             //
        //         })
        // },
    },
    mutations: {
        SET_RESUME(state, payload) {
            state.resume = payload.data.data;
        }
    }
};
