import axios from "axios";

export const timelineModule = {
    namespaced: true,
    state: {
        timeline: '',
        friendRequests: '',
        myFriends: '',
    },
    getters: {},
    actions: {
        show({commit}, id) {
            return axios.get(`network/timeline/${id}`)
                .then(res => {
                    commit('SET_TIMELINE', res)
                })
        },
        update({commit}, data) {
            return axios.post(`network/timeline/${data.id}`, data.request)
                .then(res => {
                    commit('SET_TIMELINE', res)
                })
        },
        getMyFriends({commit}) {
            return axios.get('network/my-friends')
                .then(res => {
                    commit('SET_FRIENDS', res);
                })
        },
        friendRequests({commit}) {
            return axios.get(`network/friend-requests`)
                .then(res => {
                    commit('SET_FRIEND_REQUESTS', res)
                })
        },
        acceptRequest({commit}, item) {
            return axios.get(`network/accept-request/${item.sender_id}`)
                .then(() => {
                    commit('REMOVE_ITEM_FROM_FRIEND_REQUESTS', item)
                })
        },
        denyRequest({commit}, item) {
            return axios.get(`network/deny-request/${item.sender_id}`)
                .then(() => {
                    commit('REMOVE_ITEM_FROM_FRIEND_REQUESTS', item)
                })
        },
        requestFriendship({commit}, id) {
            return axios.get(`network/friend-request/${id}`)
                .then(res => {
                    commit('CHANGE_FRIEND_STATUS_TO_PENDING')
                })
        },
        unfriend({commit}, id) {
            return axios.get(`network/unfriend/${id}`)
                .then(res => {
                    commit('CHANGE_FRIEND_STATUS_TO_NOT_FRIENDS')
                })
        }
    },
    mutations: {
        SET_TIMELINE(state, payload) {
            state.timeline = payload.data.data;
        },
        SET_FRIEND_REQUESTS(state, payload) {
            state.friendRequests = payload.data;
        },
        REMOVE_ITEM_FROM_FRIEND_REQUESTS(state, payload) {
            state.friendRequests = state.friendRequests.filter(item => item !== payload)
        },
        CHANGE_FRIEND_STATUS_TO_NOT_FRIENDS(state) {
            state.timeline.friend_status = 'not_friends';
        },
        CHANGE_FRIEND_STATUS_TO_PENDING(state) {
            state.timeline.friend_status = 'pending';
        },
        SET_FRIENDS(state, payload) {
            state.myFriends = payload.data.data;
        }
    }
};
