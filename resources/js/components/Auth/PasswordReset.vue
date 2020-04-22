<template>
    <div>
        <v-form v-model="valid">
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="password"
                    :rules="passwordRules"
                    label="رمز عبور"
                    type="password"
                    :error-messages="errors.password || null"
                    @input="clearServerErrors('password')"
                ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="passwordConfirm"
                    :rules="passwordConfirmRules"
                    label="تکرار رمز عبور"
                    type="password"
                    :error-messages="errors.password || null"
                    @input="clearServerErrors('password')"
                ></v-text-field>
            </v-col>
        </v-form>

        <v-btn @click="resetPassword" :loading="submitting" :disabled="!valid">ذخیره رمز عبور جدید</v-btn>
    </div>
</template>

<script>
    import {FormMixin} from "../../Mixins/FormMixin";
    import {SubmittingMixin} from "../../Mixins/SubmittingMixin";
    import {eventBus} from "../../app";

    export default {
        name: "PasswordReset",
        mixins: [FormMixin, SubmittingMixin],
        data() {
            return {
                valid: false,
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
        methods: {
            resetPassword() {
                this.toggleSubmitter();
                const url = this.route('auth.forgot.password.reset');
                const formData = {
                    token: this.$route.params.token,
                    email: this.$route.params.email,
                    password: this.password,
                    password_confirmation: this.passwordConfirm,
                };
                console.log(formData);
                return axios.post(url, formData)
                    .then(res => {
                        eventBus.$emit('showMessage', {message: 'رمز عبور با موفقیت تغییر پیدا کرد.', color: 'success'});
                        this.$router.push({name: 'login'});
                        this.submitSuccess();
                    })
                    .catch(err => {
                        if ([422, 417].includes(err.response.status)) {
                            eventBus.$emit('showMessage', {message: err.response.data, color: 'red'});
                            this.toggleSubmitter();
                        } else {
                            this.submitFailure(err);
                        }
                    })
            }
        }
    }
</script>

<style scoped>

</style>
