import Vue from 'vue'
import Vuex from 'vuex'
import {authModule} from './auth.module'
import {jobModule} from "./Job/job.module";
import {companyModule} from "./Job/company.module";
import {resumeModule} from "./Job/resume.module";
import {timelineModule} from "./network/timeline.module";
import {postModule} from "./network/post.module";
import {commentModule} from "./network/comment.module";
import {friendModule} from "./network/friend.module";
import {chatModule} from "./chat/chat.module";
import {selectablesModule} from "./selectables.module";


Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        appLoading: false,
    },
    modules: {
        authModule,
        jobModule,
        companyModule,
        resumeModule,
        timelineModule,
        postModule,
        commentModule,
        friendModule,
        chatModule,
        selectablesModule,
    },
    actions: {},
    mutations: {
        SET_LOADING(state, payload) {
            state.appLoading = payload;
        }
    }
})
