<template>
    <div>
        <div v-if="!showPreview">
            <h1>{{ i18n.cam }}</h1>

            <div class="edit-template-filters">
                <div class="clearfix">
                    <ul class="list-sub float-left">
                        <li class="active">
                            <a href="#">{{ i18n.templates }}</a>
                        </li>
                        <li>
                            <a href="#">{{ i18n.myTemplates }}</a>
                        </li>
                    </ul>

                    <multiselect
                        v-model="selectedCategory"
                        :options="templateCategories"
                        key="slug"
                        label="name"
                        :placeholder="i18n.chooseTemplateCategory"
                        class="float-left"
                        @select="onSelectCategory"
                    >
                        <span slot="noResult">{{ i18n.noTemplateFound }}</span>
                    </multiselect>

                    <input type="search" class="form-control float-right" :placeholder="i18n.searchTemplate">
                </div>
            </div>

            <div class="template-grid">
                <div v-if="isFetchingTemplate" class="template-is-loading text-center">
                    Loading...
                </div>

                <div v-else class="row">
                    <div v-for="template in templates" class="col-sm-2">
                        <div class="template-thumb">
                            <div class="thumbnail-container">
                                <img :src="templateImgUrlRoot + '/' + template.slug + '.png'" :alt="template.name">

                                <div class="thumbnail-buttons">
                                    <button
                                        type="button"
                                        class="button template-select"
                                        @click="selectTemplate(template)"
                                    ><i class="fa fa-check"></i> {{ i18n.useThisTemplate }}</button>

                                    <button
                                        type="button"
                                        class="button template-preview"
                                        @click="previewTemplate(template)"
                                    ><i class="fa fa-eye"></i> {{ i18n.preview }}</button>
                                </div>
                            </div>

                            <span class="template-name">{{ template.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <edit-design
                :is-preview="true"
                :template="template"
                :title="i18n.previewTemplate"
                :template-name="selectedTemplate.name"
            ></edit-design>
        </div>
    </div>
</template>

<script>
    const EditDesign = (resolve) => { // eslint-disable-line func-style
        require.ensure(['./EditDesign.vue'], () => {
            resolve(require('./EditDesign.vue')); // eslint-disable-line global-require
        });
    };

    export default {
        components: {
            EditDesign
        },

        data() {
            return {
                templateImgUrlRoot: `${weMail.cdn}/templates`,
                isFetchingTemplate: true,
                categories: [],
                templates: [],
                selectedCategory: {},
                template: {},
                selectedTemplate: {},
                showPreview: false
            };
        },

        computed: {
            ...Vuex.mapState('campaignEdit', ['i18n', 'campaign']),

            templateCategories() {
                let i = 0;

                const categories = this.categories.map((category) => {
                    i += category.count;

                    return {
                        slug: category.slug,
                        name: `${category.name} (${category.count})`
                    };
                });

                categories.unshift({
                    slug: 'all',
                    name: `${this.i18n.all} (${i})`
                });

                return categories;
            }
        },

        created() {
            weMail.event.$on('campaign-customizer-close-preview', this.closePreview);
            weMail.event.$on('campaign-customizer-select-template', this.setEmailTemplate);
        },

        mounted() {
            const vm = this;

            weMail.api.templates().categories().get().done((response) => {
                this.categories = response;
            });

            weMail.api.templates().get().done((response) => {
                this.templates = response.data;

                this.selectedCategory = this.templateCategories[0];

                vm.isFetchingTemplate = false;
            });
        },

        methods: {
            onSelectCategory(category) {
                console.log(category);
                // fetch categorywise template
            },

            setTemplate(selectedTemplate, callback) {
                const vm = this;

                weMail.api.templates(selectedTemplate.slug).get().done((template) => {
                    if (_.isEmpty(template)) {
                        this.alert({
                            type: 'error',
                            text: vm.i18n.templateNotFound
                        });
                    }

                    vm.template = template;

                    if (typeof callback === 'function') {
                        callback();
                    }
                });
            },

            previewTemplate(selectedTemplate) {
                const vm = this;

                vm.selectedTemplate = selectedTemplate;

                this.setTemplate(selectedTemplate, () => {
                    vm.showPreview = true;
                });
            },

            closePreview() {
                this.showPreview = false;
            },

            selectTemplate(selectedTemplate) {
                const vm = this;

                this.setTemplate(selectedTemplate, () => {
                    vm.setEmailTemplate();
                });
            },

            setEmailTemplate() {
                this.$store.commit('campaignEdit/setEmailTemplate', this.template);
                this.$router.push({
                    name: 'campaignEditDesign'
                });
            }
        }
    };
</script>

<style lang="scss">
    .edit-template-filters {
        margin-top: 15px;
        margin-bottom: 15px;

        .list-sub {
            margin-right: 20px;
            line-height: 2.2;
        }

        .multiselect {
            width: 280px;
        }

        input[type="search"] {
            width: 230px;
        }
    }

    .template-thumb {

        .thumbnail-container {
            position: relative;
            display: block;
            line-height: 0;
            border: 1px solid $wp-input-border-color;

            img {
                width: 100%;
                height: auto;
            }

            .thumbnail-buttons {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                text-align: center;
                background-color: rgba(0, 0, 0, 0.6);
                opacity: 0;

                @include transition;

                .button {
                    box-shadow: none;
                }

                .template-select {
                    margin-top: 60px;
                }

                .template-preview {
                    margin-top: 15px;
                }
            }

            &:hover {

                .thumbnail-buttons {
                    opacity: 1;
                }
            }
        }

        .template-name {
            display: block;
            margin-top: 2px;
            font-weight: 500;
            color: #666;
            text-align: center;
        }
    }
</style>
