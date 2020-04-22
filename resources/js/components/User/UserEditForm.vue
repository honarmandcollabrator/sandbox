<template>
    <v-form v-model="valid">
        <v-row>
            <v-col cols="12" md="6" sm="6">
                <v-text-field
                    counter
                    label="نام"
                    v-model="name"
                    :rules="nameRules"
                    :error-messages="errors.name || null"
                    @input="clearServerErrors('name')"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" sm="6">
                <v-text-field
                    counter
                    label="نام کاربری"
                    v-model="username"
                    :rules="usernameRules"
                    :error-messages="errors.username || null"
                    @input="clearServerErrors('username')"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" sm="6">
                <v-file-input
                    accept="image/png, image/jpeg, image/bmp"
                    prepend-icon="mdi-camera"
                    label="آواتار (اختیاری)"
                    id="avatar"
                    :rules="avatarRules"
                    :error-messages="errors.avatar || null"
                    @change="clearServerErrors('avatar')"
                ></v-file-input>
            </v-col>
            <v-col cols="12" md="6" sm="6">
                <v-autocomplete
                    clearable
                    :loading="provincesLoading"
                    :disabled="provincesDisabled"
                    label="محل سکونت (اختیاری)"
                    v-model="province_id"
                    :rules="province_idRules"
                    :items="provinces"
                    item-text="name"
                    item-value="id"
                    :error-messages="errors.province_id || null"
                    @input="clearServerErrors('province_id')"
                ></v-autocomplete>
            </v-col>
        </v-row>


        <v-textarea
            label="درباره من (اختیاری)"
            counter
            v-model="about"
            :rules="aboutRules"
            :error-messages="errors.about || null"
            @input="clearServerErrors('about')"
        ></v-textarea>


        <div class="d-flex justify-end">
            <v-btn @click="startUpdate" color="success" :loading="submitting" :disabled="!valid" text>
                <v-icon>mdi-content-save</v-icon>
            </v-btn>
        </div>
    </v-form>
</template>

<script>
    import {FormMixin} from "../../Mixins/FormMixin";
    import {SubmittingMixin} from "../../Mixins/SubmittingMixin";
    import {mapActions, mapState} from "vuex";
    import {LoadingMixin} from "../../Mixins/LoadingMixin";

    export default {
        name: "UserEditForm",
        mixins: [FormMixin, SubmittingMixin, LoadingMixin],
        data() {
            return {
                provincesLoading: true,
                provincesDisabled: false,
                name: '',
                nameRules: [
                    v => !!v || 'الزامی است',
                ],
                province_id: '',
                province_idRules: [],
                username: '',
                usernameRules: [
                    v => !!v || 'الزامی است',
                ],
                about: '',
                aboutRules: [],
                avatarRules: [
                    file => {
                        if (file.size) {
                            return file.size <= 0.4 * 1024 * 1024 || 'حذاکثر سایز آواتار باید 400 کیلوبایت باشد.'
                        } else {
                            return true;
                        }
                    },
                ]
            }
        },
        props: {
            user: {
                type: Object,
            }
        },
        computed: {
            ...mapState(
                {
                    provinces: state => state.selectablesModule.provinces,
                }
            ),
        },
        methods: {
            ...mapActions(
                'selectablesModule',
                [
                    'getProvinces',
                ]
            ),
            update(formData) {
                const $url = this.route('user.update', {id: this.user.details.id});
                return axios.post($url, formData);
            },
            populateForm() {
                this.name = this.user.name;
                this.username = this.user.username;
                this.province_id = this.user.details.province.id;
                this.about = this.user.about;
            },
            startUpdate() {
                this.toggleSubmitter();
                let data = new FormData();
                let avatar = document.getElementById('avatar').files[0];

                /*=== nullable fields appended if they are filled by user ===*/
                if (avatar !== undefined) {
                    data.append('avatar', avatar);
                }
                if (this.about !== null) {
                    data.append('about', this.about);
                }
                /*=== end ===*/

                data.append('name', this.name);
                data.append('username', this.username);
                if (this.province_id === undefined || this.province_id === null) {
                    data.append('province_id', '');
                } else {
                    data.append('province_id', this.province_id);
                }
                data.append('_method', 'put');

                this.update(data)
                    .then(res => {
                        this.$emit('user-profile-updated', res.data.data);
                        this.submitSuccess();
                    })
                    .catch(err => {
                        this.submitFailure(err)
                    })
            }
        },
        mounted() {
            this.populateForm();

            /*=== Loading provinces for selectable provinces in user edit for first time ===*/

            if (this.provinces !== undefined) {
                this.provincesLoading = false;
            } else {
                this.getProvinces()
                    .then(() => {
                        if (this.provinces !== undefined) {
                            this.provincesLoading = false;
                        } else {
                            this.provincesLoading = false;
                            this.provincesDisabled = true;
                        }
                    })
                    .catch(err => {
                        this.provincesLoading = false;
                        this.provincesDisabled = true;
                        this.loadingFailure(err);
                    })
            }
        }
    }
</script>
