<template>
    <div>
        <p>Overview component</p>

        <!-- Button trigger modal -->
        <button type="button" class="button button-primary" data-toggle="wemail-modal" data-target="#exampleModal">
          Launch demo modal
        </button>

        <!-- Modal -->
        <div class="wemail-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="wemail-modal-dialog" role="document">
            <div class="wemail-modal-content">
              <div class="wemail-modal-header">
                <h3 class="wemail-modal-title" id="exampleModalLabel">Modal title</h3>
                <button type="button" class="close" data-dismiss="wemail-modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="wemail-modal-body">
                ...
              </div>
              <div class="wemail-modal-footer">
                <button type="button" class="button button-secondary" data-dismiss="wemail-modal">Close</button>
                <button type="button" class="button button-primary">Save changes</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    export default {
        routeName: 'overview',

        mixins: weMail.getMixins('routeComponent')
    };
</script>
