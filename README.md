# shoelace

Shoelace is a command line tool, backed by a hosted set of packages, to help you get your project environment off the ground quickly and consistently.

It values repeatable, consistent development environments and leverages Vagrant and server provisioning tools like Ansible to help achieve this.

Shoelace aims to provide generic but useful starting points for the configurations it provides in packages, so don't be shy about making changes once you have the package in your project!

### Requirements

- Vagrant
- A server with PHP 5, if hosting packages yourself

Provisioners are installed to and run on the VM, so you don't need these locally (if using a Vagrant VM at least).

### Installation

Download the command line tool for either Windows or Mac and place it in a path that's globally accesible from your terminal (add it if you need to).

Set a new environment variable of `SHOCELACE_SERVER` and set this to `http://shoelace.codeeverything.com/src/` - this is the default package server, you can host your own if you like (see below).

### Initialising a project

Let's start with an example that will generate everything we need to add:
- A Vagrant machine running Ubuntu Trusty
- An Ansible playbook to provision LAMP on that machine
- An .editorconfig file with some sensible defaults for common file type

`shoelace init --vagrant=trusty --provision=ansible/basic-lamp --editorconfig`

The `init` command is the only one currently available and takes the following (optional) arguments:
- `--vagrant`: The name of the Vagrant machine to use
- `--provision`: A string with format `PROVISIONER/CONFIG`, for example `ansible/basic-lamp`. This tells Shoelace to get the Vagrant machine above which is configured to provision with the provisioner and give that provisioner the config to achieve the setup you need.
- `--editorconfig`: Currently a boolean flag. If included a sensible `.editorconfig` file will be included

Let's try another example:

`shoelace init --vagrant=trusty`

This will give us a `Vagrantfile` for Ubuntu Trusty and nothing else.

`shoelace init --editorconfig`

Perhaps we don't need any VM for this project, but we do want a shared Editor Config for all developers.

`shoelace init --provision=ansible/basic-lamp --editorconfig`

Here we don't have a VM again, but do include the editor config and provisioning scripts for use against whatever infrastructure the project will use.

### Hosting packages and customisation

You can host packages and extend the default offering to include your own configs quite simply by:
- Forking the Shoelace repo
- Cloning this to your own server in a web servable directory
  - This must be running PHP 5 for the packager to work
- Have devs. change their `SHOELACE_SERVER` environment variable to point at your server. This should include enough of a path to resolve `/packager.php`

### Notes

- `shoelace init` is not (currently), intended to be re-runnable with adverse effets. It's expected to be run once at the start of a project before anything else. Please reuse with care!
