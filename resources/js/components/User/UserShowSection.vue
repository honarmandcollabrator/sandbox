<template>
    <v-container>
        <v-card v-if="!appLoading">

            <v-img
                class="white--text align-end"
                height="200px"
                src="/images/application/profile-background.svg"
            >
            </v-img>

            <v-card-text class="mt-n6">
                <v-row class="px-4">
                    <v-col cols="12" md="5" class="justify-start mt-n8">
                        <v-hover style="border-radius: 28px">
                            <template v-slot:default="{ hover }">
                                <v-progress-circular
                                    :rotate="90"
                                    :size="160"
                                    :width="15"
                                    class="mt-n12"
                                    :color="user.complete_percentage === 100 ? 'success' : (user.complete_percentage > 50 ? 'yellow' : 'red')"
                                    :value="user.complete_percentage"
                                    reactive
                                >
                                    <v-avatar
                                        color="white"
                                        class="elevation-1"
                                        size="145"
                                    >
                                        <v-img :src="user.avatar"></v-img>
                                    </v-avatar>
                                    <v-fade-transition>
                                        <v-overlay
                                            style="border-radius: 200px"
                                            v-if="hover"
                                            absolute
                                            :color="user.complete_percentage === 100 ? 'success' : (user.complete_percentage > 50 ? 'yellow' : 'red')"
                                        >
                                            {{user.complete_percentage}}%
                                        </v-overlay>
                                    </v-fade-transition>
                                </v-progress-circular>
                            </template>
                        </v-hover>
                        <p class="mt-3 mb-0 font-weight-bold">{{user.name}}</p>
                        <span class="mb-3 caption">نام کاربری:
                            {{user.username}}
                        </span>

                    </v-col>

                    <v-col cols="12" md="4">
                        <span v-if="!!user.details.province.id">
                            <v-icon>mdi-map-marker</v-icon>
                            {{user.details.province.name}}
                        </span>
                    </v-col>


                    <v-col cols="12" md="3" class="d-flex justify-end align-baseline">
                        <template v-if="!isMe">
                            <v-btn
                                @click="startRequest"
                                v-if="!isMe && !appLoading && user.details.friend_status === 'not_friends'"
                                color="primary" small :loading="submitting"
                            >درخواست دوستی
                            </v-btn>
                            <v-btn
                                @click="startAccept"
                                v-if="!isMe && !appLoading && user.details.friend_status === 'can_accept'"
                                color="primary" small :loading="submitting"
                            >پذیرش درخواست دوستی
                            </v-btn>
                            <v-btn
                                v-else-if="!isMe && !appLoading && user.details.friend_status === 'pending'"
                                color="primary" small disabled
                            >در انتظار...
                            </v-btn>
                            <app-dialog-alert
                                v-else-if="!isMe && !appLoading && user.details.friend_status === 'approved'"
                                title="حذف دوستی"
                                message="آیا مطمئن هستید که میخواهید این دوستی را حذف کنید؟"
                            >
                                <template #activatorButton>
                                    <v-btn
                                        color="red white--text" small
                                    >
                                        حذف دوستی
                                    </v-btn>
                                </template>
                                <template #button>
                                    <v-btn
                                        @click="startUnfriend"
                                        color="red white--text"
                                        :loading="submitting"
                                    >
                                        متوجه ام، حذف کن.
                                    </v-btn>
                                </template>
                            </app-dialog-alert>
                        </template>

                        <app-dialog-form v-if="isMe || isSuperAdmin">
                            <template #activatorButton>
                                <v-btn small icon color="warning" class="mx-3">
                                    <v-icon>mdi-pen</v-icon>
                                </v-btn>
                            </template>

                            <template #form>
                                <user-edit-form :user="user" @user-profile-updated="updateProfile"></user-edit-form>
                            </template>
                        </app-dialog-form>

                    </v-col>


                    <v-col cols="12" md="12">
                        <div v-if="user.about !== null" class="my-3">
                            <span class="font-weight-bold">درباره من:</span>
                            <br>
                            {{user.about}}
                        </div>
                    </v-col>


                    <v-col cols="12" v-if="me.complete_percentage !== 100">
                        <v-divider></v-divider>
                        <p class="my-3 font-weight-bold">وضعیت تکمیل پروفایل: </p>

                        <v-progress-linear
                            :color="user.complete_percentage === 100 ? 'success' : (user.complete_percentage > 50 ? 'yellow' : 'red')"
                            :buffer-value="user.complete_percentage"
                            height="25"
                            reactive
                        >
                            <strong>{{user.complete_percentage}}%</strong>
                        </v-progress-linear>
                        <br>
                        <span
                            v-if="user.complete_percentage !== 100 && isMe"
                            class="caption"
                        >
                            <v-chip x-small>آواتار</v-chip> -
                            <v-chip x-small>درباره من</v-chip> -
                            <v-chip x-small>استان محل سکونت</v-chip> -
                            <v-chip x-small>یک تجربه کاری</v-chip> -
                            <v-chip x-small>پر کردن رزومه کاری</v-chip>
                            باعث افزایش درصد خواهند شد.
                        </span>
                    </v-col>

                </v-row>


                <v-divider class="mt-10"></v-divider>

            </v-card-text>

            <v-card-actions>
                <v-btn
                    :to="{name: 'network-timeline', params: {id: user.details.timeline_id}}"
                    depressed class="warning lighten-3" light block style="color: black !important;"
                >
                    پست
                    ها
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-container>
</template>


