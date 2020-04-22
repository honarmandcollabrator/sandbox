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
                                    <v-form v-model="valid" ref="registerForm">
                                        <v-row>
                                            <v-text-field
                                                outlined
                                                v-model="name"
                                                :rules="nameRules"
                                                label="نام"
                                                :error-messages="errors.name || null"
                                                @input="clearServerErrors('name')"
                                            ></v-text-field>
                                            <v-text-field
                                                outlined
                                                dir="ltr"
                                                v-model="email"
                                                :rules="emailRules"
                                                label="ایمیل"
                                                :error-messages="errors.email || null"
                                                @input="clearServerErrors('email')"
                                            ></v-text-field>
                                            <v-text-field
                                                outlined
                                                v-model="password"
                                                :rules="passwordRules"
                                                dir="ltr"
                                                label="رمز عبور"
                                                type="password"
                                                :error-messages="errors.password || null"
                                                @input="clearServerErrors('password')"
                                            ></v-text-field>
                                            <v-text-field
                                                outlined
                                                v-model="passwordConfirm"
                                                :rules="passwordConfirmRules"
                                                dir="ltr"
                                                label="تکرار رمز عبور"
                                                type="password"
                                                :error-messages="errors.password || null"
                                                @input="clearServerErrors('password')"
                                            ></v-text-field>
                                        </v-row>
                                        <div class="mt-5">
                                            <v-btn @click="startRegister" color="warning" :disabled="!valid">
                                                ثبت نام
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

    import {mapActions} from "vuex";
    import {FormMixin} from "../../Mixins/FormMixin";
    import {SubmittingMixin} from "../../Mixins/SubmittingMixin";
    import router from "../../router";

    export default {
        name: "Register",
        mixins: [FormMixin, SubmittingMixin],
        data() {
            return {
                valid: false,
                name: '',
                nameRules: [
                    v => !!v || 'الزامی است',
                ],
                email: '',
                emailRules: [
                    v => !!v || 'الزامی است',
                    v => /.+@.+\..+/.test(v) || 'ایمیل باید صحیح باشد',
                ],
                username: '',
                usernameRules: [
                    v => !!v || 'الزامی است',
                ],
                password: '',
                passwordRules: [
                    v => !!v || 'الزامی است',
                    v => v.length >= 8 || 'رمز عبور باید حداقل 8 کارکتر داشته باشد',
                ],
                passwordConfirm: '',
                passwordConfirmRules: [
                    v => this.password === v || 'رمزها باهم برابر نیستند'
                ]
            }
        },
        computed: {
            formData() {
                return {
                    name: this.name,
                    email: this.email,
                    username: this.username,
                    password: this.password,
                    password_confirmation: this.passwordConfirm
                }
            },
        },
        methods: {
            ...mapActions([
                'register'
            ]),
            startRegister() {
                this.toggleSubmitter();
                this.register(this.formData)
                    .then(() => {
                        return router.push({name: 'verify'})
                    })
                    .catch(err => {
                        this.submitFailure(err)
                    })
            }
        }
    }
</script>

<style scoped>

</style>
