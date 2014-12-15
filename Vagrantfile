# -*- mode: ruby -*-
# vi: set ft=ruby :

require 'rbconfig'

def os
  @os ||= (
  host_os = RbConfig::CONFIG['host_os']
  case host_os
    when /mswin|msys|mingw|cygwin|bccwin|wince|emc/
      :windows
    when /darwin|mac os/
      :macosx
    when /linux/
      :linux
    when /solaris|bsd/
      :unix
    else
      raise Error::WebDriverError, "unknown os: #{host_os.inspect}"
  end
  )
end

def shareType
  @shareType ||= (
  case os
    when :windows
      :smb
    else
      :nfs
  end
  )
end

Vagrant.configure('2') do |config|

  config.vm.define 'db' do |db|
    db.vm.box       = 'ubuntu/trusty64'
    db.vm.host_name = 'db'
    db.vm.network :private_network, ip: '192.168.56.102'
    db.vm.network 'public_network', :bridge => 'en0: Wi-Fi (AirPort)'

    # Mount and configure using salt
    db.vm.synced_folder './vagrant_files/salt/roots/', '/srv/'
    db.vm.provision :salt do |salt|
      salt.minion_config = 'vagrant_files/salt/minion'
      salt.run_highstate = true
      salt.verbose       = false
      salt.install_type  = 'git'
      salt.install_args  = 'v2014.1.10'
    end

    db.vm.provider :virtualbox do |v|
      v.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
      v.customize ['modifyvm', :id, '--memory', 1024]
      v.customize ['modifyvm', :id, '--name', 'db']
      v.gui = true
    end
  end

  config.vm.define 'web' do |web|
    web.vm.box       = 'ubuntu/trusty64'
    web.vm.host_name = 'web'
    web.vm.network 'private_network', ip: '192.168.56.100'
    web.vm.network 'private_network', ip: '192.168.56.101'
    web.vm.network 'public_network', :bridge => 'en0: Wi-Fi (AirPort)'

    # Mount the project into the VM
    web.vm.synced_folder './', '/home/vagrant/apps/mpstoolbox'

    # Mount and configure using salt
    web.vm.synced_folder './vagrant_files/salt/roots/', '/srv/'
    web.vm.provision :salt do |salt|
      salt.minion_config = 'vagrant_files/salt/minion'
      salt.run_highstate = true
      salt.verbose       = false
      salt.install_type  = 'git'
      salt.install_args  = 'v2014.1.10'
    end

    web.vm.provider :virtualbox do |v|
      v.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
      v.customize ['modifyvm', :id, '--memory', 1024]
      v.customize ['modifyvm', :id, '--name', 'web']
      v.gui = true
    end
  end
end