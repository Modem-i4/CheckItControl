<template>
    <v-app>
        <v-card-title>
            <h2>{{ $t('Квізи') }}</h2>
            <Dialog :items="items" CRUD="/api/quiz" v-model="newItem" :valid="valid" ref="dialog"
                    class="ml-7"
                    @refresh="fetch" @redirect="redirect">
                <v-row>
                    <v-text-field
                        :label="this.$t('Назва квізу')"
                        :rules="[
                            value => !!value || this.$t('Не може бути пустим'),
                            value => (value && value.length >= 3) || this.$t('Введіть принаймні 3 символи'),
                          ]"
                        v-model="newItem.title"
                        @input="checkValidity"
                    ></v-text-field>
                </v-row>
                <v-row>
                    <v-text-field
                        :label="this.$t('Опис квізу')"
                        :rules="[
                            value => !!value || this.$t('Не може бути пустим'),
                            value => (value && value.length >= 15) || this.$t('Введіть принаймні 15 символів'),
                          ]"
                        v-model="newItem.description"
                        @input="checkValidity"
                    ></v-text-field>
                </v-row>
                <v-row>
                    <v-autocomplete
                        v-model="newItem.subject_id"
                        :items="subjects"
                        item-text="title"
                        item-value="id"
                        :label="this.$t('Предмет')"
                        @input="checkValidity"
                    ></v-autocomplete>
                </v-row>
            </Dialog>
            <v-spacer></v-spacer>
            <v-text-field v-model="search"
                          append-icon="mdi-magnify"
                          :label="this.$t('Пошук')"
                          single-line
                          hide-details
                          clearable
                          autocomplete="nope" />
        </v-card-title>

        <b-tabs pills>
            <b-tab :title="this.$t('Всі')" active @click="searches.subject_id = ''; fetch()"/>
            <b-tab v-for="subject in subjects" :title="subject.title" :key="subject.id" @click="searches.subject_id = subject.id; fetch()"/>
        </b-tabs>

        <b-tabs align="center">
            <b-tab :title="this.$t('Всі')" active @click="repositoryFilters.kind = ''; fetch()"/>
            <b-tab :title="this.$t('Від авторів')" @click="repositoryFilters.kind = 'authors'; fetch()"/>
            <b-tab :title="this.$t('Користувацькі')" @click="repositoryFilters.kind = 'users'; fetch()"/>
            <b-tab :title="this.$t('Моєї школи')" @click="repositoryFilters.kind = 'my_school'; fetch()"/>
            <b-tab :title="this.$t('Власні')" @click="repositoryFilters.kind = 'own'; fetch()"/>
            <b-tab :title="this.$t('Улюблені')" @click="repositoryFilters.kind = 'favorite'; fetch()"/>
        </b-tabs>

        <v-data-table :headers="headers"
                      :items="items"
                      :loading="loading"
                      class="elevation-1"
                      :item-class="`cursor-pointer`">
            <template #item.actions="{ item }">
                <v-btn outlined color="warning" :loading="item.loading" @click="fav(item)">
                    <v-icon v-if="item.favorite">mdi-star</v-icon>
                    <v-icon v-else>mdi-star-outline</v-icon>
                </v-btn>
                <v-btn outlined @click="openQuiz(item.id)" :color="item.author == user_id ? 'primary' : ''"> <!-- don't change -->
                    <v-icon>mdi-arrow-right-bold</v-icon>
                </v-btn>
            </template>
        </v-data-table>
    </v-app>
</template>

<script>

import DataTableCore from "../../mixins/DataTableCore";
import Dialog from "../../components/modals/AddDialog";
import Favorites from "../../mixins/Favorites";
export default {
    mixins: [
        DataTableCore,
        Favorites
    ],
    components: {
        Dialog
    },
    data() {
        return {
            CRUD: "/api/quizzes",
            valid: false,
            subjects: [],
            headers: [
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('Назва'), value: 'title' },
                { text: this.$t('Опис'), value: 'description', sortable: false },
                { text: this.$t('Використано разів'), value: 'total_lessons' },
                { value: 'actions' },
            ],
        }
    },
    mounted() {
        this.getSubjects()
        this.setNewItem()
    },
    props: {
        user_id: {
            default: 0
        },
    },
    methods: {
        setNewItem() {
            this.newItem = {
                title: "",
                description: "",
                subject_id: -1
            }
        },
        getSubjects() {
            axios.get("/api/subjects")
                .then(response => {
                    this.subjects = response.data
                })
        },
        checkValidity() {
            this.valid = this.newItem.title.length > 3 &&
                this.newItem.description.length > 15 &&
                this.newItem.subject_id > 0
        },
        redirect(id) {
            this.fav({id: id})
            this.openQuiz(id)
        },
        openQuiz(id) {
            window.location.href = `/quiz/${id}`
        }
    }
};
</script>

<style>
.cursor-pointer {
    cursor: pointer;
}
</style>
