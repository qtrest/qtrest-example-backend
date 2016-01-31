Vagrant.require_version ">= 1.5"
Vagrant.configure("2") do |config|
	#config.vbguest.auto_update = false
    config.vm.provider :virtualbox do |v|
        v.name = "kupon"
        v.customize [
            "modifyvm", :id,
            "--name", "kupon",
            "--memory", 1024,
            "--natdnshostresolver1", "on",
            "--cpus", 1,
        ]
		v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/v-root", "1"]
    end
    config.vm.box = "ubuntu/trusty64"
    config.vm.network :forwarded_port, guest: 80, host: 8080
    config.vm.provision "ansible_local" do |ansible|
        ansible.playbook = "ansible/playbook.yml"
        ansible.inventory_path = "ansible/inventories/dev"
        ansible.limit = 'all'
    end
end
