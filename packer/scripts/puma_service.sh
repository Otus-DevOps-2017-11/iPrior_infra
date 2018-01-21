#!/usr/bin/env bash

wget https://gist.githubusercontent.com/Nklya/5db89c8ae4613fe1609fe87f2cdb0203/raw/7dccf8a0f1352dd865f955706085993a203f6ae7/puma.service -O /tmp/puma.service

mv /tmp/puma.service /etc/systemd/system/puma.service

sudo systemctl enable puma

exit 0
