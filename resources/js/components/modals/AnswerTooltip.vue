<template>
    <v-tooltip top>
        <template #activator="{ on, attrs }">
            <div class="text-center" v-bind="attrs" v-on="on">
                <slot></slot>
            </div>
        </template>
        {{sentence.replace("__", word)}}
    </v-tooltip>
</template>

<script>
export default {
    props: {
        sentence: "",
        word: ""
    },
};
</script>

