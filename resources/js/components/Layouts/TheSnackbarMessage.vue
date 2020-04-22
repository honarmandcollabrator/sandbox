<template>
    <div class="text-center">
        <v-snackbar
            :color="color"
            v-model="snackbar"
            :timeout="timeout"
        >
            {{ text }}
            <v-btn
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
        name: "TheSnackbarMessage",
        data: () => ({
            snackbar: false,
            text: '',
            color: '',
            timeout: 20000,
        }),
        methods: {
            show(payload) {
                this.text= payload.message;
                this.color = payload.color;
                this.snackbar = true;
            }
        },
        created() {
            eventBus.$on('showMessage', this.show)
        }

    }
</script>

<style scoped>

</style>
