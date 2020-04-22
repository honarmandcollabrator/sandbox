<template>
    <v-navigation-drawer
        v-model="drawer"
        mobile-break-point="700"
        :expand-on-hover="$vuetify.breakpoint.smAndUp"
        app
        right
        clipped
    >
        <!--        :color="$vuetify.theme.dark ? '#303030' : '#fafafa'"-->

        <template v-if="isLoggedIn" #prepend>
            <v-list>
                <v-list-item
                    :to="{name: 'user', params: {id: me.details.id}}"
                >
                    <v-list-item-avatar>
                        <v-img :src="me.avatar || '/images/avatar.png'"></v-img>
                    </v-list-item-avatar>
                </v-list-item>

            </v-list>
        </template>

        <v-list>

            <v-list-item to="/" exact>
                <v-list-item-action>
                    <v-icon color="green darken-3">mdi-home</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>خانه</v-list-item-title>
                </v-list-item-content>
            </v-list-item>

            <v-list-item :to="{name: 'login'}">
                <v-list-item-action>
                    <v-icon style="transform: rotate(180deg);" color="green darken-3">mdi-login-variant</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>ورود</v-list-item-title>
                </v-list-item-content>
            </v-list-item>

            <v-list-group>
                <template v-slot:activator>
                    <v-list-item-action>
                        <v-icon color="warning accent-4">mdi-dots-horizontal-circle</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>
                            دارای زیرمنو
                        </v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item
                    v-for="(service, i) in services"
                    :key="i"
                    :to="service.route"
                >
                    <v-list-item-action>
                        <v-icon>{{ service.icon }}</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{ service.name }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
        </v-list>

    </v-navigation-drawer>
</template>

<script>
    import {LoadingMixin} from "../../Mixins/LoadingMixin";
    import {AuthMixin} from "../../Mixins/AuthMixin";


    export default {
        name: "TheNavigation",
        mixins: [AuthMixin, LoadingMixin],
        data() {
            return {
                drawer: null,
                services: [
                    {route: '/coming-soon', icon: 'mdi-dots-horizontal-circle', name: 'item 1'},
                    {route: '/coming-soon', icon: 'mdi-dots-horizontal-circle', name: 'item 2'},
                    {route: '/coming-soon', icon: 'mdi-dots-horizontal-circle', name: 'item 3'},
                ]
            }
        },
        computed: {
            navigationItems() {
                return [
                    {
                        color: 'blue accent-2',
                        icon: 'login-variant',
                        text: 'ورود',
                        route: 'login',
                        show: !this.isLoggedIn
                    },
                ]
            },
            showItems() {
                return this.navigationItems.filter(item => item.show)
            }
        },
        methods: {
            toggleDrawer() {
                this.drawer = !this.drawer
            },
        }
    }
</script>

<style scoped>

</style>