<script>

    import {LoadingMixin} from "../../Mixins/LoadingMixin";
    import AppDialogForm from "../Layouts/AppDialogForm";
    import UserEditForm from "./UserEditForm";
    import AppDialogAlert from "../Layouts/AppDialogAlert";
    import {SubmittingMixin} from "../../Mixins/SubmittingMixin";
    import {AuthMixin} from "../../Mixins/AuthMixin";
    import {mapMutations} from "vuex";

    export default {
        name: "UserShowSection",
        components: {AppDialogAlert, UserEditForm, AppDialogForm},
        mixins: [LoadingMixin, SubmittingMixin, AuthMixin],
        data() {
            return {
                user: {},
                isMe: false,
                showEditor: false,
                headers: [
                    {
                        id: 1,
                        text: 'محل کار',
                        align: 'right',
                        value: 'work_place_name',
                    },
                    {
                        id: 2,
                        text: 'سمت شغلی',
                        align: 'right',
                        value: 'job_role',
                    },
                    {
                        id: 3,
                        text: 'تاریخ شروع فعالیت',
                        align: 'right',
                        value: 'started_at',
                    },
                    {
                        id: 4,
                        text: 'تاریخ پایان فعالیت',
                        align: 'right',
                        value: 'finished_at',
                    },
                    {
                        id: 5,
                        text: 'ویرایش',
                        align: 'right',
                        value: 'action',
                    },
                ],
            }
        },
        computed: {
            id() {
                return this.$route.params.id;
            },
            computedHeaders() {
                if (this.isMe || this.isSuperAdmin) {
                    return this.headers;
                } else {
                    return this.headers.filter(function (item) {
                        return item.id < 5;
                    });
                }
            }
        },
        methods: {
            ...mapMutations(
                [
                    'SAVE_USER_DATA',
                ]
            ),
            getUser(id) {
                this.loadingStart();
                const $url = this.route('user.show', {'user': id});
                return axios.get($url)
                    .then(res => {
                        this.user = res.data.data;
                        this.isMe = res.data.data.details.is_mine;
                        if (this.isMe) {
                            /*=== Just a refresher for me data in store ===*/
                            this.SAVE_USER_DATA(res.data.data);
                        }
                        this.loadingFinish();
                    })
                    .catch(err => {
                        this.loadingFailure(err);
                    });
            },
            updateProfile(payload) {
                this.user = payload;

                /*=== Update me in authModule if the user is myself ===*/
                if (this.me.details.id === payload.details.id) {
                    this.SAVE_USER_DATA(payload);
                }

            },
            request() {
                const $url = this.route('friendship.request', this.user.details.id);
                return axios.put($url)
            },
            accept() {
                const $url = this.route('friendship.accept', this.user.details.id);
                return axios.put($url)
            },
            unfriend() {
                const $url = this.route('friendship.unfriend', this.user.details.id);
                return axios.put($url)
            },
            startRequest() {
                this.toggleSubmitter();
                this.request()
                    .then(res => {
                        this.user.details.friend_status = 'pending';
                        this.toggleSubmitter();
                    })
                    .catch(err => {
                        this.loadingFailure(err);
                        this.toggleSubmitter();
                    })
            },
            startAccept() {
                this.toggleSubmitter();
                this.accept()
                    .then(res => {
                        this.user.details.friend_status = 'approved';
                        this.toggleSubmitter();
                    })
                    .catch(err => {
                        this.loadingFailure(err);
                        this.toggleSubmitter();
                    })
            },
            startUnfriend() {
                this.toggleSubmitter();
                this.unfriend()
                    .then(res => {
                        this.user.details.friend_status = 'not_friends';
                        this.toggleSubmitter();
                    })
                    .catch(err => {
                        this.loadingFailure(err);
                        this.toggleSubmitter();
                    })
            },
        },
        created() {
            this.getUser(this.id)
        },
        beforeRouteUpdate(to, from, next) {
            const id = to.params.id;
            this.getUser(id);
            next()
        },
    }
</script>
