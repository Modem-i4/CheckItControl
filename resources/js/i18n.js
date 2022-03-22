import en from './lang/en.json';
import uk from './lang/uk.json';
import VueI18n from "vue-i18n";
import Vue from "vue";

Vue.use(VueI18n);

export default new VueI18n({
    locale: localStorage.getItem('lang') || 'uk',
    messages : {
        en: en,
        uk: uk
    }
});
