#!/bin/sh

PATH=$PATH:/bin

TIMELAPSE=/var/www/tmp/timelapse.mp4
TEMPVIDEO=/var/www/tmp/tempvideo.mp4
CAMERAINF=/tmp/Camera.json

# stop ffmpeg and clean temporary file
/etc/init.d/ffmpeg stop
rm -fv $CAMERAINF $TEMPVIDEO $TIMELAPSE
/etc/init.d/ffmpeg clean_tl
