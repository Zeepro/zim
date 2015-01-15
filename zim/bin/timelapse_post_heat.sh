#!/bin/sh

PATH=$PATH:/bin

CAMERAINF=/tmp/Camera.json

# stop ffmpeg
/etc/init.d/ffmpeg stop

# check camera availability before switch camera mode
retry=0
while [ $retry -lt 4 ]
do
	fuser /dev/video0
	if [ $? != 0 ]
	then
		break
	fi
	sleep 3
	retry=`expr $retry + 1`
done

if [ $retry -ge 4 ]
then
	rm -fv $CAMERAINF
	exit 1
fi

# final command to be added by mobile site automatically to re-open camera with timelapse images
