<template>
    <tr>
        <td>
            <table class="content" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left">
                        <pre>socialFollow.vue</pre>
                    </td>
                </tr>
            </table><!-- .content -->

            <div class="control-buttons">
                <p>
                    <i class="fa fa-arrows move"></i>
                    <i class="fa fa-pencil"></i>
                    <i class="fa fa-clone"></i>
                    <i class="fa fa-trash-o"></i>
                </p>
            </div>

            <img class="content-type-image" :src="contentTypeImg" alt="">
        </td>
    </tr>
</template>

<script>
    export default {
        props: {
            contentTypeImg: {
                type: String,
                required: false
            }
        }
    };
</script>
