#!/bin/bash
# Script: portscanner.sh
# Author: Dylan 'Chromosome' Navarro
# Description: This is a modified version of the scanning file provided in class.
# Argumants:
# - hostfile: a file containing a list of IPs or a list of hostnames.
# - portfile: a file containing a list of ports to scan on each host.
# - format: can choose between detailed format or csv (default) format.

if [ $# -eq 0 ]
then 
    echo "Invalid amount of arguments supplied. You need to provide a hosts file and a ports file."
    # General error exit
    exit 1
fi

hostfile=$1
portfile=$2
format=$3

if [ ! -f "$hostfile" ] | [ ! -f "$portfile" ]
then 
    echo "One or more supplied files not found. Exiting..."
    exit 1
fi

if [ "$format" != "detailed" ]
then
    echo "host,port"
    for host in $(cat $hostfile); do
        for port in $(cat $portfile); do
            timeout .1 bash -c "echo > /dev/tcp/$host/$port" 2> /dev/null && echo "$host,$port"
        done
    done
else
    for host in $(cat $hostfile); do
        echo "Host: $host"
        for port in $(cat $portfile); do
            timeout .1 bash -c "echo > /dev/tcp/$host/$port" 2> /dev/null
            if [ $? -eq 0 ]
            then
                echo "Port $port: Open"
            else
                echo "Port $port: Closed"
            fi
        done
        echo "\n\n"
    done
fi
