# Script: iso-upload.ps1
# Author: Dylan 'Chromosom3' Navarro
# Description: Used to make VMs in bulk
# Needs PowerCLI Installed (Install-Module -Name VMware.PowerCLI)

# Connection Information
$vsphere_ip = Read-Host "Please enter the server IP address"
$account = Get-Credential -Message "Please enter your vSphere Credentials"
$content_lib_name = ""
$iso_dir = ""

Set-PowerCLIConfiguration -InvalidCertificateAction Ignore -Confirm:$false
Set-PowerCLIConfiguration -WebOperationTimeoutSeconds 3600 -Scope Session
Connect-VIServer -Server $vsphere_ip -Credential $account

$content_lib = Get-ContentLibrary -Name $content_lib_name

if (!$content_lib) {
    Write-Host "Content Library Not Found"
    exit
}


$isoFiles = Get-ChildItem -Path $directoryPath -Filter *.iso

foreach ($isoFile in $isoFiles) {
     Write-Host "Uploading $($isoFile)"
    New-ContentLibraryItem -ContentLibrary $content_lib -Name $isoFile -Files $($isoFile.FullName)
}
