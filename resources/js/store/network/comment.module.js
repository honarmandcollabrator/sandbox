import axios from "axios";

export const commentModule = {
    namespaced: true,
    state: {},
    getters: {},
    actions: {
        store({rootState}, data) {
            return axios.post(`network/timeline/${rootState.authModule.user.timeline_id}/post/${data.postId}/comment`, data.request)
        },
        update({rootState}, data) {
            return axios.post(`network/timeline/${rootState.authModule.user.timeline_id}/post/${data.postId}/comment/${data.commentId}`, data.request)
        },
        destroy({rootState}, id) {
            return axios.delete(`network/timeline/${rootState.authModule.user.timeline_id}/post/${id}`)
        }
    },
    mutations: {}
};
