import axios from "axios";

export const postModule = {
    namespaced: true,
    state: {
        post: '',
        feed: '',
    },
    getters: {},
    actions: {
        feedPosts({rootState, commit}) {
            return axios.get(`network/timeline/1/post`)
                .then(res => {
                    commit('SET_FEED', res);
                })
        },
        hashtagPosts({rootState, commit}, hashtag) {
            return axios.get(`network/timeline/1/post?hashtag=${hashtag}`)
                // .then(res => {
                //     commit('SET_FEED', res);
                // })
        },
        store({rootState}, request) {
            return axios.post(`network/timeline/${rootState.authModule.user.timeline_id}/post`, request)
        },
        update({rootState}, request) {
            return axios.post(`network/timeline/${rootState.authModule.user.details.timeline_id}/post/${request.id}`, request.data)
        },
        destroy({rootState}, id) {
            return axios.delete(`network/timeline/${rootState.authModule.user.timeline_id}/post/${id}`)
        },
        toggleLike({rootState}, id) {
            return axios.get(`network/timeline/${rootState.authModule.user.timeline_id}/like-post/${id}`)
        },
        toggleShare({rootState}, id) {
            return axios.get(`network/timeline/${rootState.authModule.user.timeline_id}/share-post/${id}`)
        }
    },
    mutations: {
        SET_FEED(state, payload) {
            state.feed = payload.data.data;
        }
    }
};
