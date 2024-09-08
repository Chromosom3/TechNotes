#!/bin/bash

# Script: vm-cpu-check.sh
# Author: Dylan 'Chromosom3' Navarro
# Description: Automates the process of adding 'processorX.use = "FALSE"' to all VM configs for all the e-cores on the system

# This is using an I7-14700K 
# https://www.intel.com/content/www/us/en/products/sku/236783/intel-core-i7-processor-14700k-33m-cache-up-to-5-60-ghz/specifications.html
# Make sure to set pcores to pcores*2 because of hyper threading ecores don't use hyper threading.

pcores=16
ecores=12
vm_files="/media/shared/vmware/"

vm_configs=()

while IFS= read -r -d '' file; do
    vm_configs+=("$file")  
done < <(find "$vm_files" -type f -name "*vmx" -print0)

echo "Found the configuration files for the following VMs"
for file in "${vm_configs[@]}"; do
    display_name=$(grep -oP 'displayName\s*=\s*"\K[^"]+' "$file")
    echo "- $display_name"
    if grep -q "processor$pcores\.use\s*=\s*\"FALSE\"" "$file"; then
        echo "  * Already excluding efficient-cores"
    else
        first=$pcores
        last=$ecores
        echo "  * Modifying configuration..."
        for (( i=$first; last > 0; i++ )); do
            ((last--))  # Decrement the countdown
            echo "processor$i.use = \"FALSE\"" >> "$file"
        done

    fi
done

