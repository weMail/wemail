<div class="wrap">
    <div id="wemail-app" v-cloak>
        <router-view></router-view>
        <div id="wemail-route-loading" v-if="showLoadingAnime"><span>Loading</span></div>
        <!-- <pre>{{ $data }}</pre> -->
    </div>
</div>
