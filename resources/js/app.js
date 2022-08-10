require('./bootstrap');

window.Vue = require('vue');
import Vuetify from 'vuetify';
import { VApp } from "vuetify/lib";
import '@mdi/font/css/materialdesignicons.css'
import i18n from "./i18n";
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import BootstrapVue from 'bootstrap-vue'

Vue.component('dashboard-page', require('./components/pages/DashboardPage').default);
Vue.component('quizzes-page', require('./components/pages/QuizzesPage').default);
Vue.component('lessons-page', require('./components/pages/LessonsPage').default);
Vue.component('single-lesson-page', require('./components/pages/SingleLessonPage').default);

Vue.component('schools-page', require('./components/pages/SchoolsPage').default);
Vue.component('single-school', require('./components/pages/SingleSchoolPage').default);
Vue.component('single-quiz', require('./components/pages/SingleQuizPage').default);
Vue.component('groups-page', require('./components/pages/GroupsPage').default);

Vue.component('lang-changer', require('./components/LangChanger').default);

Vue.use(Vuetify);
Vue.use(BootstrapVue);
//Vue.prototype.$userId = document.querySelector("meta[name='user-id']").getAttribute('content');

new Vue({
    i18n,
    vuetify : new Vuetify(),
    iconfont: 'mdi',
    components: {
        VApp
    },
}).$mount('#app');

