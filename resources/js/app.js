/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from "./store/index";
import vuetify from './plugins/vuetify' // path to vuetify export

/*=== InfiniteLoading Plugin ===*/
import InfiniteLoading from "vue-infinite-loading";

Vue.use(InfiniteLoading, {
    slots: {
        noResults: 'نتیجه دیگری یافت نشد',
        noMore: 'نتیجه دیگری یافت نشد',
    }
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('app', require('./App.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


export const eventBus = new Vue();

import route from './route/route'

Vue.mixin({
    methods: {
        route,
        objectIsEmpty(someObject) {
            return Object.keys(someObject).length === 0;
        },
        scrollToTop() {
            window.scrollTo(0, 0);
        }
    },
});


new Vue({
    router,
    vuetify,
    store,
    render: h => h(App)
}).$mount('#app');

