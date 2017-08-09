<div class="wrap">
    <div id="wemail-app" v-cloak>
        <router-view></router-view>
        <pre>{{loadingRouteRequires}}</pre>
        <div id="wemail-route-loading" v-if="loadingRouteRequires"><span>Loading</span></div>
    </div>
</div>
