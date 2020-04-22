import axios from "axios";
import router from "../../router"

export const jobModule = {
    namespaced: true,
    state: {
        jobs: '',
        meta: '',
        options: '',
        filters: '',
        myJobs: '',
        job: '',
        appliedResumes: '',
    },
    getters: {},
    actions: {
        index({commit}) {
            return axios.get('job')
                .then(res => {
                    commit('SET_JOBS', res)
                })
        },
        getMyJobs({commit}) {
            return axios.get('my-jobs')
                .then(res => {
                    commit('SET_MY_JOBS', res);
                })
        },
        show({state, commit}, id) {
            return axios.get(`job/${id}`)
                .then(res => {
                    commit('SET_JOB', res);
                })
        },
        store({state}, request) {
            return axios.post(`job`, request)
                .then(res => {
                    router.push({name: 'job-page', params: {id: res.data.data.id}})
                })
        },
        update({commit, state}, request) {
            return axios.put(`job/${state.job.id}`, request)
                .then(res => {
                    commit('SET_JOB', res)
                })
        },
        destroy({state}) {
            return axios.delete(`job/${state.job.id}`)
        },
        jobOptions({commit}) {
            return axios.get(`job-filter-options`)
                .then(res => {
                    commit('SET_OPTIONS', res)
                })
        },
        filterJob({commit}, filters) {
            commit('SET_FILTERS', filters);
            return axios.get(`filter/job`, {
                params: filters
            })
                .then(res => {
                    commit('SET_JOBS', res)
                })
        },
        apply({state}) {
            return axios.get(`apply-resume/${state.job.id}`);
        },

        jobResumes({state, commit}, id) {
            return axios.get(`applied-resumes/${id}`)
                .then(res => {
                    commit('SET_APPLIED_RESUMES', res);
                })

        },
        loadJobHomePage({commit}) {
            return axios.all([
                axios.get('job'),
                axios.get(`job-filter-options`),
            ])
                .then((resArr) => {
                    commit('SET_JOBS', resArr[0]);
                    commit('SET_OPTIONS', resArr[1]);
                })
        },
        loadMyJobsPage({commit, rootState}) {
            return axios.all([
                axios.get('my-jobs'),
                axios.get(`company/${rootState.authModule.user.company_id}`),
            ])
                .then((resArr) => {
                    commit('SET_MY_JOBS', resArr[0]);
                    commit('companyModule/SET_COMPANY', resArr[1], {root: true})
                })
        },
        changePage({commit, rootState}, page) {
            return axios.get(`job?page=${page}`)
                .then(res => {
                    commit('SET_JOBS', res)
                })
        },
        changeFilteredPage({commit, rootState, state}, page) {
            return axios.get(`filter/job?page=${page}`,{
                params: state.filters
            })
                .then(res => {
                    commit('SET_JOBS', res)
                })
        },
        clearFilter({commit}) {
            commit('CLEAR_FILTER')
        }
    },
    mutations: {
        SET_JOBS(state, payload) {
            state.jobs = payload.data.data;
            state.meta = payload.data.meta;
        },
        SET_MY_JOBS(state, payload) {
            state.myJobs = payload.data.data
        },
        SET_JOB(state, payload) {
            state.job = payload.data.data
        },
        SET_OPTIONS(state, payload) {
            state.options = payload.data
        },
        SET_APPLIED_RESUMES(state, payload) {
            state.appliedResumes = payload.data.data
        },
        SET_FILTERS(state, payload) {
            state.filters = payload
        },
        CLEAR_JOB(state) {
            state.job = ''
        },
        CLEAR_FILTER(state) {
            state.filters = ''
        }
    }
};
