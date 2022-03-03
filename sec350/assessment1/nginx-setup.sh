#!/bin/bash

# Script: nginx-setup.sh
# Author: Dylan 'Chromosome' Navarro
# Description: Used to procision my Ubuntu Nginx server for my SEC-350 assesment.
# See dhcp-setup.sh for more detailed on what is going on here. They are basically the same system setup.

useradd dylan -s /bin/bash
usermod -aG sudo dylan
passwd dylan

hostnamectl set-hostname nginx-dylan

rm /etc/netplan/00-installer-config.yaml
curl https://raw.githubusercontent.com/Chromosom3/TechNotes/main/sec350/assessment1/nginx.yaml > /etc/netplan/00-installer-config.yaml
netplan apply

apt update
sudp apt upgrade -y
apt install nginx -y
systemctl enable nginx --now