<template>
    <div v-if="isLoaded">
        <h1> List:
            <span>{{ list.name }}</span>
        </h1>
        <pre>{{ list }}</pre>
    </div>
</template>

<script>
    export default {
        routeName: 'listsShow',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('listsShow', ['list'])
        }
    };
</script>
