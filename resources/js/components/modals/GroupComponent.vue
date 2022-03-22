<template>
    <v-card class="col-md-6">
        <v-card-title>
            {{ item.title }}
            <v-icon small class="ml-2" @click="edit" v-if="!editing">mdi-pencil</v-icon>
            <div v-else>
                <v-icon small class="ml-2" @click="save">mdi-content-save</v-icon>
                <v-icon small class="ml-2" @click="cancel">mdi-cancel</v-icon>
            </div>
        </v-card-title>
        <v-card-subtitle>
            <ul>
                <li v-for="(student, i) in item.students" :key="i">
                    <v-text-field v-model="item.students[i]" dense :rules="studentRules" :disabled="!editing"
                                  append-icon="mdi-close" class="text-dark"
                    @click:append="clearStudent(i)"
                    @input="addNewItemIfRequired(i)"/>
                </li>
                <div v-if="limitReached" class="text-danger text-center">
                    {{ $t('Ви досягли ліміту учнів для одного класу! Створіть ще одну підргупу, щоб додати більше учнів') }}
                </div>
            </ul>
        </v-card-subtitle>
    </v-card>
</template>

<script>

export default {
    data() {
        return {
            CRUD: "/api/groups/",
            studentRules: [
                value => (value.length < 1 || value.length >= 7) || this.$t('Введіть принаймні 7 символів'),
            ],
            editing: false,
            backup: {}
        }
    },
    props: {
        item: {},
        loading: Boolean,
        limitReached: false
    },
    methods: {
        edit() {
            this.item.students = this.item.students ?? [];
            this.editing = true
            this.backup = [...this.item.students]
            if(this.item.students.length >= 16) {
                this.limitReached = true;
            }
            else {
                this.item.students.push("")
            }
        },
        save() {
            this.editing = false
            this.$emit('loading', true)
            this.item.students = this.item.students.filter((el) => el !== "")
            axios.patch(`${this.CRUD}${this.item.id}`, {'students': this.item.students})
                .then(response => {
                    this.$emit('loading', false)
                })
        },
        cancel() {
            console.log(this.backup)
            this.editing = false
            this.item.students = [...this.backup]
            console.log(this.item)
        },
        addNewItemIfRequired($i) {
            if(this.item.students.length-1 === $i) {
                if(this.item.students.length >= 16) {
                    this.limitReached = true;
                }
                else {
                    this.item.students.push("")
                }
            }
        },
        clearStudent($i) {
            if(this.item.students.length-1 !== $i) {
                this.item.students.splice($i, 1)
                this.limitReached = false
            }
        }
    }
};
</script>

