# IdP AuthN + AuthZ Function
Bundle the php files and make a function. (input null; output the encryption key of the SP)
The encryption key will be used to encrypt the assertion, embedded in simplesamlphp authproc filter.

## get_attribute.php, api.js
- access https://sp1.local/api/attribute.php (after an user is redirected)
- get the attribute to use this protocol (AuthN + AuthZ)