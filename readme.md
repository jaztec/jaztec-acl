JaztecAcl
=========

Simple database driven ACL module for ZF2.

## Dependencies

- ZF2
- ZF-Commons/ZfcUser
- ZF-Commons/ZfcUserDoctrineORM
- KablauJoustra/KJSencha

## Installation
- Run the setup.sql script inside your MySQL editor.
- Use the module in your zf2 application
- Change the Controllers you wish to protect by ACL to extending the JaztecAcl\Controller\AutherizedController
- (When using KJ Direct objects extend them from JaztecAcl\Direct\AbstractDirectObject)
- Add the Controller name as given inside the Module.php's to the acl_resources table and you're in bussiness.
