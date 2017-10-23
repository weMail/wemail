<template>
    <div>
        <p>Overview component</p>

        <!-- Button trigger modal -->
        <button type="button" class="button button-primary" data-toggle="modal" data-target="#exampleModal">
          Launch demo modal
        </button>

        <!-- Modal -->
        <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Modal title</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                ...
              </div>
              <div class="modal-footer">
                <button type="button" class="button button-secondary" data-dismiss="modal">Close</button>
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
