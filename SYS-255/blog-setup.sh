#!/usr/bin/env bash

# Script: blog-setup.sh
# Author: Dylan 'Chromosome' Navarro
# Description: This script is designed to automate the setup of blog01 for my SYS255 class. The script will achive the following:
# - Change the IP to be on the 10.0.5.0/24 subnet.
# - Crate a named user with sudo permissions.
# - Disable root SSH
# - Use realmd to join to the domain environment.
# - Install LAMP on the server.
# - Install wordpress for a blog software.

############################################################################################################################################################

function networkSetup(){
    # Changes the network config for the SYS-255 network (10.0.5.0/24).
    ls /etc/sysconfig/network-scripts | grep ens  # list the network interfaces then filters for ens interfaces.
    read -p "Interface to change (ens192):  " INTERFACE2CHANGE  #Takes a user input for the network interface.
    if [ -z $INTERFACE2CHANGE ]  # This if statement just checks to see if the user inputs nothing.
    then
        INTERFACE2CHANGE="ens192"  # Sets to the default interface (ens192).
    fi
    echo "You selected $INTERFACE2CHANGE"  # Prints a confirmation.

    INTERFACE_PATH="/etc/sysconfig/network-scripts/ifcfg-$INTERFACE2CHANGE"  # Variable to store the full file path for the new network config.
    INTERFACE_UUID=`cat $INTERFACE_PATH | grep UUID`  # Variable to grab the UUID from the current ifcfg file.
    mv $INTERFACE_PATH $INTERFACE_PATH-back  # Makes a backup of the current interface configuration.

    read -p "Assign IP (Only need last octal): 10.0.5." IP  # Takes user input for the last octal of the IP.

    networkCreate  # Calls the function that creates the network configuration file.

    systemctl restart network  # Restarts the network service.
}

function networkCreate(){
    touch $INTERFACE_PATH  # Makes the new interface file
    ## This block just creates the contents of the file.
    echo "TYPE=Ethernet
    PROXY_METHOD=none
    BROWSER_ONLY=no
    BOOTPROTO=none
    DEFROUTE=yes
    IPV4_FAILURE_FATAL=no
    IPV6INIT=no
    IPV6_AUTOCONF=\"yes\"
    IPV6_DEFROUTE=\"yes\"
    IPV6_FAILURE_FATAL=\"no\"
    IPV6_ADDR_GEN_MODE=\"stable-privacy\"
    NAME=$INTERFACE2CHANGE
    $INTERFACE_UUID
    DEVICE=$INTERFACE2CHANGE
    ONBOOT=yes
    IPADDR=10.0.5.$IP
    PREFIX=24
    GATEWAY=10.0.5.2
    DNS1=10.0.5.6
    DOMAIN=dylan.local" >> $INTERFACE_PATH
    ## End of the new network file. 
}


function networkChangeHostname(){
    read -p "Enter system hostname (blog01-dylan):  " NEW_HOSTNAME
    if [ -z $NEW_HOSTNAME ] 
    then
        NEW_HOSTNAME="blog01-dylan"  # Sets to the hostname to the default value (blog01-dylan). 
    fi
    hostnamectl set-hostname $NEW_HOSTNAME  # Sets the hostname. 
    echo "Hostname changed to $NEW_HOSTNAME."
    systemctl restart network  # Restarts the network service.
}


function newSudoUser(){
    # Crates a new user with sudo permissions.
    read -p "New username (dylan):  " NEW_USER  # User provides input for the new user's username.
    if [ -z $NEW_USER ]  
    then
        NEW_USER="dylan"  # If the user hit enter sets the username to the default (dylan).
    fi
    useradd -m -d /home/$NEW_USER $NEW_USER && usermod -aG wheel $NEW_USER  # Creates the user and their home directory then adds them to wheel (admin) group.  
    passwd $NEW_USER  # Changes the users password. Prompts the person running the script to enter the new password. 
}


function secureSSH(){
    # Disable root ssh
    sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config  # Removed the comment character from the sshd_config file to disable root ssh.
    systemctl restart sshd  # Restarts the ssh service.
}


