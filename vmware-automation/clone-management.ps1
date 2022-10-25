# Script: clone-management.ps1
# Author: Dylan 'Chromosom3' Navarro
# Description: Used to manage cloning of VMs in ESXi without vCenter Server
# Needs PowerCLI Installed (Install-Module -Name VMware.PowerCLI)

try {
    Get-VMHost
} catch {
    # Connection Information
    $esxi_ip = Read-Host "Please enter the server IP address"
    $esxi_account = Get-Credential -Message "Please enter your ESXi Credentials"
    $disconnect = Read-Host "Do you want to disconnect after this session? [Y/n]"
    if ($disconnect.ToLower() -eq "y") {
        $disconnect = $true
    } elseif ($disconnect.ToLower() -eq "n") {
        $disconnect = $false
    } else {
        Write-Host("Invalid option... Disconnecting after session.")
        $disconnect = $true
    }
    Set-PowerCLIConfiguration -InvalidCertificateAction Ignore -Confirm:$false
    Connect-VIServer -Server $esxi_ip -Credential $esxi_account
    # Opts out of the VMware Customer Experience Improvement Program
    Set-PowerCLIConfiguration -Scope User -ParticipateInCEIP $false -Confirm:$false
}

# Add code here for the rest of the script.

if ($disconnect) {
    Disconnect-VIServer -Server * -Force -Confirm:$false
}
