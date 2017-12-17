#!/usr/bin/env bash

sudo apt-get update
sudo apt-get install -y git-core
git clone https://github.com/Otus-DevOps-2017-11/iPrior_infra.git /tmp/otus
cd /tmp/otus && git checkout Infra-2
sudo chmod 0755 /tmp/otus/install_ruby.sh
sudo chmod 0755 /tmp/otus/install_mongodb.sh
sudo chmod 0755 /tmp/otus/deploy.sh
/tmp/otus/install_ruby.sh > /tmp/install_ruby.log
/tmp/otus/install_mongodb.sh > /tmp/install_mongo.log
sudo su - appuser -c "/tmp/otus/deploy.sh > /tmp/deploy.log"

exit 0