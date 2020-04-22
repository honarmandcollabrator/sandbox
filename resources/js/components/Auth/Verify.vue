<template>
    <div>
        <v-card :disabled="verifying" :loading="verifying">
            <v-card-text>
                <v-alert color="warning">
                    ایمیل فعالسازی برای شما ارسال شد. لطفا وارد ایمیل خود شوید و روی لینک فعالسازی کلیک کنید.
                </v-alert>

                <v-text-field
                    v-model="link"
                    label="لینک فعالسازی را اینجا کپی کرده و روی دکمه فعالسازی کنید."
                >

                </v-text-field>
                <v-btn @click="startVerify">فعالسازی</v-btn>
                <v-btn :loading="submitting" @click="resendEmail" :disabled="!(countDown === 0)">
                    ارسال دوباره لینک فعالسازی اکانت
                    <span v-if="!(countDown === 0)"> (تا {{countDown}} ثانیه دیگر...) </span>
                </v-btn>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
    import {mapActions} from "vuex";
    import {LoadingMixin} from "../../Mixins/LoadingMixin";
    import {eventBus} from "../../app";
    import {SubmittingMixin} from "../../Mixins/SubmittingMixin";

    export default {
        name: "Verify",
        mixins: [LoadingMixin, SubmittingMixin],
        data() {
            return {
                verifying: false,
                link: '',
                countDown: 10,
            }
        },
        computed: {
            baseUrl() {
                return this.route().ziggy.baseDomain;
            }
        },
        methods: {
            ...mapActions([
                'submitVerifyLink'
            ]),
            resendEmail() {
                this.toggleSubmitter();
                const url = this.route('verification.resend');
                return axios.post(url)
                    .then(() => {
                        this.countDown = 60;
                        this.countDownTimer();
                        this.toggleSubmitter();
                        eventBus.$emit('showMessage', {message: 'ایمیل فعالسازی جدیدی برای شما ارسال شد.', color: 'success'})
                    })
                    .catch(err => {
                        this.submitFailure(err);
                    })
            },
            startSubmit(url) {
                this.verifying = true;
                return this.submitVerifyLink(url)
                    .then(() => {
                        this.$router.push({name: 'network-feed'})
                    })
                    .catch(err => {
                        if ([403, 417].includes(err.response.status)) {
                            eventBus.$emit('showMessage', {message: 'ایمیل فعالسازی صحت نداشت.', color: 'red'});
                            this.verifying = false;
                        } else if (err.response.status === 429) {
                            eventBus.$emit('showMessage', {
                                message: 'درخواستهای بسیاری ارسال کرده اید، لطفا چند دقیقه دیگر دوباره سعی کنید.',
                                color: 'warning'
                            });
                            this.verifying = false;
                        } else {
                            this.appError(err);
                            this.verifying = false;
                        }
                    })
            },
            startVerify() {
                const url = decodeURIComponent(this.link.split('queryURL=')[1]).replace(`https://${this.baseUrl}`, '');
                this.startSubmit(url);
            },
            countDownTimer() {
                if (this.countDown > 0) {
                    setTimeout(() => {
                        this.countDown -= 1
                        this.countDownTimer()
                    }, 1000)
                }
            }
        },
        mounted() {
            if (this.$route.query.queryURL) {
                const url = this.$route.query.queryURL.replace(`https://${this.baseUrl}`, '');
                this.startSubmit(url);
            }
            this.countDownTimer();

        },
    }
</script>

<style scoped>

</style>
