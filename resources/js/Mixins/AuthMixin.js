import {mapGetters, mapState} from "vuex";

export const AuthMixin = {
    computed: {
        ...mapGetters(
            [
                'myTimelineId',

                'isLoggedIn',
                'isVerified',
                'jwt',
                'myId',

                /*=== Roles ===*/
                'isSuperAdmin',
                'isAdmin',
                'isNetworkManager',
                'isJobManager',
                'isContactManager',
                'isGold',
                'isSilver',
                'isNormal',

                /*=== Abilities ===*/
                'canManageContacts',
                'canAccessChat',
                'canManageJobs',
                'canHaveCompany',
                'canFilterJobs',

                'hasResume',
                'hasCompany',
            ]
        ),
        ...mapState(
            {
                me: state => state.authModule.me,
                status: state => state.authModule.status,
            }
        )
    }
};
