<template>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="clearfix">
                <h3 v-if="!finishedImporting" class="float-left">{{ sprintf('Importing %d MailChimp Subscribers', total) }}...</h3>
                <h3 v-else class="float-left text-success">{{ sprintf('Finished importing %d MailChimp Subscribers', total) }}...</h3>
            </div>
            <div class="progress">
                <div
                    :class="progressbarClass"
                    role="progressbar"
                    :style="{ width: `${percentageDone}%`}"
                    :aria-valuenow="percentageDone"
                    aria-valuemin="0"
                    aria-valuemax="100"
                >{{ percentageDone }}%</div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</template>

<script>
    export default {
        props: {
            settings: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                finishedImporting: false,
                total: 0,
                completed: 0,
                apiHandler: {
                    abort() {
                        //
                    }
                }
            };
        },

        computed: {
            percentageDone() {
                let done = 0;

                if (this.completed !== 0) {
                    done = ((this.completed / this.total) * 100).toFixed(2); // eslint-disable-line no-magic-numbers
                }

                if (done > 100) { // eslint-disable-line no-magic-numbers
                    done = 100; // eslint-disable-line no-magic-numbers
                }

                return done;
            },

            progressbarClass() {
                const classNames = ['progress-bar'];
                const done = parseInt(this.percentageDone, 10);

                if (done < 33) { // eslint-disable-line no-magic-numbers
                    classNames.push('bg-danger progress-bar-striped progress-bar-animated');
                } else if (done < 66) { // eslint-disable-line no-magic-numbers
                    classNames.push('bg-warning progress-bar-striped progress-bar-animated');
                } else if (done < 100) { // eslint-disable-line no-magic-numbers
                    classNames.push('bg-info progress-bar-striped progress-bar-animated');
                } else {
                    classNames.push('bg-success');
                }

                return classNames;
            }
        },

        created() {
            const vm = this;

            if (vm.settings.total) {
                vm.total = vm.settings.total;
                vm.completed = vm.settings.completed;

                return vm.import();
            }

            if (!vm.settings.mailchimp_list_id || !vm.settings.map.length) {
                return vm.$router.push({
                    name: 'importMailChimpSettings'
                });
            }

            vm.finishedImporting = false;

            vm.apiHandler = weMail.api.import().mailchimp().save(vm.settings).done((response) => {
                if (response.total && response.completed) {
                    vm.total = response.total;
                    vm.completed = response.completed;

                    vm.continue();
                }
            });

            return vm.apiHandler;
        },

        destroyed() {
            this.apiHandler.abort();
        },

        methods: {
            import() {
                const vm = this;

                vm.finishedImporting = false;

                vm.apiHandler = weMail.api.import().post().done((response) => {
                    if (response.total && response.completed) {
                        vm.total = response.total;
                        vm.completed = response.completed;

                        vm.continue();
                    }
                }).fail(() => {
                    vm.error(__('Something went wrong. Please try again.'));
                });
            },

            continue() {
                if (this.completed < this.total) {
                    this.import();

                } else if (this.completed >= this.total) {
                    this.settings.mailchimp_list_id = '';
                    this.settings.wemail_list_id = '';
                    this.settings.import_confirmed_only = false;
                    this.settings.import_mailchimp_list = true;
                    this.settings.overwite_existing_subscriber = true;
                    this.settings.life_stage = '';
                    this.settings.map = [];

                    this.finishedImporting = true;
                }
            }
        }
    };
</script>

<style lang="scss" scoped>
    h3 {
        margin-bottom: 3px;
    }

    .progress {
        height: 22px;
    }
</style>
