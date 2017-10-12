export default function (Customizer) {
    return {
        store: weMail.store,

        // context = campaign, woocommerce, wp etc module that has template builder
        components: weMail.customizerContentComponents[Customizer.context],

        data: {
            source: 'iframe',
            contentType: '',
            oldSectionIndex: 0,
            oldContentIndex: 0,
            newSectionIndex: 0,
            newContentIndex: 0
        },

        computed: {
            customizerObj() {
                return Customizer.customizer;
            },

            template() {
                return Customizer.template;
            },

            isPreview() {
                return Customizer.isPreview;
            }
        },

        created() {
            weMail.event.$on('customizer-content-drag-start', this.onDragStart);
            weMail.event.$on('customizer-content-drag-end', this.onDragEnd);
        },

        mounted() {
            this.printGlobalElementStyles();

            if (this.isPreview) {
                return;
            }

            const contentTypes = $('tbody', '#customizer-content-types').get(0);

            weMail.Sortable.create(contentTypes, {
                group: {
                    name: 'sortable-group',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                scrollSensitivity: 300,

                onStart(e) {
                    weMail.event.$emit('customizer-content-drag-start', e);
                },

                onEnd(e) {
                    weMail.event.$emit('customizer-content-drag-end', e);
                }
            });

            this.bindSortable();
            this.bindMoveButtons();
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
                return Customizer.customizer.contentTypes.settings[type].image;
            },

            bindSortable() {
                const vm = this;

                $('#wemail-customizer-iframe').contents().find('.responsive-table > tbody').each(function () {
                    weMail.Sortable.create(this, {
                        group: 'sortable-group',
                        scrollSensitivity: 300,
                        handle: '.move',

                        setData(dataTransfer, dragEl) {
                            // Firefox requires this line. Without this line element disappears
                            // after clicking the move icon
                            dataTransfer.setData('text/html', dragEl.innerHTML);

                            const img = new Image();
                            img.src = vm.getContentTypeImage(dragEl.dataset.contentType);
                            dataTransfer.setDragImage(img, 0, 0);
                        },

                        onStart(e) {
                            weMail.event.$emit('customizer-content-drag-start', e);
                        },

                        onEnd(e) {
                            weMail.event.$emit('customizer-content-drag-end', e);
                        },

                        onAdd(e) {
                            vm.updateContent(e);
                        },

                        onUpdate(e) {
                            vm.updateContent(e);
                        }
                    });
                });
            },

            onDragStart(e) {
                $('#wemail-customizer-iframe').contents().find('#wemail-customizer').addClass('content-is-dragging');
            },

            onDragEnd(e) {
                $('#wemail-customizer-iframe').contents().find('#wemail-customizer').removeClass('content-is-dragging');
            },

            bindMoveButtons() {
                const vm = this;

                $('#wemail-customizer-iframe')
                    .contents()
                    .find('#wemail-customizer')
                    .on('click', '.move-up', function (e) {
                        vm.moveUp(e, this);
                    });

                $('#wemail-customizer-iframe')
                    .contents()
                    .find('#wemail-customizer')
                    .on('click', '.move-down', function (e) {
                        vm.moveDown(e, this);
                    });
            },

            moveUp(e, el) {
                this.source = 'iframe';
                this.contentType = el.dataset.contentType;
                this.oldSectionIndex = parseInt(el.dataset.sectionIndex, 10);
                this.oldContentIndex = parseInt(el.dataset.contentIndex, 10);
                this.newSectionIndex = 0;
                this.newContentIndex = 0;

                if (this.oldSectionIndex === 0 && this.oldContentIndex === 0) {
                    return;

                } else if (this.oldContentIndex !== 0) {
                    this.newSectionIndex = this.oldSectionIndex;
                    this.newContentIndex = this.oldContentIndex - 1;
                } else {
                    this.newSectionIndex = this.oldSectionIndex - 1;
                    this.newContentIndex = Customizer.template.sections[this.newSectionIndex].contents.length;
                }

                this.moveContent();

                return;
            },

            moveDown(e, el) {
                this.source = 'iframe';
                this.contentType = el.dataset.contentType;
                this.oldSectionIndex = parseInt(el.dataset.sectionIndex, 10);
                this.oldContentIndex = parseInt(el.dataset.contentIndex, 10);
                this.newSectionIndex = 0;
                this.newContentIndex = 0;

                const itemsInOldSection = Customizer.template.sections[this.oldSectionIndex].contents.length;

                if ((this.oldSectionIndex === this.template.sections.length - 1) && (this.oldContentIndex === itemsInOldSection - 1)) {
                    return;

                } else if (this.oldContentIndex !== itemsInOldSection - 1) {
                    this.newSectionIndex = this.oldSectionIndex;
                    this.newContentIndex = this.oldContentIndex + 1;
                } else {
                    this.newSectionIndex = this.oldSectionIndex + 1;
                    this.newContentIndex = 0;
                }

                this.moveContent();

                return;
            },

            updateContent(e) {
                this.source = e.item.dataset.source;
                this.contentType = e.item.dataset.contentType;
                this.oldSectionIndex = parseInt(e.item.dataset.sectionIndex, 10);
                this.oldContentIndex = parseInt(e.oldIndex, 10);
                this.newSectionIndex = parseInt(e.item.parentElement.dataset.sectionIndex, 10);
                this.newContentIndex = parseInt(e.newIndex, 10);

                if (this.source === 'iframe') {
                    this.moveContent();
                } else {
                    this.addContent(e);
                }
            },

            moveContent() {
                const content = Customizer.template
                    .sections[this.oldSectionIndex]
                    .contents.splice(
                        this.oldContentIndex, 1
                    );

                Customizer.template
                    .sections[this.newSectionIndex]
                    .contents.splice(
                        this.newContentIndex, 0, content[0]
                    );
            },

            addContent(e) {
                $(e.item).remove();
                const contentType = Customizer.customizer.contentTypes.settings[this.contentType];
                const contentSettings = $.extend(true, {}, contentType);

                let totalContents = 0;

                Customizer.template.sections.forEach((section) => {
                    section.contents.forEach(() => {
                        ++totalContents;
                    });
                });

                let content = {
                    id: totalContents + 1,
                    type: contentSettings.type
                };

                content = $.extend(true, content, contentSettings.default);

                Customizer.template
                    .sections[this.newSectionIndex]
                    .contents.splice(
                        this.newContentIndex, 0, content
                    );
            },

            editContent(sectionIndex, contentIndex) {
                weMail.event.$emit('open-content-settings', sectionIndex, contentIndex);
            }
        }
    };
};
