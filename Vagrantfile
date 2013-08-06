Vagrant.configure("2") do |config|
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  config.vm.network :private_network, ip: "192.168.56.100"
    config.vm.network :forwarded_port, guest: 80, host: 8080
    config.ssh.forward_agent = true

  config.vm.provider :virtualbox do |v|
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--memory", 1024]
    v.customize ["modifyvm", :id, "--name", "tangent-lamp-mpstoolbox"]
  end

  
  config.vm.synced_folder "./", "/var/www/mpstoolbox", id: "vagrant-root" 
  config.vm.provision :shell, :inline => "sudo apt-get update"


  config.vm.provision :shell, :inline => 'echo -e "mysql_root_password=tmtwdev
controluser_password=awesome" > /etc/phpmyadmin.facts;'

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet/manifests"
    puppet.module_path = "puppet/modules"
    puppet.options = ['--verbose']
  end
end
