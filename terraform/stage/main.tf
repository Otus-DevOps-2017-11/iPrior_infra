provider "google" {
  region  = "${var.region}"
  version = "1.4.0"
  project = "${var.project}"
}

module "app" {
  source          = "../modules/app"
  public_key_path = "${var.public_key_path}"
  zone            = "${var.zone}"
  app_disk_image  = "${var.app_disk_image}"
  instance_name   = "stage-reddit-app"
}

module "db" {
  source            = "../modules/db"
  public_key_path   = "${var.public_key_path}"
  zone              = "${var.zone}"
  db_disk_image     = "${var.db_disk_image}"
  instance_name     = "stage-reddit-db"
  app_instance_name = "stage-reddit-app"
}

module "vpc" {
  source        = "../modules/vpc"
  source_ranges = ["0.0.0.0/0"]
}
