import Vue from 'vue'
import Vuetify from 'vuetify'
import en from 'vuetify/es5/locale/en'
import fa from 'vuetify/es5/locale/fa'

Vue.use(Vuetify);

const opts = {
    rtl: true,
    lang: {
        locales: { en, fa},
        current: 'fa',
    },
};


export default new Vuetify(opts)
