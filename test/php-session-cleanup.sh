#!/bin/sh
find /var/cpanel/php/sessions/ea-php70 -type f -cmin +12 -name "ci_session*" -exec rm -f {} \;