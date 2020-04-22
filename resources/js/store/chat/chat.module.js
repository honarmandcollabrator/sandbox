import axios from "axios";
import router from "../../router"

export const chatModule = {
    namespaced: true,
    state: {
        myChatFriends: '',
        sessionChats: '',
    },
    getters: {},
    actions: {
        getSessionChats({commit}, sessionId) {
            return axios.post(`chat/session/${sessionId}/chats`)
                .then(res => {
                    commit('SET_SESSION_CHATS', res);
                })
        },
        getMyChatFriends({commit}) {
            return axios.post('chat/friends')
                .then(res => {
                    commit('SET_FRIENDS', res);
                })
        },
    },
    mutations: {
        SET_SESSION_CHATS(state, payload) {
            state.sessionChats = payload.data.data;
        },
        SET_FRIENDS(state, payload) {
            state.myChatFriends = payload.data.data;
        }
    }
};


