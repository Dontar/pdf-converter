SCR_PATH=`dirname $0`
PRJ_PATH=`realpath ${SCR_PATH}/..`

docker rm -f pdf-converter
docker run \
	--name pdf-converter \
	--network ecn_debug \
	-p 3000:3000 \
	-v ${PRJ_PATH}:/srv/pdf-converter \
	-d localhost:5000/pdf-converter:latest
