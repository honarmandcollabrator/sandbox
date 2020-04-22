<template>
    <v-dialog v-model="dialog" max-width="600" :fullscreen="fullscreen"
              :transition="fullscreen ? 'dialog-bottom-transition' : ''">
        <template #activator="{ on }">
            <span v-on="on">
                <slot name="activatorButton"></slot>
            </span>
        </template>
        <v-card :loading="dialogLoading" :disabled="dialogLoading">
            <v-card-title>
                <v-row>
                    <v-col cols="10">
                        {{title}}
                    </v-col>
                    <v-col cols="2">
                        <div class="d-flex justify-end">
                            <v-btn @click="dialog = false" color="red" icon>
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </div>
                    </v-col>
                </v-row>
            </v-card-title>
            <v-card-text>

                <slot name="form"></slot>

            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    import {eventBus} from "../../app";

    export default {
        name: "AppDialogForm",
        props: {
            title: {
                type: String,
            },
            fullscreen: {
                type: Boolean,
            }
        },
        data() {
            return {
                dialog: false,
                dialogLoading: false,
            }
        },
        methods: {
            close() {
                this.dialog = false;
            }
        },
        created() {
            eventBus.$on('closeAppDialogForm', () => {
                this.close();
            });
            eventBus.$on('toggleLoadingAppDialogForm', () => {
                this.dialogLoading = !this.dialogLoading;
            });
            eventBus.$on('stopLoadingAppDialogForm', () => {
                this.dialogLoading = false;
            })
        }
    }
</script>
