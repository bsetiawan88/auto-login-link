<?php

class Auto_Login_Link_Admin {
    
    var $auto_atc_model;

    public function __construct() {
        $this->auto_atc_model = new Auto_Login_Link_Model();
    }

    public function index() {
        $tab = isset($_GET['tab']) ? esc_attr($_GET['tab']) : '';

        switch($tab) {
            case 'create' :
                $this->create();
                break;
            case 'edit' :
                $this->edit();
                break;
            case 'delete' :
                $this->delete();
                break;
            default:
                $this->list();
                break;
        }
    }

    public function create() {
        if (isset($_POST['auto-login-link-nonce']) && wp_verify_nonce($_POST['auto-login-link-nonce'], 'form-action')) {
            $this->auto_atc_model->create($_POST);
            Auto_Login_Link::set_notice('success', __('New Auto Login Link has been created', Auto_Login_Link::LANGUAGE_DOMAIN));
            $this->list();
        } else {
            echo Auto_Login_Link::exec_template('admin/form', array_merge(['title' => __('Create New Auto Login Link Link', Auto_Login_Link::LANGUAGE_DOMAIN)], $this->_get_form_data()));
        }
    }

    public function edit() {
        $id = absint($_GET['id']);
        $form_data = $this->auto_atc_model->get(['id' => $id, 'single' => TRUE]);

        if (empty($form_data)) {
            Auto_Login_Link::redirect(admin_url('admin.php?page=' . Auto_Login_Link::PLUGIN_SLUG . '-atc'));
        }

        if (isset($_POST['auto-login-link-nonce']) && wp_verify_nonce($_POST['auto-login-link-nonce'], 'form-action')) {
            $this->auto_atc_model->update($id, $_POST);
            Auto_Login_Link::set_notice('success', __('Auto Login Link has been updated', Auto_Login_Link::LANGUAGE_DOMAIN));
            $this->list();
        } else {
            echo Auto_Login_Link::exec_template('admin/form', array_merge(['title' => __('Edit Auto Login Link', Auto_Login_Link::LANGUAGE_DOMAIN), 'form_data' => $form_data], $this->_get_form_data()));
        }
    }

    public function delete() {
        $this->auto_atc_model->delete($_GET['id']);
        Auto_Login_Link::set_notice('success', __('Auto Login Link has been deleted', Auto_Login_Link::LANGUAGE_DOMAIN));
        $this->list();
    }

    public function list() {
        if (isset($_POST['auto-login-link-nonce']) && wp_verify_nonce($_POST['auto-login-link-nonce'], 'bulk-action') && isset($_POST['item'])) {
            $this->auto_atc_model->delete($_POST['item']);
            Auto_Login_Link::set_notice('success', __('Auto Login Links have been deleted', Auto_Login_Link::LANGUAGE_DOMAIN));
        }

        $list_table = new Auto_Login_Link_Table();
        $list_table->prepare_items();

        echo Auto_Login_Link::exec_template('admin/list', ['title' => __('Auto Login Link', Auto_Login_Link::LANGUAGE_DOMAIN), 'list_table' => $list_table]);
    }

    private function _get_form_data() {

        return [
            'users' => get_users()
        ];
    }
}