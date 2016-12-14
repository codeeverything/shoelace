# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "minimal/trusty64"

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.21"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--usb", "off"]
    vb.customize ["modifyvm", :id, "--usbehci", "off"]
  end

  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Define a Vagrant Push strategy for pushing to Atlas. Other push strategies
  # such as FTP and Heroku are also available. See the documentation at
  # https://docs.vagrantup.com/v2/push/atlas.html for more information.
  # config.push.define "atlas" do |push|
  #   push.app = "YOUR_ATLAS_USERNAME/YOUR_APPLICATION_NAME"
  # end

  # Enable provisioning with a shell script. Additional provisioners such as
  # Puppet, Chef, Ansible, Salt, and Docker are also available. Please see the
  # documentation for more information about their specific syntax and use.
  config.vm.provision "shell", inline: <<-SHELL
    if ! dpkg -s ansible > /dev/null; then
        echo -e "Ansible not found - Installing..."
        apt-get update -y
        apt-get install software-properties-common -y
        apt-add-repository ppa:ansible/ansible
        apt-get update -y
        apt-get install ansible -y
    else
        echo -e "Ansible already installed. Awesome!"
    fi

    echo -e "Setting up a swap file..."
    #create the swap space 1GB
    echo "Creating 1GB swap space in /swapfile..."
    fallocate -l 1G /swapfile
    ls -lh /swapfile

    #secure the swapfile
    echo "Securing the swapfile..."
    chown root:root /swapfile
    chmod 0600 /swapfile
    ls -lh /swapfile

    #turn the swapfile on
    echo "Turning the swapfile on..."
    mkswap /swapfile
    swapon /swapfile


    echo "Verifying..."
    swapon -s
    grep -i --color swap /proc/meminfo

    echo "Adding swap entry to /etc/fstab"
    echo "\n/swapfile none            swap    sw              0       0" >> /etc/fstab

    echo "Result: "
    cat /etc/fstab

    echo "Swap file added..."
  SHELL

  # Run Ansible from the Vagrant VM
  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = ".shoelace/ansible/playbook.yml"
  end

  config.vm.provision "shell", inline: <<-SHELL
    cd /vagrant && ./project.sh
  SHELL
end
