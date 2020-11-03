# IdP AuthN + AuthZ Function
Bundle the php files and make a function. (input null; output the encryption key of the SP).
The encryption key will be used to encrypt the assertion, embedded in simplesamlphp authproc filter.

## get_attribute.php
- access https://sp1.local/api/attribute.php (after an user is redirected).
- get the attribute to use this protocol (AuthN + AuthZ).

### input
- server_name (String): the origin name of the server (IdP). Federated IdP should register it in advance.

### output (Associative array)
- result (String): the result of the request to the SP.
- attributes (Array of String): the attributes used to the Authorization.
- key (Associative Array of int): public key that will be used in get_zi function (get_zi.php).
- session_id (String): the session id used in these sequense.

## get_zi.php
- access https://sp1.local/api/cal/wij.php.
- get the calculated value w_{ij} from the SP.

### input
- attrs (Array of String): attributes used to the Authorization (got in get_attribute function).
- key (Associative Array of int): public key to encrypt some values (got in get_attribute function).
- rand (Array of int): random values selected for each attribute (attrs).
- server_name (String): the origin name of the server (IdP).
- session_id (String): the session id used in these sequense.
- uid (String): the user id of an user attempting to access to the SP (and be authenticated against the IdP).

### output (Associative array)
- result (String): 'OK' or 'NG' indicating the request to the SP has completed successfully or not.
- hash_function (String): the hash function used in magic protocol.
- algo (String): the algorithm used in the hash function.
- key (String): the shared secret key used in the hash function.
- w_ij (Array of int): the calculated values gotten from the SP.
- A_i (Array of int) : the attribute values.

## get_ci.php
- calculate whether the attribute value is greater than the threshold of the SP or not.


### input
- hash_function (String): the hash function used in magic protocol.
- algo (String): the algorithm used in the hash function.
- key (String): the shared secret key used in the hash function.
- wij (Array of int): the values gotten from the SP.
- A (Array of int): the attribute values of the user.
- rand (Array of int): the random values used in get_zi function.

### output (Array)
- ci (Array of int): the values of 1 or 0, indicating the attribute value is greater than the threshold value, or not.
