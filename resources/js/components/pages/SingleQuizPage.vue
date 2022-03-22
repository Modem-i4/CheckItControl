<template>
    <v-app>
        <v-card-title>
            <h2 class="text-center w-100">
                <span v-if="!editing">{{title}}</span>
                <v-text-field v-else v-model="title" class="title-field"/>
            </h2>
            <h5 class="text-center w-100">
                <span v-if="!editing">{{description}}</span>
                <v-textarea v-else v-model="description" counter="1000"
                            :rules="[v => v.length <= 1000 || this.$t('Не більше 1000 символів')]"/>
            </h5>
        </v-card-title>
        <div class="position-relative">
            <div class="position-absolute" style="right: 0;">
                <div v-if="editing">
                    <v-btn outlined color="error" @click="cancel"
                    >{{ $t('Скасувати') }}</v-btn>
                    <v-btn outlined color="success" @click="save"
                           :loading="savingLoading"
                           :disabled="description.length > 1000"
                    >Зберегти</v-btn>
                </div>
                <div v-else>
                    <v-btn outlined color="primary" @click="edit" v-if="editable"
                    ><v-icon>mdi-pencil</v-icon></v-btn>
                    <v-btn outlined color="warning" :loading="favorite.loading" @click="fav(favorite)">
                        <v-icon v-if="favorite.favorite">mdi-star</v-icon>
                        <v-icon v-else>mdi-star-outline</v-icon>
                    </v-btn>
                </div>
            </div>
            <b-tabs content-class="mt-3">
                <b-tab :title="this.$t('Запитання')" active>
                    <v-data-table :headers="headersQuestions"
                                  :items="questions"
                                  :loading="loading"
                                  class="elevation-1"
                                  item-key="key">
                        <template #item.question="{ item }">
                            <v-text-field v-model="item[0][0]" v-if="editing" @focus="addNewQuestionIfRequired(item)"/>
                            <span v-else>{{ item[0][0] }}</span>
                        </template>
                        <template #item.trueAnswer="{ item }">
                            <AnswerTooltip :sentence="item[0][0]" :word="item[1][0]">
                                <v-text-field v-model="item[1][0]" v-if="editing"/>
                                <span v-else>{{ item[1][0] }}</span>
                            </AnswerTooltip>
                        </template>
                        <template #item.falseAnswers="{ item }">
                            <div class="row text-center">
                                <div class="col-6">
                                    <AnswerTooltip :sentence="item[0][0]" :word="item[2][0]">
                                        <v-text-field v-model="item[2][0]" v-if="editing"/>
                                        <span v-else>{{ item[2][0] }}</span>
                                    </AnswerTooltip>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <AnswerTooltip :sentence="item[0][0]" :word="item[3][0]">
                                            <v-text-field v-model="item[3][0]" v-if="editing"/>
                                            <span v-else>{{ item[3][0] }}</span>
                                        </AnswerTooltip>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </v-data-table>
                </b-tab>
                <b-tab title="Дартс">
                    <v-data-table :headers="headersDarts"
                                  :items="darts"
                                  :loading="loading"
                                  class="elevation-1">
                        <template #item.question="{ item }">
                            <v-text-field v-model="item[0][0]" v-if="editing" @focus="addNewDartsIfRequired(item)"/>
                            <span v-else>{{ item[0][0] }}</span>
                        </template>
                        <template #item.trueAnswer="{ item }">
                            <div class="text-center">
                                <vue-numeric-input v-model="item[1][0]" align="center" v-if="editing"/>
                                <span v-else>{{ item[1][0] }}</span>
                            </div>
                        </template>
                    </v-data-table>
                </b-tab>
            </b-tabs>
        </div>
    </v-app>
</template>

<script>

import VueNumericInput from 'vue-numeric-input';
import AnswerTooltip from "../modals/AnswerTooltip";
import Favorites from "../../mixins/Favorites";
export default {
    mixins: [
        Favorites
    ],
    components: {
        AnswerTooltip,
        VueNumericInput
    },
    data() {
        return {
            favorite: {id: 0, favorite: false, loading: false},
            title: "",
            description: "",
            questions: [],
            darts: [],
            backup: {questions: [], darts: []},
            quiz_id: 0,
            editable: false,
            loading: true,
            editing: false,
            savingLoading: false,
            headersQuestions: [
                { text: this.$t("Запитання [поставте __ на місці пропуску слова]"), value: 'question', sortable: false, width: "50%" },
                { text: this.$t("Правильна відповідь"), value: 'trueAnswer',sortable: false, width: "20%"  },
                { text: this.$t("Хибні відповіді"), value: 'falseAnswers', sortable: false, width: "30%"  }
            ],
            headersDarts: [
                { text: this.$t("Запитання"), value: 'question', sortable: false, width: "60%" },
                { text: this.$t("Правильна відповідь"), value: 'trueAnswer',sortable: false, width: "40%"  },
            ],
        }
    },
    mounted() {
        this.quiz_id = window.location.href.split("/").pop()
        this.favorite.id = this.quiz_id
        this.getQuiz()
    },
    methods: {
        getQuiz() {
            axios.get(`/api/quiz/${this.quiz_id}`)
                .then(response => {
                    console.log(response.data)
                    this.title = response.data.title;
                    this.description = response.data.description;
                    this.editable = response.data.editable;
                    this.questions = response.data.quizJSON ?? [];
                    this.darts = response.data.dartsJSON ?? [];
                    this.favorite.favorite = response.data.favorite;
                    this.loading = false
                })
        },

        edit() {
            this.backup.questions = this.questions.slice()
            this.backup.darts = this.darts.slice()
            this.questions.push([[""],[""],[""],[""]])
            this.darts.push([[""],[0],[0],[0]])
            this.editing = true
        },
        cancel() {
            this.questions = this.backup.questions
            this.darts = this.backup.darts
            this.editing = false
        },
        save() {
            this.savingLoading = true
            this.questions = this.questions.filter(el => el[0][0] !== "")
            this.darts = this.darts.filter(el => el[0][0] !== "")
            axios.put(`/api/quiz/${this.quiz_id}`, {
                title: this.title,
                description: this.description,
                questions: this.questions,
                darts: this.darts
            })
                .then(response => {
                    this.savingLoading = false
                    this.editing = false
                })
        },
        addNewQuestionIfRequired(item) {
            if(this.questions[this.questions.length-1] === item) {
                this.questions.push([[""],[""],[""],[""]])
            }
        },
        addNewDartsIfRequired(item) {
            if(this.darts[this.darts.length-1] === item) {
                this.darts.push([[""],[0]])
            }
        },
    },
};
</script>

<style>
.title-field input {
    text-align: center;
    font-size: xx-large;
}
</style>
