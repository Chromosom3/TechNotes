# Script: c2domain_commands.ps1
# Author: Dylan 'Chromosom3' Navarro
# Description: Retrieves C2 commands from the target server.

$target_server = "http://www.malware430.com/html/x.php"

$commands = @()
foreach ($attempt in 1..250) {
    $EncodedText = "$((Invoke-WebRequest -UserAgent "HTTP get re" -Uri $target_server).Content)"
    $DecodedText = [System.Text.Encoding]::ASCII.GetString([System.Convert]::FromBase64String($EncodedText))
    if (!($DecodedText -in $commands)){
        Write-Host "New command found: $DecodedText"
        $commands += ,$DecodedText
    }
    # Anti DOS Sleep
    Start-Sleep -Seconds 2
}
$file = "comamnds.txt"
New-Item $file
Add-Content $file "The following commands were found on the remote system.`n`n"
foreach ($command in $commands){
    Add-Content $file $command
}