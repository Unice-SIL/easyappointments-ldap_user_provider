## EASYAPPOINTMENTS LDAP USER PROVIDER

This plugin allows you to fetch user in LDAP by username, verify if he is member of a LDAP group mapped with a Easyappointments role and create/update user in Easyappointments database.

### DISCLAIMER
I developed and tested this plugin with easyappointments 1.2.1, I can't guarantee it will work with another version.

### REQUIRES
You need to install [easyappointments-authentication](https://github.com/FredericCasazza/easyappointments-authentication) before use this plugin

### INSTALLATION
Put files in your easyappointments directory.

### CONFIGURATION
To configure the plugin, edit the config.php file at the root of your application directory.

##### Define the user provider
```
// config.php
...
/**
 * The class used for authentication
 */
const USER_PROVIDER_CLASS = 'provider/LdapUserProvider/LdapUserProvider';

```

##### Configure LDAP USER PROVIDER
```
// config.php
..
/**
 * LDAP host server
 * Default = ''
 */
const LDAP_USER_PROVIDER_HOST = 'my_ldap.domain.com';

/**
 * Ldap port server
 * Default 389
 */
const LDAP_USER_PROVIDER_PORT = 389;

/**
 * LDAP user
 * Default = ''
 */
const LDAP_USER_PROVIDER_USER = ';

/**
 * LDAP password
 * Default = ''
 */
const LDAP_USER_PROVIDER_PASSWORD = '';

/**
 * LDAP base dn to search user
 * Default = ''
 */
const LDAP_USER_PROVIDER_USER_DN = 'ou=people,dc=domain,dc=com';

/**
 * LDAP user attribute uid
 * Default = 'uid'
 */
const LDAP_USER_PROVIDER_USER_UID_ATTRIBUTE = 'uid';

/**
 * LDAP user search filter
 * Default = ''
 */
const LDAP_USER_PROVIDER_USER_FILTER = '';

/**
 * LDAP user memberof attribute
 * Default = 'memberof'
 */
const LDAP_USER_PROVIDER_MEMBEROF = 'memberof';

/**
 * LDAP user attributes mapping between ldap and User class attributes
 * Default = array()
 */
const LDAP_USER_PROVIDER_USER_ATTRIBUTES_MAPPING = array(
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
 * LDAP base dn used to search group
 * Default = ''
 */
const LDAP_USER_PROVIDER_GROUP_DN = 'ou=groups,dc=domain,dc=com';

/**
 * LDAP group id attribute
 * Default = 'groupname'
 */
const LDAP_USER_PROVIDER_GROUP_ID_ATTRIBUTE = 'cn';

/**
 * LDAP role mapping between ldap group and easyappointments role slug
 * Default = array()
 */
const LDAP_USER_PROVIDER_ROLES_MAPPING = const LDAP_USER_PROVIDER_ROLES_MAPPING = array(
    'admin'     => 'application:easyappointements:admin:members',
    'provider'  => 'application:easyappointements:provider:members',
    'secretary' => 'application:easyappointements:secretary:members',
);

/**
 * Default role assigned to user if he is not a member of any group present in LDAP_USER_PROVIDER_ROLES_MAPPING
 * Default = 'customer'
 */
const LDAP_USER_PROVIDER_DEFAULT_ROLE_SLUG = 'customer';

/**
 * Model used to fetch, insert, update user in easyappointments database
 * Default = 'LdapUser_model'
 */
const LDAP_USER_PROVIDER_MODEL = 'LdapUser_model';

/**
 * Method of the model used to fetch user by username
 * Default = 'find_user_by_username'
 */
const LDAP_USER_PROVIDER_MODEL_METHOD_EXISTS = 'find_user_by_username';

/**
 * Method of the model used to create user in easyappointments database
 * Default = 'insert'
 */
const LDAP_USER_PROVIDER_MODEL_METHOD_CREATE = 'insert';

/**
 * Method of the model used to update user in easyappointments database
 * Default = 'update'
 */
const LDAP_USER_PROVIDER_MODEL_METHOD_UPDATE = 'update';

```
 
 ### REPORT AN ISSUE
 Send me an email to frederic.casazza@unice.fr