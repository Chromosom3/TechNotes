# Script: c2domain_alternative_HTML.ps1
# Author: Dylan 'Chromosom3' Navarro
# Description: Retrieves the Alternative HTML from the C2 Domain

$target_server = "http://www.malware430.com/html/x.php"

$messages = @()
foreach ($attempt in 1..250) {
    $Text = "$((Invoke-WebRequest -UserAgent "DylanNavarro" -Uri $target_server).Content)"
    if (!($Text -in $messages)){
        Write-Host "New message found: $Text"
        $messages += ,$Text
    }
    # Anti DOS Sleep
    Start-Sleep -Seconds 2
}
$file = "messages.txt"
New-Item $file
Add-Content $file "The following messages were found on the remote system.`n`n"
foreach ($message in $messages){
    Add-Content $file $message
}