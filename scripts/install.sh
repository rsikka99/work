#!/usr/bin/env bash
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#   _____                             _            _____  __    __  #
#  /__   \__ _ _ __   __ _  ___ _ __ | |_    /\/\ /__   \/ / /\ \ \ #
#    / /\/ _` | '_ \ / _` |/ _ \ '_ \| __|  /    \  / /\/\ \/  \/ / #
#   / / | (_| | | | | (_| |  __/ | | | |_  / /\/\ \/ /    \  /\  /  #
#   \/   \__,_|_| |_|\__, |\___|_| |_|\__| \/    \/\/      \/  \/   #
#                    |___/                                          #
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
# This script is used to ensure the proper permissions are set on directories along with any setup functions we need to run.

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )

echo $DIR;

# Folders under the data folder need to be writable by the web user
chmod 777 "$DIR/data/cache"
chmod 777 "$DIR/data/indexes"
chmod 777 "$DIR/data/logs"
chmod 777 "$DIR/data/sessions"
chmod 777 "$DIR/data/temp"
chmod 777 "$DIR/data/uploads"


# Our public folder needs its cache folder set to be writable
chmod 777 "$DIR/public/cache"



git clone https://github.com/tangentmtw/PrintIQMPS.git /home/bitnami/apps/dev.mpstoolbox.com