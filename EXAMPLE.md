# Example

Let's take a look at how to setup a simple project and get going.

We'll setup a Vagrant LEMP box with Ansible, add a ```.editorconfig``` file and create a fresh Laravel project.

- Create a project for Shoelace and clone the repo here (only needs to be done once)
- Create your project folder
- Copy the ```shoelace.sh``` file from the root of Shoelace
- Copy the contents of ```vagrant/provisioned/basic lemp``` to your project root
- Copy the contents of ```ansible/basic lemp``` to the ```ansible``` folder you just copied into your project root
- Run ```vagrant up``` from your project root (provisioning may take a while)
- Once provisioned run ```vagrant ssh``` to get onto your Vagrant machine
- Run ```cd /vagrant && ./shoelace.sh create-project laravel/laravel```. Wait...
- Once complete exit your SSH session and return to your project folder
- Copy the ```.editorconfig``` file from the ```editorconfig``` folder in Shoelace to the root of your project