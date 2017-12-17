#!/usr/bin/env bash

cd /home/appuser && git clone https://github.com/Otus-DevOps-2017-11/reddit.git
cd /home/appuser/reddit && bundle install
cd /home/appuser/reddit && puma -d

ps aux | grep puma | grep tcp
