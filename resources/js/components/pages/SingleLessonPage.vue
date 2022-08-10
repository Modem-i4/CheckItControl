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
                    {{item.name}}
                </template>
                <template #item.status="{ item }">
                    <v-checkbox v-model="item.status"
                                :true-value="-1"
                                :false-value="0"/>
                </template>
            </v-data-table>
        </b-modal>
        <v-card-title>
            <h2 class="text-center w-100">{{ $t('Урок') }}</h2>
        </v-card-title>
        <div class="text-center">
            <div class="row h2">
                <div class="col">
                    {{ $t('Кімната') }}: <br/>{{lesson.code}}
                </div>
                <template v-if="status !== Status.finished">
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
                </template>
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
                    {{statusString}}
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
                            <div class="text-warning" v-if="item.stats && item.stats[1][0]"><v-icon color="yellow">mdi-thumbs-up-down</v-icon>
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
                <v-data-table :headers="studentsHeaders"
                              :items="group"
                              :loading="loading"
                              class="elevation-1"
                              :items-per-page="-1"
                              hide-default-footer>
                    <template #item.status="{ item }">
                        <template v-if="item.status === -2">
                            {{ $t('Покинув гру') }}
                        </template>
                        <template v-else-if="item.status === -1">
                            {{ $t('Відсутній') }}
                        </template>
                        <template v-else-if="item.status > 0">
                            {{ status === Status.finished ? $t('Завершив') : $t('У грі') }}
                        </template>
                        <template v-else>
                            {{ $t('Очікується') }}
                        </template>
                    </template>
                    <template #item.validity="{ value }">
                        {{ Math.round(value*100) }}%
                    </template>
                    <template #item.activity="{ value }">
                        {{Math.round(value*100) }}%
                    </template>
                </v-data-table>
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
                { text: this.$t('Правильна відповідь'), value: 'trueAnswer',align: 'center',sortable: false, width: "20%"  },
                { text: this.$t('Хибні відповіді'), value: 'falseAnswers', align: 'center',sortable: false, width: "30%"  }
            ],
            dartsHeaders: [
                { text: this.$t('Запитання'), value: 'question', sortable: false, width: "50%" },
                { text: this.$t('Відповідь'), value: 'answer',align: 'center',sortable: false, width: "12.5%"  },
                { text: this.$t('Правильно'), value: 'trueAnswer',align: 'center',sortable: false, width: "12.5%"  },
                { text: this.$t('В межах 30%'), value: 'confidence30Answers',align: 'center',sortable: false, width: "12.5%"  },
                { text: this.$t('Хибно'), value: 'falseAnswers',align: 'center',sortable: false, width: "12.5%"  },
            ],
            studentsHeaders: [
                { text: this.$t("Ім'я"), value: 'name', sortable: false },
                { text: this.$t('Статус'), value: 'status', align: 'center',sortable: false, width: "20%" },
                { text: this.$t('Правильно'), value: 'correct', align: 'center', width: "1%" },
                { text: this.$t('Хибно'), value: 'incorrect', align: 'center', width: "1%" },
                { text: this.$t('Правильність'), value: 'validity', align: 'center', width: "1%" },
                { text: this.$t('Активність'), value: 'activity', align: 'center', width: "1%" },
                { text: this.$t('Оцінка'), value: 'mark', align: 'center', width: "1%" },
            ],
            studentsModalHeaders: [
                { text: this.$t("Ім'я"), value: 'name', sortable: false },
                { text: this.$t('Відсутній'), value: 'status', align: 'center',sortable: false, width: "1%" },
            ],
        }
    },
    props: {
        id: Number
    },
    mounted() {
        this.getLesson()
        window.onfocus = this.onFocus()
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
                    this.getLessonStatus()
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
                    //this.getQuizStats()
                })
        },
        okStudentModal() {
            this.setMaxPlayers()
            this.setStudentsStats()
            // if student "checked" (true) then -1, else 0
            this.initPhoton()
        },
        setMaxPlayers() {
            this.maxPlayers = 0
            this.group.forEach(o => {
                this.maxPlayers += o.status !== -1 ? 1 : 0;
            })
            console.log(this.group)
        },
        setStudentsStats() {
            let groupMinimized = []
            this.group.forEach(o => {groupMinimized.push([[o.name,0,0,o.status]])})
            axios.post(this.CRUD + `/setStudentsStats/${this.lesson.id}`, {
                '_token': $('meta[name=csrf-token]').attr('content'),
                'data': groupMinimized
            })
        },
        getLessonStatus() {
            axios.get(this.CRUD + `/lessonStatus/${this.lesson.id}`)
                .then(response => {
                    console.log(response)
                    this.applyStatus(response.data.status)
                })
        },
        getStudentStats() { // + LessonStatus
            axios.get(`${this.CRUD}/studentStats/${this.id}`)
                .then(response => {
                    this.interpretStudentStats(response.data.StudentsStats)
                    this.applyStatus(response.data.status)
                })
        },
        getQuizStats() { // + LessonStatus
            this.loading = true
            axios.get(this.CRUD + `/quizStats/${this.lesson.id}`)
                .then(response => {
                    this.interpretStudentStats(response.data.StudentsStats)
                    console.log(response.data.QuizStats)
                    let quizStats = response.data.QuizStats
                    this.items.forEach((item, i) => {
                        item.stats = quizStats[i]
                        console.log(item.stats[0][0])
                        console.log(item.stats[0][1])
                        console.log("-----------")
                        console.log(item.stats[1][0])
                        console.log(item.stats[1][1])
                        console.log("-----------")
                        console.log(item.stats[2][0])
                        console.log(item.stats[2][1])
                    })
                    let dartsStats = response.data.DartsStats
                    this.darts.forEach((item, i) => {
                        item.stats = dartsStats[i]
                    })
                    this.loading = false
                    this.applyStatus(response.data.status)
                })
        },
        interpretStudentStats(groupRaw) {
            if(groupRaw) {
                let maxActivity = 0;
                groupRaw.forEach(o => {
                    maxActivity = Math.max(maxActivity, o[0][1]) // + o[2]
                    console.log("ACTIVITY:")
                    console.log(o[1])
                    console.log(o[0][1])
                })
                console.log("MAX ACTIVITY:")
                console.log(maxActivity)
                let group = []
                groupRaw.forEach(o => {
                    o = o[0]
                    let stat = {}
                    stat.name = o[0]
                    stat.correct = o[1]
                    stat.incorrect = o[2]
                    stat.status = o[3]
                    stat.validity = o[1]/(o[1]+o[2]) || 0
                    stat.activity = o[1]/maxActivity || 0
                    stat.mark = ((stat.validity*0.6+stat.activity*0.4)*10 + 2).toFixed(2)
                    group.push(stat)
                })
                console.log("group")
                console.log(group)
                this.group = group
                this.setMaxPlayers()
            }
        },
        applyStatus(status) {
            if(this.status !== status) {
                this.status = status
                console.log(`STATUS: ${status}`)
                switch (this.status) {
                    case this.Status.ordered:
                        if (this.lesson.group_id) {
                            this.lesson.students.forEach(o => this.group.push({name: o, status: 0}))
                            this.$bvModal.show('absent-students-modal')
                        } else {
                            this.initPhoton()
                        }
                        break;
                    case this.Status.launched:
                        this.launchStatusChecker()
                        //if no room can be re-created
                        this.initPhoton()
                        break;
                    case this.Status.started:
                        this.launchStatusChecker()
                        //if no room must get finished
                        this.initPhoton()
                        if(this.timeStarted === null) {
                            this.canForceStart = false
                            this.statusString = "Гра триває!"
                            this.timeStarted = this.room.getCustomProperty("timeStarted") // in secs
                            let roundDuration = this.room.getCustomProperty("roundDuration") // in secs
                            let roundTimer = window.setInterval(() => {
                                let remainedTime = roundDuration-Math.round(Date.now()/1000 - this.timeStarted)
                                if(remainedTime < 0) {
                                    this.remainedTime = null
                                    clearInterval(roundTimer)
                                }
                                else {
                                    this.remainedTime = `${Math.floor(remainedTime/60)}:${String(remainedTime%60).padStart(2, '0')}`
                                }
                            }, 1000)
                        }
                        break;
                    case this.Status.finished:
                        this.statusString = "Гру завершено"
                        if(this.PhotonServer !== null)
                            this.PhotonServer.disconnect()
                        this.getQuizStats()
                        break;
                }
            }
        },
        setLessonStatus(status) {
            this.applyStatus(status)
            axios.post(this.CRUD + `/setLessonStatus/${this.lesson.id}`, {
                'status': status
            })
        },
        launchStatusChecker() {
            if(this.statusChecker === null) {
                this.statusChecker = window.setInterval(() => {
                    switch (this.status) {
                        case this.Status.launched:
                            this.getStudentStats()
                            break;
                        case this.Status.started:
                            this.getQuizStats()
                            break;
                        case this.Status.finished:
                            this.getQuizStats()
                            clearInterval(this.statusChecker)
                            break;
                        default:
                            this.getLessonStatus()
                    }
                }, 2000)
            }
        },
        onFocus() {
            console.log("Focused")
            if(this.PhotonServer !== null && !this.PhotonServer.isConnectedToMaster()) {
                this.PhotonServer.connectToNameServer({region: this.AppInfo.AppLocation});
            }
        }
    }
};
</script>

<style>
#absent-students-modal .modal-dialog {
    top: 6em
}
</style>

