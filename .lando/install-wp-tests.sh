#!/bin/sh

wp scaffold plugin-tests --dir=/app
chmod +x /app/bin/install-wp-tests.sh
/app/bin/install-wp-tests.sh wordpress_test root '' database latest