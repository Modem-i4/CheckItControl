<template>
    <v-app>
        <b-modal id="absent-students-modal" :title="this.$t('Відмітьте відсутніх учнів')" size="lg"
                 no-close-on-backdrop no-close-on-esc hide-header-close ok-only
                @ok="okStudentModal">
            <v-data-table :headers="studentsModalHeaders"
                          :items="group"
                          class="elevation-1"
            :items-per-page="16"
            hide-default-footer>
                <template #item.name="{ item }">
                    {{item[0][0]}}
                </template>
                <template #item.status="{ item }">
                    <v-checkbox v-model="item[1][0]"/>
                </template>
            </v-data-table>
        </b-modal>
        <v-card-title>
            <h2 class="text-center w-100">{{ $t('Урок') }}</h2>
        </v-card-title>
        <div class="text-center">
            <div class="row h2">
                <div class="col-4">
                    {{ $t('Кімната') }}: <br/>{{lesson.code}}
                </div>
                <div class="col-4">
                    Гравців: {{playersNow}}/{{maxPlayers}}
                    <br>
                    <template v-if="remainedTime">{{ $t('Залишилося часу') }}: {{remainedTime}}</template>
                </div>
                <div class="col-4">
                    <v-btn v-if="canRestartGame" @click="restartGame">{{ $t('Перезапустити гру') }}</v-btn>
                    <v-btn @click="forceStart" :disabled="!this.canForceStart" v-else
                    >{{ $t('Розпочати раунд') }}<template v-if="lesson.students"><br/>{{ $t('завчасно') }}</template></v-btn>
                </div>
            </div>
            <div class="row h4">
                <div class="col">
                    {{lesson.quiz_title}}
                    <br>
                    {{lesson.group_title}}
                </div>
            </div>
            <div class="row h4">
                <div class="col">
                    {{status}}
                </div>
            </div>
        </div>
        <b-tabs content-class="mt-3">
            <b-tab :title="this.$t('Запитання')" active>
                <v-data-table :headers="quizHeaders"
                              :items="items"
                              :loading="loading"
                              :items-per-page="-1"
                              v-if="items"
                              class="elevation-1">
                    <template #item.question="{ item }">
                        {{item[0][0]}}
                    </template>
                    <template #item.trueAnswer="{ item }">
                        <div class="text-center">
                            <div class="text-success" v-if="item.stats && item.stats[0][0]"><v-icon color="green">mdi-thumb-up</v-icon>
                                {{item.stats[0][0]}}</div> <!-- added -->
                            <div v-else><v-icon/></div>
                            <div>{{item[1][0]}}</div>
                            <div class="text-danger" v-if="item.stats && item.stats[0][1]"><v-icon color="red">mdi-delete</v-icon>
                                {{item.stats[0][1]}}</div> <!-- torn -->
                            <div v-else><v-icon/></div>
                        </div>
                    </template>
                    <template #item.falseAnswers="{ item }">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-success" v-if="item.stats && item.stats[1][1]"><v-icon color="green">mdi-delete</v-icon>
                                    {{item.stats[1][1]}}</div> <!-- added -->
                                <div v-else><v-icon/></div>
                                <div>{{item[2][0]}}</div>
                                <div class="text-danger" v-if="item.stats && item.stats[1][0]"><v-icon color="red">mdi-thumb-up</v-icon>
                                    {{item.stats[1][0]}}</div> <!-- torn -->
                                <div v-else><v-icon/></div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="text-success" v-if="item.stats && item.stats[2][1]"><v-icon color="green">mdi-delete</v-icon>
                                        {{item.stats[2][1]}}</div> <!-- added -->
                                    <div v-else><v-icon/></div>
                                    <div>{{item[3][0]}}</div>
                                    <div class="text-danger" v-if="item.stats && item.stats[2][0]"><v-icon color="red">mdi-thumb-up</v-icon>
                                        {{item.stats[2][0]}}</div> <!-- torn -->
                                    <div v-else><v-icon/></div>
                                </div>
                            </div>
                        </div>
                    </template>
                </v-data-table>
            </b-tab>
            <b-tab :title="this.$t('Дартс')">
                <v-data-table :headers="dartsHeaders"
                              :items="darts"
                              :loading="loading"
                              :items-per-page="-1"
                              class="elevation-1">
                    <template #item.question="{ item }">
                        {{item[0][0]}}
                    </template>
                    <template #item.answer="{ item }">
                        <div class="text-center">
                            <div>{{item[1][0]}}</div>
                        </div>
                    </template>
                    <template #item.trueAnswer="{ item }">
                        <div class="text-center">
                            <div class="text-success" v-if="item.stats && item.stats[0][0]"><v-icon color="green">mdi-thumb-up</v-icon>
                                {{item.stats[0][0]}}</div>
                            <div v-if="item.stats && !item.stats[0][0]"><v-icon/></div>
                        </div>
                    </template>
                    <template #item.confidence30Answers="{ item }">
                        <div class="text-center">
                            <div class="text-danger" v-if="item.stats && item.stats[1][0]"><v-icon color="red">mdi-thumb-down</v-icon>
                                {{item.stats[1][0]}}</div>
                            <div v-if="item.stats && !item.stats[1][0]"><v-icon/></div>
                        </div>
                    </template>
                    <template #item.falseAnswers="{ item }">
                        <div class="text-center">
                            <div class="text-danger" v-if="item.stats && item.stats[2][0]"><v-icon color="red">mdi-thumb-down</v-icon>
                                {{item.stats[2][0]}}</div>
                            <div v-if="item.stats && !item.stats[2][0]"><v-icon/></div>
                        </div>
                    </template>
                </v-data-table>
            </b-tab>
            <b-tab :title="this.$t('Учні')" :disabled="!this.lesson.group_id">
                <template v-if="!this.StudentsStats">
                    <v-data-table :headers="studentsTabHeaders"
                                  :items="group"
                                  :loading="loading"
                                  class="elevation-1"
                                  :items-per-page="16"
                                  hide-default-footer>
                        <template #item.name="{ item }">
                            {{item[0][0]}}
                        </template>
                        <template #item.status="{ item }">
                            <template v-if="item[1][0] === -2">
                                {{ $t('Покинув гру') }}
                            </template>
                            <template v-else-if="item[1][0] === -1">
                                {{ $t('Відсутній') }}
                            </template>
                            <template v-else-if="item[1][0] > 0">
                                {{ $t('У грі') }}
                            </template>
                            <template v-else>
                                {{ $t('Очікується') }}
                            </template>
                        </template>
                    </v-data-table>
                </template>
                <template v-else>
                    <v-data-table :headers="studentsRatingHeaders"
                                  :items="StudentsStats"
                                  :loading="loading"
                                  class="elevation-1"
                                  :items-per-page="16"
                                  hide-default-footer>
                        <template #item.validity="{ value }">
                            {{ Math.round(value*100) }}%
                        </template>
                        <template #item.activity="{ value }">
                            {{Math.round(value*100) }}%
                        </template>
                    </v-data-table>
                </template>
            </b-tab>
        </b-tabs>
    </v-app>
