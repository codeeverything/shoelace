# TODO

- Make the Ansible playbooks generic, i.e. agnostic of target environment
- Update PHPStorm settings export with more useful stuff
- `shoelace init --vagrant=basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`
  - `shoelace init --box=[vagrant|ec2]/basic-ubuntu --provision=[ansible|puppet|chef]/basic-lamp --editorconfig[=specific]`
  - If provision is not set then look for the Vagrant variant in the `basic` sub folder, otherwise look in `provisioned/[provisioner]/[box name]`
  - `shoelace init` should make a web request to the server, which will package up the required files and send back as a ZIP file. Once downloaded this should be unzipped in the same dir as the command was run. It will replace any contents that currently exist
- ansible
  - lamp
    - os
      - common? (across OSs?)
      - ubuntu
        - common (across versions)
        - 14.04
          - ANSIBLE ROLES
        - 15.05
      - centos
        - 5
        - 6

OR

- ansible
  - os
    - ubuntu
      - common
      - 14.04
        - lamp
      - 15.05
        - lamp
    - centos
      - 5
        - lamp
- Allow mixing of default shoelace packages with custom ones from own server(s)
  - a "shoelace.json" file, which can define "packages"
    - or maybe if using a custom URL then we try to resolve the package there and if we can't look to the "master shoelace"?
  - pass this with the request to get a package for the project
  - If a package has the same name as a default one then check if there is an "conflict" property set. Could be "overwrite" or "extend"
  - This means you could make custom versions of packages...?
- write the arguments that were given to a shoelace.json file
  - idea is that we can use these when running shoelace init again
  - or, for deployment to some EC2 instance we can use the same provisioning scripts?
- `shoelace deploy --save` (--save tells us to use the same instance next time we deploy rather than spinning up another one) `--new` (force a new instance to be used, kill old one?)
