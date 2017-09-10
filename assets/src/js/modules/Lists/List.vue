<template>
    <div v-if="!isLoaded">
        <h1> List:
            <span>{{ list.name }}</span>
        </h1>
        <pre>{{ list }}</pre>
    </div>
</template>

<script>
    export default {
        routeName: 'list',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...weMail.Vuex.mapState('list', ['list'])
        }
    };
</script>
