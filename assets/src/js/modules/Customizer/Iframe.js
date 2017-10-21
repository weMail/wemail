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
            newContentIndex: 0,
            highlighterStyle: {
                bottom: '0',
                width: '600px',
                height: '0px',
                opacity: '0',
                zIndex: '-1'
            }
        },

        computed: {
            customizerObj() {
                return Customizer.customizer;
            },

            i18n() {
                return this.customizerObj.i18n;
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

            weMail.event.$on('customizer-show-section-highlighter', this.showSectionHighlighter);
            weMail.event.$on('customizer-hide-section-highlighter', this.hideSectionHighlighter);
        },

        mounted() {
            this.printGlobalElementStyles();

            if (this.isPreview) {
                return;
            }

            const contentTypes = $('tbody', '#customizer-content-types').get(0);

            Sortable.create(contentTypes, {
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
                },

                onMove(e) {
                    $('#wemail-customizer-iframe').contents().find('#wemail-customizer').find('.hovering-content').removeClass('hovering-content');

                    if ($(e.to).hasClass('empty-content-zone')) {
                        $(e.to).addClass('hovering-content');
                    }
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

                _.forEach(this.template.globalElementStyles, (styles, selector) => {
                    style += `${selector} {`;

                    _.forEach(styles, (value, property) => {
                        const prop = _.kebabCase(property);

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
                    Sortable.create(this, {
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

                        onMove(e) {
                            $('#wemail-customizer-iframe').contents().find('#wemail-customizer').find('.hovering-content').removeClass('hovering-content');

                            if ($(e.to).hasClass('empty-content-zone')) {
                                $(e.to).addClass('hovering-content');
                            }
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
                $('#wemail-customizer-iframe').contents().find('.hovering-content').removeClass('.hovering-content');
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

                // id property is required only for v-for -> :key property.
                // It helps to keep order after re-order/sorting contents
                const ids = [];

                Customizer.template.sections.forEach((section) => {
                    section.contents.forEach((content) => {
                        ids.push(parseInt(content.id, 10));
                    });
                });

                let content = {
                    id: _.max(ids) + 1,
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
                weMail.event.$emit('customizer-open-content-settings', sectionIndex, contentIndex);
            },

            cloneContent(sectionIndex, contentIndex) {
                const content = $.extend(
                    true,
                    {},
                    Customizer.template.sections[sectionIndex].contents[contentIndex]
                );

                const newContentIndex = parseInt(contentIndex, 10) + 1;

                Customizer.template
                    .sections[sectionIndex]
                    .contents.splice(
                        newContentIndex, 0, content
                    );
            },

            deleteContent(sectionIndex, contentIndex) {
                this.alert({
                    type: 'warning',
                    text: this.i18n.deleteContentWarnMsg,
                    showCancelButton: true,
                    confirmButtonText: this.i18n.confirmDeleteContent,
                    cancelButtonText: this.i18n.cancelDeleteContent,
                    confirmButtonColor: '#dc3232',
                    cancelButtonColor: '#cccccc'
                }).then((deleteIt) => {
                    if (deleteIt) {
                        Customizer.template
                            .sections[sectionIndex]
                            .contents.splice(
                                contentIndex, 1
                            );

                        weMail.event.$emit('customizer-deleted-content', sectionIndex, contentIndex);
                    }
                });
            },

            showSectionHighlighter(section) {
                const sectionTable = $('#wemail-customizer-iframe').contents().find(`.section.section-${section} > td > table`);
                const position = sectionTable.position();

                this.highlighterStyle = {
                    top: `${position.top}px`,
                    width: `${sectionTable.outerWidth(true)}px`,
                    height: `${sectionTable.outerHeight(true)}px`,
                    opacity: '1',
                    zIndex: '100'
                };
            },

            hideSectionHighlighter(section) {
                this.highlighterStyle = {
                    bottom: '0',
                    width: '600px',
                    height: '0px',
                    opacity: '0',
                    zIndex: '-1'
                };
            }
        }
    };
};
