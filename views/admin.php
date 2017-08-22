<div class="wrap">
    <div id="wemail-admin" class="wemail" v-cloak>
        <router-view></router-view>
        <div id="wemail-route-loading" v-if="showLoadingAnime">
            <span><?php _e( 'Loading', 'wemail' ); ?></span>
        </div>
    </div>
</div>
