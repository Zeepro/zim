#!/bin/sh

TIMELAPSE=/var/www/tmp/timelapse.mp4

# stop ffmpeg
/etc/init.d/ffmpeg stop
/bin/rm -fv /tmp/Camera.json

# clean temporary file
/bin/rm -fv $TIMELAPSE
/etc/init.d/ffmpeg clean_tl
