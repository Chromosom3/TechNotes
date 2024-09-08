# Script: vm-cpu-check.ps1
# Author: Dylan 'Chromosom3' Navarro
# Description: Automates the process of adding 'processorX.use = "FALSE"' to all VM configs for all the e-cores on the system

# This is using an I9-12900H
# https://www.intel.com/content/www/us/en/products/sku/132214/intel-core-i912900h-processor-24m-cache-up-to-5-00-ghz/specifications.html

$pcores = 12
$ecores = 8
$vm_files = "C:\Users\$($env:USERNAME)\Documents\Virtual Machines"  # Change this to your appropriate path for Windows

# Get all .vmx files in the directory and subdirectories
$vm_configs = Get-ChildItem -Path $vm_files -Recurse -Filter "*.vmx" |
    Where-Object { $_.Extension -eq ".vmx" } |
    Select-Object -ExpandProperty FullName

Write-Host "Found the configuration files for the following VMs"

foreach ($file in $vm_configs) {
    # Extract the displayName value
    $display_name = Select-String -Path $file -Pattern 'displayName\s*=\s*"(.*)"' | ForEach-Object { $_.Matches.Groups[1].Value }
    
    Write-Host "- $display_name"

    # Check if processor8.use = "FALSE" exists
    $processor_exists = Select-String -Path $file -Pattern "processor$pcores\.use\s*=\s*`"FALSE`"" -Quiet

    if ($processor_exists) {
        Write-Host "  * Already excluding efficient-cores"
    } else {
        $first = $pcores
        $last = $ecores

        Write-Host "  * Modifying configuration..."

        for ($i = $first; $last -gt 0; $i++) {
            $last--

            # Append "processorX.use = 'FALSE'" to the configuration file
            Add-Content -Path $file -Value "processor$i.use = `"FALSE`""
        }
    }
}
