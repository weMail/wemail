<?php

namespace WeDevs\WeMail\Modules\Form;

use WeDevs\WeMail\Traits\Hooker;

class Routes {

    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_get_route_data_formIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_formCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_formEdit', 'edit', 10, 2 );
    }

    /**
     * formIndex route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return [type] [description]
     */
    public function index( $params, $query ) {
        if ( !empty( $params['status'] ) ) {
            $query['status'] = $params['status'];
        }

        return [
            'i18n' => [
                'forms'              => __( 'Forms', 'wemail' ),
                'addNew'             => __( 'Add New', 'wemail' ),
                'name'               => __( 'Name', 'wemail' ),
                'entries'            => __( 'Entries', 'wemail' ),
                'createdAt'          => __( 'Created At', 'wemail' ),
                'searchForms'        => __( 'Search Forms', 'wemail' ),
                'noFormFound'        => __( 'No form found', 'wemail' ),
                'all'                => __( 'All', 'wemail' ),
                'trashed'            => __( 'Trashed', 'wemail' ),
                'bulkActions'        => __( 'Bulk Actions', 'wemail' ),
                'apply'              => __( 'Apply', 'wemail' ),
                'moveToTrash'        => __( 'Move to Trash', 'wemail' ),
                'deletePermanently'  => __( 'Delete Permanently', 'wemail' ),
                'restore'            => __( 'Restore', 'wemail' ),
                'trash'              => __( 'Trash', 'wemail' ),
                'view'               => __( 'View', 'wemail' ),
                'items'              => __( 'items', 'wemail' ),
                'delete'             => __( 'Delete', 'wemail' ),
                'cancel'             => __( 'Cancel', 'wemail' ),
                'deleteFormWarnMsg'  => __( 'Are you sure you want to delete this form?', 'wemail' ),
                'deleteFormsWarnMsg' => __( 'Are you sure you want to delete these forms?', 'wemail' ),
                'formDeleted'        => __( 'Form deleted successfully', 'wemail' ),
                'close'              => __( 'Close', 'wemail' ),
                'edit'               => __( 'Edit', 'wemail' )
            ],
            'forms' => wemail()->form->all( $query ),
            'listTable' => [
                'columns' => [
                    'name',
                    'entries',
                    'createdAt'
                ],
                'sortableColumns' => [
                    'name'      => 'name',
                    'entries'   => 'entries',
                    'createdAt' => 'created_at'
                ]
            ]
        ];
    }

    /**
     * formCreate route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function create() {
        return [
            'i18n' => [
                'addNewForm' => __( 'Add New Form', 'wemail' ),
                'cancel'     => __( 'Cancel', 'wemail' ),
                'save'       => __( 'Save', 'wemail' ),
                'name'       => __( 'Name', 'wemail' ),
                'formStyle'       => __( 'Form Style', 'wemail' ),
                'addToList' => __( 'Add to list', 'wemail' ),
            ],
            'form' => [
                'name' => '',
                'style' => 'inline',
                'lists' => []
            ],
            'formStyles' => [
                [
                    'name' => 'inline',
                    'label' => __( 'Inline', 'wemail' ),
                    'image' => 'form-styles/inline.png'
                ],
                [
                    'name' => 'floating-bar',
                    'label' => __( 'Floating Bar', 'wemail' ),
                    'image' => 'form-styles/floating-bar.png'
                ],
                [
                    'name' => 'floating-box',
                    'label' => __( 'Floating Box', 'wemail' ),
                    'image' => 'form-styles/floating-box.png'
                ],
                [
                    'name' => 'modal',
                    'label' => __( 'Modal', 'wemail' ),
                    'image' => 'form-styles/modal.png'
                ],
            ],
            'lists' => wemail()->lists->items()
        ];
    }

    /**
     * formEdit route data
     *
     * @since 1.0.0
     *
     * @param array $params
     *
     * @return array
     */
    public function edit( $params ) {
        return [
            'i18n' => [
                'fields'                => __( 'Fields', 'wemail' ),
                'style'                 => __( 'Style', 'wemail' ),
                'settings'              => __( 'Settings', 'wemail' ),
                'email'                 => __( 'Email', 'wemail' ),
                'firstName'             => __( 'First Name', 'wemail' ),
                'lastName'              => __( 'Last Name', 'wemail' ),
                'fullName'              => __( 'Full Name', 'wemail' ),
                'header'                => __( 'Header', 'wemail' ),
                'html'                  => __( 'HTML', 'wemail' ),
                'deleteWarnMsg'         => __( 'Are you sure you want to delete this field?', 'wemail' ),
                'yesDeleteIt'           => __( 'Yes, delete it', 'wemail' ),
                'noCancelIt'            => __( 'No, cancel it', 'wemail' ),
                'close'                 => __( 'Close', 'wemail' ),
                'label'                 => __( 'Label', 'wemail' ),
                'placeholder'           => __( 'Placeholder', 'wemail' ),
                'templateStyle'         => __( 'Style', 'wemail' ),
                'backgroundColor'       => __( 'Background Color', 'wemail' ),
                'color'                 => __( 'Color', 'wemail' ),
                'border'                => __( 'Border', 'wemail' ),
                'submitButton'          => __( 'Submit Button', 'wemail' ),
                'form'                  => __( 'Form', 'wemail' ),
                'left'                  => __( 'Left', 'wemail' ),
                'center'                => __( 'Center', 'wemail' ),
                'right'                 => __( 'Right', 'wemail' ),
                'position'              => __( 'Position', 'wemail' ),
                'size'                  => __( 'Size', 'wemail' ),
                'auto'                  => __( 'Auto', 'wemail' ),
                'block'                 => __( 'Block', 'wemail' ),
                'fieldLabels'           => __( 'Field Labels', 'wemail' ),
                'top'                   => __( 'Top', 'wemail' ),
                'hidden'                => __( 'Hidden', 'wemail' ),
                'align'                 => __( 'Align', 'wemail' ),
                'onSubmit'              => __( 'On Submit', 'wemail' ),
                'showMessage'           => __( 'Show Message', 'wemail' ),
                'redirectToCustomUrl'   => __( 'Redirect to a custom url', 'wemail' ),
                'formAction'            => __( 'Form Action', 'wemail' ),
                'subscribeToList'       => __( 'Subscribe to list', 'wemail' ),
                'saveForm'              => __( 'Save Form', 'wemail' ),
            ],
            'form' => wemail()->form->get($params['id']),
            'formFields' => [
                'firstName',
                'lastName',
                'fullName',
                'header',
                'html'
            ],
            'fieldSettings' => [
                'firstName' => [
                    'id' => 0,
                    'type' => 'firstName',
                    'label' => 'First Name',
                    'placeholder' => __( 'Type your first name', 'wemail' ),
                    'required' => true,
                    'style' => [
                        'marginBottom' => '0'
                    ]
                ],
                'lastName' => [
                    'id' => 0,
                    'type' => 'lastName',
                    'label' => 'Last Name',
                    'placeholder' => __( 'Type your last name', 'wemail' ),
                    'required' => true,
                    'style' => [
                        'marginBottom' => '0'
                    ]
                ],
                'fullName' => [
                    'id' => 0,
                    'type' => 'fullName',
                    'label' => 'Full Name',
                    'placeholder' => __( 'Type your full name', 'wemail' ),
                    'required' => true,
                    'style' => [
                        'marginBottom' => '0'
                    ]
                ],
                'header' => [
                    'id' => 0,
                    'type' => 'header',
                    'text' => __( 'Subscribe to our newsletter', 'wemail' ),
                    'style' => [
                        'color' => '',
                        'fontSize' => '22px',
                        'marginBottom' => '0'
                    ]
                ],
                'html' => [
                    'id' => 0,
                    'type' => 'html',
                    'html' => '<p style="margin-bottom: 0">Add a descriptive message telling what your visitor is signing up for here.</p>',
                    'style' => [
                        'fontSize' => '14px',
                        'marginBottom' => '0'
                    ]
                ],
            ]
        ];
    }
}
