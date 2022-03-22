<template>
    <v-app>
        <div class="card">
            <div class="card-header h3 alert-success">Вітаємо у системі!</div>
            <div class="card-body">
                <template v-if="is_premium">
                    <h4>Ваш аккаун проплачено!</h4>
                    <i>Скористайтеся вкладками вгорі</i>
                    <hr>
                    <h4>Навчальне відео для повної версії:</h4>
                    <iframe class="w-100" src="https://www.youtube.com/embed/h-_dES3yZc0" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="height:23em"></iframe>
                </template>
                <template v-else>
                    <h4>Ви використовуєте безкоштовну версію</h4>
                    <i>Для розблокування вкладок "школа", "групи" та "учні", а також для можливості перегляду оцінок придбайте підписку або зв'яжіться з розробниками </i>
                    <hr>
                    <h4>Маєте промокод? Активуйте свій кабінет!</h4>
                    <v-text-field
                        v-model="promocode"
                        id="promocode-field"
                        @input="error = '' "
                        counter="5"
                        label="Код активації"
                        :error-messages="error"
                        append-outer-icon="mdi-send"
                        @click:append-outer="sendPromocode">
                    </v-text-field>
                    <hr>
                    <h4>Навчальне відео для обмеженої версії:</h4>
                    <iframe class="w-100" src="https://www.youtube.com/embed/h-_dES3yZc0" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" style="height:23em"></iframe>
                </template>
            </div>
        </div>
    </v-app>
</template>

<script>

export default {
    data() {
        return {
                promocode: "",
                error: ""
            }
        },
    props: {
        is_premium: Boolean,
    },
    methods: {
        sendPromocode() {
            if(this.promocode.length < 5)
                this.error = "Промокод закороткий"
            else if(this.promocode.length > 5)
                this.error = "Промокод задовгий"
            else {
                this.error = false
                axios.post("api/checkCode", {code: this.promocode})
                    .then(response => location.reload())
                    .catch(e => {
                        this.error = e.response.data === "Wrong code" ? "Хибний код" :
                                    e.response.data === "Code is already used" ? "Код уже було активовано :(" :
                                    e.response.data === "Database error" ? "Помилка бази даних" :
                                    "Невідома помилка"
                    })
            }
        }
    }
};
</script>

<style>
#promocode-field {
    text-transform: uppercase;
}
.v-input__append-outer {
    margin: 0 !important;
    padding: 0 !important;;
}
</style>
