<template>
    <div class="text-center">
        <v-snackbar
            v-model="snackbar"
            :timeout="timeout"
        >
            {{ text }}
            <v-btn
                color="blue"
                text
                @click="snackbar = false"
            >
                بستن
            </v-btn>
        </v-snackbar>
    </div>
</template>

<script>
    import {eventBus} from "../../app";

    export default {
        name: "TheSnackbarCatchAll",
        data: () => ({
            snackbar: false,
            text: 'مشکلی در برقراری ارتباط با سرور وجود دارد.',
            timeout: 5000,
        }),
        methods: {
            show(payload) {
                this.text= 'مشکلی در برقراری ارتباط با سرور وجود دارد.';
                this.snackbar = true;

                if (payload === 'create_limit_exceeded') {
                    this.text= 'متاسفانه امکان این عملیات در حال حاضر وجود ندارد. درخواست های زیادی ارسال کرده اید.'
                }
            }
        },
        created() {
            eventBus.$on('somethingBadHappened', this.show)
        }

    }
</script>

<style scoped>

</style>