</template>

<script>

import DataTableCore from "../../mixins/DataTableCore";
import PhotonServer from "../../mixins/PhotonServer";
export default {
    mixins: [
        PhotonServer
    ],
    data() {
        return {
            CRUD: "/api/lessons",
            lesson: {
                students: []
            },
            items: [],
            stats: [],
            darts: [],
            StudentsStats: null,
            maxActivity: 0,
            loading: true,
            quizHeaders: [
                { text: this.$t('Запитання'), value: 'question', sortable: false, width: "50%" },
                { text: this.$t('Правильна відповідь'), value: 'trueAnswer',sortable: false, width: "20%"  },
                { text: this.$t('Хибні відповіді'), value: 'falseAnswers', sortable: false, width: "30%"  }
            ],
            dartsHeaders: [
                { text: this.$t('Запитання'), value: 'question', sortable: false, width: "50%" },
                { text: this.$t('Відповідь'), value: 'answer',sortable: false, width: "12.5%"  },
                { text: this.$t('Правильно'), value: 'trueAnswers',sortable: false, width: "12.5%"  },
                { text: this.$t('В межах 30%'), value: 'confidence30Answers',sortable: false, width: "12.5%"  },
                { text: this.$t('Хибно'), value: 'falseAnswers',sortable: false, width: "12.5%"  },
            ],
            studentsTabHeaders: [
                { text: this.$t("Ім'я"), value: 'name', sortable: false },
                { text: this.$t('Статус'), value: 'status', sortable: false, width: "25%" },
            ],
            studentsModalHeaders: [
                { text: this.$t("Ім'я"), value: 'name', sortable: false },
                { text: this.$t('Відсутній'), value: 'status', sortable: false, width: "1%" },
            ],
            studentsRatingHeaders: [
                { text: this.$t("Ім'я"), value: 'name' },
                { text: this.$t('Правильно'), value: 'correct', width: "1%" },
                { text: this.$t('Хибно'), value: 'incorrect', width: "1%" },
                { text: this.$t('Правильність'), value: 'validity', width: "1%" },
                { text: this.$t('Активність'), value: 'activity', width: "1%" },
                { text: this.$t('Оцінка'), value: 'mark', width: "1%" },
            ],
        }
    },
    props: {
        id: Number
    },
    mounted() {
        this.getLesson()
    },
    methods: {
        getLesson() {
            this.loading = true
            axios.get(`${this.CRUD}/${this.id}`)
                .then(response => {
                    this.loading = false
                    this.lesson = response.data;
                    try {
                        this.getQuestions()
                    }
                    catch (e) {}
                    if(this.lesson.group_id) {
                        this.lesson.students.forEach(o => this.group.push([[o], [0]]))
                        console.log(this.group)
                        if(window.location.href.includes("?new")) {
                            this.$bvModal.show('absent-students-modal')
                        }
                    }
                    else {
                        this.initPhoton()
                    }
                })
        },
        getQuestions() {
            axios.get(`/api/quizzes/quiz/pure/${this.lesson.quiz_id}`)
                .then(response => {
                    this.items = response.data;
                    this.getDarts()
                })
        },
        getDarts() {
            axios.get(`/api/quizzes/darts/pure/${this.lesson.quiz_id}`)
                .then(response => {
                    this.darts = response.data;
                    this.getQuizStats()
                })
        },
        getQuizStats() {
            this.loading = true
            axios.get(`${this.CRUD}/quizStats/${this.id}`)
                .then(response => {
                    if(response.data.QuizStats == null) {
                        this.loading = false
                        return;
                    }
                    let quizStats = JSON.parse(response.data.QuizStats)
                    this.items.forEach((item, i) => {
                        item.stats = quizStats[i]
                    })
                    let dartsStats = JSON.parse(response.data.DartsStats)
                    this.darts.forEach((item, i) => {
                        item.stats = dartsStats[i]
                    })

                    if(response.data.StudentsStats) {
                        let StudentsStatsRaw = JSON.parse(response.data.StudentsStats)
                        console.log(response.data.StudentsStats)
                        console.log(StudentsStatsRaw)
                        let maxActivity = 0;
                        StudentsStatsRaw.forEach(o => {
                            maxActivity = Math.max(maxActivity, o[1]) // + o[2]
                        })
                        let studentsStats = []
                        StudentsStatsRaw.forEach(o => {
                            let stat = {}
                            stat.name = o[0]
                            stat.correct = o[1]
                            stat.incorrect = o[2]
                            stat.validity = o[1]/(o[1]+o[2])
                            stat.activity = o[1]/maxActivity
                            stat.mark = ((stat.validity*0.6+stat.activity*0.4)*10 + 2).toFixed(2)
                            studentsStats.push(stat)
                        })
                        this.StudentsStats = studentsStats
                    }
                    this.loading = false
                })
        },
        okStudentModal() {
            this.maxPlayers = 0
            this.group.forEach((o,i) => {
                this.maxPlayers += o[1][0] ? 0 : 1;
                this.group[i][1][0] = o[1][0] ? -1 : 0;
            })
            // if student "checked" (true) then -1, else 0
            this.initPhoton()
        }
    }
};
</script>

<style>
#absent-students-modal .modal-dialog {
    top: 6em
}
</style>

