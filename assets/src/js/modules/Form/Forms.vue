<template>
    <div>
        <do-action action="before-forms"></do-action>
        Forms component
        <ul class="list-inline">
            <li class="list-inline-item">
                <router-link :to="{path: '/forms/1'}">Form 1</router-link>
            </li>
            <li class="list-inline-item">
                <router-link :to="{path: '/forms/2'}">Form 2</router-link>
            </li>
        </ul>

        <do-action action="after-forms"></do-action>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                parentData: 'data from parent'
            };
        }
    };
</script>
