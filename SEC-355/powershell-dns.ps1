# Script: powershell-dns.ps1
# Author: Dylan 'Chromosome' Navarro
# Description: This script is designed to allow you to scan the DNS entries for a network range.
# Example Usage: ./powershell-dns.ps1 192.168.4.4 192.168.3 24

param ($dns_server, $subnet, $mask)

try{Write-Host("DNS Server: $dns_server `nSubnet: $subnet `nSubnet Mask: $([int]$mask)")}
catch{Write-Host("Subnet Mask Must be an INTEGER")}

if ([int]$mask -notin 8,16,24){
    Write-Host("The only supported subnet masks are 8, 16, 24")
    exit
}

function Resolve-DNS {
    param($resolve_ip)
    Resolve-DnsName -DnsOnly $resolve_ip -Server $dns_server -ErrorAction Ignore
}

function Set-ForthOctet {
    param ($ip)
    foreach ($forth_octet in 1..255) {
        $final_ip = ("$ip.$forth_octet")
        Resolve-DNS($final_ip)
    }

}

function Set-ThirdOctet {
    param ($ip)
    foreach ($third_octet in 1..255) {
        $full_ip = ("$ip.$third_octet")
        Set-ForthOctet($full_ip)
    }

}

function Set-SecondOctet {
    param ($ip)
    foreach ($second_octet in 1..255) {
        $full_ip = ("$ip.$second_octet")
        Set-ThirdOctet($full_ip)
    }

}

switch ([int]$mask)
{
    8 {Set-SecondOctet($subnet)}
    16 {Set-ThirdOctet($subnet)}
    24 {Set-ForthOctet($subnet)}
}
