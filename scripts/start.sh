SCR_PATH=`dirname $0`
PRJ_PATH=`realpath ${SCR_PATH}/..`

docker rm -f pdf-converter
docker run \
	--name pdf-converter \
	-p 3000:3000 \
	-d localhost:5000/pdf-converter:latest
