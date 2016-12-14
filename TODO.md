# TODO

- Make the Ansible playbooks generic, i.e. agnostic of target environment
- Update PHPStorm settings export with more useful stuff
- `shoelace init --vagrant=basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`
  - `shoelace init --box=[vagrant|ec2]/basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`
  - If provision is not set then look for the Vagrant variant in the `basic` sub folder, otherwise look in `provisioned/[provisioner]/[box name]`
  - `shoelace init` should make a web request to the server, which will package up the required files and send back as a ZIP file. Once downloaded this should be unzipped in the same dir as the command was run. It will replace any contents that currently exist