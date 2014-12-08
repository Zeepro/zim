#!/bin/sh

PATH=$PATH:/bin

IMAGESPAT=/var/www/tmp/img%03d.jpg
IMAGEPATH=/var/www/tmp/
TIMELAPSE=/var/www/tmp/timelapse.mp4
TEMPVIDEO=/var/www/tmp/tempvideo.mp4
WATERMARK=/var/www/images/logo_calque_60.png
PHOTOPATH=/var/www/tmp/image.jpg
POWEREDVD=/var/www/images/powered.mp4
CAMERAINF=/tmp/Camera.json

# stop ffmpeg
/etc/init.d/ffmpeg stop
rm -fv $CAMERAINF

# get estimated duration and fade out point
fadeptr=`find $IMAGEPATH -maxdepth 1 -name img*.jpg | wc -l`
echo -n "fadeptr = "
echo $fadeptr

# clean temporary file and exit if we have no image files
if [ $fadeptr -eq 0 ]
then
	rm -fv $TEMPVIDEO $TIMELAPSE
	/etc/init.d/ffmpeg clean_tl
	exit
fi

# touch timelapse file
touch $TIMELAPSE
chown www-data $TIMELAPSE
chmod 777 $TIMELAPSE

# take photo as last frame
retry=0
while [ $retry -lt 4 ]
do
	fuser /dev/video0
	if [ $? != 0 ]
	then
		# take photo if device is available
		ffmpeg -f video4linux2 -i /dev/video0 -y -vframes 1 $PHOTOPATH
		break
	fi
	/bin/sleep 3
	retry=`expr $retry + 1`
done

# assign proper last image file
last_image=$PHOTOPATH # with photo
if [ $retry -ge 4 ]
then
	# without photo
	last_image=`find $IMAGEPATH -maxdepth 1 -name img*.jpg | sort | tail -n 1`
fi

# timelapse generation
if [ -e $TIMELAPSE ]
then
	ffmpeg -r 10 -loop 1 -i $last_image -t 1 -y -vcodec libx264 -crf 29 $TEMPVIDEO
	ffmpeg -r 10 -f image2 -i $IMAGESPAT -i $TEMPVIDEO -i $WATERMARK -i $POWEREDVD -y -filter_complex "[0:v][1:v]concat=n=2:v=1[bg];[bg][2:v]overlay=380:5[pt];[pt]fade=t=out:$fadeptr:10:color=white[tl];[tl][3:v]concat=n=2:v=1" -vcodec libx264 -crf 29 -pix_fmt yuv420p $TIMELAPSE
	rm -fv $TEMPVIDEO
	
	chown www-data $TIMELAPSE
	chmod 777 $TIMELAPSE
fi

# clean temporary file
/etc/init.d/ffmpeg clean_tl
