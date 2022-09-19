#!/bin/bash
# Script: portscanner2.sh
# Author: Dylan 'Chromosome' Navarro
# Description: This is a modified version of the portschanner.sh file.

if [ $# -eq 0 ]
then 
    echo "Invalid amount of arguments supplied. You need to provide a hosts file and a ports file."
    # General error exit
    exit 1
fi

# hostfile=$1
subnet=$1
port=$2
format=$3

if [ "$format" != "detailed" ]
then
    echo "host,port"
    for host in $(seq 1 254); do
        full_ip="$subnet.$host"
        timeout .1 bash -c "echo > /dev/tcp/$full_ip/$port" 2> /dev/null && echo "$full_ip,$port"
    done
else
    for host in $(seq 1 254); do
        full_ip="$subnet.$host"
        echo "Host: $full_ip"
            timeout .1 bash -c "echo > /dev/tcp/$full_ip/$port" 2> /dev/null
            if [ $? -eq 0 ]
            then
                echo "Port $port: Open"
            else
                echo "Port $port: Closed"
            fi
        echo "\n\n"
    done
fi
