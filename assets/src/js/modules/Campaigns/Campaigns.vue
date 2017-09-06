<template>
    <div>
        <p>Campaigns component</p>
        <p>{{ hello }}</p>
        <p>{{ world }}</p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                hello: 'hello this is es6',
                world: 'world'
            };
        }
    };
</script>