# Realmd          | NEED TO TEST ON NETWORK |
function realmdSetup(){
    # First lets download realmd and all the dependencies. 
    yum install realmd samba samba-common oddjob oddjob-mkhomedir sssd -y

    echo "--- Please provide the domain name as well as a domain admin account ---"
    read -p "Enter Domain (dylan.local):  " DOMAIN
    read -p "Enter username (dylan.navarro-adm):  " DOMAIN_USER


    if [ -z $DOMAIN ]  #Checks to see if the user entered anything for the domain name. 
    then
        DOMAIN="dylan.local"  # If the user did not provide any input we will set it to the default domain (dylan.local).
    fi

    if [ -z $DOMAIN_USER ]  # This will check to see if the user provided a user account to join the system to the domain with.
    then
        DOMAIN_USER="dylan.navarro-adm"  # If the user did not provide any input we will set it to the default user (dylan.navarro-adm).
    fi

    realm join --user=$DOMAIN_USER $DOMAIN
}

function lampSetup(){
    yum install httpd mariadb-server mariadb php php-mysql php-gd wget rsync -y  # Gets everything for the LAMP stack.
    
    # Service Setup
    systemctl start httpd
    systemctl enable httpd
    systemctl start mariadb
    systemctl enable mariadb

    # Call Other Functions
    httpdSetup
    mysqlSetup
    phpSetup
    wordpressSetup
}

function httpdSetup(){
    sed -i 's/^/#/' /etc/httpd/conf.d/welcome.conf  # Comments out the default apache file. 
    firewall-cmd --add-service=http --permanent  # This will add a firewall rule for port 80 (HTTP).
    firewall-cmd --add-service=https --permanent  # This will add a firewall rule for port 443 (HTTPS).
    firewall-cmd --reload  # This will update the firewall to apply the changes.
}

function mysqlSetup(){
    read -p "You must set a mysql root password: " SQL_ROOT_PASS
    echo "You have set $SQL_ROOT_PASS as your password for the mysql root user."
    read -p "You must set a wordpress mysql password: " WORDPRESS_SQL_PASS
    echo "You have set $WORDPRESS_SQL_PASS as your password for the wordpress mysql user."

    # These next few lines are basically the equivilent of running mysql_secure_installation and selecting certain options
    # Logs into mysql as root. Then changes the root password. Removes remote root logon. Removes anonymous logons. Removes test dbs. Flushes privilages
    # Added some stuff for wordpress while we are in SQL with no root pass. Makes wordpress DB and User. Grants perms to user on database.
mysql -u root <<-EOF
UPDATE mysql.user SET Password=PASSWORD('$SQL_ROOT_PASS') WHERE User='root';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.db WHERE Db='test' OR Db='test_%';
CREATE DATABASE wordpress;
CREATE USER wordpressuser@localhost IDENTIFIED BY '$WORDPRESS_SQL_PASS';
GRANT ALL PRIVILEGES ON wordpress.* TO wordpressuser@localhost IDENTIFIED BY '$WORDPRESS_SQL_PASS';
FLUSH PRIVILEGES;
EOF
}

function phpSetup(){
    yum install epel-release yum-utils -y
    yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm -y
    yum-config-manager --enable remi-php73
    yum install php php-common php-opcache php-mcrypt php-cli php-gd php-curl php-mysqlnd -y
    systemctl restart httpd
}

function wordpressSetup(){
    wget http://wordpress.org/latest.tar.gz  # Downloads the latest build of wordpress.
    tar xzvf latest.tar.gz  # Unpacks the download
    rsync -avP wordpress/ /var/www/html/ && mkdir /var/www/html/wp-content/uploads # Moves all the files and makes the uploads directory.
    rm -rf wordpress latest.tar.gz  # Removes the empty wordpress folder and the tar from earlier.
    cp /var/www/html/wp-config-sample.php /var/www/html/wp-config.php 
    sed -i "s/database_name_here/wordpress/" /var/www/html/wp-config.php  # Changes the mysql database in the config.
    sed -i "s/username_here/wordpressuser/" /var/www/html/wp-config.php  # Changes the mysql user in the config.
    sed -i "s/password_here/$WORDPRESS_SQL_PASS/" /var/www/html/wp-config.php  # Changes the mysql password in the config.
    chown -R apache:apache /var/www/html/*  # Sets apache user and group to be the owner of everything in the html folder.
}

############################################################################################################################################################
# Call Functions
newSudoUser
secureSSH
lampSetup
networkSetup
networkChangeHostname
realmdSetup