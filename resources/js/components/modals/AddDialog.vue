
<template>
    <v-toolbar flat>
        <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ on, attrs }">
                <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on" @click="$emit('create')">
                    {{ $t('створити') }}
                </v-btn>
            </template>
            <v-card>
                <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                    <v-container>
                        <slot></slot>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">{{ $t('Скасувати') }}</v-btn>
                    <v-btn color="blue darken-1" text @click="save" :disabled="!valid">{{ $t('Зберегти') }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
                <v-card-title class="headline">{{ $t('Ви певні, що хочете видалити?') }}</v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="closeDelete">{{ $t('Скасувати') }}</v-btn>
                    <v-btn color="blue darken-1" text @click="deleteItemConfirm">{{ $t('Видалити') }}</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-toolbar>
</template>

<script>
export default {
    model: {
        prop: 'newItem',
        event: 'newItemChange'
    },
    data: () => ({
        dialog: false,
        dialogDelete: false,
        editedIndex: -1,
        defaultItem: {
            quiz_id: -1,
            players_amount: 10,
            code: ''
        },
    }),

    props: {
        items: Array,
        CRUD: "",
        newItem: {},
        valid: false
    },

    computed: {
        formTitle() {
            return this.editedIndex === -1 ?  this.$t('Додати') : this.$t('Змінити')
        },

        editedItem: {
            get: function () {
                return this.newItem
            },
            set: function (value) {
                this.$emit('newItemChange', value)
            }
        }
    },

    watch: {
        dialog(val) {
            val || this.close()
        },
        dialogDelete(val) {
            val || this.closeDelete()
        },
    },


    methods: {

        editItem(item) {
            this.editedIndex = this.items.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem(item) {
            this.editedIndex = this.items.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        deleteItemConfirm() {
            $.post(`${this.CRUD}/remove`, {
                '_token': $('meta[name=csrf-token]').attr('content'),
                'id': this.editedItem.id},
                (id) => this.$emit('refresh')
            )
            this.items.splice(this.editedIndex, 1)
            this.closeDelete()
        },

        close() {
            this.dialog = false
            /*
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })*/
        },

        closeDelete() {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },

        save() {
            if (this.editedIndex > -1) {
                Object.assign(this.items[this.editedIndex], this.editedItem)
                axios.post(this.CRUD + "edit/", {
                    '_token': $('meta[name=csrf-token]').attr('content'),
                    'data': this.editedItem})
            } else {
                $.post(this.CRUD,  {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        'data': this.editedItem},
                    (id) => {
                        this.$emit('redirect', id)
                        console.log(id)
                    }
                )
                //this.items.push(this.editedItem)
            }
            this.close()
        },
    },
}
</script>
