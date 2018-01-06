#!/usr/bin/env bash

gcloud beta compute \
	--project "infra-189212" instances create "reddit-app" \
	--zone "europe-west1-b" \
	--machine-type "f1-micro" \
	--subnet "default" \
	--maintenance-policy "MIGRATE" \
	--service-account "628149288272-compute@developer.gserviceaccount.com" \
	--scopes "https://www.googleapis.com/auth/devstorage.read_only","https://www.googleapis.com/auth/logging.write","https://www.googleapis.com/auth/monitoring.write","https://www.googleapis.com/auth/servicecontrol","https://www.googleapis.com/auth/service.management.readonly","https://www.googleapis.com/auth/trace.append" \
	--min-cpu-platform "Automatic" \
	--tags "puma-server" \
	--image-family "reddit-full" \
	--image-project "infra-189212" \
	--boot-disk-size "10" \
	--boot-disk-type "pd-standard" \
	--boot-disk-device-name "reddit-app" \
    --metadata startup-script='#!/usr/bin/env bash
sudo su - appuser -c "cd ~/reddit && puma -d"
'
exit 0
