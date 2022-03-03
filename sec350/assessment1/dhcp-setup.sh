#!/bin/bash

# Script: dhcp-setup.sh
# Author: Dylan 'Chromosome' Navarro
# Description: Used to procision my Ubuntu DHCP server for my SEC-350 assesment.


# I will be runing this as root. Let's make a new sudo user.
useradd dylan -s /bin/bash
usermod -aG sudo dylan
passwd dylan

# Let's confiugre the system hostname
hostnamectl set-hostname dhcp01-dylan

# Let's just confirm the network is setup right, should be if I was able to curl this script. 
rm /etc/netplan/00-installer-config.yaml
curl https://raw.githubusercontent.com/Chromosom3/TechNotes/main/sec350/assessment1/dhcp.yaml > /etc/netplan/00-installer-config.yaml
netplan apply

# Let's get the service setup now
apt update
apt upgrade -y
apt install isc-dhcp-server -y
mv /etc/dhcp/dhcpd.conf{,.backup}
curl https://raw.githubusercontent.com/Chromosom3/TechNotes/main/sec350/assessment1/dhcpd.conf > /etc/dhcp/dhcpd.conf
sudo systemctl enable isc-dhcp-server --now