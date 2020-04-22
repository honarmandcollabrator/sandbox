import axios from "axios";

export const companyModule = {
    namespaced: true,
    state: {
        company: '',
    },
    actions: {
        // index({state}) {
        //     return axios.get('job')
        //         .then(res => {
        //             state.jobs = res.data.data
        //         })
        // },
        // show({commit, rootState}) {
        //     return axios.get(`company/${rootState.authModule.user.company_id}`)
        //         .then(res => {
        //             commit('SET_COMPANY', res)
        //         })
        // },
        store({commit, rootState}, request) {
            return axios.post(`company`, request)
                .then(res => {
                    commit('SET_COMPANY', res)
                    rootState.authModule.user.company_id = res.data.id;
                })
        },
        update({commit}, request) {
            return axios.post(`company/${request.id}`, request.data)
                .then(res => {
                    commit('SET_COMPANY', res)
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
        SET_COMPANY(state, payload) {
            state.company = payload.data.data;
        }
    }
};
