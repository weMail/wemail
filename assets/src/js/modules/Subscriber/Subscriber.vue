<template>
    <div v-if="isLoaded">
        <h1> Subscriber:
            <span v-if="subscriber.first_name">{{ subscriber.first_name }} {{ subscriber.last_name }}</span>
            <span v-else>{{ subscriber.email }}</span>
        </h1>
        <pre>{{ subscriber }}</pre>
    </div>
</template>

<script>
    export default {
        routeName: 'subscriber',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...weMail.Vuex.mapState('subscriber', ['subscriber'])
        }
    };
</script>
