<?php
/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      F.Casazza <frederic.casazza@unice.fr>
 * ---------------------------------------------------------------------------- */

defined('BASEPATH') OR exit('No direct script access allowed');

Class LdapUserProvider implements UserProviderInterface {

    protected $CI;
    protected $ldap_conn;
    protected $ldap_user_provider_host;
    protected $ldap_user_provider_port;
    protected $ldap_user_provider_user;
    protected $ldap_user_provider_password;
    protected $ldap_user_provider_user_dn;
    protected $ldap_user_provider_user_filter;
    protected $ldap_user_provider_user_uid_attribute;
    protected $ldap_user_provider_group_dn;
    protected $ldap_user_provider_group_id_attribute;
    protected $ldap_user_provider_memberOf;
    protected $ldap_user_provider_user_attributes_mapping;
    protected $ldap_user_provider_roles_mapping;
    protected $ldap_user_provider_default_role_slug;
    protected $ldap_user_provider_model;
    protected $ldap_user_provider_model_method_exists;
    protected $ldap_user_provider_model_method_create;
    protected $ldap_user_provider_model_method_update;
    protected $user_attributes_mapping = array(
        'id'            => 'uid',
        'email'         => 'mail',
        'first_name'    => 'givenname',
        'last_name'     => 'sn',
        'mobile_number' => 'mobilenumber',
        'phone_number'  => 'phonenumber',
        'address'       => 'address',
        'city'          => 'city',
        'state'         => 'state',
        'zip_code'      => 'zip_code',
    );

    /**
     * LdapUserProvider constructor.
     */
    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        $this->CI->load->add_package_path(__DIR__);
        $this->CI->load->file(__DIR__.'/LdapUser.php');
        $this->ldap_user_provider_host = defined('Config::LDAP_USER_PROVIDER_HOST')? Config::LDAP_USER_PROVIDER_HOST : '';
        $this->ldap_user_provider_port = defined('Config::LDAP_USER_PROVIDER_PORT')? Config::LDAP_USER_PROVIDER_PORT : 389;
        $this->ldap_user_provider_user = defined('Config::LDAP_USER_PROVIDER_USER')? Config::LDAP_USER_PROVIDER_USER : null;
        $this->ldap_user_provider_password = defined('Config::LDAP_USER_PROVIDER_PASSWORD')? Config::LDAP_USER_PROVIDER_PASSWORD : null;
        $this->ldap_user_provider_user_dn = defined('Config::LDAP_USER_PROVIDER_USER_DN')? Config::LDAP_USER_PROVIDER_USER_DN : null;
        $this->ldap_user_provider_group_dn = defined('Config::LDAP_USER_PROVIDER_GROUP_DN')? Config::LDAP_USER_PROVIDER_GROUP_DN : null;
        $this->ldap_user_provider_user_uid_attribute = defined('Config::LDAP_USER_PROVIDER_USER_UID_ATTRIBUTE')? Config::LDAP_USER_PROVIDER_USER_UID_ATTRIBUTE : 'uid';
        $this->ldap_user_provider_group_id_attribute = defined('Config::LDAP_USER_PROVIDER_GROUP_ID_ATTRIBUTE')? Config::LDAP_USER_PROVIDER_GROUP_ID_ATTRIBUTE : 'groupname';
        $this->ldap_user_provider_user_filter = defined('Config::LDAP_USER_PROVIDER_USER_FILTER')? Config::LDAP_USER_PROVIDER_USER_FILTER : '';
        $this->ldap_user_provider_memberOf = defined('Config::LDAP_USER_PROVIDER_MEMBEROF')? Config::LDAP_USER_PROVIDER_MEMBEROF : 'memberOf';
        $this->ldap_user_provider_user_attributes_mapping = defined('Config::LDAP_USER_PROVIDER_USER_ATTRIBUTES_MAPPING')? Config::LDAP_USER_PROVIDER_USER_ATTRIBUTES_MAPPING : array();
        $this->ldap_user_provider_roles_mapping = defined('Config::LDAP_USER_PROVIDER_ROLES_MAPPING')? Config::LDAP_USER_PROVIDER_ROLES_MAPPING : array();
        $this->ldap_user_provider_default_role_slug = defined('Config::LDAP_USER_PROVIDER_DEFAULT_ROLE_SLUG')? Config::LDAP_USER_PROVIDER_DEFAULT_ROLE_SLUG : 'customer';
        $this->ldap_user_provider_model = defined('Config::LDAP_USER_PROVIDER_MODEL')? Config::LDAP_USER_PROVIDER_MODEL : 'LdapUser_model';
        $this->ldap_user_provider_model_method_exists = defined('Config::LDAP_USER_PROVIDER_MODEL_METHOD_EXISTS')? Config::LDAP_USER_PROVIDER_MODEL_METHOD_EXISTS : 'find_user_by_username';
        $this->ldap_user_provider_model_method_create = defined('Config::LDAP_USER_PROVIDER_MODEL_METHOD_CREATE')? Config::LDAP_USER_PROVIDER_MODEL_METHOD_CREATE : 'insert';
        $this->ldap_user_provider_model_method_update = defined('Config::LDAP_USER_PROVIDER_MODEL_METHOD_UPDATE')? Config::LDAP_USER_PROVIDER_MODEL_METHOD_UPDATE : 'update';
        $this->ldap_user_provider_user_attributes_mapping['username'] = strtolower($this->ldap_user_provider_user_uid_attribute);
        $this->user_attributes_mapping = array_merge($this->user_attributes_mapping, $this->ldap_user_provider_user_attributes_mapping);
    }

    /**
     * @param $username
     * @return LdapUser|null
     */
    public function loadUserByUsername($username)
    {
        $this->_init_ldap_connection();
        $user_data = $this->_find_user_in_ldap($username);
        if($user_data['count']==0 || empty($user_data)){
            $this->_close_ldap_connection();
            return null;
        }
        $role_slug = $this->_get_user_role_slug($username);
        if(empty($role_slug)) $role_slug = $this->ldap_user_provider_default_role_slug;
        $user_data[0]['role_slug'] = $role_slug;
        $this->_close_ldap_connection();
        $user = new LdapUser($user_data[0], $this->user_attributes_mapping);
        $this->_set_user_in_db($user);
        return $user;
    }

    /**
     *
     */
    private function _init_ldap_connection(){
        $this->ldap_conn = ldap_connect($this->ldap_user_provider_host, $this->ldap_user_provider_port);
        ldap_bind($this->ldap_conn, $this->ldap_user_provider_user, $this->ldap_user_provider_password);
    }

    /**
     *
     */
    private function _close_ldap_connection(){
        ldap_close($this->ldap_conn);
    }

    /**
     * @param $username
     * @return array
     */
    private function _find_user_in_ldap($username){
        $ldap_filter = "{$this->ldap_user_provider_user_filter}({$this->ldap_user_provider_user_uid_attribute}={$username})";
        $ldap_attributes = array_values($this->user_attributes_mapping);
        $ldap_search = ldap_search($this->ldap_conn, $this->ldap_user_provider_user_dn, $ldap_filter, $ldap_attributes);
        $user_data = ldap_get_entries($this->ldap_conn, $ldap_search);
        return $user_data;
    }

    /**
     * @param $username
     * @return int|null|string
     */
    private function _get_user_role_slug($username){
        foreach($this->ldap_user_provider_roles_mapping as $slug => $group){
            $ldap_filter = "(&({$this->ldap_user_provider_user_uid_attribute}={$username})({$this->ldap_user_provider_memberOf}={$this->ldap_user_provider_group_id_attribute}={$group},{$this->ldap_user_provider_group_dn}))";
            $ldap_search = ldap_search($this->ldap_conn, $this->ldap_user_provider_user_dn, $ldap_filter, array($this->ldap_user_provider_user_uid_attribute));
            $user_data = ldap_get_entries($this->ldap_conn, $ldap_search);
            if($user_data['count']>0) return $slug;
        }
        return null;
    }

    /**
     * @param LdapUser $user
     * @return LdapUser
     */
    private function _set_user_in_db(LdapUser $user){
        $this->CI->load->model($this->ldap_user_provider_model, 'ldapuser_model');
        $method_exists = $this->ldap_user_provider_model_method_exists;
        $method_create = $this->ldap_user_provider_model_method_create;
        $method_update = $this->ldap_user_provider_model_method_update;
        $user_data = $this->CI->ldapuser_model->$method_exists($user->getUsername());
        if(empty($user_data)) {
            $user_id =$this->CI->ldapuser_model->$method_create($user);
            $user->setId($user_id);
        }else {
            $user->setId($user_data['user_id']);
            $this->CI->ldapuser_model->$method_update($user);
        }
        return $user;
    }
}