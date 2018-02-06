<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed.');

class LdapUser_Model extends CI_Model {

    /**
     * @param $username
     * @return null
     */
    public function find_user_by_username($username){
        $user_data = $this->db
            ->select('ea_users.id AS user_id, ea_users.email AS user_email, ea_roles.slug AS role_slug, ea_user_settings.username')
            ->from('ea_users')
            ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
            ->join('ea_user_settings', 'ea_user_settings.id_users = ea_users.id')
            ->where('ea_user_settings.username', $username)
            ->get()->row_array();
        return ($user_data) ? $user_data : NULL;
    }

    /**
     * @param $user
     * @return int
     * @throws Exception
     */
    public function insert($user){
        $user_data = $this->_get_user_data_as_array($user);
        $this->db->trans_begin();
        if (!$this->db->insert('ea_users', $user_data)) {
            throw new Exception('Could not insert user into the database.');
        }
        $user_id = intval($this->db->insert_id());
        $user_settings['id_users'] = $user_id;
        $user_settings['username'] = $user->getUsername();
        if (!$this->db->insert('ea_user_settings', $user_settings)) {
            $this->db->trans_rollback();
            throw new Exception('Could not insert user settings into the database.');
        }
        $this->db->trans_complete();
        return $user_id;
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public function update($user)
    {
        $user_data = $this->_get_user_data_as_array($user);
        $user_id = $user->getId();
        $this->db->trans_begin();
        if (!$this->db->update('ea_users', $user_data, array('id'=>$user_id))) {
            throw new Exception('Could not update user into the database.');
        }
        $this->db->trans_complete();

        return true;
    }

    /**
     * Get the admin users role id.
     *
     * @return int Returns the role record id.
     */
    public function get_role_id($role_slug) {
        return intval($this->db->get_where('ea_roles', array('slug' => $role_slug))->row()->id);
    }

    private function _get_user_data_as_array($user){
        $user_data['first_name'] = $user->getFirstName();
        $user_data['last_name'] = $user->getLastName();
        $user_data['email'] = $user->getEmail();
        $user_data['mobile_number'] = $user->getMobileNumber();
        $user_data['phone_number'] = $user->getPhoneNumber();
        $user_data['address'] = $user->getAddress();
        $user_data['city'] = $user->getCity();
        $user_data['state'] = $user->getState();
        $user_data['zip_code'] = $user->getZipCode();
        $user_data['id_roles'] = $this->get_role_id($user->getRole());
        return $user_data;
    }
}