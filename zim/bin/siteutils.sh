#!/bin/sh

RETVAL=0

force_reco() {
	zfw_setenv force_reco 1

	return $RETVAL
}


# main program

case "$1" in
	force_reco)
		force_reco
		;;
	*)
		echo "Usage: $0 {force_reco|*}"
		exit 1
esac

exit $?
