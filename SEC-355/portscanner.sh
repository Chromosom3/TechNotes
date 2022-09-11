#!/bin/bash

if [ $# -eq 0 ]
then 
    echo "Invalid amount of arguments supplied. You need to provide a hosts file and a ports file."
    # General error exit
    exit 1
fi

hostfile=$1
portfile=$2

if [ ! -f "$hostfile" ] | [ ! -f "$portfile" ]
then 
    echo "One or more supplied files not found. Exiting..."
    exit 1
fi

echo "host,port"
for host in $(cat $hostfile); do
	for port in $(cat $portfile); do
		timeout .1 bash -c "echo > /dev/tcp/$host/$port" 2> /dev/null && echo "$host,$port"
	done
done
