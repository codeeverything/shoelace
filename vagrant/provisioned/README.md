# Provisioners for Vagrant Machines

## Basic LEMP (with Ansible)

Installs ```minimal/trusty64``` Vagrant box with Ansible and then runs the playbook to setup:

- PHP (5.6)
- MySQL
- NGINX
- Composer
- XDebug

```project.sh``` will be run after the Ansbile playbook and can be used to run any other project provisioning required that Ansible can't accommodate.

```xdebug.sh start|stop``` can be run to either enable or disable remote debugging via XDebug (enables and disables the whole extension).