#!/usr/bin/env bash

# Script: blog-setup.sh
# Author: Dylan 'Chromosome' Navarro
# Description: This script is designed to automate the process of securing SSH for my SYS265 class.
# The script creates a new user, downloads SSH public key and puts in the users authorized_keys, then disables root SSH.

echo "Creating new user: $1"

useradd -m -d /home/$1 $1

mkdir /home/$1/.ssh
cd /home/$1/.ssh
wget https://raw.githubusercontent.com/Chromosom3/TechNotes/master/SYS-255/id_rsa.pub
mv id_rsa.pub authorized_keys
chmod 700 /home/$1/.ssh
chmod 600 /home/$1/.ssh/authorized_keys
chown -R $1:$1 /home/$1/.ssh

# Copied from my blog setup script from sys-255
function secureSSH(){
	# Disable root ssh
	sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config  # Removed the comment character from the sshd_config file to disable root ssh.
	systemctl restart sshd  # Restarts the ssh service.
}

secureSSH
