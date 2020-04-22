<template>
    <v-container>
        <v-row min-height="584" align="center" justify="center">
            <v-col cols="12" md="8">
                <v-card :loading="submitting" :disabled="submitting">
                    <v-bottom-navigation
                        grow
                        color="teal"
                    >
                        <v-btn :to="{name: 'login'}">
                            <span>ورود</span>
                            <v-icon>mdi-login-variant</v-icon>
                        </v-btn>

                        <v-btn :to="{name: 'register'}">
                            <span>ثبت نام</span>
                            <v-icon>mdi-account-plus-outline</v-icon>
                        </v-btn>

                    </v-bottom-navigation>
                    <v-row justify="center" align="center">
                        <v-col cols="6">
                            <v-card-text>
                                <v-container>
                                    <v-form v-model="valid" ref="form">
                                        <v-row>
                                            <v-text-field
                                                outlined
                                                dir="ltr"
                                                v-model="email"
                                                :rules="emailRules"
                                                label="ایمیل"
                                                required
                                                :error-messages="errors.email || null"
                                                @input="clear"
                                            ></v-text-field>
                                            <v-text-field
                                                outlined
                                                dir="ltr"
                                                v-model="password"
                                                :rules="passwordRules"
                                                label="رمز عبور"
                                                required
                                                type="password"
                                                :error-messages="errors.password || null"
                                                @input="clear"
                                            ></v-text-field>
                                        </v-row>


                                        <div class="mt-5">
                                            <v-btn @click="startLogin" color="primary" :disabled="!valid">
                                                ورود
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-container>
                            </v-card-text>
                        </v-col>
                        <v-col cols="4">
                            <v-img src="/images/application/tootiko-logo-tree.png"></v-img>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>

    </v-container>
</template>


<script>
    import {mapActions} from 'vuex';
    import {FormMixin} from "../../Mixins/FormMixin";
    import {SubmittingMixin} from "../../Mixins/SubmittingMixin";
    import router from "../../router";
    import AppDialogForm from '../Layouts/AppDialogForm';
    import {eventBus} from "../../app";

    export default {
        name: "Login",
        components: {AppDialogForm},
        mixins: [FormMixin, SubmittingMixin],
        data: () => ({
            loginLoading: false,
            valid: false,
            password: '',
            passwordRules: [
                v => !!v || 'الزامی است',
                v => v.length >= 8 || 'رمز عبور باید حداقل 8 کارکتر داشته باشد',
            ],
            email: '',
            emailRules: [
                v => !!v || 'الزامی است',
                v => /.+@.+\..+/.test(v) || 'ایمیل باید صحیح باشد',
            ],
            forgotPasswordEmail: '',
            forgotPasswordEmailRules: [
                v => !!v || 'الزامی است',
                v => /.+@.+\..+/.test(v) || 'ایمیل باید صحیح باشد',
            ],
        }),
        computed: {
            formData() {
                return {
                    email: this.email,
                    password: this.password
                }
            },
        },
        methods: {
            ...mapActions([
                'login'
            ]),
            clear() {
                this.clearServerErrors('email');
                this.clearServerErrors('password')
            },
            startLogin() {
                this.loginLoading = true;
                this.login(this.formData)
                    .then(() => {
                        return router.push({name: 'network-feed'})
                    })
                    .catch(err => {
                        this.submitFailure(err);
                        this.loginLoading = false;
                    })
            },
            forgotPassword() {
                this.toggleSubmitter();
                const url = this.route('auth.forgot.password.email');
                return axios.post(url, {
                    email: this.forgotPasswordEmail,
                })
                    .then(res => {
                        eventBus.$emit('showMessage', {
                            message: 'لینک بازیابی به ایمیل شما ارسال شد.',
                            color: 'success'
                        });
                        this.submitSuccess();
                    })
                    .catch(err => {

                        if (err.response.status === 404) {
                            eventBus.$emit('showMessage', {
                                message: err.response.data.message,
                                color: 'red'
                            });
                            this.toggleSubmitter();
                        } else {
                            this.submitFailure(err)
                        }
                    });
            }
        },
        created() {
// console.log(`${router.resolve({name: 'password.reset'}).href}/<token>/<email>`);
        }
    }
</script>

<style scoped>

</style>
