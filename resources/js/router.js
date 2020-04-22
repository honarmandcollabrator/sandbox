import Vue from 'vue'
import Router from 'vue-router'
import Login from "./components/Auth/Login";
import Register from "./components/Auth/Register";
import store from "./store/index"
import UserShowSection from "./components/User/UserShowSection";
import Verify from "./components/Auth/Verify";
import PasswordReset from "./components/Auth/PasswordReset";
import ComingSoon from "./components/ComingSoon";
import HomePage from "./components/HomePage";

Vue.use(Router);

const router = new Router({
    scrollBehavior(to, from, savedPosition) {
        return {x: 0, y: 0}
    },
    mode: 'history',
    // base: `hse`,
    routes: [
        {
            path: '',
            name: 'home-page',
            component: HomePage,
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        },
        {
            path: '/user/:id',
            name: 'user',
            component: UserShowSection,
        },
        {
            path: '/verify',
            name: 'verify',
            component: Verify,
        },
        {
            path: '/email/verify',
            name: 'automatic.verify',
            component: Verify,
        },
        {
            path: '/password-reset/:token/:email',
            name: 'password.reset',
            component: PasswordReset,
        },
        {
            path: '/coming-soon',
            name: 'coming-soon',
            component: ComingSoon,
        },
    ]
});


router.beforeEach((to, from, next) => {

    const loggedIn = store.state.authModule.status.loggedIn;
    const verified = store.state.authModule.status.verified;

    const allAccessRoutes = ['home-page'];
    const guestOnlyRoutes = ['login', 'register', 'password.reset'];
    const verifyRoutes = ['verify', 'automatic.verify'];

    /*=== We check if user dashboard is open or not, Then we decide to give him the requested route or not. ===*/

    if (allAccessRoutes.includes(to.name)) {
        next();
    } else if (!loggedIn) {
        if (guestOnlyRoutes.includes(to.name)) {
            next()
        } else {
            next({name: 'login'})
        }
    } else if (loggedIn && !verified) {
        if (verifyRoutes.includes(to.name)) {
            next()
        } else {
            next({name: 'verify'})
        }
    } else {
        if (guestOnlyRoutes.includes(to.name) || verifyRoutes.includes(to.name)) {
            next({name: 'home-page'})
        } else {
            next()
        }
    }
});

export default router;
