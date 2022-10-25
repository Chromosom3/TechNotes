# Script: clone-management.ps1
# Author: Dylan 'Chromosom3' Navarro
# Description: Used to manage cloning of VMs in ESXi without vCenter Server
# Needs PowerCLI Installed (Install-Module -Name VMware.PowerCLI)
# Needs Posh-SSH Installed (Install-Module -Name Posh-SSH)

function Connect {
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

function Disconnect {
    Disconnect-VIServer -Server * -Force -Confirm:$false
}

function Menu {
    try {
        [uint16]$selection = Read-Host("[1] List VMs`n[2] Create Linked Clone`nSelection")
    } catch {
        Write-Host("Bad user... Invalid selection. Must be an integer.")
    }
    
    switch ($selection)
    {
        1 {Get-VM | Select-Object Name, Id; Menu}
        2 {"It is two."}
        19 {exit}
        20 {Disconnect}
    }
}


try {
    Get-VMHost
} catch {
    Connect
} Finally {
    Menu 
    if ($disconnect) {
        Disconnect 
    }    
}