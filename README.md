# shoelace

A set of configs and such to help bootstrap projects

## editorconfig

From the ```www.editorconfig.org``` website:

> EditorConfig helps developers define and maintain consistent coding styles between different editors and IDEs. The EditorConfig project consists of a file format for defining coding styles and a collection of text editor plugins that enable editors to read the file format and adhere to defined styles. EditorConfig files are easily readable and they work nicely with version control systems.

### Usage

Copy the ```.editorconfig``` file into your projects route folder.

Depending on your IDE you may need to install a plugin for to apply the defined configuration:

- PHPStorm: Baked in support
- Sublime: Plugin - HERE
- Eclipse?
- Others

## Ansible

Contains Ansible playbooks that can be used either in conjunction with the Vagrant boxes in this repo, or to provision remote servers.

From the Ansible website:

> Ansible seamlessly unites workflow orchestration with configuration management, provisioning, and application deployment in one easy-to-use and deploy platform.
> Regardless of where you start with Ansible, you'll find our simple, powerful and agentless automation platform has the capabilities to solve your most challenging problems.

## IDE Settings

Exports of IDE settings for specific project types to get you up and running with the minimum of fuss. For example providing configuration of remote debugging with a Vagrant box, or integration with PHP Code Sniffer/PHPUnit running remotely.

## Vagrant

Vagrant boxes with some useful additions. Can be basic raw boxes or boxes with attached provisioner(s).

Can contain helpful extras to aid in management of the machine such as an easy way of starting and stopping ```xdebug```, or carrying out project specific provisioning/setup tasks.

From the Vagrant website:

> Vagrant provides easy to configure, reproducible, and portable work environments built on top of industry-standard technology and controlled by a single consistent workflow to help maximize the productivity and flexibility of you and your team.