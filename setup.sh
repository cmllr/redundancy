#!/bin/bash
COLOR_red='\E[31m'
COLOR_green='\E[32m'
INIT=Blue
#Texts
Redundancy_Version="1.9.X"
Title="Redundancy requirement installer for version $Redundancy_Version"
Welcome="Welcome to the Redundancy requirement installer.\nThis program will prepare your Redundancy Installation to get installed.\nPress OK to continue"
License=$(<LICENSE)
Info="Redundancy will now set up the needed permissions. Do you want to continue?"
Action="Setting permissions on Redundancy.conf, DataBase.inc.php, Storage, Temp and Snapshots"
Database_Server="Redundancy needs to create an empty database. Please enter your database server (default is localhost)"
Database_User="Please enter the database user"
Database_Pass="Please enter the password the database user: " 
Database_Name="Please enter a name for the new database"
Database_Install="Redundancy will now connect to the database server to create a new database. Continue?"
Abort="Setup aborted"
Missing="Please enter a value"
Failed="false"
#End of texts

check_returncode(){
        if [ $? != 0 ]
        then
                Failed="true"
        fi
}

#Welcome dialog
whiptail --title "$Title" --msgbox "$Welcome" 8 78
#License screen
whiptail --title "$Title" --ok-button "Next" --textbox LICENSE 0 60
whiptail --title "$Title" --yesno "Do You accept the terms and conditions?" 8 78
if [ $? != 0 ]
then
	exit 127
fi
#Information screen
whiptail --title "$Title" --yesno "$Info" 8 78
status=$?
if [ $status != 0 ]; then
	echo "Setup aborted. Nothing changed.";
	exit 127
fi

{
	echo 0
	CONFR=$(chmod 777 ./Redundancy.conf)
	check_returncode
	echo 20
	DATABASER=$(chmod 777 ./Includes/DataBase.inc.php)
	check_returncode
	echo 40
	STORAGER=$(chmod 777 -R ./Storage/)
	check_returncode
	echo 60
	TEMPR=$(chmod 777 -R ./Temp/)
	check_returncode
	echo 80
	SNAPSHOTSR=$(chmod 777 -R ./Snapshots/)
	check_returncode
	echo 100
} | whiptail --title "$Title" --gauge "$Action" 8 78 0
if [ $Failed != "false" ]
then
	echo "Setup failed. Could not set permissions."
	exit 127 
fi
SERVER=""
USER=""
DB=""
PASS=""
#Get Server value
SERVER=$(whiptail --inputbox "$Database_Server" 8 78 --title "$Title" 3>&1 1>&2 2>&3)
if [ $? != 0 ]; then
        echo "$Abort"
        exit 127
fi

while [ -z $SERVER ]
do
whiptail --msgbox "$Missing" 8 78 --title "$Title"
SERVER=$(whiptail --inputbox "$Database_Server" 8 78 --title "$Title" 3>&1 1>&2 2>&3)
if [ $? != 0 ]; then
	echo "$Abort"
	exit 127
fi
done

#Get User value
USER=$(whiptail --inputbox "$Database_User" 8 78 --title "$Title"  3>&1 1>&2 2>&3)
if [ $? != 0 ]; then
        echo "$Abort"
        exit 127
fi

while [ -z $USER ]
do
whiptail --msgbox "$Missing" 8 78 --title "$Title"
USER=$(whiptail --inputbox "$Database_User" 8 78 --title "$Title"  3>&1 1>&2 2>&3)
if [ $? != 0 ]; then
        echo "$Abort"
        exit 127
fi
done

#Get Database name
DB=$(whiptail --inputbox "$Database_Name" 8 78 --title "$Title"  3>&1 1>&2 2>&3)
if [ $? != 0 ]; then
        echo "$Abort"
        exit 127
fi

while [ -z $DB ]
do
whiptail --msgbox "$Missing" 8 78 --title "$Title"
DB=$(whiptail --inputbox "$Database_Name" 8 78 --title "$Title"  3>&1 1>&2 2>&3)
if [ $? != 0 ]; then
        echo "$Abort"
        exit 127
fi
done
#Get server password empty passwords could be possible
PASS=$(whiptail --passwordbox "$Database_Pass" 8 78 --title "$Title" 3>&1 1>&2 2>&3)

whiptail --title "$Title" --yesno "Use this config? Database $DB on server $SERVER with $USER" 8 78

status=$?
if [ $status != 0 ]; then
        echo "Setup aborted. Nothing changed.";
        exit 127
fi

#Input database values

mysqladmin -u $USER -p$PASS create $DB 

if [ $? != 0 ]; then
	whiptail --title "$Title" --msgbox "The setup failed. Could not connect to the database." 8 78
	exit 127
fi
whiptail --title "$Title" --msgbox "The setup was completed. Please open the final setup using your webbrowser" 8 78

