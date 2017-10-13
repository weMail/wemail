<template>
    <div v-if="isLoaded">
        <h1>{{ i18n.campaign }}: {{ campaign.name }}</h1>
        <p>
            <router-link :to="{name: 'campaignEditSetup', params: {id: $route.params.id}}">Edit</router-link>
        </p>
        <pre>{{ campaign }}</pre>
    </div>
</template>

<script>
    export default {
        routeName: 'campaignShow',

        mixins: weMail.getMixins('routeComponent'),

        computed: {
            ...Vuex.mapState('campaignShow', ['i18n', 'campaign'])
        }
    };
</script>
