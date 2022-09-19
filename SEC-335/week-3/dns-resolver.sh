#!/bin/bash
# Script: dns-resolver.sh
# Author: Dylan 'Chromosome' Navarro
# Description: This script is designed to allow you to scan the DNS entries for a network range.

subnet=$1
dns_server=$2
echo "DNS Resolution for $subnet.0/24"
for host in $(seq 1 254); do
    full_ip="$subnet.$host"   
    nslookup $full_ip $dns_server 
done