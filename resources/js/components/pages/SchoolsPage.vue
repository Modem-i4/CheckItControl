<template>
    <v-app>
        <v-card-title>
            <h2>Школи</h2>
            <Dialog :items="items" :CRUD="CRUD" v-model="newItem" :valid="newItem.title.length >= 10" ref="dialog"
                    class="ml-7"
                    @refresh="fetch" @redirect="setSchoolId">
                <v-row>
                    <v-text-field
                        :label="this.$t('Назва школи')"
                        :rules="rules"
                        v-model="newItem.title"
                    ></v-text-field>
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
        <v-data-table :headers="headers"
                      :items="items"
                      :loading="loading"
                      class="elevation-1">
            <template #item.teachers="{ value }">
                <div class="d-flex w-100">
                    <v-card v-for="teacher in value" :key="teacher" class="w-50 text-center">
                        {{teacher}}
                    </v-card>
                </div>
            </template>
            <template #item.actions="{ item }">
                <v-btn-toggle>
                    <v-btn @click="setSchoolId(item.id)">
                        {{ $t('Приєднатися') }} 
                        <v-icon>mdi-arrow-right</v-icon>
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
            CRUD: "/api/schools",
            valid: false,
            newItem : {
                title: ""
            },
            headers: [
                { text: this.$t('id'), value: 'id' },
                { text: this.$t('Назва'), value: 'title', width: '25%' },
                { text: this.$t('Вчителі'), value: 'teachers', sortable: false, width: '75%' },
                { value: 'actions', sortable: false },
            ],
            rules: [
                value => !!value || 'Не може бути пустим',
                value => (value && value.length >= 10) || 'Введіть принаймні 10 символів',
            ]
        }
    },
    methods: {
        setSchoolId(school_id) {
            this.loading = true
            axios.post(`/api/users/set-school/${school_id}`)
                .then(response => {
                    location.reload()
                })
        },
    }
};
</script>

