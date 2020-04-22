<template>
    <div>
        <v-container>
            <v-row class="justify-center text-center">
                <v-col v-if="user">
                    <h3 class="text-center mt-12"> {{`سلام ${user.name}`}}</h3>
                    <h3 class="green--text text-center mt-12">نقش کاربر: {{user.role}}</h3>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script>
    import {mapActions} from "vuex";

    export default {
        data() {
            return {}
        },
        computed: {
            user() {
                return this.$store.state.authModule.user
            },
            jwt() {
                return this.$store.state.authModule.currentJwt
            }
        },
        methods: {
            ...mapActions([
                'refresh'
            ]),
        },
        mounted() {
            if (this.$store.getters.jwtData) {
                if (Date.now() > this.$store.getters.tokenRefreshTime) {
                    this.$store.dispatch('refresh')
                }
            }
        }
    }
</script>

<style scoped>

</style>
