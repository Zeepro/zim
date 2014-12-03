#!/bin/sh

TIMELAPSE=/var/www/tmp/timelapse.mp4

# stop ffmpeg
/etc/init.d/ffmpeg stop
/bin/rm -fv /tmp/Camera.json

# touch timelapse file
touch $TIMELAPSE
/bin/chown www-data $TIMELAPSE
/bin/chmod 777 $TIMELAPSE

# take photo as last frame
retry=0
while [ $retry -lt 4 ]
do
	/bin/fuser /dev/video0
	if [ $? != 0 ]
	then
		# take photo if device is available
		ffmpeg -f video4linux2 -i /dev/video0 -y -vframes 1 /var/www/tmp/image.jpg
		break
	fi
	/bin/sleep 3
	retry=`/usr/bin/expr $retry + 1`
done

# timelapse generation
if [ $retry -lt 4 ]
then
	# with photo
	ffmpeg -r 10 -f image2 -s 640x360 -i /var/www/tmp/img%03d.jpg -i /var/www/tmp/image.jpg -i /var/www/images/logo_calque_60.png -y -filter_complex "[0:v][1:v]concat=n=2:v=1[bg];[bg][2:v]overlay=380:5" -vcodec libx264 -crf 35 $TIMELAPSE
else
	# without photo
	ffmpeg -r 10 -f image2 -s 640x360 -i /var/www/tmp/img%03d.jpg -i /var/www/images/logo_calque_60.png -y -filter_complex "[0:v][1:v]overlay=380:5" -vcodec libx264 -crf 35 $TIMELAPSE
fi
/bin/chown www-data $TIMELAPSE
/bin/chmod 777 $TIMELAPSE

# clean temporary file
/etc/init.d/ffmpeg clean_tl
