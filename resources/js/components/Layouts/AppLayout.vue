<template>
    <v-app>

        <the-navigation ref="sidebarNavigation"></the-navigation>

        <v-app-bar clipped-right app>
            <v-app-bar-nav-icon @click.stop="handleDrawer"></v-app-bar-nav-icon>

            <v-btn small @click="$router.go()" icon color="success">
                <v-icon>mdi-sync</v-icon>
            </v-btn>

            <v-btn small @click="$router.forward()" icon>
                <v-icon>mdi-arrow-right-bold-box</v-icon>
            </v-btn>

            <v-btn small @click="$router.back()" icon>
                <v-icon>mdi-arrow-left-bold-box</v-icon>
            </v-btn>


            <v-spacer></v-spacer>


            <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                    <v-btn v-on="on" icon @click="start_logout" small color="warning accent-2 white--text">
                        <v-icon style="transform: rotate(180deg);">mdi-logout-variant</v-icon>
                    </v-btn>
                </template>
                <span>خروج</span>
            </v-tooltip>


            <v-progress-linear
                :active="appLoading"
                indeterminate
                absolute
                bottom
                :color="$vuetify.theme.dark ? 'red' : 'primary'"
            ></v-progress-linear>

        </v-app-bar>
        <v-content>
            <v-container>

                <v-row>

                    <v-col cols="12">
                        <slot name="content"></slot>
                    </v-col>

                </v-row>


            </v-container>
        </v-content>
        <v-footer id="footer" class="font-weight-medium">
            <v-col class="text-center" cols="12">
                {{ new Date().getFullYear() }} — <strong>TOOTIKO</strong>
            </v-col>
        </v-footer>
    </v-app>
</template>

<script>
    import {mapActions, mapState} from "vuex";
    import TheNavigation from "./TheNavigation";
    import {AuthMixin} from "../../Mixins/AuthMixin";
    import router from "../../router";

    export default {
        name: 'AppLayout',
        mixins: [AuthMixin],
        components: {
            TheNavigation,
        },
        data: () => ({
            drawer: null,
        }),
        computed: {
            ...mapState(
                {
                    appLoading: state => state.appLoading,
                }
            ),
            baseUrl() {
                return window.location.origin;
            }
        },
        methods: {
            ...mapActions([
                "logout"
            ]),
            start_logout() {
                this.logout()
                    .then(() => {
                        return this.$router.push({name: 'login'})
                    });
            },
            handleDrawer() {
                this.$refs.sidebarNavigation.toggleDrawer()
            }
        },
        created() {
            // this.$vuetify.theme.dark = true;
            if (this.isSuperAdmin) {
            }
        },
    }
</script>
