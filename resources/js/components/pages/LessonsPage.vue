<template>
    <v-app>
        <v-card-title>
            <h2>Уроки</h2>
            <Dialog :items="items" :CRUD="CRUD" v-model="newItem" :valid="valid" ref="dialog"
                    class="ml-7"
                    @refresh="fetch" @redirect="redirect" @create="checkRoomName">
                <template v-if="!quizzes[0]">
                    <div class="h4 text-center">{{ $t('Перед тим, як розпочати урок, відмітьте кілька квізів як "улюблені". Тоді ви зможете обрати з-поміж них потрібний для проведення уроку') }}</div>
                    <div class="col text-center">
                        <v-btn href="/quizzes" class="align-center">{{ $t('Перейти на сторінку квізів') }}</v-btn>
                    </div>
                </template>
                <b-tabs align="center" v-model="dialogTab" @input="checkValidity" v-else>
                    <b-tab title="Швидка гра" @click="newItem.group_id = null">
                        <v-row class="mt-2">
                            <v-text-field
                                label="Назва кімнати"
                                :rules="[
                                    value => !!value || this.$t('Не може бути пустим'),
                                    value => (value && value.length >= 3) || this.$t('Введіть принаймні 3 символи'),
                                  ]"
                                v-model="newItem.code"
                                @input="checkValidity"
                            ></v-text-field>
                        </v-row>
                        <v-row>
                            <v-autocomplete
                                v-model="subject_id"
                                :items="subjects"
                                item-text="title"
                                item-value="id"
                                :label="this.$t('Предмет')"
                                @change="updateQuizzes"
                            ></v-autocomplete>
                        </v-row>
                        <v-row>
                            <v-autocomplete
                                v-model="newItem.quiz_id"
                                :items="quizzes"
                                item-text="title"
                                item-value="id"
                                :label="this.$t('Квіз')"
                                :rules="quizRules"
                                @change="checkValidity"
                            ></v-autocomplete>
                        </v-row>
                    </b-tab>
                    <b-tab :title="this.$t('Гра з оцінками')" :disabled="!is_premium_user">
                        <v-row class="mt-2">
                            <v-text-field
                                :label="this.$t('Назва кімнати')"
                                :rules="[
                                    value => !!value || this.$t('Не може бути пустим'),
                                    value => (value && value.length >= 3) || this.$t('Введіть принаймні 3 символи'),
                                  ]"
                                v-model="newItem.code"
                                @input="checkValidity"
                            ></v-text-field>
                        </v-row>
                        <v-row>
                            <v-autocomplete
                                v-model="newItem.group_id"
                                :items="groups"
                                item-text="title"
                                item-value="id"
                                :label="this.$t('Клас')"
                                @input="checkValidity"
                            ></v-autocomplete>
                        </v-row>
                        <v-row>
                            <v-autocomplete
                                v-model="subject_id"
                                :items="subjects"
                                item-text="title"
                                item-value="id"
                                :label="this.$t('Предмет')"
                                @change="updateQuizzes"
                            ></v-autocomplete>
                        </v-row>
                        <v-row>
                            <v-autocomplete
                                v-model="newItem.quiz_id"
                                :items="quizzes"
                                item-text="title"
                                item-value="id"
                                :label="this.$t('Квіз')"
                                :rules="quizRules"
                                @change="checkValidity"
                            ></v-autocomplete>
                        </v-row>
                    </b-tab>
                </b-tabs>
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
        <v-data-table :headers="headers"
                      :items="items"
                      :loading="loading"
                      class="elevation-1">

            <template #item.actions="{ item }">
                <!--<v-icon small class="mr-2" @click="$refs.dialog.editItem(item)">mdi-pencil</v-icon>-->
                <v-btn-toggle>
                    <v-btn @click="$refs.dialog.deleteItem(item)">
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                    <v-btn :href="`/lessons/${item.id}`">
                        <v-icon>mdi-arrow-right-thick</v-icon>
                    </v-btn>
                </v-btn-toggle>
            </template>
        </v-data-table>
    </v-app>
</template>

<script>

import DataTableCore from "../../mixins/DataTableCore";
import Dialog from "../../components/modals/AddDialog";
export default {
    mixins: [
        DataTableCore
    ],
    components: {
        Dialog
    },
    data() {
        return {
            CRUD: "/api/lessons",
            headers: [
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('Назва'), value: 'code' },
                { text: this.$t('Квіз'), value: 'quiz_title', sortable: false },
                { text: this.$t('Дата'), value: 'date' },
                { text: this.$t('Клас'), value: 'group' },
                {value: "actions", sortable: false, width: "15%"}
            ],
            valid: false,
            subjects: [],
            quizzes: [],
            groups: [],
            subject_id: 1,
            dialogTab: 1,
            defaultItem: {
                quiz_id: null,
                group_id: null,
                code: ''
            },
            newItem : {
                quiz_id: null,
                group_id: null,
                code: ''
            },
            quizRules : [
                function (val) {
                    if (val === null)
                        return this.$t('Оберіть квіз!')
                    return true
                }
            ]
        }
    },
    props: {
        username: "",
        is_premium_user: {
            default: true
        },
    },
    mounted() {
        this.updateSubjects()
        this.updateGroups()
        this.resetItem()
    },
    methods: {
        resetItem() {
            this.newItem = this.defaultItem;
            this.newItem.quiz_id = null;
            this.newItem.group_id = null;
            this.newItem.code = `${this.$t('Кімната вчителя')} ${this.username}`
        },
        updateSubjects() {
            axios.get("/api/subjects")
                .then(response => {
                    this.subjects = response.data;
                    this.updateQuizzes()
                })
        },
        updateQuizzes() {
            axios.get(`/api/quizzes/${this.subject_id}`)
                .then(response => {
                    this.quizzes = response.data;
                })
        },
        updateGroups() {
            axios.get("/api/groups/list")
                .then(response => {
                    this.groups = response.data;
                    console.log(response.data)
                })
        },
        redirect (id) {
            window.location.href = `/lessons/${id}?new`
        },
        checkValidity() {
            this.valid = this.newItem.code.length > 3 &&
                this.newItem.quiz_id !== null &&
                (this.dialogTab === 0 || this.newItem.group_id !== null)
        },
        checkRoomName() {
            this.items.forEach(o => {
                if(o.code === this.newItem.code) {
                    let number = /\d*$/.exec(this.newItem.code)
                    this.newItem.code = `${number[0] ? this.newItem.code.slice(0, -1 * number[0].length) : this.newItem.code+" "}${+number[0] + 1}`
                    this.checkRoomName()
                }
            })
        },
    },
};
</script>

<style>
.tabs [role=tablist] {
    padding-left: 0 !important;
}
</style>
