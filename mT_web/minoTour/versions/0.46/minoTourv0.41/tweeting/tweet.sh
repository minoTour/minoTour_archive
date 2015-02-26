#!/bin/bash
#Twitter tweeter by http://360percents.com
#v1.1 on May 12th 2011
 
#REQUIRED PARAMS
username="matt.loose@nottingham.ac.uk"
password="pplgn962"
tweet="$*" #must be less than 140 chars
 
#EXTRA OPTIONS
uagent="Mozilla/5.0" #user agent (fake a browser)
sleeptime=0 #add pause between requests
 
if [ $(echo "${tweet}" | wc -c) -gt 140 ]; then
    echo "[FAIL] Tweet is over 140 chars!" && exit 1
fi
 
touch "cookie.txt" #create a temp. cookie file
 
#INITIAL PAGE
echo "[+] Fetching twitter.com..." && sleep $sleeptime
initpage=`curl -s -b "cookie.txt" -c "cookie.txt" -L --sslv3 -A "$uagent" "https://mobile.twitter.com/session/new"`
token=`echo "$initpage" | grep "authenticity_token" | sed -e 's/.*value="//' | sed -e 's/" \/>.*//'`
 
#LOGIN
echo "[+] Submitting the login form..." && sleep $sleeptime
loginpage=`curl -s -b "cookie.txt" -c "cookie.txt" -L --sslv3 -A "$uagent" -d "authenticity_token=$token&username=$username&password=$password" "https://mobile.twitter.com/session"`
 
#HOME PAGE
echo "[+] Getting your twitter home page..." && sleep $sleeptime
homepage=`curl -s -b "cookie.txt" -c "cookie.txt" -L -A "$uagent" "http://mobile.twitter.com/"`
 
#TWEET
echo "[+] Posting a new tweet..." && sleep $sleeptime
tweettoken=`echo "$homepage" | grep "authenticity_token" | sed -e 's/.*value="//' | sed -e 's/" \/>.*//' | tail -n 1`
update=`curl -s -b "cookie.txt" -c "cookie.txt" -L -A "$uagent" -d "authenticity_token=$tweettoken&tweet[text]=$tweet&tweet[display_coordinates]=false" "http://mobile.twitter.com/"`
 
#LOGOUT
echo "[+] Logging out..."
logout=`curl -s -b "cookie.txt" -c "cookie.txt" -L -A "$uagent" "http://mobile.twitter.com/session/destroy"`
 
rm "cookie.txt"
