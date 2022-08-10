<template>
    <a class="nav-link" @click="change">{{this.lang === 'en' ? 'UK' : 'EN'}}</a>
</template>
<script>
    export default {
        name: "LangChanger",
        data() {
            return {
                lang: localStorage.getItem('lang') || 'uk'
            }
        },
        methods: {
            change() {
                let newLang = this.lang === 'en' ? 'uk' : 'en'
                localStorage.setItem('lang', newLang);
                window.location.replace('/set_locale/'+newLang)
            }
        }
    }
</script>
<style>
    a {
        cursor: pointer;
    }
</style>
