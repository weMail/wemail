export default function (customizer) {
    return {
        // context = campaign, woocommerce, wp etc module that has template builder
        components: weMail.customizerComponents[customizer.context],

        data: {
            template: customizer.template,
            source: 'iframe',
            contentType: '',
            oldSection: 0,
            oldIndex: 0,
            newSection: 0,
            newIndex: 0
        },

        mounted() {
            this.printGlobalElementStyles();

            const contentTypes = $('tbody', '#customizer-content-types').get(0);

            weMail.Sortable.create(contentTypes, {
                group: {
                    name: 'sortable-group',
                    pull: 'clone'
                },
                sort: false,
                scrollSensitivity: 300
            });

            this.bindSortable();
        },

        watch: {
            'template.globalElementStyles': {
                deep: true,
                handler: 'printGlobalElementStyles'
            }
        },

        methods: {
            // Note: We cannot use a script tag inside Vue instance
            printGlobalElementStyles() {
                let style = '';

                weMail._.forEach(this.template.globalElementStyles, (styles, selector) => {
                    style += `${selector} {`;

                    weMail._.forEach(styles, (value, property) => {
                        const prop = weMail._.kebabCase(property);

                        style += `${prop}: ${value};`;
                    });

                    style += '}';
                });

                $('#wemail-customizer-iframe').contents().find('#global-element-style').html(style);
            },

            getContentTypeImage(type) {
                return customizer.customizer.contentTypes.settings[type].image;
            },

            bindSortable() {
                const vm = this;

                $('#wemail-customizer-iframe').contents().find('.responsive-table > tbody').each(function () {
                    weMail.Sortable.create(this, {
                        group: 'sortable-group',
                        scrollSensitivity: 300,
                        handle: '.move',
                        onAdd(e) {
                            vm.updateContent(e);
                        },
                        onUpdate(e) {
                            vm.updateContent(e);
                        }
                    });
                });
            },

            updateContent(e) {
                this.source = e.item.dataset.source;
                this.contentType = e.item.dataset.contentType;
                this.oldSection = e.item.dataset.sectionIndex;
                this.oldIndex = e.oldIndex;
                this.newSection = e.item.parentElement.dataset.sectionIndex;
                this.newIndex = e.newIndex;

                if (this.source === 'iframe') {
                    this.moveContent();
                } else {
                    this.addContent(e);
                }
            },

            moveContent() {
                const content = customizer
                    .template
                    .sections[this.oldSection]
                    .contents.splice(
                        this.oldIndex, 1
                    );

                customizer
                    .template
                    .sections[this.newSection]
                    .contents.splice(
                        this.newIndex, 0, content[0]
                    );
            },

            addContent(e) {
                $(e.item).remove();
                const contentType = customizer.customizer.contentTypes.settings[this.contentType];
                const content = $.extend(true, {}, contentType);

                delete content.image;

                customizer
                    .template
                    .sections[this.newSection]
                    .contents.splice(
                        this.newIndex, 0, content
                    );
            }
        }
    };
};
