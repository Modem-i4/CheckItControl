<template>
    <v-app>
        <v-card>
            <v-card-title>
                <h2 class="text-center w-50 mx-auto">{{ $t('Школа') }} {{school}}</h2>
                <v-btn class="position-absolute" style="right: 1.5em"
                @click="leaveSchool"
                >{{ $t('Покинути школу') }}  <v-icon>mdi-arrow-right</v-icon></v-btn>
            </v-card-title>
            <v-card-text class="d-inline-flex">
                <v-card class="col-6">
                    <v-card-title>
                        <h2 class="text-center w-100">{{ $t('Вчителі') }}</h2>
                    </v-card-title>
                    <v-card-text>
                        <v-card v-for="teacher in teachers" :key="teacher" class="h4 text-center">
                            {{teacher}}
                        </v-card>
                    </v-card-text>
                </v-card>
                <v-card class="col-6">
                    <v-card-title>
                        <h2 class="text-center w-100">{{ $t('Класи') }}</h2>
                    </v-card-title>
                    <v-card-text>
                        <v-card v-for="group in groups" :key="groups" class="h4 text-center">
                            {{group}}
                        </v-card>
                    </v-card-text>
                </v-card>
            </v-card-text>
        </v-card>
    </v-app>
</template>

<script>

export default {
    data() {
        return {
            school: "",
            teachers: [],
            groups: []
        }
    },
    props: {
        school_id: 0
    },
    mounted() {
        this.getSchool()
        this.getTeachers()
        this.getGroups()
    },
    methods: {
        getSchool() {
            axios.get(`/api/schools/${this.school_id}`)
                .then(response => {
                    this.school = response.data.title;
                })
        },
        getTeachers() {
            axios.get(`/api/users/school/${this.school_id}`)
                .then(response => {
                    this.teachers = response.data;
                })
        },
        getGroups() {
            axios.get(`/api/groups/school/${this.school_id}`)
                .then(response => {
                    this.groups = response.data;
                    console.log(this.groups)
                })
        },
        leaveSchool() {
            axios.post(`/api/users/set-school/null`)
                .then(response => {
                    location.reload()
                })
        }
    },
};
</script>

