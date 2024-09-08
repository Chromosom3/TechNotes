# Script: user-accounts.ps1
# Author: Dylan 'Chromosome' Navarro
# Description: This script is designed to fetch user account information from Active Directory and orginize it in a CSV.

$outputFolder = [Environment]::GetFolderPath("MyDocuments")
$outputFile = "$outputFolder\user-accounts.csv"
$searchBase = ""  # Specify your search base here. Example: OU=Test,DC=example,DC=com

Get-ADUser -Filter 'enabled -eq $true'-Properties * -SearchBase $searchBase | Select-Object DisplayName, SamAccountName, EmailAddress, Description, HomeDirectory, DistinguishedName, LockedOut, BadLogonCount, PasswordExpired, PasswordLastSet, PasswordNeverExpires, PasswordNotRequired, CannotChangePassword, lastLogon, Created, Modified, AccountExpirationDate | Export-Csv $outputFile

#Grabs the output of the last command 
$importedUsers = Import-Csv $outputFile

#Defines the list we are going to through the output into. 
$Output = @()

#Loop for each user in the domain.
foreach ($user in $importedUsers) {
    #Variable for the groups a user is a member of. 
    $userGroups = @()
    #Gets the groups people are in.
    Get-ADPrincipalGroupMembership $user.SamAccountName | Foreach-Object {
        $userGroups += $_.name #This just combines all the groups into the $userGroups variable.
    }
    #This combines the original information from Get-ADUser with the information from Get-ADPrincipalGroupMembership.
    $combined = @{
        "Name" = $user.DisplayName
        "Username" = $user.SamAccountName
        "Email" = $user.EmailAddress
        "Description" = $user.Description
        "Drive" = $user.HomeDirectory
        "Location" = $user.DistinguishedName
        "Lockedout" = $user.LockedOut
        "Failed Logon" = $user.BadLogonCount
        "Pass Expired" = $user.PasswordExpired
        "Pass Last Changed" = $user.PasswordLastSet
        "Pass Never Expire" = $user.PasswordNeverExpires
        "Pass Not Required" = $user.PasswordNotRequired
        "Cannot Change Pass" = $user.CannotChangePassword
        "Last Logon" = $user.lastLogon #I think this might need some work, doesnt show actual dates just shows strange value.
        "Created" = $user.Created
        "Modified" = $user.Modified
        "Experation" = $user.AccountExpirationDate
        "Groups" = ($userGroups -join ',') #Takes the $userGroups variable and combines the values with a ',' in between.
    }
    $Output += New-Object PSObject -Property $combined #Adds the combined values to the output value to be exported later. 
}

$Output | Export-Csv $outputFile #Exports the user and group info to CSV.
