JaztecAcl
=========
--Travis here-- what now...

Simple database driven ACL module for ZF2.

## Installation
- Run the /data/db/setup.sql script inside your MySQL editor.
- Use the module in your zf2 application
- Change the Controllers you wish to protect by ACL to extending the JaztecAcl\Controller\AuthorizedController
- When using KJ Direct objects extend them from JaztecAcl\Direct\AutherizedDirectObject
- Add the Controller names or the AutherizedDirectObject::$aclDenominator as given inside the Module.php's to the acl_resources table and you're in bussiness.
