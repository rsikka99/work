Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  ## For masterless, mount your salt file root
  config.vm.synced_folder "vagrant_files/salt/roots/salt/", "/srv/salt/"


  ##config.vm.network :private_network, ip: "192.168.56.101"
  config.vm.network :forwarded_port, guest: 80, host: 8080
  config.ssh.forward_agent = true

  config.vm.provider :virtualbox do |v|
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--memory", 1024]
    v.customize ["modifyvm", :id, "--name", "mpstoolbox"]
  end


  config.vm.synced_folder "./", "/home/vagrant/mpstoolbox"
  ##config.vm.provision :shell, :inline =>
##    "if [[ ! -f /apt-get-run ]]; then sudo apt-get update && sudo touch /apt-get-run; fi"

##  config.vm.provision :shell, :inline =>
##    "sudo apt-get install virtualbox-guest-additions-iso"


  ## Use all the defaults:
  config.vm.provision :salt do |salt|

    salt.minion_config = "vagrant_files/salt/minion"
    salt.run_highstate = true
    salt.verbose = true

  end
end

