<template>
    <v-dialog
        v-model="dialog"
        width="600"
        overlay-color="red"
    >
        <template #activator="{ on }">
             <span v-on="on">
                <slot name="activatorButton"></slot>
            </span>
        </template>
        <v-card>
            <v-card-title
                class="headline red white--text"
                primary-title
            >
                <v-icon class="white--text mr-2">mdi-alert</v-icon>
                {{title}}
            </v-card-title>
            <v-card-text class="mt-2">
                {{message}}
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="dialog = false" text color="primary">بستن</v-btn>

                <slot name="button"></slot>

            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import {eventBus} from "../../app";

    export default {
        name: "AppDialogAlert",
        props: {
            title: {
                type: String,
                default: 'Delete'
            },
            message: {
                type: String,
                default: 'آیا مطمئن هستید که میخواید این مورد را حذف کنید؟'
            },
        },
        data() {
            return {
                dialog: false,
            }
        },
        methods: {
            close() {
                this.dialog = false;
            }
        },
        created() {
            eventBus.$on('closeAppDialogAlert', () => {
                this.close();
            })
        }
    }
</script>

<style scoped>

</style>
