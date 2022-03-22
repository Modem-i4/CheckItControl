<template>
    <v-app>
        <v-card-title>
            <h2>Класи</h2>
            <Dialog :items="items" :CRUD="CRUD" v-model="newItem" :valid="newItem.title.length >= 3" ref="dialog"
                    class="ml-7"
                    @refresh="fetch" @redirect="fetch">
                <v-row>
                    <v-text-field
                        :label="this.$t('Назва групи')"
                        :rules="groupRules"
                        v-model="newItem.title"
                    ></v-text-field>
                </v-row>
            </Dialog>
            <v-spacer></v-spacer>
            <v-text-field v-model="search"
                          append-icon="mdi-magnify"
                          label="Пошук"
                          single-line
                          hide-details
                          clearable
                          autocomplete="nope" />
        </v-card-title>
        <v-data-table :headers="headers"
                      :items="items"
                      :loading="loading"
                      class="elevation-1">
            <template #body="{ item }">
                <div class="card-group">
                    <GroupComponent v-for="(item, i) in items" :key="i"
                                    :item="item" @loading="(isLoading) => loading = isLoading"/>
                </div>
            </template>
        </v-data-table>
    </v-app>
</template>

<script>

import DataTableCore from "../../mixins/DataTableCore";
import GroupComponent from "../modals/GroupComponent";
import Dialog from "../modals/AddDialog";
export default {
    mixins: [DataTableCore],
    components: {GroupComponent, Dialog},
    data() {
        return {
            CRUD: "/api/groups",
            groupRules: [
                value => !!value || this.$t('Не може бути пустим'),
                value => (value && value.length >= 3) || this.$t('Введіть принаймні 3 символи'),
            ],
            studentRules: [
                value => !!value || this.$t('Не може бути пустим'),
                value => (value && value.length >= 7) || this.$t('Введіть принаймні 7 символів'),
            ],
            newItem: {
                title: ""
            }
        }
    },
};
</script>

<style>
    .card-group .v-text-field__slot input[disabled="disabled"] {
        color: #4b4b4b !important
    }
</style>
