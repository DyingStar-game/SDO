curl http://127.0.0.1/sdo/servers \
   -H "Cache-Control: must-revalidate" \
   -H "Pragma: no-cache" \
   -H "Expires: 0" \
   -H "Accept: application/json" > servers.json