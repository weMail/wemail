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
        routeName: 'listsSubscribers',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('listsSubscribers', ['list'])
        }
    };
</script>
